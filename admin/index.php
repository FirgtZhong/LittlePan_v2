<?php
include("../includes/common.php");
$title='外链网盘管理中心';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$mysqlversion=$DB->getColumn("select VERSION()");
?>
<div class="stat-grid">
  <a href="file.php" class="stat-card">
    <div class="stat-icon" style="background:#0078d4"><i class="fa fa-cloud"></i></div>
    <div class="stat-body">
      <div class="stat-num" id="count2">0</div>
      <div class="stat-label">今日上传文件</div>
    </div>
  </a>
  <a href="file.php" class="stat-card">
    <div class="stat-icon" style="background:#107c10"><i class="fa fa-cloud-upload"></i></div>
    <div class="stat-body">
      <div class="stat-num" id="count3">0</div>
      <div class="stat-label">昨日上传文件</div>
    </div>
  </a>
  <a href="file.php" class="stat-card">
    <div class="stat-icon" style="background:#ca5010"><i class="fa fa-inbox"></i></div>
    <div class="stat-body">
      <div class="stat-num" id="count1">0</div>
      <div class="stat-label">现存文件</div>
    </div>
  </a>
  <div class="stat-card">
    <div class="stat-icon" style="background:#d13438"><i class="fa fa-users"></i></div>
    <div class="stat-body">
      <div class="stat-num" id="count4">0</div>
      <div class="stat-label">今日访问 / 总计 <span id="count5">0</span></div>
    </div>
  </div>
</div>

<div class="info-card">
  <div class="info-head">服务器信息</div>
  <ul class="kv-list">
    <li><b>PHP 版本：</b><span><?=phpversion()?> (<?=ini_get('safe_mode')?'线程安全':'非线程安全'?>)</span></li>
    <li><b>MySQL 版本：</b><span><?=htmlspecialchars($mysqlversion)?></span></li>
    <li><b>服务器软件：</b><span><?=htmlspecialchars($_SERVER['SERVER_SOFTWARE'])?></span></li>
    <li><b>程序最大运行时间：</b><span><?=ini_get('max_execution_time')?>s</span></li>
    <li><b>POST 请求许可：</b><span><?=ini_get('post_max_size')?></span></li>
    <li><b>文件上传许可：</b><span><?=ini_get('upload_max_filesize')?></span></li>
    <li><b>源码版本：</b><span><a href="<?=htmlspecialchars($conf['url'])?>"><font color="green">LittlePan_v2-v1.9.2-RC</font></a></span></li>
  </ul>
</div>
<script>
$(document).ready(function(){
    $.ajax({
		type : "GET",
		url : "ajax.php?act=getcount",
		dataType : 'json',
		async: true,
		success : function(data) {
            $('#count1').html(data.count1);
			$('#count2').html(data.count2);
			$('#count3').html(data.count3);
			$('#count4').html(data.count4);
			$('#count5').html(data.count5);
        }
    });
});
</script>
<?php include './footer.php';?>
