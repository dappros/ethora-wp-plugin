<?php
/**
 * Keeps the AI agent's knowledge of this site current.
 *
 * Ethora re-crawls the website on a schedule. With a refresh key set, this
 * class also tells Ethora which pages changed the moment they change: when a
 * post or page is published, updated, unpublished or trashed, its address is
 * sent to the Ethora refresh hook, which re-indexes that page (or removes it
 * when it no longer exists). Nothing is sent without a refresh key.
 *
 * @package EthoraChatAssistant
 * @since 1.8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Ethora_Refresh_Hook {

    const OPTION = 'ethora_chat_assistant_refresh_key';
    const MAX_URLS_PER_CALL = 20;

    /** Addresses to send at the end of this request. */
    private $pending = array();

    /** Published addresses captured before a post is changed, by post id. */
    private $before = array();

    public function __construct() {
        // After the main settings, so the field follows them in the section.
        add_action('admin_init', array($this, 'register_setting'), 20);
        add_action('pre_post_update', array($this, 'remember_published_url'), 10, 1);
        add_action('wp_trash_post', array($this, 'remember_published_url'), 10, 1);
        add_action('transition_post_status', array($this, 'on_transition'), 10, 3);
        add_action('shutdown', array($this, 'flush'));
        add_action('admin_post_ethora_refresh_all', array($this, 'refresh_all'));
        add_action('admin_notices', array($this, 'admin_notice'));
    }

    public function register_setting() {
        register_setting(
            'ethora_chat_assistant_settings',
            self::OPTION,
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_key'),
                'default' => '',
            )
        );
        add_settings_field(
            self::OPTION,
            esc_html__('Website refresh key (optional)', 'ethora-chat-assistant'),
            array($this, 'field_callback'),
            'ethora-chat-assistant',
            'ethora_chat_assistant_section'
        );
    }

    public function sanitize_key($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (!preg_match('/^erk_[a-f0-9]{48}$/', $value)) {
            add_settings_error(self::OPTION, 'ethora_refresh_key_invalid', esc_html__('The refresh key should look like erk_ followed by 48 characters. Copy it again from the Web Index panel in Ethora.', 'ethora-chat-assistant'));
            return (string) get_option(self::OPTION, '');
        }
        return $value;
    }

    public function field_callback() {
        $value = (string) get_option(self::OPTION, '');
        echo '<input type="password" autocomplete="off" name="' . esc_attr(self::OPTION) . '" value="' . esc_attr($value) . '" class="regular-text" placeholder="erk_..." />';
        echo '<p class="description">' . esc_html__('Re-indexes a post or page in your AI agent\'s knowledge as soon as it is published, updated or unpublished, instead of at the next scheduled refresh. Create the key in Ethora: open the agent, Web Index, Kept up to date, Publish hook.', 'ethora-chat-assistant') . '</p>';
        if ($value !== '') {
            $url = wp_nonce_url(admin_url('admin-post.php?action=ethora_refresh_all'), 'ethora_refresh_all');
            echo '<p><a class="button" href="' . esc_url($url) . '">' . esc_html__('Refresh website knowledge now', 'ethora-chat-assistant') . '</a></p>';
        }
    }

    private function key() {
        return (string) get_option(self::OPTION, '');
    }

    private function hook_url() {
        $api = (string) get_option('ethora_chat_assistant_api_url', ETHORA_DEFAULT_API_URL);
        if ($api === '') {
            $api = ETHORA_DEFAULT_API_URL;
        }
        $base = preg_replace('#/v[12]/?$#', '', untrailingslashit($api));
        return untrailingslashit($base) . '/v2/sources/refresh-hook';
    }

    /** Whether a post's address is public: a published post of a viewable type. */
    private function is_public_post($post) {
        if (!$post || wp_is_post_revision($post) || wp_is_post_autosave($post)) {
            return false;
        }
        if (!is_post_type_viewable($post->post_type)) {
            return false;
        }
        return !post_password_required($post);
    }

    /** Before an update or trash: the address the post had while published. */
    public function remember_published_url($post_id) {
        // The first capture wins: trashing renames the slug (__trashed) before
        // the update hooks fire a second time.
        if ($this->key() === '' || isset($this->before[$post_id])) {
            return;
        }
        $post = get_post($post_id);
        if ($post && $post->post_status === 'publish' && $this->is_public_post($post)) {
            $this->before[$post->ID] = get_permalink($post);
        }
    }

    public function on_transition($new_status, $old_status, $post) {
        if ($this->key() === '' || !$this->is_public_post($post)) {
            return;
        }
        if ($new_status !== 'publish' && $old_status !== 'publish') {
            return;
        }
        // The old address too: a changed slug, an unpublished or trashed post
        // all leave a page that no longer exists, which Ethora then removes.
        if (!empty($this->before[$post->ID])) {
            $this->queue($this->before[$post->ID]);
        }
        if ($new_status === 'publish') {
            $this->queue(get_permalink($post));
        }
    }

    private function queue($url) {
        $url = esc_url_raw((string) $url, array('http', 'https'));
        if ($url !== '' && !in_array($url, $this->pending, true)) {
            $this->pending[] = $url;
        }
    }

    /** Sends what changed in this request, without making the editor wait. */
    public function flush() {
        if (empty($this->pending) || $this->key() === '') {
            return;
        }
        foreach (array_chunk($this->pending, self::MAX_URLS_PER_CALL) as $urls) {
            $this->post(array('urls' => $urls), false);
        }
        $this->pending = array();
    }

    private function post($body, $blocking) {
        return wp_remote_post(
            $this->hook_url(),
            array(
                'blocking' => $blocking,
                'timeout' => $blocking ? 20 : 3,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->key(),
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode($body),
                'data_format' => 'body',
            )
        );
    }

    /** "Refresh website knowledge now" on the settings page. */
    public function refresh_all() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'ethora-chat-assistant'));
        }
        check_admin_referer('ethora_refresh_all');
        $result = 'error';
        if ($this->key() !== '') {
            $response = $this->post(array('all' => true), true);
            $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
            if ($code === 200) {
                $result = 'queued';
            } elseif ($code === 401) {
                $result = 'bad_key';
            }
        }
        wp_safe_redirect(add_query_arg('ethora_refresh', $result, admin_url('options-general.php?page=ethora-chat-assistant')));
        exit;
    }

    public function admin_notice() {
        if (!isset($_GET['ethora_refresh']) || !current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }
        $result = sanitize_key(wp_unslash($_GET['ethora_refresh'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ($result === 'queued') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Ethora is refreshing your website knowledge. Changed pages are re-indexed within a few minutes.', 'ethora-chat-assistant') . '</p></div>';
        } elseif ($result === 'bad_key') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Ethora did not accept the refresh key. Create a new one in the Web Index panel and paste it here.', 'ethora-chat-assistant') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Could not reach Ethora to refresh your website knowledge. Check the refresh key and the API URL, then try again.', 'ethora-chat-assistant') . '</p></div>';
        }
    }
}
