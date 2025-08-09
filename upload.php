<?php
include("./includes/common.php");

$title = '上传文件 - ' . $conf['title'];
include SYSTEM_ROOT . 'header1.php';

$maxfilesize = ini_get('upload_max_filesize');
$csrf_token = md5(mt_rand(0, 999) . time());
$_SESSION['csrf_token'] = $csrf_token;
?>
	<style>
		.div1, .div2 {
		  display: inline-block;}
	</style>
<main id="main-container">
	<div class="content content-full">
		<div>
			<!-- 主上传区域 -->
			<div class="div2 bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
				<div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
					<div class="block-header block-header-default">
					</div>
					<div class="flex flex-wrap gap-3 grow mx-5 my-10">
						<div id="progressBar" style="display: block;">
							<!-- 动态显示进度条 -->
						</div>
						<h1 class="flex items-center font-bold text-windows-dark">选择一个文件开始上传</h1>
						<br />
						<input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $csrf_token ?>">
						<div id="upload_block" class="mb-3"></div>
						<div id="upload_frame">
							<button id="uploadFile" class="flex items-center gap-2 px-4 py-2 bg-windows-blue text-white rounded-lg hover:bg-windows-blue/90 transition-windows btn-hover">
								<i class="fa fa-upload mr-1"></i> 立即上传 </button>
							<!-- 文件在首页显示复选框 -->
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="show" checked="">
									<label class="custom-control-label" for="show">在首页文件列表显示</label>
								</div>
							</div>
							<!-- 文件加密复选框和密码输入框 -->
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="ispwd">
									<label class="custom-control-label" for="ispwd">设定密码</label>
								</div>
							</div>
							<div class="form-group" id="pwd_frame" style="display: none;">
								<input type="text" class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-windows-blue/30 focus:border-windows-blue transition-windows w-full md:w-64" id="pwd" placeholder="请输入密码" autocomplete="off">
								<small class="form-text text-muted">密码只能为字母或数字</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- 右侧的上传提示块 -->
			<div class="div2 bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
				<div class="block block-rounded block-fx-shadow mx-5 my-5">
					<div class="block-header block-header-default">
						<h3 class="block-title"><i class="fa fa-exclamation-circle mr-1"></i> 上传提示</h3>
					</div>
					<div class="block-content">
						<ul class="fa-ul list-unstyled">
							<li><span class="fa-li"><i class="fa fa-info-circle text-warning"></i></span>您的IP是 <?php echo $clientip ?>，请不要上传违规文件！</li>
							<li><span class="fa-li"><i class="fa fa-info-circle text-warning"></i></span>上传无格式限制，当前服务器单个文件上传最大支持 <b><?php echo $maxfilesize ?></b>！</li>
							<li><span class="fa-li"><i class="fa fa-info-circle text-warning"></i></span>上传违规文件会被服务器拦截，提示上传失败！</li>
							<li><span class="fa-li"><i class="fa fa-info-circle text-warning"></i></span>文件名不要带有英文特殊符号或者emoji字符！</li> <?php if ($conf['videoreview'] == 1) { ?> <li><span class="fa-li"><i class="fa fa-info-circle text-warning"></i></span>当前网站已开启视频文件审核，如上传视频需审核通过后才能下载和播放。</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<script>
	// 控制密码输入框显示/隐藏
	document.getElementById('ispwd').addEventListener('change', function() {
	    document.getElementById('pwd_frame').style.display = this.checked ? 'block' : 'none';
	});
</script>
<script src="assets/js/upload.js"></script>
<?php include SYSTEM_ROOT . 'footer1.php'; ?>
