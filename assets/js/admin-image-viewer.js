/* Admin 图片查看器：替代 iframe file-view.php 用于图片预览
   特性：鼠标拖拽平移、滚轮缩放、双击重置、键盘控制、Fluent UI 统一外观
   提供：window.ImageViewer.open(viewUrl, name, dlUrl)
        window.showImage(viewUrl, name, dlUrl)  // PHP 端 onclick 调用入口
   依赖：admin-fluent.css 中的 .iv-* 样式
   可选：FluentUI.ready（如果存在会等待 fluent-dialog 升级；否则降级为普通 div 容器） */
(function () {
  var dlg = null, body = null, img = null, spinner = null, scaleLbl = null;
  var scale = 1, tx = 0, ty = 0;
  var dragging = false, lastX = 0, lastY = 0, hasMoved = false;
  var curUrl = '', curName = '', curDl = '';

  function applyTransform() {
    if (!img) return;
    img.style.transform = 'translate(' + tx + 'px, ' + ty + 'px) scale(' + scale + ')';
  }
  function updateLabel() {
    if (scaleLbl) scaleLbl.textContent = Math.round(scale * 100) + '%';
  }
  function reset() {
    scale = 1; tx = 0; ty = 0;
    applyTransform(); updateLabel();
  }
  function setScale(s) {
    s = Math.max(0.2, Math.min(8, s));
    scale = s; applyTransform(); updateLabel();
  }
  function zoomIn() { setScale(scale * 1.2); }
  function zoomOut() { setScale(scale / 1.2); }

  function close() {
    if (!dlg) return;
    try { if (typeof dlg.hide === 'function') dlg.hide(); } catch (e) {}
    if (dlg.parentNode) dlg.parentNode.removeChild(dlg);
    dlg = null; body = null; img = null; spinner = null; scaleLbl = null;
    document.removeEventListener('keydown', onKey);
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  }

  function onKey(e) {
    if (!dlg) return;
    var step = 40;
    switch (e.key) {
      case 'Escape': close(); e.preventDefault(); break;
      case 'ArrowUp':    ty += step; applyTransform(); e.preventDefault(); break;
      case 'ArrowDown':  ty -= step; applyTransform(); e.preventDefault(); break;
      case 'ArrowLeft':  tx += step; applyTransform(); e.preventDefault(); break;
      case 'ArrowRight': tx -= step; applyTransform(); e.preventDefault(); break;
      case '+': case '=': zoomIn(); e.preventDefault(); break;
      case '-': case '_': zoomOut(); e.preventDefault(); break;
      case '0': reset(); e.preventDefault(); break;
    }
  }

  function onMove(e) {
    if (!dragging || !dlg) return;
    var dx = e.clientX - lastX, dy = e.clientY - lastY;
    if (Math.abs(dx) > 1 || Math.abs(dy) > 1) hasMoved = true;
    tx += dx; ty += dy;
    lastX = e.clientX; lastY = e.clientY;
    applyTransform();
  }
  function onUp() {
    if (dragging) {
      dragging = false;
      if (body) body.classList.remove('is-grabbing');
    }
  }

  function buildDom() {
    // 使用 fluent-dialog 若可用，否则降级为 div 模态容器
    var isFluent = window.FluentUI && window.FluentUI.ready;
    dlg = document.createElement(isFluent ? 'fluent-dialog' : 'div');
    dlg.className = 'flu-dialog flu-dialog-wide iv-dialog';
    if (isFluent) {
      try { dlg.modal = true; } catch (e) { dlg.setAttribute('modal', ''); }
    } else {
      dlg.style.position = 'fixed';
      dlg.style.zIndex = '2000';
    }

    // 头部
    var header = document.createElement('div');
    header.className = 'iv-header';
    var title = document.createElement('span');
    title.className = 'iv-title';
    title.textContent = curName || '图片预览';
    var closeBtn = document.createElement('button');
    closeBtn.className = 'iv-close';
    closeBtn.type = 'button';
    closeBtn.title = '关闭 (Esc)';
    closeBtn.innerHTML = '<i class="fa fa-times"></i>';
    closeBtn.addEventListener('click', close);
    header.appendChild(title);
    header.appendChild(closeBtn);
    dlg.appendChild(header);

    // 主体
    body = document.createElement('div');
    body.className = 'iv-body';
    img = document.createElement('img');
    img.className = 'iv-img';
    img.alt = curName || '';
    img.draggable = false;
    img.addEventListener('load', function () {
      img.classList.add('is-loaded');
      if (spinner) spinner.style.display = 'none';
      reset();
    });
    img.addEventListener('error', function () {
      if (spinner) {
        spinner.innerHTML = '<i class="fa fa-exclamation-triangle" style="color:#f59e0b;font-size:28px;"></i><div class="iv-spin-text">图片加载失败</div>';
      }
    });
    img.src = curUrl;
    body.appendChild(img);

    // 加载提示
    spinner = document.createElement('div');
    spinner.className = 'iv-spinner';
    spinner.innerHTML = '<i class="fa fa-circle-o-notch fa-spin"></i><div class="iv-spin-text">加载中...</div>';
    body.appendChild(spinner);

    // 拖拽
    body.addEventListener('mousedown', function (e) {
      if (e.button !== 0) return;
      dragging = true; hasMoved = false;
      lastX = e.clientX; lastY = e.clientY;
      body.classList.add('is-grabbing');
      e.preventDefault();
    });
    body.addEventListener('dblclick', function () { reset(); });
    body.addEventListener('wheel', function (e) {
      e.preventDefault();
      var delta = e.deltaY < 0 ? 1.15 : 1 / 1.15;
      // 以鼠标位置为锚点缩放（让缩放更自然）
      var rect = body.getBoundingClientRect();
      var cx = e.clientX - rect.left - rect.width / 2;
      var cy = e.clientY - rect.top - rect.height / 2;
      var oldScale = scale;
      setScale(scale * delta);
      var ratio = scale / oldScale;
      tx = cx - (cx - tx) * ratio;
      ty = cy - (cy - ty) * ratio;
      applyTransform();
    }, { passive: false });

    dlg.appendChild(body);

    // 工具栏
    var toolbar = document.createElement('div');
    toolbar.className = 'iv-toolbar';

    function mkBtn(act, icon, title) {
      var b = document.createElement('button');
      b.className = 'iv-btn'; b.type = 'button';
      b.setAttribute('data-act', act);
      b.title = title;
      b.innerHTML = '<i class="fa ' + icon + '"></i>';
      return b;
    }
    var bOut = mkBtn('zoomout', 'fa-search-minus', '缩小 (-)');
    scaleLbl = document.createElement('span');
    scaleLbl.className = 'iv-scale';
    scaleLbl.textContent = '100%';
    var bIn = mkBtn('zoomin', 'fa-search-plus', '放大 (+)');
    var bReset = mkBtn('reset', 'fa-refresh', '重置 (0)');

    // 下载按钮（带链接）
    var bDl = document.createElement('a');
    bDl.className = 'iv-btn';
    bDl.href = curDl || curUrl;
    bDl.download = curName || '';
    bDl.target = '_blank';
    bDl.rel = 'noopener noreferrer';
    bDl.title = '下载';
    bDl.innerHTML = '<i class="fa fa-download"></i>';

    // 新窗口打开
    var bNew = document.createElement('a');
    bNew.className = 'iv-btn';
    bNew.href = curUrl;
    bNew.target = '_blank';
    bNew.rel = 'noopener noreferrer';
    bNew.title = '新窗口打开';
    bNew.innerHTML = '<i class="fa fa-external-link"></i>';

    toolbar.appendChild(bOut);
    toolbar.appendChild(scaleLbl);
    toolbar.appendChild(bIn);
    toolbar.appendChild(bReset);
    toolbar.appendChild(bDl);
    toolbar.appendChild(bNew);

    // 快捷键提示
    var hint = document.createElement('span');
    hint.className = 'iv-hint';
    hint.innerHTML = '<kbd>滚轮</kbd>缩放 · <kbd>↑↓←→</kbd>移动 · <kbd>0</kbd>重置 · <kbd>Esc</kbd>关闭';
    toolbar.appendChild(hint);

    toolbar.addEventListener('click', function (e) {
      var t = e.target.closest('.iv-btn');
      if (!t) return;
      var act = t.getAttribute('data-act');
      if (act === 'zoomin') zoomIn();
      else if (act === 'zoomout') zoomOut();
      else if (act === 'reset') reset();
    });

    dlg.appendChild(toolbar);
    document.body.appendChild(dlg);

    // 显示
    if (isFluent) {
      try { if (typeof dlg.show === 'function') dlg.show(); else dlg.hidden = false; }
      catch (e) { dlg.hidden = false; }
    }
  }

  function open(viewUrl, name, dlUrl) {
    close(); // 关闭已存在的
    curUrl = viewUrl;
    curName = name || '';
    curDl = dlUrl || viewUrl;

    // 若 FluentUI 尚未 ready（首次加载 CDN 可能未升级），用普通 div；否则等 ready 再创建
    if (window.FluentUI && window.FluentUI.ready && window.customElements && customElements.whenDefined) {
      Promise.race([
        customElements.whenDefined('fluent-dialog'),
        new Promise(function (r) { setTimeout(r, 1500); })
      ]).then(function () {
        buildDom();
        document.addEventListener('keydown', onKey);
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
      });
    } else {
      buildDom();
      document.addEventListener('keydown', onKey);
      document.addEventListener('mousemove', onMove);
      document.addEventListener('mouseup', onUp);
    }
  }

  window.ImageViewer = { open: open, close: close, reset: reset, zoomIn: zoomIn, zoomOut: zoomOut };
  // PHP onclick 直接调用入口
  window.showImage = open;
})();
