<!-- @format -->

<div class="ethora-wrap">
  <h1>Ethora Chat Assistant</h1>
  <div class="ethora-logo">
    <img src="<?= plugin_dir_url(__FILE__) . 'logo.png'; ?>" alt="Ethora Logo" />
  </div>
  <form method="post" action="options.php">
    <?php
        settings_fields('ethora_chat_assistant_settings');
        do_settings_sections('ethora-chat-assistant');
        submit_button('Save Settings');
        ?>
  </form>
  <p class="powered-by-text">
    Powered by <a href="https://ethora.com" target="_blank">Ethora</a>
  </p>
</div>
