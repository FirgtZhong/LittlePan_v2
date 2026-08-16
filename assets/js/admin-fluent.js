/* Admin Fluent UI 交互工具：替代 layer.js
   提供 loading / alert / success / error / confirm / toast / dialog
   依赖 @fluentui/web-components 已注册 <fluent-dialog> 等自定义元素。
   通过 readyPromise 等待 customElements 定义完成，避免异步加载竞态。 */
window.FluentUI = (function () {
  var readyPromise;
  if (window.customElements && customElements.whenDefined) {
    readyPromise = Promise.race([
      customElements.whenDefined('fluent-dialog'),
      new Promise(function (r) { setTimeout(r, 4000); })
    ]);
  } else {
    readyPromise = Promise.resolve();
  }

  function el(tag, attrs, html) {
    var e = document.createElement(tag);
    if (attrs) for (var k in attrs) {
      if (k === 'class') e.className = attrs[k];
      else if (k === 'style') e.style.cssText = attrs[k];
      else e.setAttribute(k, attrs[k]);
    }
    if (html != null) e.innerHTML = html;
    return e;
  }

  var loadingCount = 0, loadingOverlay = null;
  function loading() {
    loadingCount++;
    if (!loadingOverlay) {
      loadingOverlay = el('div', { class: 'flu-loading-overlay' });
      loadingOverlay.innerHTML = '<fluent-progress-ring></fluent-progress-ring>';
      document.body.appendChild(loadingOverlay);
    }
    return loadingCount;
  }
  function closeLoading() {
    loadingCount = Math.max(0, loadingCount - 1);
    if (loadingCount === 0 && loadingOverlay) {
      loadingOverlay.remove();
      loadingOverlay = null;
    }
  }

  function closeDialog(dlg) {
    if (!dlg) return;
    try { dlg.hide(); } catch (e) {}
    dlg.remove();
  }

  function dialog(opts) {
    return readyPromise.then(function () {
      var dlg = el('fluent-dialog', { class: 'flu-dialog' + (opts.wide ? ' flu-dialog-wide' : '') });
      try { dlg.modal = true; } catch (e) { dlg.setAttribute('modal', ''); }

      var titleEl = el('div', { class: 'flu-dialog-title' }, opts.title || '');
      var bodyEl = el('div', { class: 'flu-dialog-body' });
      if (typeof opts.body === 'string') bodyEl.innerHTML = opts.body;
      else if (opts.body) bodyEl.appendChild(opts.body);

      var footer = el('div', { class: 'flu-dialog-footer' });
      var hasOk = opts.okText !== null && opts.okText !== false;
      var hasCancel = opts.cancelText !== null && opts.cancelText !== false;

      function done(cb) { closeDialog(dlg); if (cb) cb(); }

      if (hasCancel) {
        var cancelBtn = el('fluent-button', { appearance: 'stealth' }, opts.cancelText || '取消');
        cancelBtn.addEventListener('click', function () { done(opts.onCancel); });
        footer.appendChild(cancelBtn);
      }
      if (hasOk) {
        var okBtn = el('fluent-button', { appearance: 'accent' }, opts.okText || '确定');
        okBtn.addEventListener('click', function () {
          if (opts.onOk && opts.onOk(dlg) === false) return;
          done();
        });
        footer.appendChild(okBtn);
      }

      dlg.appendChild(titleEl);
      dlg.appendChild(bodyEl);
      if (hasOk || hasCancel) dlg.appendChild(footer);
      document.body.appendChild(dlg);
      try { dlg.show(); } catch (e) { dlg.hidden = false; }
      return dlg;
    });
  }

  function msgBox(msg, type, onOk) {
    var icon = type === 'success'
      ? '<i class="fa fa-check-circle" style="color:#107c10;font-size:26px;"></i>'
      : type === 'error'
        ? '<i class="fa fa-exclamation-circle" style="color:#d13438;font-size:26px;"></i>'
        : '<i class="fa fa-info-circle" style="color:#0078d4;font-size:26px;"></i>';
    return dialog({
      title: '提示',
      body: '<div class="flu-msg">' + icon + '<span>' + msg + '</span></div>',
      okText: '确定',
      cancelText: false,
      onOk: onOk
    });
  }
  function alert(msg, onOk) { return msgBox(msg, 'info', onOk); }
  function success(msg, onOk) { return msgBox(msg, 'success', onOk); }
  function error(msg, onOk) { return msgBox(msg, 'error', onOk); }
  function confirm(msg, onOk, onCancel) {
    return dialog({
      title: '确认操作',
      body: '<div class="flu-msg"><i class="fa fa-question-circle" style="color:#0078d4;font-size:26px;"></i><span>' + msg + '</span></div>',
      okText: '确定',
      cancelText: '取消',
      onOk: onOk,
      onCancel: onCancel
    });
  }

  var toastTimer = null;
  function toast(msg, type) {
    var t = document.getElementById('flu-toast');
    if (!t) { t = el('div', { id: 'flu-toast', class: 'flu-toast' }); document.body.appendChild(t); }
    t.className = 'flu-toast show ' + (type === 'error' ? 'toast-error' : 'toast-success');
    t.innerHTML = '<i class="fa ' + (type === 'error' ? 'fa-times-circle' : 'fa-check-circle') + '"></i> ' + msg;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.className = 'flu-toast'; }, 2200);
  }

  return {
    loading: loading, closeLoading: closeLoading,
    dialog: dialog, alert: alert, success: success, error: error,
    confirm: confirm, toast: toast
  };
})();
