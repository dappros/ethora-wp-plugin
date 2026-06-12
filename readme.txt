=== Ethora Chat Assistant ===
Contributors: rldp, ethorateam
Tags: chatbot, chat, assistant, customer-support, live-chat
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.6.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add the Ethora AI chat assistant to your WordPress site. Enter a Bot ID, pick a position, and a customizable chat widget appears on your pages.

== Description ==

**Ethora Chat Assistant** adds the Ethora AI chat widget to your WordPress website. You enter your Ethora Bot ID, choose whether the widget script loads in the header or footer, and the assistant appears on your site so visitors can chat with your AI agent.

The widget JavaScript is bundled with the plugin and served from your own site (it is not loaded from a remote CDN). To provide live chat and AI answers, the widget connects at runtime to the Ethora chat service and a small number of other third-party services. These are listed in full in the **External services** section below.

= Features =

* Configure your Bot ID from a single settings screen.
* Load the widget script in the page header or footer.
* The assistant's answers are produced by your configured Ethora AI agent.
* Widget branding and behaviour are controlled by your Ethora app configuration.
* The widget is responsive and works across screen sizes.

= Typical use cases =

* Business and e-commerce websites.
* Customer support and help centres.
* Lead generation and pre-sales chat.
* Any site that needs an AI chat assistant.

= How it works =

1. Install and activate the plugin.
2. Create an app / AI agent in your Ethora account and copy its Bot ID.
3. In **Settings > Ethora Chat Assistant**, paste the Bot ID and choose the script position.
4. Save. The chat widget is now live on your site.

== Installation ==

1. Upload the `ethora-chat-assistant` folder to `/wp-content/plugins/`, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > Ethora Chat Assistant** to configure your Bot ID and position.
4. Enter your Ethora Bot ID, choose script position (header or footer), and click **Save Settings**.

== Frequently Asked Questions ==

= What is a Bot ID and where do I get one? =

A Bot ID is the unique identifier of your Ethora AI agent / app. Create a free account and an app at https://app.chat.ethora.com, configure your AI agent, and copy the Bot ID from the app settings.

= Does the plugin send data to external services? =

Yes. The plugin itself stores only your Bot ID and position setting in WordPress. However, the embedded chat widget connects at runtime to the Ethora chat service (and to Google Sign-In and a link-preview service) in order to deliver live messaging and AI responses. The full list, including what data is sent and the relevant terms and privacy policies, is in the **External services** section below.

= Can I point the widget at a self-hosted Ethora server? =

Yes. The settings screen has an optional **API URL (advanced)** field. It defaults to Ethora Cloud (https://api.chat.ethora.com/v1). If you run a self-hosted or dedicated Ethora server, enter its API base URL there and the widget will connect to it instead.

= Is the widget JavaScript loaded from a remote server? =

No. The widget JavaScript is bundled inside the plugin and served from your own WordPress site. Network requests happen only after a visitor opens the chat, and only to the documented services below.

= Is the chat widget mobile responsive? =

Yes. The Ethora chat widget is responsive and works on all common devices and screen sizes.

= Can I use this on multiple sites? =

Yes. Install it on as many sites as you like; each site uses its own Bot ID.

== External services ==

This plugin embeds the Ethora chat / AI assistant. Once you set a Bot ID and a visitor opens the chat, the widget connects to the following third-party services. No data is sent to these services until a visitor interacts with the chat widget.

**1. Ethora chat & AI service (provided by Dappros Ltd, trading as Ethora)**
- Purpose: deliver real-time messaging, authenticate the chat session, and return AI assistant responses.
- Data sent: chat messages, the configured Bot ID, session/authentication tokens, and basic profile data for signed-in users.
- When: while a visitor uses the chat widget.
- Service endpoints: Ethora REST API and XMPP real-time messaging servers on ethora.com domains.
- Provider: https://ethora.com
- Terms of Service: https://wiki.ethora.com/wiki/Info:Privacy_policy#Terms_of_Service
- Privacy Policy: https://wiki.ethora.com/wiki/Info:Privacy_policy

**2. Google Sign-In (Google Firebase Authentication / Google OAuth 2.0)**
- Purpose: optional "Sign in with Google" login for chat users.
- Data sent: email address and basic Google profile information, only when a visitor chooses to sign in with Google.
- When: during the optional Google sign-in flow.
- Domains: identitytoolkit.googleapis.com, securetoken.googleapis.com, accounts.google.com.
- Terms of Service: https://policies.google.com/terms
- Privacy Policy: https://policies.google.com/privacy

**3. LinkPreview (linkpreview.net)**
- Purpose: generate preview cards for URLs shared inside chat messages.
- Data sent: URLs contained in chat messages.
- When: when a message contains a link.
- Provider & Privacy Policy: https://www.linkpreview.net

Profile images for signed-in users may be displayed directly from Google's user-content servers (lh3.googleusercontent.com).

== Source code ==

The chat widget bundled with this plugin is built from Ethora's open-source code.

- Plugin source: https://github.com/dappros/ethora-wp-plugin
- Chat widget source: https://github.com/dappros/ethora (Ethora SDK monorepo; the embeddable widget is built with Vite)
- The non-minified, human-readable build of the widget is shipped alongside the plugin as `assets/js/ethora_assistant.js`.

The widget is produced with a standard `npm install` + Vite build from the source repository above; no build step is required to run the plugin.

== Changelog ==

= 1.6.1 =
* Reworded the readme description to remove promotional phrasing.
* Hardened the script-position setting sanitization (strict whitelist comparison).
* Clarified the External services disclosure with separate Terms of Service and Privacy Policy links.

= 1.6.0 =
* Corrected the External services disclosure to accurately list every third-party service the chat widget contacts (Ethora chat/AI, Google Sign-In, LinkPreview), with the data sent and the relevant terms and privacy policies.
* Removed inaccurate "no external dependencies / no remote resource loading" statements from the readme and admin screen.
* Replaced the previous placeholder source file with the actual non-minified widget build to comply with the human-readable code guideline.
* Corrected the documentation link for obtaining a Bot ID (now app.chat.ethora.com).

= 1.2.0 =
* Added frontend and admin Ethora branding.
* Improved admin interface styling.

= 1.1.0 =
* Added option to choose between head or footer injection.
* Enhanced settings page layout.

= 1.0.0 =
* Initial release with Bot ID setting and script injection.

== Upgrade Notice ==

= 1.6.1 =
Readme and sanitization improvements addressing WordPress.org review feedback.

= 1.6.0 =
Accurate external-services disclosure and a genuine non-minified source build, prepared for WordPress.org review.
