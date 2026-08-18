<?php
@header('Content-Type: text/html; charset=UTF-8');
$accent = !empty($conf['web_color']) ? $conf['web_color'] : '#0078d4';
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?=htmlspecialchars($title ?? '')?></title>
  <link href="//cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link href="../assets/css/admin-fluent.css" rel="stylesheet"/>
  <script src="//cdn.staticfile.org/jquery/2.1.4/jquery.min.js"></script>
  <script type="module" src="https://cdn.jsdmirror.com/npm/@fluentui/web-components@2.6.1/dist/web-components.min.js"></script>
  <script src="../assets/js/admin-fluent.js"></script>
  <script src="../assets/js/admin-image-viewer.js"></script>
  <style>:root{--accent:<?=htmlspecialchars($accent)?>;}</style>
</head>
<body>
<fluent-design-system-provider accent-color="<?=htmlspecialchars($accent)?>" class="page-provider">
<?php if($islogin==1){?>
  <header class="topbar">
    <div class="topbar-inner">
      <a class="topbar-brand" href="./"><i class="fa fa-cube"></i><span>外链网盘管理中心</span></a>
      <nav class="topbar-nav">
        <a class="nav-link <?=checkIfActive('index,')?>" href="./"><i class="fa fa-home"></i><span>后台首页</span></a>
        <a class="nav-link <?=checkIfActive('file')?>" href="./file.php"><i class="fa fa-list"></i><span>文件管理</span></a>
        <details class="nav-dropdown <?=checkIfActive('set')?>">
          <summary class="nav-link"><i class="fa fa-cog"></i><span>系统设置</span> <i class="fa fa-caret-down"></i></summary>
          <div class="dropdown-menu">
            <a href="./set.php?mod=site">网站信息设置</a>
            <a href="./set.php?mod=notice">首页公告设置</a>
            <a href="./set.php?mod=tongji">第三方统计设置</a>
            <a href="./set.php?mod=about">关于页面设置</a>
            <a href="./set.php?mod=file">文件上传设置</a>
            <a href="./set.php?mod=green">内容安全设置</a>
            <a href="./set.php?mod=api">上传API设置</a>
            <a href="./set.php?mod=iptype">用户IP地址设置</a>
            <a href="./set.php?mod=account">管理账号设置</a>
          </div>
        </details>
        <a class="nav-link" href="./login.php?logout"><i class="fa fa-power-off"></i><span>退出登录</span></a>
      </nav>
    </div>
  </header>
<?php }?>
<main class="page-main">
