<?php
/**
 * Admin page template for Ethora Chat Assistant
 *
 * @package EthoraChatAssistant
 * @since 1.4.0
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
                <li><?php esc_html_e('Easy bot ID configuration', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Flexible script positioning (header or footer)', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Official Ethora assistant with local implementation', 'ethora-chat-assistant'); ?></li>
                <li><?php esc_html_e('Professional chat experience for your visitors', 'ethora-chat-assistant'); ?></li>
            </ul>
            
            <div class="ethora-local-info">
                <h4><?php esc_html_e('Local Implementation:', 'ethora-chat-assistant'); ?></h4>
                <p><?php esc_html_e('This plugin includes the official Ethora assistant JavaScript file locally, ensuring:', 'ethora-chat-assistant'); ?></p>
                <ul>
                    <li><?php esc_html_e('Full compliance with WordPress plugin guidelines', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('No external dependencies or remote resource loading', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Authentic Ethora assistant functionality', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Faster loading times and better performance', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Improved reliability and security', 'ethora-chat-assistant'); ?></li>
                    <li><?php esc_html_e('Same features as the cloud-based version', 'ethora-chat-assistant'); ?></li>
                </ul>
                <p><em><?php esc_html_e('The official Ethora assistant JavaScript file is stored locally within the plugin directory.', 'ethora-chat-assistant'); ?></em></p>
                <p><strong><?php esc_html_e('Architecture:', 'ethora-chat-assistant'); ?></strong> <?php esc_html_e('Official Ethora assistant with local JavaScript, WordPress-compliant enqueue methods, authentic functionality.', 'ethora-chat-assistant'); ?></p>
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
