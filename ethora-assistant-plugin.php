<?php
/*
Plugin Name: Ethora Chat Assistant
Description: Injects custom script with dynamic bot ID and position option.
Version: 1.3
*/

add_action('admin_menu', function () {
    add_options_page('Ethora Chat Assistant', 'Ethora Chat Assistant', 'manage_options', 'ethora-chat-assistant', function () {
        include plugin_dir_path(__FILE__) . 'settings-page.php';
    });
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'settings_page_ethora-chat-assistant') return;

    wp_enqueue_style(
        'ethora-chat-assistant-style',
        plugin_dir_url(__FILE__) . 'ethora-admin-style.css',
        [],
        '1.0'
    );
});

add_action('admin_init', function () {
    register_setting('ethora_chat_assistant_settings', 'ethora_chat_assistant_bot_id');
    register_setting('ethora_chat_assistant_settings', 'ethora_chat_assistant_position');

    add_settings_section('ethora_chat_assistant_section', '', null, 'ethora-chat-assistant');

    add_settings_field(
        'ethora_chat_assistant_bot_id',
        'Bot ID',
        function () {
            $value = esc_attr(get_option('ethora_chat_assistant_bot_id', ''));
            echo "<input type='text' name='ethora_chat_assistant_bot_id' value='$value' class='regular-text' />";
        },
        'ethora-chat-assistant',
        'ethora_chat_assistant_section'
    );

    add_settings_field(
        'ethora_chat_assistant_position',
        'Script Position',
        function () {
            $value = get_option('ethora_chat_assistant_position', 'head');
            echo "<select name='ethora_chat_assistant_position'>
                    <option value='head' " . selected($value, 'head', false) . ">Header</option>
                    <option value='footer' " . selected($value, 'footer', false) . ">Footer</option>
                </select>";
        },
        'ethora-chat-assistant',
        'ethora_chat_assistant_section'
    );
});

function inject_ethora_chat_assistant_script() {
    $botId = esc_attr(get_option('ethora_chat_assistant_bot_id'));
    if (!$botId) return;

    echo "<script 
        src='https://dappros-wp-scripts.s3.us-east-2.amazonaws.com/ethora_assistant.js' 
        id='chat-content-assistant' 
        data-bot-id='{$botId}'>
    </script>
   ";
}

add_action('wp_head', function () {
    if (get_option('ethora_chat_assistant_position', 'head') === 'head') {
        inject_ethora_chat_assistant_script();
    }
});

add_action('wp_footer', function () {
    if (get_option('ethora_chat_assistant_position', 'head') === 'footer') {
        inject_ethora_chat_assistant_script();
    }
});
