=== Ethora Chat Assistant ===
Contributors: rldp, ethorateam
Tags: chatbot, chat, assistant, customer-support, live-chat, widget, javascript
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate the powerful Ethora chatbot into your WordPress website with easy configuration and flexible positioning options.

== Description ==

**Ethora Chat Assistant** is a WordPress plugin that integrates the official Ethora AI chatbot into your website with a local JavaScript implementation. This plugin provides customer support and engagement through the authentic Ethora assistant that works without external dependencies.

= Key Features: =

* **Easy Setup**: Simple bot ID configuration through WordPress admin
* **Flexible Positioning**: Choose between header or footer script injection
* **Lightweight**: Minimal impact on page load times
* **Professional Design**: Clean, modern chat interface
* **Mobile Responsive**: Works perfectly on all devices
* **Customizable**: Easy to configure and maintain

= Perfect For: =

* E-commerce websites
* Business websites
* Customer support portals
* Lead generation sites
* Any site needing live chat functionality

= How It Works: =

1. Install and activate the plugin
2. Enter your Ethora bot ID in the settings
3. Choose where to position the chat widget (header or footer)
4. Save settings and your chat widget is live!

**Local Implementation:** This plugin includes the official Ethora assistant JavaScript file locally, ensuring compliance with WordPress plugin guidelines. The chat widget is the authentic Ethora assistant that provides the same functionality as the cloud version, but without external resource requests.

== Installation ==

1. Upload the `ethora-chat-assistant` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Ethora Chat Assistant** to configure your bot ID and positioning
4. Enter your Ethora bot ID and choose script position (header or footer)
5. Click 'Save Settings' and your chat widget will be live

== Frequently Asked Questions ==

= What is a bot ID? =

A bot ID is a unique identifier for your specific Ethora chatbot instance. You can obtain this from your Ethora dashboard on beta.ethora.com .

= Why does this plugin load external scripts? =

This plugin now includes the official Ethora assistant JavaScript file locally within the plugin directory. This approach ensures:
- Full compliance with WordPress plugin guidelines
- No external dependencies or remote resource loading
- Authentic Ethora assistant functionality
- Faster loading times and better performance
- Improved reliability and security
- Complete control over the chat widget functionality

The plugin uses the same official Ethora assistant that would normally load from the cloud, but now runs locally for better WordPress compliance.



= Is the chat widget mobile responsive? =

Yes! The Ethora chat widget is fully responsive and works perfectly on all devices and screen sizes.

= Can I use this on multiple sites? =

Yes, you can install this plugin on multiple WordPress sites. Each site will need its own bot ID configuration.

= Does this affect my website's performance? =

The plugin is designed to be lightweight and has minimal impact on page load times. The chat script loads asynchronously to ensure optimal performance.

== Changelog ==

= 1.5.0 =
* Updated version number to 1.5.0
* All WordPress plugin review compliance issues resolved
* Plugin ready for WordPress.org submission

= 1.4.0 =
* Fixed WordPress plugin review compliance issues
* Integrated official Ethora assistant JavaScript file locally
* Removed all remote dependencies to comply with WordPress guidelines
* Updated plugin architecture to use authentic Ethora assistant
* Improved performance and reliability with local implementation
* Added human-readable source code (ethora_assistant_source.js)
* Documented all external services and data usage in readme.txt
* Replaced external image URLs with local assets
* Added comprehensive documentation for Google Firebase and OAuth services

= 1.2.0 =
* Added frontend and admin Ethora branding
* Improved admin interface styling

= 1.1.0 =
* Added option to choose between head or footer injection
* Enhanced settings page layout

= 1.0.0 =
* Initial release with bot ID setting
* Basic script injection functionality

== Upgrade Notice ==

= 1.5.0 =
This update resolves all WordPress plugin review compliance issues and prepares the plugin for WordPress.org submission. All external dependencies have been properly documented and local assets have been implemented.

For development and contribution, please visit our GitHub repository.

== Local Implementation ==

This plugin is designed as a **self-contained WordPress plugin** that includes all necessary resources locally. This approach ensures:

**Benefits of Local Implementation:**
* **WordPress Compliance:** Fully compliant with WordPress.org plugin guidelines
* **No External Dependencies:** Official Ethora assistant JavaScript file included within the plugin
* **Authentic Functionality:** Same features and capabilities as the cloud version
* **Improved Performance:** Faster loading times without external resource requests
* **Enhanced Security:** No external script loading reduces security risks
* **Better Reliability:** No dependency on external CDNs or services
* **Complete Control:** Full control over the chat widget functionality and updates

**Technical Implementation:**
* Uses WordPress-compliant `wp_enqueue_script()` and `wp_enqueue_style()` functions
* Implements proper security measures (nonces, capability checks, data sanitization)
* Follows WordPress coding standards and best practices
* Includes comprehensive error handling and fallbacks
* Supports internationalization
* Integrates official Ethora assistant with proper initialization

**Architecture:**
* **Local Resources:** Official Ethora assistant JavaScript stored in `/assets/js/` directory
* **Modular Design:** Clean separation of concerns between PHP and JavaScript
* **WordPress Integration:** Proper hooks and filters for extensibility
* **Performance Optimized:** Minimal impact on page load times
* **Mobile Responsive:** Works perfectly on all devices and screen sizes
* **Authentic Experience:** Same functionality as the cloud-based Ethora assistant

== External services ==

This plugin uses the following external services:

**Google Firebase Authentication**
- Service: Google Firebase Authentication API
- Purpose: User authentication and session management for chat functionality
- Data sent: User credentials, email address, profile information
- When: During user sign-in/sign-up and session validation
- Domains: identitytoolkit.googleapis.com, securetoken.googleapis.com
- Terms of Service: https://policies.google.com/terms
- Privacy Policy: https://policies.google.com/privacy

**Google OAuth**
- Service: Google OAuth 2.0
- Purpose: Access to user profile information for personalized chat experience
- Data sent: OAuth tokens, user email, profile data
- When: During authentication flow
- Domains: www.googleapis.com
- Scopes: userinfo.email, userinfo.profile
- Terms of Service: https://policies.google.com/terms
- Privacy Policy: https://policies.google.com/privacy

**Ethora Chat Service**
- Service: Ethora AI Chat API
- Purpose: Chat functionality and AI responses
- Data sent: Chat messages, user context, bot configuration
- When: During chat interactions
- Provider: Ethora (ethora.com)
- Terms of Service: https://ethora.com/terms
- Privacy Policy: https://ethora.com/privacy

== Source Code ==

This plugin includes both minified and source code versions:

**Minified Version:** assets/js/ethora_assistant.min.js
- Production-ready minified JavaScript file
- Optimized for performance and reduced file size

**Source Code:** assets/js/ethora_assistant_source.js
- Human-readable source code with detailed comments
- Includes documentation of external services and data usage
- Available for review and modification

**Original Source Repository:** https://github.com/dappros/ethora-chat-component/tree/assistant-ui
- Complete source code repository
- Build tools and development environment
- Issue tracking and contribution guidelines

**Build Process:**
The minified version is built from the source repository using npm/webpack. The source code is maintained at the GitHub repository above and includes:
- React library integration
- Firebase Authentication setup
- Ethora chat component implementation
- Local asset management (no external image dependencies)

== Support ==

For support, feature requests, or bug reports, please contact the Ethora team or visit our documentation.

== License ==

This plugin is licensed under the GPL v2 or later.

== Credits ==

Developed by the RDLP & Ethora Team. For more information about Ethora, visit [ethora.com](https://ethora.com).
