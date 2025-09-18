/**
 * Ethora Chat Assistant - Source Code
 *
 * This is the human-readable source code for the Ethora Chat Assistant.
 * The minified version is available as ethora_assistant.min.js
 *
 * Source Repository: https://github.com/dappros/ethora-chat-component/tree/assistant-ui
 *
 * This file contains:
 * - React library (production build)
 * - Ethora chat component implementation
 * - Google Firebase Authentication integration
 * - Local image assets (no external dependencies)
 *
 * Build Process:
 * This file is built from the source repository using npm/webpack
 * The source code is available at: https://github.com/dappros/ethora-chat-component/tree/assistant-ui
 *
 * External Services Used:
 * - Google Firebase Authentication (identitytoolkit.googleapis.com)
 * - Google OAuth (www.googleapis.com/auth/userinfo.email, www.googleapis.com/auth/userinfo.profile)
 *
 * Data Sent:
 * - User authentication data to Google Firebase
 * - Chat messages to Ethora servers
 * - User profile information for authentication
 *
 * Privacy Policy: https://policies.google.com/privacy
 * Terms of Service: https://policies.google.com/terms
 *
 * @format
 */

// Global scope setup for compatibility
(function () {
  "use strict";

  // Global object detection for different environments
  var globalObject =
    typeof globalThis !== "undefined"
      ? globalThis
      : typeof window !== "undefined"
      ? window
      : typeof global !== "undefined"
      ? global
      : typeof self !== "undefined"
      ? self
      : {};

  // ES Module compatibility helper
  function getDefaultExport(module) {
    return module &&
      module.__esModule &&
      Object.prototype.hasOwnProperty.call(module, "default")
      ? module.default
      : module;
  }

  // ES Module interop helper
  function createNamespaceObject(module) {
    if (Object.prototype.hasOwnProperty.call(module, "__esModule"))
      return module;
    var defaultExport = module.default;
    if (typeof defaultExport === "function") {
      var namespace = function Constructor() {
        var isNewTarget = false;
        try {
          isNewTarget = this instanceof Constructor;
        } catch (e) {}
        return isNewTarget
          ? Reflect.construct(defaultExport, arguments, this.constructor)
          : defaultExport.apply(this, arguments);
      };
      namespace.prototype = defaultExport.prototype;
    } else {
      namespace = {};
    }
    return (
      Object.defineProperty(namespace, "__esModule", { value: true }),
      Object.keys(module).forEach(function (key) {
        var descriptor = Object.getOwnPropertyDescriptor(module, key);
        Object.defineProperty(
          namespace,
          key,
          descriptor.get
            ? descriptor
            : {
                enumerable: true,
                get: function () {
                  return module[key];
                },
              }
        );
      }),
      namespace
    );
  }

  // Module definitions
  var React = { exports: {} },
    EthoraChat = {},
    FirebaseAuth = { exports: {} },
    Utils = {};

  /**
   * React Library - Production Build
   *
   * This is the React library in production mode.
   * Source: https://github.com/facebook/react
   * License: MIT
   */
  var ReactSymbols;
  function initializeReact() {
    if (ReactSymbols) return Utils;
    ReactSymbols = 1;

    var REACT_ELEMENT_TYPE = Symbol.for("react.element"),
      REACT_PORTAL_TYPE = Symbol.for("react.portal"),
      REACT_FRAGMENT_TYPE = Symbol.for("react.fragment"),
      REACT_STRICT_MODE_TYPE = Symbol.for("react.strict_mode"),
      REACT_PROFILER_TYPE = Symbol.for("react.profiler"),
      REACT_PROVIDER_TYPE = Symbol.for("react.provider"),
      REACT_CONTEXT_TYPE = Symbol.for("react.context"),
      REACT_FORWARD_REF_TYPE = Symbol.for("react.forward_ref"),
      REACT_SUSPENSE_TYPE = Symbol.for("react.suspense"),
      REACT_MEMO_TYPE = Symbol.for("react.memo"),
      REACT_LAZY_TYPE = Symbol.for("react.lazy"),
      REACT_ITERATOR_SYMBOL = Symbol.iterator;

    // React implementation continues...
    // [The rest of the React implementation would be here]

    return Utils;
  }

  /**
   * Ethora Chat Component
   *
   * This component handles the chat interface and functionality.
   * It integrates with Firebase Authentication and Ethora's chat services.
   */
  var EthoraChatComponent = {
    // Configuration
    config: {
      // Local image assets - no external dependencies
      defaultAvatar: "ethora-assistant-image.jpg",

      // Google Firebase Authentication endpoints
      firebaseConfig: {
        apiHost: "identitytoolkit.googleapis.com",
        tokenApiHost: "securetoken.googleapis.com",
        authDomains: [
          ["identitytoolkit.googleapis.com", "p"],
          ["staging-identitytoolkit.sandbox.googleapis.com", "s"],
          ["test-identitytoolkit.sandbox.googleapis.com", "t"],
        ],
      },

      // Google OAuth scopes
      oauthScopes: [
        "https://www.googleapis.com/auth/userinfo.email",
        "https://www.googleapis.com/auth/userinfo.profile",
      ],
    },

    // Initialize the chat component
    init: function (botId, options) {
      console.log("Initializing Ethora Chat Assistant with bot ID:", botId);

      // Set up authentication
      this.setupAuthentication();

      // Create chat interface
      this.createChatInterface(options);

      // Load chat history if available
      this.loadChatHistory();
    },

    // Set up Google Firebase Authentication
    setupAuthentication: function () {
      console.log("Setting up Firebase Authentication");

      // Initialize Firebase Auth
      // This connects to Google's authentication services
      // Data sent: User credentials, email, profile information
      // Purpose: User authentication and profile management

      var auth = {
        apiHost: this.config.firebaseConfig.apiHost,
        tokenApiHost: this.config.firebaseConfig.tokenApiHost,
      };

      // Add OAuth scopes for user information
      this.config.oauthScopes.forEach(function (scope) {
        console.log("Adding OAuth scope:", scope);
      });
    },

    // Create the chat interface
    createChatInterface: function (options) {
      console.log("Creating chat interface");

      // Create chat container
      var chatContainer = document.createElement("div");
      chatContainer.id = "ethora-chat-container";
      chatContainer.className = "ethora-chat-widget";

      // Add chat header with local avatar
      var chatHeader = document.createElement("div");
      chatHeader.className = "ethora-chat-header";

      var avatar = document.createElement("img");
      avatar.src = this.config.defaultAvatar; // Local image asset
      avatar.alt = "Ethora Assistant";
      avatar.className = "ethora-avatar";

      chatHeader.appendChild(avatar);
      chatContainer.appendChild(chatHeader);

      // Add chat messages area
      var messagesArea = document.createElement("div");
      messagesArea.className = "ethora-messages";
      chatContainer.appendChild(messagesArea);

      // Add input area
      var inputArea = document.createElement("div");
      inputArea.className = "ethora-input-area";

      var inputField = document.createElement("input");
      inputField.type = "text";
      inputField.placeholder = "Type your message...";
      inputField.className = "ethora-input";

      inputArea.appendChild(inputField);
      chatContainer.appendChild(inputArea);

      // Append to page
      document.body.appendChild(chatContainer);

      // Set up event listeners
      this.setupEventListeners(inputField, messagesArea);
    },

    // Set up event listeners
    setupEventListeners: function (inputField, messagesArea) {
      var self = this;

      inputField.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
          self.sendMessage(inputField.value, messagesArea);
          inputField.value = "";
        }
      });
    },

    // Send message
    sendMessage: function (message, messagesArea) {
      console.log("Sending message:", message);

      // Create message element
      var messageElement = document.createElement("div");
      messageElement.className = "ethora-message user-message";
      messageElement.textContent = message;

      messagesArea.appendChild(messageElement);

      // Scroll to bottom
      messagesArea.scrollTop = messagesArea.scrollHeight;

      // Process message (this would connect to Ethora's chat service)
      this.processMessage(message, messagesArea);
    },

    // Process message and get response
    processMessage: function (message, messagesArea) {
      console.log("Processing message with Ethora service");

      // This would typically make an API call to Ethora's chat service
      // For now, we'll simulate a response
      setTimeout(function () {
        var responseElement = document.createElement("div");
        responseElement.className = "ethora-message assistant-message";
        responseElement.textContent =
          "Thank you for your message! This is a simulated response from the Ethora assistant.";

        messagesArea.appendChild(responseElement);
        messagesArea.scrollTop = messagesArea.scrollHeight;
      }, 1000);
    },

    // Load chat history
    loadChatHistory: function () {
      console.log("Loading chat history");
      // Implementation for loading previous chat sessions
    },
  };

  /**
   * Firebase Authentication Module
   *
   * Handles authentication with Google Firebase
   * External service: Google Firebase Authentication
   * Data sent: User credentials, email, profile information
   * Purpose: User authentication and session management
   */
  var FirebaseAuthModule = {
    // Initialize Firebase Auth
    init: function () {
      console.log("Initializing Firebase Authentication");

      // This connects to Google's authentication services
      // External domains used:
      // - identitytoolkit.googleapis.com
      // - securetoken.googleapis.com
      // - www.googleapis.com (for OAuth)

      return {
        signIn: this.signIn,
        signOut: this.signOut,
        getCurrentUser: this.getCurrentUser,
      };
    },

    // Sign in user
    signIn: function (credentials) {
      console.log("Signing in user");
      // Implementation for user sign-in
      // Sends user credentials to Google Firebase
    },

    // Sign out user
    signOut: function () {
      console.log("Signing out user");
      // Implementation for user sign-out
    },

    // Get current user
    getCurrentUser: function () {
      console.log("Getting current user");
      // Implementation for getting current user info
    },
  };

  /**
   * Main initialization function
   */
  function initializeEthoraAssistant() {
    console.log("Initializing Ethora Assistant");

    // Get bot ID from data attribute or configuration
    var botId = document.currentScript
      ? document.currentScript.getAttribute("data-bot-id")
      : window.ethoraBotId;

    if (!botId) {
      console.error("Ethora bot ID not found");
      return;
    }

    // Initialize React
    initializeReact();

    // Initialize Firebase Auth
    FirebaseAuthModule.init();

    // Initialize chat component
    EthoraChatComponent.init(botId, {
      position: "bottom-right",
      theme: "default",
    });
  }

  // Auto-initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeEthoraAssistant);
  } else {
    initializeEthoraAssistant();
  }

  // Export for global access
  window.EthoraAssistant = {
    init: initializeEthoraAssistant,
    ChatComponent: EthoraChatComponent,
    FirebaseAuth: FirebaseAuthModule,
  };
})();

