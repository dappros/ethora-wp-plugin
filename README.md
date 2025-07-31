<!-- @format -->

# ethora-wp-plugin

Simple WP plugin based on Ethora Chat Component

=== Ethora Chat Assistant ===
Contributors: Ethora team  
Tags: javascript, chatbot, custom script  
Requires at least: 5.0  
Tested up to: 6.5  
Stable tag: 1.2  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html
For chat code please check https://github.com/dappros/ethora-chat-component/tree/assistant-ui

Injects a custom chatbot script with dynamic bot ID and configurable injection point (header or footer).

== Description ==

This plugin allows you to inject a hosted JavaScript file (e.g. Ethora chatbot) into the header or footer of every WordPress page. You can configure:

- The `data-bot-id` attribute.
- Whether the script loads in the `<head>` or before `</body>`.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate it from the Plugins page.
3. Go to **Settings > Ethora Chat Assistant** to configure bot ID and injection location.

== Frequently Asked Questions ==

= Can I change the script URL? =  
Not via the UI. You'll need to edit the plugin PHP file directly.

= Does it support shortcodes? =  
No.

== Changelog ==

= 1.2 =

- Renamed plugin to "Ethora Chat Assistant".
- Added frontend and admin Ethora branding.

= 1.1 =

- Added option to choose between head or footer injection.

= 1.0 =

- Initial release with bot ID setting.

== License ==

GPLv2 or later
