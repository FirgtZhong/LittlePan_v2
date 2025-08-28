<?php
include("./includes/common.php");
$title = "关于 - " . $conf['title'];
include SYSTEM_ROOT.'header1.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
?>

<main id="main-container"><div class="bg-body-extra-light">
<!-- 以下是可编辑区域 -->
<!-- EDITABLE_START -->
<h2 style="text-align:center;">LittlePan_v2</h2><p style="text-align:center;">这是关于页面</p>
<!-- EDITABLE_END -->
<!-- 以上是可编辑区域 -->
</div></main>

<?php include SYSTEM_ROOT . 'footer1.php'; ?>
