<?php
/**
 * Uninstall file for Ethora Chat Assistant
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It removes all plugin data and options from the database.
 *
 * @package EthoraChatAssistant
 * @since 1.4.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('ethora_chat_assistant_bot_id');
delete_option('ethora_chat_assistant_position');

// Delete any transients that might have been set
delete_transient('ethora_chat_assistant_cache');

// Clear any caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// If multisite, clean up network options
if (is_multisite()) {
    delete_site_option('ethora_chat_assistant_bot_id');
    delete_site_option('ethora_chat_assistant_position');
}
