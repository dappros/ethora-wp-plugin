<?php
/**
 * Admin page template for Ethora Chat Assistant
 *
 * @package EthoraChatAssistant
 * @since 1.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'ethora-chat-assistant'));
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="ethora-wrap">
        <div class="ethora-logo">
            <img src="<?php echo esc_url(ETHORA_PLUGIN_URL . 'logo.png'); ?>" alt="<?php esc_attr_e('Ethora Logo', 'ethora-chat-assistant'); ?>" />
        </div>

        <form method="post" action="options.php">
            <?php
            settings_fields('ethora_chat_assistant_settings');
            do_settings_sections('ethora-chat-assistant');
            submit_button(esc_html__('Save Settings', 'ethora-chat-assistant'));
            ?>
        </form>
        
        <div class="ethora-info">
            <h3><?php esc_html_e('About Ethora Chat Assistant', 'ethora-chat-assistant'); ?></h3>
            <p><?php esc_html_e('This plugin integrates the official Ethora AI chatbot into your WordPress website with a local JavaScript implementation. Simply enter your bot ID and choose where to place the chat widget.', 'ethora-chat-assistant'); ?></p>
            
            <h4><?php esc_html_e('Features:', 'ethora-chat-assistant'); ?></h4>
            <ul>
                <li><?php esc_html_e('Bot ID configuration', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Script positioning (header or footer)', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Ethora assistant served locally from your site', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Chat widget for your site visitors', 'ethora-chat-assistant'); ?></li>
            </ul>
            
            <div class="ethora-local-info">
                <h4><?php esc_html_e('How it works:', 'ethora-chat-assistant'); ?></h4>
                <p><?php esc_html_e('The chat widget JavaScript is bundled with this plugin and served from your own site. To deliver live chat and AI responses, the widget connects at runtime to the Ethora chat service and a small number of other third-party services.', 'ethora-chat-assistant'); ?></p>
                <ul>
                    <li><?php esc_html_e('Widget script served locally from your site (not from a remote CDN)', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Connects to the Ethora chat & AI service to send and receive messages', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Optional Google Sign-In for chat users', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Same features as the cloud-based version', 'ethora-chat-assistant'); ?></li>
                </ul>
                <p><em><?php esc_html_e('The third-party services used, and the data sent to them, are documented in the plugin readme under "External services".', 'ethora-chat-assistant'); ?></em></p>
            </div>
            
            <p>
            <strong><?php esc_html_e('Need help?', 'ethora-chat-assistant'); ?></strong> 
            <?php esc_html_e('Visit our', 'ethora-chat-assistant'); ?> 
            <a href="https://ethora.com/ai-sdk/wordpress-ai-agent-plugin/" target="_blank">documentation</a> 
            <?php esc_html_e('or', 'ethora-chat-assistant'); ?> 
            <a href="https://ethora.com" target="_blank">contact support</a>.
            </p>

        </div>
    </div>
</div>
