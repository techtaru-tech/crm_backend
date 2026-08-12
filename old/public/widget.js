(function () {
  'use strict';

  var script = document.currentScript || (function () {
    var scripts = document.querySelectorAll('script[data-form-id]');
    return scripts[scripts.length - 1];
  })();

  if (!script) return;

  var formId    = script.getAttribute('data-form-id');
  var position  = script.getAttribute('data-position')  || 'bottom-right';
  var btnColor  = script.getAttribute('data-btn-color') || '#6366f1';
  var btnText   = script.getAttribute('data-btn-text')  || 'Get in touch';
  var baseUrl   = script.getAttribute('data-base-url')  || (script.src.replace('/widget.js', ''));

  if (!formId) { console.warn('[LeadHub] widget.js: data-form-id is required.'); return; }

  var injectStyles = function () {
    var style = document.createElement('style');
    style.textContent = [
      '.lh-widget-btn {',
      '  position: fixed;',
      '  z-index: 99998;',
      '  display: flex;',
      '  align-items: center;',
      '  gap: 8px;',
      '  padding: 12px 20px;',
      '  border-radius: 50px;',
      '  border: none;',
      '  cursor: pointer;',
      '  font-size: 14px;',
      '  font-weight: 600;',
      '  color: #fff;',
      '  box-shadow: 0 4px 16px rgba(0,0,0,0.18);',
      '  transition: transform 0.15s, opacity 0.15s;',
      '}',
      '.lh-widget-btn:hover { transform: scale(1.04); opacity: 0.93; }',
      '.lh-widget-panel {',
      '  position: fixed;',
      '  z-index: 99999;',
      '  width: 400px;',
      '  max-width: calc(100vw - 32px);',
      '  height: 600px;',
      '  max-height: calc(100vh - 120px);',
      '  border-radius: 14px;',
      '  overflow: hidden;',
      '  box-shadow: 0 8px 40px rgba(0,0,0,0.22);',
      '  background: white;',
      '  transition: opacity 0.2s, transform 0.2s;',
      '  transform-origin: bottom right;',
      '}',
      '.lh-widget-panel.lh-hidden {',
      '  opacity: 0;',
      '  pointer-events: none;',
      '  transform: scale(0.92);',
      '}',
      '.lh-widget-close {',
      '  position: absolute;',
      '  top: 8px;',
      '  right: 10px;',
      '  background: rgba(0,0,0,0.08);',
      '  border: none;',
      '  border-radius: 50%;',
      '  width: 26px;',
      '  height: 26px;',
      '  font-size: 14px;',
      '  cursor: pointer;',
      '  color: #374151;',
      '  display: flex;',
      '  align-items: center;',
      '  justify-content: center;',
      '  z-index: 10;',
      '}',
      '.lh-widget-iframe {',
      '  width: 100%;',
      '  height: 100%;',
      '  border: none;',
      '}',
    ].join('\n');
    document.head.appendChild(style);
  };

  var positionStyle = (function () {
    var map = {
      'bottom-right' : { bottom: '24px', right: '24px' },
      'bottom-left'  : { bottom: '24px', left: '24px' },
      'top-right'    : { top: '24px',    right: '24px' },
      'top-left'     : { top: '24px',    left: '24px' },
    };
    return map[position] || map['bottom-right'];
  })();

  var panelPositionStyle = (function () {
    var offset = '80px';
    if (position.indexOf('bottom') !== -1) {
      return position.indexOf('right') !== -1
        ? { bottom: offset, right: '24px' }
        : { bottom: offset, left: '24px' };
    } else {
      return position.indexOf('right') !== -1
        ? { top: offset, right: '24px' }
        : { top: offset, left: '24px' };
    }
  })();

  var applyStyles = function (el, styles) {
    Object.keys(styles).forEach(function (k) { el.style[k] = styles[k]; });
  };

  var init = function () {
    injectStyles();

    var btn = document.createElement('button');
    btn.className = 'lh-widget-btn';
    btn.setAttribute('aria-label', btnText);
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' + btnText;
    btn.style.backgroundColor = btnColor;
    applyStyles(btn, positionStyle);
    document.body.appendChild(btn);

    var panel = document.createElement('div');
    panel.className = 'lh-widget-panel lh-hidden';
    applyStyles(panel, panelPositionStyle);
    document.body.appendChild(panel);

    var closeBtn = document.createElement('button');
    closeBtn.className = 'lh-widget-close';
    closeBtn.setAttribute('aria-label', 'Close form');
    closeBtn.innerHTML = '&#10005;';
    panel.appendChild(closeBtn);

    var iframe = null;

    var openPanel = function () {
      if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.className = 'lh-widget-iframe';
        iframe.src = baseUrl + '/forms/_widget_/' + formId;
        iframe.title = 'LeadHub Form';
        panel.appendChild(iframe);
      }
      panel.classList.remove('lh-hidden');
      btn.setAttribute('aria-expanded', 'true');
    };

    var closePanel = function () {
      panel.classList.add('lh-hidden');
      btn.setAttribute('aria-expanded', 'false');
    };

    btn.addEventListener('click', function () {
      panel.classList.contains('lh-hidden') ? openPanel() : closePanel();
    });
    closeBtn.addEventListener('click', closePanel);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePanel();
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
