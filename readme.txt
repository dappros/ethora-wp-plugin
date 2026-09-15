=== Ethora AI Chatbot - AI Chat Assistant for Your Website ===
Contributors: rldp, dmytroberberov, phwizard
Tags: ai-chatbot, chatbot, ai-assistant, live-chat, customer-support
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI chatbot for WordPress that answers visitor questions from your own website pages and documents, in a chat widget styled to match your site.

== Description ==

**Ethora AI Chatbot** adds an AI chat assistant to your WordPress website. Visitors ask questions in a chat widget and get answers from an AI agent that knows your website content and your documents.

You create the AI agent in Ethora (there is a free plan, no credit card needed): add your website so the agent can index your pages, upload documents such as FAQs, price lists or manuals, and write the agent's instructions. Then paste the agent's Bot ID into this plugin and the chatbot appears on your site. No coding is needed.

The widget JavaScript is bundled with the plugin and served from your own site (it is not loaded from a remote CDN). To deliver real-time chat and AI answers, the widget connects at runtime to the Ethora chat service and a small number of other third-party services. These are listed in full in the **External services** section below.

= What you can use it for =

* **Customer support chatbot:** answer common support questions at any hour, using your own pages and documents as the source (RAG, retrieval-augmented generation).
* **Pre-sales and product questions:** help visitors on business, SaaS and WooCommerce store pages find the right product, plan or page.
* **Knowledge base assistant:** let customers, students, patients or members search your help centre, docs or policies by asking a question in plain language.
* **FAQ bot:** replace a long FAQ page with a chat that answers the question the visitor actually asked.

= Features =

* **Answers from your content:** the Ethora dashboard indexes your website pages and uploaded documents, and the AI agent answers from them.
* **Quick setup:** paste the Bot ID, save, and the chat widget is live.
* **Customizable chat widget:** primary and secondary colors, icon color, message bubble colors, input background, font family or any Google Font, left or right position, width and height.
* **Your own bot identity:** set the bot name, bot avatar and chat button icon.
* **Greeting message:** the assistant opens with a starter message, so the chat never starts empty.
* **Fullscreen mode:** visitors can expand the chat from the header.
* **Follows your site language:** the chat interface uses your WordPress site language.
* **Mobile friendly:** the chat window adapts to phones, tablets and desktops.
* **Works with your theme:** classic and block themes; choose whether the script loads in the page header or footer.
* **No remote script:** the widget code ships inside the plugin and is served from your own site.
* **Self-hosted option:** point the plugin at your own Ethora server (AWS, private cloud or on-premises) when you need full data ownership, for example for HIPAA or GDPR requirements.

= Who it is for =

* Small business, agency and portfolio websites.
* WooCommerce and other online stores.
* SaaS products and documentation sites.
* Healthcare, clinics and insurance (self-hosted deployments available).
* Education, courses and membership sites.
* Customer support teams and help centres.

= How it works =

1. Install and activate the plugin.
2. Create a free account at https://app.chat.ethora.com and create an AI agent.
3. Add your website and documents to the agent so it can answer from your content, then copy its Bot ID.
4. In **Settings > Ethora Chat Assistant**, paste the Bot ID, optionally set colors, icon and position, and save. The AI chatbot is now live on your site.

More about the plugin: https://ethora.com/ai-sdk/wordpress-ai-agent-plugin/

== Installation ==

1. Upload the `ethora-chat-assistant` folder to `/wp-content/plugins/`, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > Ethora Chat Assistant** to configure your Bot ID and position.
4. Enter your Ethora Bot ID, choose script position (header or footer), and click **Save Settings**.

== Frequently Asked Questions ==

= Is the AI chatbot free? =

The plugin is free. The Ethora service it connects to has a free plan (up to 1,000 monthly active users, no credit card). Paid plans for more users are listed at https://ethora.com/pricing/.

= How do I train the chatbot on my website content? =

In the Ethora dashboard (https://app.chat.ethora.com), open your AI agent, add your website so its pages are indexed, and upload any documents you want it to know, such as FAQs, price lists or manuals. The agent then answers visitors from that content. The plugin itself only needs the agent's Bot ID.

= Do I need coding skills? =

No. You paste a Bot ID into the settings screen. Colors, fonts, icon and position are set with normal form fields.

= Does it work with WooCommerce? =

Yes. The chat widget loads on your site's front-end pages, including WooCommerce shop and product pages. Add your product pages or product documents to the agent so it can answer product questions.

= Can I change how the chat widget looks? =

Yes. Under **Settings > Ethora Chat Assistant > Appearance** you can set colors, message bubble colors, font or Google Font, bot name, bot avatar, chat button icon, left or right position, and the chat window size.

= Which languages does it support? =

The chat interface follows your WordPress site language. What language the AI agent answers in is controlled by the agent's instructions in the Ethora dashboard.

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

= 1.7.2 =
* Tested with WordPress 7.1.
* Fixed: the bot's avatar was used as the chat button and header icon. The widget icon is now its own setting (Widget icon URL); without it the default icon is shown.
* Fixed: clicking the bot's avatar next to a message opened an empty profile. It now does nothing.
* Fixed: the bot could appear under two different names in one conversation, one in the header and greeting and another on its replies. One name is now used everywhere, and it can be set with the new Bot name setting.
* Fixed: after saving the settings page with Width and Height left empty, the chat opened as an invisible 0x0 panel. Empty sizes are no longer saved as 0, and a stored 0 from an earlier version is ignored, so affected sites are repaired by the update without re-saving.
* New settings under Appearance: Bot name, Bot avatar URL, Widget icon URL.

= 1.7.1 =
* Fixed: the "Ask me anything!" teaser bubble above the chat launcher could stay invisible indefinitely (a CSS opacity animation that never advances in a backgrounded or inactive browser tab).

= 1.7.0 =
* New: starter message from the assistant ("Hi, how can I help you?" by default, or the Agent's own greeting) - the chat never opens blank
* New: fullscreen toggle in the chat header
* New: larger, screen-adaptive default panel size for desktop
* Changed: text-only composer by default (no attach/mic - the AI cannot use uploads); re-enable via data-disable-media="false"
* Changed: interface language now follows the site language
* Fixed: chat stuck on "Connecting" for returning visitors
* Fixed: bot stopped responding after a network reconnect

= 1.6.2 =
* Added a "Settings" link to the plugin row on the Plugins screen.

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

= 1.7.2 =
Fixes an invisible chat panel after saving settings with empty Width/Height, the bot's avatar being used as the chat button, clicking the bot's avatar, and the bot showing under two names. Tested with WordPress 7.1.

= 1.6.2 =
Adds a Settings shortcut on the Plugins screen.

= 1.6.1 =
Readme and sanitization improvements addressing WordPress.org review feedback.

= 1.6.0 =
Accurate external-services disclosure and a genuine non-minified source build, prepared for WordPress.org review.
