<?php
/**
 * Plugin Name: Ethora Chat Assistant
 * Plugin URI: https://ethora.com
 * Description: Add the Ethora AI chat assistant to your WordPress site. The widget script is bundled with the plugin and connects at runtime to the Ethora chat service (see readme: External services).
 * Version: 1.7.0
 * Requires at least: 5.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Author: RLDP, Ethora Team 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ethora-chat-assistant
 * Domain Path: /languages
 *
 * @package EthoraChatAssistant
 * @version 1.7.0
 * @author RLDP, Ethora Team
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ETHORA_PLUGIN_VERSION', '1.7.0');
define('ETHORA_DEFAULT_API_URL', 'https://api.chat.ethora.com/v1');
define('ETHORA_PLUGIN_FILE', __FILE__);
define('ETHORA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ETHORA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ETHORA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Ethora_Chat_Assistant {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Admin functionality
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_action('admin_init', array($this, 'register_settings'));
            add_filter('plugin_action_links_' . ETHORA_PLUGIN_BASENAME, array($this, 'add_settings_link'));
        }
        
        // Frontend functionality
        add_action('wp_head', array($this, 'inject_script_head'));
        add_action('wp_footer', array($this, 'inject_script_footer'));
    }
    
    /**
     * Add a "Settings" link to the plugin row on the Plugins screen.
     */
    public function add_settings_link($links) {
        $url = admin_url('options-general.php?page=ethora-chat-assistant');
        $settings_link = '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'ethora-chat-assistant') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            esc_html__('Ethora Chat Assistant', 'ethora-chat-assistant'),
            esc_html__('Ethora Chat Assistant', 'ethora-chat-assistant'),
            'manage_options',
            'ethora-chat-assistant',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_ethora-chat-assistant') {
            return;
        }
        
        wp_enqueue_style(
            'ethora-chat-assistant-admin',
            ETHORA_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            ETHORA_PLUGIN_VERSION
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'ethora_chat_assistant_settings',
            'ethora_chat_assistant_bot_id',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            )
        );
        
        register_setting(
            'ethora_chat_assistant_settings',
            'ethora_chat_assistant_position',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_position'),
                'default' => 'head'
            )
        );

        register_setting(
            'ethora_chat_assistant_settings',
            'ethora_chat_assistant_api_url',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_api_url'),
                'default' => ETHORA_DEFAULT_API_URL
            )
        );

        add_settings_section(
            'ethora_chat_assistant_section',
            esc_html__('Chat Assistant Configuration', 'ethora-chat-assistant'),
            array($this, 'settings_section_callback'),
            'ethora-chat-assistant'
        );
        
        add_settings_field(
            'ethora_chat_assistant_bot_id',
            esc_html__('Bot ID', 'ethora-chat-assistant'),
            array($this, 'bot_id_field_callback'),
            'ethora-chat-assistant',
            'ethora_chat_assistant_section'
        );
        
        add_settings_field(
            'ethora_chat_assistant_position',
            esc_html__('Script Position', 'ethora-chat-assistant'),
            array($this, 'position_field_callback'),
            'ethora-chat-assistant',
            'ethora_chat_assistant_section'
        );

        add_settings_field(
            'ethora_chat_assistant_api_url',
            esc_html__('API URL (advanced)', 'ethora-chat-assistant'),
            array($this, 'api_url_field_callback'),
            'ethora-chat-assistant',
            'ethora_chat_assistant_section'
        );

        // Optional appearance / theming settings. Each maps to a data-*
        // attribute the widget reads.
        add_settings_section(
            'ethora_chat_assistant_appearance_section',
            esc_html__('Appearance (optional)', 'ethora-chat-assistant'),
            array($this, 'appearance_section_callback'),
            'ethora-chat-assistant'
        );
        foreach ($this->appearance_settings() as $key => $cfg) {
            $option = 'ethora_chat_assistant_' . $key;
            register_setting(
                'ethora_chat_assistant_settings',
                $option,
                array(
                    'type' => 'string',
                    'sanitize_callback' => $cfg['sanitize'],
                    'default' => '',
                )
            );
            add_settings_field(
                $option,
                esc_html($cfg['label']),
                array($this, 'appearance_field_callback'),
                'ethora-chat-assistant',
                'ethora_chat_assistant_appearance_section',
                array('key' => $key)
            );
        }
    }

    /**
     * Appearance settings definition: option key => label / data-* attribute /
     * input renderer / sanitizer. Keeps registration, rendering and emitting
     * in one place.
     */
    private function appearance_settings() {
        return array(
            'primary_color'   => array('label' => 'Primary color', 'attr' => 'data-primary-color', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'secondary_color' => array('label' => 'Secondary color', 'attr' => 'data-secondary-color', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'icons_color'     => array('label' => 'Icons color', 'attr' => 'data-icons-color', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'own_bubble_bg'   => array('label' => 'Your bubble background', 'attr' => 'data-own-bubble-bg', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'other_bubble_bg' => array('label' => 'Bot bubble background', 'attr' => 'data-other-bubble-bg', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'input_bg'        => array('label' => 'Input background', 'attr' => 'data-input-bg', 'render' => 'color', 'sanitize' => array($this, 'sanitize_color')),
            'font_family'     => array('label' => 'Font family', 'attr' => 'data-font-family', 'render' => 'text', 'sanitize' => 'sanitize_text_field'),
            'google_font'     => array('label' => 'Google font name', 'attr' => 'data-google-font', 'render' => 'text', 'sanitize' => 'sanitize_text_field'),
            'dock'            => array('label' => 'Widget position', 'attr' => 'data-position', 'render' => 'dock', 'sanitize' => array($this, 'sanitize_dock')),
            'width'           => array('label' => 'Width (px)', 'attr' => 'data-width', 'render' => 'number', 'sanitize' => 'absint'),
            'height'          => array('label' => 'Height (px)', 'attr' => 'data-height', 'render' => 'number', 'sanitize' => 'absint'),
        );
    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__('Configure your Ethora Chat Assistant settings below.', 'ethora-chat-assistant') . '</p>';
    }
    
    /**
     * Bot ID field callback
     */
    public function bot_id_field_callback() {
        $value = get_option('ethora_chat_assistant_bot_id', '');
        echo '<input type="text" name="ethora_chat_assistant_bot_id" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your Ethora bot ID here.', 'ethora-chat-assistant') . '</p>';
    }
    
    /**
     * Position field callback
     */
    public function position_field_callback() {
        $value = esc_attr(get_option('ethora_chat_assistant_position', 'head'));
        echo '<select name="ethora_chat_assistant_position">';
        echo '<option value="head" ' . selected($value, 'head', false) . '>' . esc_html__('Header', 'ethora-chat-assistant') . '</option>';
        echo '<option value="footer" ' . selected($value, 'footer', false) . '>' . esc_html__('Footer', 'ethora-chat-assistant') . '</option>';
        echo '</select>';
        echo '<p class="description">' . esc_html__('Choose where to inject the chat script.', 'ethora-chat-assistant') . '</p>';
    }
    
    /**
     * Position field callback
     */
    public function api_url_field_callback() {
        $value = get_option('ethora_chat_assistant_api_url', ETHORA_DEFAULT_API_URL);
        echo '<input type="url" name="ethora_chat_assistant_api_url" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr(ETHORA_DEFAULT_API_URL) . '" />';
        echo '<p class="description">' . esc_html__('Ethora API base URL the chat widget connects to. Leave as the default for Ethora Cloud; change it only if you use a self-hosted or dedicated Ethora server.', 'ethora-chat-assistant') . '</p>';
    }

    /**
     * Appearance section intro
     */
    public function appearance_section_callback() {
        echo '<p>' . esc_html__('Customize the look of the chat widget. Leave any field blank to use the default.', 'ethora-chat-assistant') . '</p>';
    }

    /**
     * Generic renderer for an appearance field (color / text / number / dock).
     */
    public function appearance_field_callback($args) {
        $settings = $this->appearance_settings();
        $key = isset($args['key']) ? $args['key'] : '';
        if (!isset($settings[$key])) {
            return;
        }
        $cfg = $settings[$key];
        $option = 'ethora_chat_assistant_' . $key;
        $value = get_option($option, '');

        switch ($cfg['render']) {
            case 'color':
                echo '<input type="text" name="' . esc_attr($option) . '" value="' . esc_attr($value) . '" class="regular-text" placeholder="#1976d2 / rgb(...)" />';
                break;
            case 'number':
                echo '<input type="number" min="0" name="' . esc_attr($option) . '" value="' . esc_attr($value) . '" class="small-text" />';
                break;
            case 'dock':
                echo '<select name="' . esc_attr($option) . '">';
                echo '<option value="" ' . selected($value, '', false) . '>' . esc_html__('Default (right)', 'ethora-chat-assistant') . '</option>';
                echo '<option value="right" ' . selected($value, 'right', false) . '>' . esc_html__('Right', 'ethora-chat-assistant') . '</option>';
                echo '<option value="left" ' . selected($value, 'left', false) . '>' . esc_html__('Left', 'ethora-chat-assistant') . '</option>';
                echo '</select>';
                break;
            default:
                echo '<input type="text" name="' . esc_attr($option) . '" value="' . esc_attr($value) . '" class="regular-text" />';
                break;
        }
    }

    /**
     * Sanitize a CSS color (hex or rgb/rgba). Empty string clears it.
     */
    public function sanitize_color($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value)) {
            return $value;
        }
        if (preg_match('/^rgba?\(\s*[0-9.,%\s]+\)$/', $value)) {
            return $value;
        }
        return '';
    }

    /**
     * Sanitize the widget dock side.
     */
    public function sanitize_dock($value) {
        $value = sanitize_key((string) $value);
        return in_array($value, array('left', 'right'), true) ? $value : '';
    }

    /**
     * Sanitize position value
     */
    public function sanitize_position($value) {
        $value = sanitize_key((string) $value);
        $allowed_values = array('head', 'footer');
        return in_array($value, $allowed_values, true) ? $value : 'head';
    }

    /**
     * Sanitize the API URL. Falls back to the default when empty or invalid.
     */
    public function sanitize_api_url($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return ETHORA_DEFAULT_API_URL;
        }
        $value = esc_url_raw($value, array('http', 'https'));
        return $value !== '' ? $value : ETHORA_DEFAULT_API_URL;
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'ethora-chat-assistant'));
        }
        
        include ETHORA_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * Inject script in head
     */
    public function inject_script_head() {
        if (get_option('ethora_chat_assistant_position', 'head') === 'head') {
            $this->enqueue_chat_script();
        }
    }
    
    /**
     * Inject script in footer
     */
    public function inject_script_footer() {
        if (get_option('ethora_chat_assistant_position', 'head') === 'footer') {
            $this->enqueue_chat_script();
        }
    }
    
    /**
     * Enqueue the chat script using WordPress functions
     * This plugin provides a local implementation of the chat assistant
     */
    private function enqueue_chat_script() {
        $bot_id = get_option('ethora_chat_assistant_bot_id');
        if (empty($bot_id)) {
            return;
        }
        
        // Enqueue the local, non-minified Ethora assistant script
        wp_enqueue_script(
            'ethora-chat-assistant',
            ETHORA_PLUGIN_URL . 'assets/js/ethora_assistant.js',
            array(),
            ETHORA_PLUGIN_VERSION,
            get_option('ethora_chat_assistant_position', 'head') === 'footer'
        );
        
        $api_url = get_option('ethora_chat_assistant_api_url', ETHORA_DEFAULT_API_URL);
        if (empty($api_url)) {
            $api_url = ETHORA_DEFAULT_API_URL;
        }

        // Derive the appId for the widget's session call from the bot ID.
        // Ethora bot JIDs look like "<appId>_<botUser>-bot@host", so the appId
        // is the local-part prefix before the first underscore. The widget can
        // also fall back to deriving it from data-bot-id, but we pass it
        // explicitly so the new-arch contract works without relying on that.
        $app_id = $bot_id;
        if (strpos($app_id, '@') !== false) {
            $app_id = substr($app_id, 0, strpos($app_id, '@'));
        }
        if (strpos($app_id, '_') !== false) {
            $app_id = substr($app_id, 0, strpos($app_id, '_'));
        }

        // Build the optional appearance attributes (only those the admin set).
        $appearance_js = '';
        foreach ($this->appearance_settings() as $key => $cfg) {
            $val = get_option('ethora_chat_assistant_' . $key, '');
            if ($val !== '' && $val !== null) {
                $appearance_js .= "\n                scriptTag.setAttribute(\"" . esc_js($cfg['attr']) . "\", \"" . esc_js($val) . "\");";
            }
        }

        // Add inline script to create the required script tag with bot ID,
        // app ID, the API URL and any appearance overrides. This is how the
        // Ethora assistant expects to be initialized.
        wp_add_inline_script(
            'ethora-chat-assistant',
            'document.addEventListener("DOMContentLoaded", function() {
                // Create script tag with config for the Ethora assistant to read
                var scriptTag = document.createElement("script");
                scriptTag.id = "chat-content-assistant";
                scriptTag.setAttribute("data-app-id", "' . esc_js($app_id) . '");
                scriptTag.setAttribute("data-bot-id", "' . esc_js($bot_id) . '");
                scriptTag.setAttribute("data-api-url", "' . esc_js($api_url) . '");' . $appearance_js . '
                document.head.appendChild(scriptTag);
            });'
        );
    }
}

/**
 * Initialize the plugin
 */
function ethora_chat_assistant_init() {
    global $ethora_chat_assistant;
    $ethora_chat_assistant = new Ethora_Chat_Assistant();
}
add_action('plugins_loaded', 'ethora_chat_assistant_init');

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'ethora_chat_assistant_activate');
function ethora_chat_assistant_activate() {
    // Set default options
    if (!get_option('ethora_chat_assistant_position')) {
        update_option('ethora_chat_assistant_position', 'head');
    }
    
    // Clear any caches
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, 'ethora_chat_assistant_deactivate');
function ethora_chat_assistant_deactivate() {
    // Clear any caches
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}
