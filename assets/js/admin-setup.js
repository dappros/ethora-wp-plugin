/* global jQuery */
(function ($) {
    'use strict';

    var config = window.ethoraSetupConfig || {};

    // The wizard origin is the api URL with `api.` rewritten to `app.` and the
    // /v1 (or other) path stripped. So `https://api.chat.ethora.com/v1` becomes
    // `https://app.chat.ethora.com`. Operators with non-standard layouts can
    // override the api URL setting; this transform stays consistent.
    function deriveWizardOrigin(apiUrl) {
        try {
            var u = new URL(apiUrl);
            u.hostname = u.hostname.replace(/^api\./, 'app.');
            u.pathname = '/';
            u.search = '';
            u.hash = '';
            return u.origin;
        } catch (_) {
            return '';
        }
    }

    function openWizard() {
        var wizardOrigin = deriveWizardOrigin(config.apiUrl || '');
        if (!wizardOrigin) {
            window.alert('Cannot determine the Ethora wizard URL. Check the API URL setting.');
            return;
        }
        var params = new URLSearchParams({
            site_title: config.siteTitle || '',
            site_url: config.siteUrl || '',
            suggested_email: config.adminEmail || '',
            return_origin: window.location.origin
        });
        var url = wizardOrigin + '/wp-setup?' + params.toString();
        var popup = window.open(url, 'ethora-wp-setup', 'width=560,height=720');
        if (!popup) {
            window.alert('Pop-up was blocked. Please allow pop-ups from this site and try again.');
            return;
        }

        // Listen for the wizard's success message. We validate the origin
        // against the configured wizard URL before trusting the App ID.
        var listener = function (event) {
            if (event.origin !== wizardOrigin) return;
            var data = event.data;
            if (!data || data.type !== 'ethora-wp-setup-complete') return;
            if (typeof data.appId !== 'string' || !/^[a-f0-9]{24}$/i.test(data.appId)) return;

            window.removeEventListener('message', listener);

            $.post(config.ajaxUrl, {
                action: 'ethora_save_app_id',
                nonce: config.nonce,
                app_id: data.appId
            }).done(function (resp) {
                if (resp && resp.success) {
                    var field = document.querySelector('input[name="ethora_chat_assistant_bot_id"]');
                    if (field) field.value = data.appId;
                    var notice = document.getElementById('ethora-setup-result');
                    if (notice) {
                        notice.style.display = 'block';
                        notice.textContent = 'Setup complete. Your AI assistant is configured (App ID: ' + data.appId + ').';
                    }
                } else {
                    window.alert('Failed to save the App ID to WordPress. You can paste it manually: ' + data.appId);
                }
            }).fail(function () {
                window.alert('Failed to save the App ID to WordPress. You can paste it manually: ' + data.appId);
            });
        };
        window.addEventListener('message', listener);
    }

    $(function () {
        $('#ethora-setup-button').on('click', function (e) {
            e.preventDefault();
            openWizard();
        });
    });
})(jQuery);
