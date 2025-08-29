<?php
include("./includes/common.php");
$title = "关于 - " . $conf['title'];
include SYSTEM_ROOT.'header1.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
?>

<main id="main-container"><div class="bg-body-extra-light">
<!-- 以下是可编辑区域 -->
<!-- EDITABLE_START -->
<h2 style="text-align:center;">LittlePan_v2版本介绍</h2><p style="text-align:center;">这是一个外链网盘管理系统，如果你有什么问题的话，欢迎去Issues(<a href="https://github.com/FirgtZhong/LittlePan_v2/issues" target="_blank"><font color="#c24f4a">https://github.com/FirgtZhong/LittlePan_v2/issues</font><span></span></a>)进行详细说明</p><p style="text-align:center;">更多详细请看<a href="https://littlepan.netlify.app/" style="font-family: inherit; font-size: 24px; background-color: rgb(255, 255, 255);"><font color="#c24f4a">使用文档</font><span></span></a></p>
<!-- EDITABLE_END -->
<!-- 以上是可编辑区域 -->
</div></main>

<?php include SYSTEM_ROOT . 'footer1.php'; ?>
