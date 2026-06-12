<!-- @format -->

# Ethora Chat Assistant

Simple, open-source WordPress plugin for embedding an AI chatbot on your website. Based on NPM Ethora Chat Component.

**Part of the [Ethora SDK ecosystem](https://github.com/dappros/ethora#ecosystem)** — see all SDKs, tools, and sample apps. Follow cross-SDK updates in the [Release Notes](https://github.com/dappros/ethora/blob/main/RELEASE-NOTES.md).

---

## Plugin Header (for WordPress.org)
=== Ethora Chat Assistant ===
 Contributors: rldp, ethorateam
 Tags: chatbot, chat, assistant, customer-support, live-chat
 Requires at least: 5.0
 Tested up to: 6.8
 Requires PHP: 7.4
 Stable tag: 1.5.0
 License: GPLv2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html


For chat code please check: https://github.com/dappros/ethora-chat-component/tree/assistant-ui

---

## Overview

Ethora Chat Assistant injects the Ethora AI chatbot into every WordPress page using a JavaScript bundle shipped locally with the plugin (no external script host). You configure your `data-bot-id` and choose whether the script loads in the document `<head>` or just before `</body>`.

---

## Features

- Lightweight script injection
- Configurable `data-bot-id`
- Injection location: header or footer
- Works with any WordPress theme
- Free and open-source (GPLv2 or later)

---

## Installation

1) Upload the plugin folder to `/wp-content/plugins/`.
2) Activate it from the **Plugins** page.
3) Go to **Settings → Ethora Chat Assistant** to configure bot ID and injection location.

---

## Description

This plugin loads a local JavaScript bundle (the Ethora assistant) and injects a small initializer `<script>` tag carrying your bot's ID into the header or footer of every WordPress page.

You can configure:
- The `data-bot-id` attribute - your bot's unique XMPP handle, generated automatically if you use the Ethora platform
- Whether the script loads in the `<head>` or before `</body>`.

The plugin emits an initializer tag similar to:

```html
<script
  id="chat-content-assistant"
  data-bot-id="<your bot's XMPP handle here>"
></script>
```

The Ethora assistant bundle (`assets/js/ethora_assistant.js`) reads this `data-bot-id` and renders the chat widget.

## Frequently Asked Questions
**Q: Where does the chat script come from?**
<br />A: It is bundled locally with the plugin (`assets/js/ethora_assistant.js`) and served from your own site — no external script host is used. At runtime the widget connects to the Ethora chat service (see the "External services" section of readme.txt).

**Q: Does it support shortcodes?**
<br />A: No.

## Changelog

### 1.5.0
Resolved WordPress.org plugin review compliance issues and prepared the plugin for submission.

### 1.4.0
Bundled the official Ethora assistant JavaScript locally and removed remote script dependencies.

### 1.2
Renamed plugin to "Ethora Chat Assistant".
Added frontend and admin Ethora branding.

### 1.1
Added option to choose between head or footer injection.

### 1.0
Initial release with bot ID setting.

## License
GPLv2 or later
https://www.gnu.org/licenses/gpl-2.0.html

## Related Projects
Ethora Chat Component: https://github.com/dappros/ethora-chat-component
RAG Demos: https://github.com/dappros/rag_demos
Ethora (platform): https://github.com/dappros/ethora
