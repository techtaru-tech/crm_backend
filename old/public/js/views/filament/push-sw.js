/*!
 |------------------------------------------------------------------
 | push-sw.js — OneSignal Web Push SDK init for the admin panel
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/push-sw.blade.php. Extracted
 | from a previously-inline <script> block to satisfy CodeCanyon's
 | "no inline scripts unless dynamic" rule.
 |
 | The OneSignal SDK loader itself is still kept in the Blade view
 | as `<script src="https://cdn.onesignal.com/.../OneSignalSDK.page.js" defer>`
 | because OneSignal's integration REQUIRES that the SDK be served
 | from their CDN (the SDK signs subscription requests against the
 | served version).
 |
 | Dynamic configuration (appId + the logged-in user's id + tenant_id)
 | is passed via a JSON island that the view renders:
 |
 |     <script type="application/json" id="onesignal-config">
 |         { "appId": "...", "userId": 123, "tenantId": 7 }
 |     </script>
 |
 | This file is otherwise pure static JS — zero Blade interpolation.
 */
(function () {
    'use strict';

    var configEl = document.getElementById('onesignal-config');
    if (!configEl) return;

    var cfg;
    try {
        cfg = JSON.parse(configEl.textContent || '{}');
    } catch (_) {
        return;
    }

    var appId = cfg.appId || '';
    if (!appId) return;

    window.OneSignalDeferred = window.OneSignalDeferred || [];
    window.OneSignalDeferred.push(async function (OneSignal) {
        await OneSignal.init({
            appId: appId,
            notifyButton: { enable: false },
        });

        // Auto-prompt after 3 seconds if not already subscribed
        setTimeout(async function () {
            var perm = await OneSignal.Notifications.permission;
            if (perm) return; // already subscribed

            var dismissed = sessionStorage.getItem('push_prompt_dismissed');
            if (dismissed) return;

            var banner = document.createElement('div');
            banner.id = 'push-prompt-banner';
            // Styles applied via #push-prompt-banner / #push-allow-btn /
            // #push-dismiss-btn rules in public/css/views/filament/push-sw.css.

            var i18n = cfg.i18n || {};
            var enableMsg = i18n.banner_enable_message || '🔔 Enable push notifications for instant lead alerts.';
            var allowLabel = i18n.banner_allow_btn || 'Allow';
            // ✕ is a literal Unicode glyph (not English) — no translation needed.
            banner.innerHTML = [
                '<span>' + enableMsg + '</span>',
                '<button id="push-allow-btn">' + allowLabel + '</button>',
                '<button id="push-dismiss-btn">✕</button>',
            ].join('');

            document.body.appendChild(banner);

            document.getElementById('push-dismiss-btn').addEventListener('click', function () {
                sessionStorage.setItem('push_prompt_dismissed', '1');
                banner.remove();
            });

            document.getElementById('push-allow-btn').addEventListener('click', async function () {
                banner.remove();
                await OneSignal.Notifications.requestPermission();

                // Tag with user ID for targeted notifications
                var userId = cfg.userId;
                var tenantId = cfg.tenantId;
                if (userId) {
                    await OneSignal.login(String(userId));
                    await OneSignal.User.addTags({
                        user_id: String(userId),
                        tenant_id: String(tenantId || ''),
                    });
                }
            });
        }, 3000);
    });
})();
