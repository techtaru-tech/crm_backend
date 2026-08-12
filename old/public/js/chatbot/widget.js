/*!
 * LeadBot — embeddable chat bubble (vanilla JS, no build step).
 *
 * Shared-hosting friendly: ships prebuilt, loaded via a plain <script>.
 * The per-bot config is published on window.__LeadBot[key] by the
 * loader.js endpoint; this script renders the bubble + panel and talks
 * to the buffered /chatbot/{uuid}/chat endpoint.
 *
 * SECURITY: every piece of server/visitor text is rendered with
 * textContent (never innerHTML), so a malicious knowledge base / reply /
 * visitor message cannot inject HTML or script into the host page.
 */
(function () {
  'use strict';

  var script = document.currentScript;
  var key = script && script.getAttribute('data-leadbot');
  if (!key || !window.__LeadBot || !window.__LeadBot[key]) return;

  var cfg = window.__LeadBot[key];
  if (window.__LeadBotMounted && window.__LeadBotMounted[key]) return;
  window.__LeadBotMounted = window.__LeadBotMounted || {};
  window.__LeadBotMounted[key] = true;

  var COLOR = /^#[0-9a-fA-F]{3,8}$/.test(cfg.color || '') ? cfg.color : '#4f46e5';
  var STORAGE_KEY = '__leadbot_conv_' + key;

  function token() {
    try { return window.sessionStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
  }
  function setToken(v) {
    try { if (v != null) window.sessionStorage.setItem(STORAGE_KEY, String(v)); } catch (e) {}
  }

  // ---- styles (scoped to .lb- prefix) --------------------------------
  var css = [
    '.lb-launch{position:fixed;bottom:24px;right:24px;z-index:2147483000;width:60px;height:60px;border:none;border-radius:50%;background:' + COLOR + ';color:#fff;cursor:pointer;box-shadow:0 6px 20px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center;}',
    '.lb-launch svg{width:26px;height:26px;}',
    '.lb-panel{position:fixed;bottom:96px;right:24px;z-index:2147483000;width:360px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 120px);background:#fff;border-radius:16px;box-shadow:0 12px 48px rgba(0,0,0,.22);display:none;flex-direction:column;overflow:hidden;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;}',
    '.lb-panel.lb-open{display:flex;}',
    '.lb-head{background:' + COLOR + ';color:#fff;padding:16px 18px;font-weight:700;font-size:1rem;display:flex;align-items:center;justify-content:space-between;}',
    '.lb-close{background:none;border:none;color:#fff;font-size:22px;line-height:1;cursor:pointer;opacity:.85;}',
    '.lb-body{flex:1;overflow-y:auto;padding:16px;background:#f9fafb;display:flex;flex-direction:column;gap:10px;}',
    '.lb-msg{max-width:80%;padding:10px 13px;border-radius:14px;font-size:.9rem;line-height:1.4;white-space:pre-wrap;word-wrap:break-word;}',
    '.lb-bot{align-self:flex-start;background:#fff;color:#111827;border:1px solid #e5e7eb;border-bottom-left-radius:4px;}',
    '.lb-user{align-self:flex-end;background:' + COLOR + ';color:#fff;border-bottom-right-radius:4px;}',
    '.lb-cta{align-self:flex-start;margin-top:2px;display:inline-block;padding:9px 14px;border-radius:10px;background:' + COLOR + ';color:#fff;text-decoration:none;font-size:.85rem;font-weight:600;}',
    '.lb-typing{align-self:flex-start;color:#9ca3af;font-size:.85rem;padding:4px 6px;}',
    '.lb-foot{border-top:1px solid #e5e7eb;padding:10px;display:flex;gap:8px;background:#fff;}',
    '.lb-input{flex:1;border:1px solid #d1d5db;border-radius:10px;padding:10px 12px;font-size:.9rem;outline:none;resize:none;max-height:90px;font-family:inherit;}',
    '.lb-send{border:none;border-radius:10px;background:' + COLOR + ';color:#fff;padding:0 16px;font-weight:600;cursor:pointer;font-size:.9rem;}',
    '.lb-send:disabled{opacity:.55;cursor:not-allowed;}'
  ].join('');

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // ---- launch button -------------------------------------------------
  var launch = document.createElement('button');
  launch.className = 'lb-launch';
  launch.setAttribute('aria-label', cfg.launchLabel || 'Chat');
  launch.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3C6.5 3 2 6.86 2 11.5c0 2.3 1.13 4.38 2.97 5.88L4 21l3.9-1.55c1.27.42 2.65.65 4.1.65 5.5 0 10-3.86 10-8.6S17.5 3 12 3z"/></svg>';
  document.body.appendChild(launch);

  // ---- panel ---------------------------------------------------------
  var panel = document.createElement('div');
  panel.className = 'lb-panel';

  var head = document.createElement('div');
  head.className = 'lb-head';
  var headTitle = document.createElement('span');
  headTitle.textContent = cfg.name || 'Chat'; // textContent → safe
  var closeBtn = document.createElement('button');
  closeBtn.className = 'lb-close';
  closeBtn.setAttribute('aria-label', 'Close');
  closeBtn.textContent = '×';
  head.appendChild(headTitle);
  head.appendChild(closeBtn);

  var body = document.createElement('div');
  body.className = 'lb-body';

  var foot = document.createElement('div');
  foot.className = 'lb-foot';
  var input = document.createElement('textarea');
  input.className = 'lb-input';
  input.rows = 1;
  input.placeholder = cfg.placeholder || 'Type your message…';
  var send = document.createElement('button');
  send.className = 'lb-send';
  send.textContent = cfg.sendLabel || 'Send';
  foot.appendChild(input);
  foot.appendChild(send);

  panel.appendChild(head);
  panel.appendChild(body);
  panel.appendChild(foot);
  document.body.appendChild(panel);

  // ---- rendering helpers (textContent only) --------------------------
  function addMessage(text, who) {
    var el = document.createElement('div');
    el.className = 'lb-msg ' + (who === 'user' ? 'lb-user' : 'lb-bot');
    el.textContent = text == null ? '' : String(text);
    body.appendChild(el);
    body.scrollTop = body.scrollHeight;
    return el;
  }

  function addBookingLink(url) {
    // Only render http(s) links; anything else (javascript:, data:) is
    // dropped so a compromised reply can't smuggle a dangerous href.
    if (!/^https?:\/\//i.test(url || '')) return;
    var a = document.createElement('a');
    a.className = 'lb-cta';
    a.href = url;
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    a.textContent = cfg.launchLabel ? (cfg.launchLabel) : 'Book a time';
    body.appendChild(a);
    body.scrollTop = body.scrollHeight;
  }

  var greeted = false;
  function ensureGreeting() {
    if (greeted) return;
    greeted = true;
    if (cfg.greeting) addMessage(cfg.greeting, 'bot');
  }

  var open = false;
  function toggle(force) {
    open = (typeof force === 'boolean') ? force : !open;
    panel.classList.toggle('lb-open', open);
    if (open) { ensureGreeting(); input.focus(); }
  }

  launch.addEventListener('click', function () { toggle(); });
  closeBtn.addEventListener('click', function () { toggle(false); });

  // ---- send flow -----------------------------------------------------
  var busy = false;
  function doSend() {
    if (busy) return;
    var text = (input.value || '').trim();
    if (!text) return;

    addMessage(text, 'user');
    input.value = '';
    busy = true;
    send.disabled = true;

    var typing = document.createElement('div');
    typing.className = 'lb-typing';
    typing.textContent = '…';
    body.appendChild(typing);
    body.scrollTop = body.scrollHeight;

    var payload = { message: text };
    var t = token();
    if (t) payload.conversation_token = parseInt(t, 10);

    fetch(cfg.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    }).then(function (r) {
      return r.json().catch(function () { return {}; });
    }).then(function (data) {
      if (typing.parentNode) typing.parentNode.removeChild(typing);
      if (data && data.conversation_token != null) setToken(data.conversation_token);
      addMessage((data && data.reply) ? data.reply : '…', 'bot');
      if (data && data.booking_url) addBookingLink(data.booking_url);
    }).catch(function () {
      if (typing.parentNode) typing.parentNode.removeChild(typing);
      addMessage('⚠', 'bot');
    }).then(function () {
      busy = false;
      send.disabled = false;
      input.focus();
    });
  }

  send.addEventListener('click', doSend);
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); doSend(); }
  });
})();