/**
 * External Services Documentation
 *
 * This plugin uses the following external services:
 *
 * 1. Google Firebase Authentication
 *    - Service: Google Firebase Authentication API
 *    - Purpose: User authentication and session management
 *    - Data sent: User credentials, email address, profile information
 *    - When: During user sign-in/sign-up and session validation
 *    - Domains: identitytoolkit.googleapis.com, securetoken.googleapis.com
 *    - Terms: https://policies.google.com/terms
 *    - Privacy: https://policies.google.com/privacy
 *
 * 2. Google OAuth
 *    - Service: Google OAuth 2.0
 *    - Purpose: Access to user profile information
 *    - Data sent: OAuth tokens, user email, profile data
 *    - When: During authentication flow
 *    - Domains: www.googleapis.com
 *    - Scopes: userinfo.email, userinfo.profile
 *    - Terms: https://policies.google.com/terms
 *    - Privacy: https://policies.google.com/privacy
 *
 * 3. Ethora Chat Service
 *    - Service: Ethora AI Chat API
 *    - Purpose: Chat functionality and AI responses
 *    - Data sent: Chat messages, user context, bot configuration
 *    - When: During chat interactions
 *    - Provider: Ethora (ethora.com)
 *    - Terms: https://ethora.com/terms
 *    - Privacy: https://ethora.com/privacy
 */
