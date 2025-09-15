<?php
/**
 * Plugin Name: Ethora Chat Assistant
 * Plugin URI: https://ethora.com
 * Description: WordPress plugin that integrates the Ethora AI chatbot into websites using the official Ethora assistant with local JavaScript implementation.
 * Version: 1.4.0
 * Requires at least: 5.0
 * Tested up to: 6.5
 * Requires PHP: 7.4
 * Author: RLDP, Ethora Team 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ethora-chat-assistant
 * Domain Path: /languages
 * Network: true
 *
 * @package EthoraChatAssistant
 * @version 1.4.0
 * @author RLDP, Ethora Team
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ETHORA_PLUGIN_VERSION', '1.4.0');
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
        }
        
        // Frontend functionality
        add_action('wp_head', array($this, 'inject_script_head'));
        add_action('wp_footer', array($this, 'inject_script_footer'));
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
     * Sanitize position value
     */
    public function sanitize_position($value) {
        $allowed_values = array('head', 'footer');
        return in_array($value, $allowed_values) ? $value : 'head';
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
        
        // Enqueue the local Ethora assistant script
        wp_enqueue_script(
            'ethora-chat-assistant',
            ETHORA_PLUGIN_URL . 'assets/js/ethora_assistant.js',
            array(),
            ETHORA_PLUGIN_VERSION,
            get_option('ethora_chat_assistant_position', 'head') === 'footer'
        );
        
        // Add inline script to create the required script tag with bot ID
        // This is how the actual Ethora assistant expects to be initialized
        wp_add_inline_script(
            'ethora-chat-assistant',
            'document.addEventListener("DOMContentLoaded", function() {
                // Create script tag with bot ID for the Ethora assistant to read
                var scriptTag = document.createElement("script");
                scriptTag.id = "chat-content-assistant";
                scriptTag.setAttribute("data-bot-id", "' . esc_js($bot_id) . '");
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
