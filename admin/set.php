<?php
/**
 * 系统设置
**/
include("../includes/common.php");
$title='系统设置';
include './head.php';
if($islogin==1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="set-wrap" style="max-width:920px;margin:0 auto;">
<?php
$mod=isset($_GET['mod'])?$_GET['mod']:null;
?>
<?php
if($mod=='site') {
	?>
	<div class="info-card">
	<div class="info-head">网站信息设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
		<div class="form-group">
		  <label class="col-sm-2 control-label">网站标题</label>
		  <div class="col-sm-10"><input type="text" name="title" value="<?php echo $conf['title']; ?>" class="form-control" required/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">关键字</label>
		  <div class="col-sm-10"><input type="text" name="keywords" value="<?php echo $conf['keywords']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">网站描述</label>
		  <div class="col-sm-10"><input type="text" name="description" value="<?php echo $conf['description']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">禁止访问IP</label>
		  <div class="col-sm-10"><textarea class="form-control" name="blackip" rows="2" placeholder="多个IP用|隔开"><?php echo $conf['blackip']?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">文件查看页公告</label>
		  <div class="col-sm-10"><textarea class="form-control" name="gg_file" rows="3" placeholder="不填写则不显示"><?php echo htmlspecialchars($conf['gg_file'])?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">网站主题颜色</label>
		  <div class="col-sm-10"><textarea class="form-control" name="web_color" rows="2" placeholder="请在此写入16进制颜色代码(RGB)"><?php echo htmlspecialchars($conf['web_color'])?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">网站左上角图标</label>
		  <div class="col-sm-10"><textarea class="form-control" name="icon" rows="2" placeholder="请在此写入图片链接URL"><?php echo htmlspecialchars($conf['icon'])?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">网站背景图片</label>
		  <div class="col-sm-10"><textarea class="form-control" name="bg" rows="2" placeholder="请在此写入图片链接URL"><?php echo htmlspecialchars($conf['bg'])?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">背景蒙版设定</label>
		  <div class="col-sm-10" style="display:flex;gap:8px;flex-wrap:wrap;">
			<textarea class="form-control" name="red-rgba" rows="1" placeholder="0~255(Red)" style="flex:1;min-width:120px;"><?php echo htmlspecialchars($conf['red-rgba'])?></textarea>
			<textarea class="form-control" name="green-rgba" rows="1" placeholder="0~255(Green)" style="flex:1;min-width:120px;"><?php echo htmlspecialchars($conf['green-rgba'])?></textarea>
			<textarea class="form-control" name="blue-rgba" rows="1" placeholder="0~255(Blue)" style="flex:1;min-width:120px;"><?php echo htmlspecialchars($conf['blue-rgba'])?></textarea>
			<textarea class="form-control" name="alpha-rgba" rows="1" placeholder="0~1(Alpha)" style="flex:1;min-width:120px;"><?php echo htmlspecialchars($conf['alpha-rgba'])?></textarea>
		  </div>
		</div>
		<div class="form-group">
	  <label class="col-sm-2 control-label">统计代码</label>
	  <div class="col-sm-10"><font color="gray">已迁移至<a href="./set.php?mod=tongji">第三方统计设置</a>页面</font></div>
	</div>
		<div class="form-group">
		  <div class="col-sm-offset-2 col-sm-10"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	</div>
	<?php
} elseif($mod=='notice') {
	?>
	<div class="info-card">
	<div class="info-head">首页公告设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
		<div class="form-group">
		  <label class="col-sm-2 control-label">显示公告</label>
		  <div class="col-sm-10">
		    <select class="form-control" name="notice_open" default="<?php echo $conf['notice_open']?>">
		      <option value="0">隐藏</option>
		      <option value="1">显示</option>
		    </select>
		    <font color="green">选择"显示"后，首页将显示公告内容</font>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">公告标题</label>
		  <div class="col-sm-10"><input type="text" name="notice_title" value="<?php echo htmlspecialchars($conf['notice_title'])?>" class="form-control" placeholder="例如：最新公告"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">公告内容</label>
		  <div class="col-sm-10"><textarea class="form-control" name="notice_content" rows="5" placeholder="请输入公告内容，支持换行"><?php echo htmlspecialchars($conf['notice_content'])?></textarea></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">发布时间</label>
		  <div class="col-sm-10"><input type="text" name="notice_time" value="<?php echo htmlspecialchars($conf['notice_time'])?>" class="form-control" placeholder="例如：2026.07.31 12:00"/></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-2 col-sm-10"><fluent-button type="submit" appearance="accent">保存修改</fluent-button></div>
		</div>
	  </form>
	</div>
	</div>
	<?php
} elseif($mod=='tongji') {
	?>
	<div class="info-card">
	<div class="info-head">第三方统计设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
		<div class="form-group">
		  <label class="col-sm-2 control-label">第三方统计</label>
		  <div class="col-sm-10">
		    <select class="form-control" name="tongji_open" default="<?php echo isset($conf['tongji_open'])?$conf['tongji_open']:'0'?>">
		      <option value="0">关闭</option>
		      <option value="1">开启</option>
		    </select>
		    <font color="green">开启后将在前台页面底部插入第三方统计代码（如百度统计、Google Analytics、51la等）</font>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">统计代码</label>
		  <div class="col-sm-10">
		    <textarea class="form-control" name="tongji" rows="8" placeholder="请在此粘贴第三方统计代码（支持script标签）"><?php echo htmlspecialchars(isset($conf['tongji'])?$conf['tongji']:'')?></textarea>
		    <font color="blue">提示：百度统计、Google Analytics、CNZZ、51la等均可直接粘贴完整script代码</font>
		  </div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-2 col-sm-10"><fluent-button type="submit" appearance="accent">保存修改</fluent-button></div>
		</div>
	  </form>
	</div>
	<div class="info-foot">
	<i class="fa fa-info-circle"></i>
	网站自身统计（今日访问/总访问）始终开启，无需在此设置。本页面仅用于添加第三方统计代码。
	</div>
	</div>
	<?php
} elseif($mod=='about') {
	$about_file = SYSTEM_ROOT . '../about.php';
	$about_content = '';
	if(file_exists($about_file)) {
		$full_content = file_get_contents($about_file);
		if(preg_match('/<!-- EDITABLE_START -->(.*?)<!-- EDITABLE_END -->/s', $full_content, $matches)) {
			$about_content = $matches[1];
		} else {
			$about_content = "<!-- 错误：未找到可编辑区域，请检查about.php是否包含正确的标记 -->";
		}
	}
	?>
	<div class="info-card">
	<div class="info-head">关于页面设置</div>
	<div class="info-body">
	  <form onsubmit="return saveAbout(this)" method="post" class="form-horizontal" role="form">
	    <div class="form-group">
	      <label class="col-sm-2 control-label">关于页面内容编辑</label>
	      <div class="col-sm-10">
	        <div id="editor" style="width:100%;height:600px;"></div>
	        <textarea name="content" id="content" style="display:none;"><?php echo htmlspecialchars($about_content);
	?></textarea>
	        <font color="red">注意：修改会直接影响根目录下的about.php文件，请谨慎操作</font><br><font color="blue">使用<a href="//wangeditor.com">wangEditor</a>编辑</font>
	      </div>
	    </div>
	    <div class="form-group">
	      <div class="col-sm-offset-2 col-sm-10">
	        <fluent-button type="submit" appearance="accent">保存修改</fluent-button>
	      </div>
	    </div>
	  </form>
	</div>
	</div>
	<script src="/wangEditor.min.js"></script>
	<script>
	const E = window.wangEditor
	const editor = new E('#editor')
	const textarea = document.getElementById('content')
	editor.config.height = 400
	editor.config.zIndex = 100
	editor.config.showFullScreen = false
	editor.config.menus = [
	  'head', 'bold', 'fontSize', 'fontName', 'italic', 'underline',
	  'strikeThrough', 'foreColor', 'backColor', 'link', 'list',
	  'justify', 'quote', 'emoticon', 'image', 'table', 'code', 'undo', 'redo'
	]
	editor.config.onchange = function (html) {
		textarea.value = html
	}
	editor.create()
	editor.txt.html(textarea.value)
	function saveAbout(obj) {
		FluentUI.loading();
		$.ajax({
			type : 'POST',
			url : 'ajax.php?act=saveAbout',
			data : $(obj).serialize(),
			dataType : 'json',
			success : function(data) {
				FluentUI.closeLoading();
				if(data.code == 0) {
					FluentUI.success('保存成功！', function(){ window.location.reload(); });
				} else {
					FluentUI.error(data.msg);
				}
			},
			error:function(data) {
				FluentUI.closeLoading();
				FluentUI.toast('服务器错误', 'error');
				return false;
			}
		});
		return false;
	}
	</script>
	<?php
} elseif($mod=='api') {
	$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
	$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
	$admin_path = substr($sitepath, strrpos($sitepath, '/'));
	$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].str_replace($admin_path,'',$sitepath).'/';
	?>
	<div class="info-card">
	<div class="info-head">上传API设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
		<div class="form-group">
		  <label class="col-sm-3 control-label">上传API开关</label>
		  <div class="col-sm-9"><select class="form-control" name="api_open" default="<?php echo $conf['api_open']?>"><option value="0">关闭</option><option value="1">开启</option></select></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">来源域名白名单</label>
		  <div class="col-sm-9"><input type="text" name="api_referer" value="<?php echo $conf['api_referer']; ?>" class="form-control" placeholder="多个域名用|隔开"/><font color="green">多个域名用|隔开，不填写则不限制来源域名</font></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-3 col-sm-9"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	</div>
	<div class="info-card">
	<div class="info-head">上传API文档</div>
	<div class="info-body">
	<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;padding:12px 16px;background:<?php echo $conf['api_open']?'#f0fdf4':'#fef2f2';?>;border:1px solid <?php echo $conf['api_open']?'#bbf7d0':'#fecaca';?>;border-radius:6px;margin-bottom:14px;">
	  <div>
		<b style="font-size:14px;color:#111;">API接口地址：</b>
		<code style="background:#fff;padding:3px 8px;border-radius:4px;border:1px solid #e5e7eb;font-family:Consolas,monospace;font-size:12px;color:var(--accent,#0078d4);"><?php echo $siteurl?>api.php</code>
	  </div>
		<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;background:<?php echo $conf['api_open']?'#16a34a':'#dc2626';?>;color:#fff;">
			<i class="fa <?php echo $conf['api_open']?'fa-check-circle':'fa-exclamation-circle';?>"></i>
			<?php echo $conf['api_open']?'当前API已启用':'当前API未启用，请在上方开启';?>
		</span>
	</div>
	<p style="color:#4b5563;font-size:13px;line-height:1.7;">当前API支持JSON、JSONP、FORM 3种返回方式，支持Web跨域调用，也支持程序中直接调用。请求方式：<code>POST multipart/form-data</code>。</p>

	<h3>请求参数说明</h3>
	<table>
	<thead><tr>
	<th>字段名</th><th>变量名</th><th>是否必填</th><th>示例值</th><th>描述</th>
	</tr></thead>
	<tbody>
	<tr><td>文件</td><td><code>file</code></td><td>是</td><td>-</td><td>multipart格式文件</td></tr>
	<tr><td>是否首页显示</td><td><code>show</code></td><td>否</td><td>1</td><td>默认为是</td></tr>
	<tr><td>是否设置密码</td><td><code>ispwd</code></td><td>否</td><td>0</td><td>默认为否</td></tr>
	<tr><td>下载密码</td><td><code>pwd</code></td><td>否</td><td>123456</td><td>默认留空</td></tr>
	<tr><td>返回格式</td><td><code>format</code></td><td>否</td><td>json</td><td>json、jsonp、form 三种，默认 json</td></tr>
	<tr><td>跳转页面url</td><td><code>backurl</code></td><td>否</td><td>http://...</td><td>上传成功后的跳转地址，仅 form 格式有效</td></tr>
	<tr><td>callback</td><td><code>callback</code></td><td>否</td><td>callback</td><td>仅 jsonp 格式有效</td></tr>
	</tbody></table>

	<h3>返回参数说明</h3>
	<table>
	<thead><tr>
	<th>字段名</th><th>变量名</th><th>类型</th><th>示例值</th><th>描述</th>
	</tr></thead>
	<tbody>
	<tr><td>上传状态</td><td><code>code</code></td><td>Int</td><td>0</td><td>0 为成功，其他为失败</td></tr>
	<tr><td>提示信息</td><td><code>msg</code></td><td>String</td><td>上传成功！</td><td>失败时返回错误描述</td></tr>
	<tr><td>文件MD5</td><td><code>hash</code></td><td>String</td><td>f1e807cb…</td><td>文件内容 MD5</td></tr>
	<tr><td>文件名称</td><td><code>name</code></td><td>String</td><td>example.jpg</td><td>-</td></tr>
	<tr><td>文件大小</td><td><code>size</code></td><td>Int</td><td>58937</td><td>单位：字节</td></tr>
	<tr><td>文件格式</td><td><code>type</code></td><td>String</td><td>jpg</td><td>-</td></tr>
	<tr><td>下载地址</td><td><code>downurl</code></td><td>String</td><td>http://…</td><td>-</td></tr>
	<tr><td>预览地址</td><td><code>viewurl</code></td><td>String</td><td>http://…</td><td>仅图片、音乐、视频文件有值</td></tr>
	</tbody></table>

	<h3>文件删除接口（DELETE）</h3>
	<p style="color:#4b5563;font-size:13px;line-height:1.7;">请求方式：<code>DELETE application/x-www-form-urlencoded</code>；必选参数 <code>hash</code>；若文件设置了密码，删除时必须提交 <code>file_pwd</code>；未设置密码的文件只能通过后台删除。</p>
	<p><b>请求示例：</b></p>
<pre><code>curl -X DELETE \
  -d "hash=3a78c73f88489c8f33c0e430683b5d5&file_pwd=123456" \
  <?php echo $siteurl?>api.php</code></pre>
	<p><b>成功响应：</b></p>
<pre><code>{
  "code": 0,
  "msg": "文件删除成功"
}</code></pre>
	<p><b>失败响应：</b></p>
<pre><code>{
  "code": -1,
  "msg": "文件密码错误"
}</code></pre>
	<p style="color:#b45309;background:#fffbeb;border:1px solid #fde68a;padding:8px 12px;border-radius:6px;font-size:13px;"><i class="fa fa-lightbulb-o"></i> 更完整的开发者使用示例（cURL / file_get_contents / Guzzle）、响应解释与注意事项，请访问前台 <a href="/apidoc.php" target="_blank" rel="noopener noreferrer">/apidoc.php</a> 查看。</p>
	</div>
	</div>
	<?php
} elseif($mod=='account_n' && $_POST['do']=='submit') {
	$user=trim($_POST['user']);
	$oldpwd=$_POST['oldpwd'];
	$newpwd=$_POST['newpwd'];
	$newpwd2=$_POST['newpwd2'];
	$err='';
	if($user==null){
		$err='用户名不能为空！';
	} else {
		saveSetting('admin_user',$user);
		if(!empty($newpwd) && !empty($newpwd2)) {
			if($oldpwd!=$conf['admin_pwd']) $err='旧密码不正确！';
			elseif($newpwd!=$newpwd2) $err='两次输入的密码不一致！';
			else saveSetting('admin_pwd',$newpwd);
		}
		if(!$err){
			$ad=$CACHE->clear();
			if(!$ad) $err='修改失败！'.$DB->error();
		}
	}
	?>
	<div class="info-card">
	<div class="info-head">管理员账号修改结果</div>
	<div class="info-body">
	<?php if($err){?>
		<div class="login-msg alert-error"><i class="fa fa-exclamation-circle"></i> <?=htmlspecialchars($err)?></div>
		<p style="margin-top:16px;"><a href="./set.php?mod=account" class="grid-btn btn-edit">返回修改</a></p>
	<?php } else { ?>
		<div class="login-msg alert-success"><i class="fa fa-check-circle"></i> 修改成功！请重新登录</div>
		<p style="margin-top:16px;"><a href="./login.php?logout" class="grid-btn btn-edit">重新登录</a></p>
	<?php }?>
	</div>
	</div>
	<?php
} elseif($mod=='account') {
	?>
	<div class="info-card">
	<div class="info-head">管理员账号设置</div>
	<div class="info-body">
	  <form action="./set.php?mod=account_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit"/>
		<div class="form-group">
		  <label class="col-sm-2 control-label">用户名</label>
		  <div class="col-sm-10"><input type="text" name="user" value="<?php echo $conf['admin_user']; ?>" class="form-control" required/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">旧密码</label>
		  <div class="col-sm-10"><input type="password" name="oldpwd" value="" class="form-control" placeholder="请输入当前的管理员密码"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">新密码</label>
		  <div class="col-sm-10"><input type="password" name="newpwd" value="" class="form-control" placeholder="不修改请留空"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-2 control-label">重输密码</label>
		  <div class="col-sm-10"><input type="password" name="newpwd2" value="" class="form-control" placeholder="不修改请留空"/></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-2 col-sm-10"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	</div>
	<?php
} elseif($mod=='iptype') {
	?>
	<div class="info-card">
	<div class="info-head">用户IP地址获取设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
	    <div class="form-group">
		  <label class="col-sm-2 control-label">用户IP地址获取方式</label>
		  <div class="col-sm-10"><select class="form-control" name="ip_type" default="<?php echo $conf['ip_type']?>"><option value="0">0_X_FORWARDED_FOR</option><option value="1">1_X_REAL_IP</option><option value="2">2_REMOTE_ADDR</option></select></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-2 col-sm-10"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	<div class="info-foot">
	<i class="fa fa-info-circle"></i>
	此功能设置用于防止用户伪造IP请求。<br/>
	X_FORWARDED_FOR：之前的获取真实IP方式，极易被伪造IP<br/>
	X_REAL_IP：在网站使用CDN的情况下选择此项，在不使用CDN的情况下也会被伪造<br/>
	REMOTE_ADDR：直接获取真实请求IP，无法被伪造，但可能获取到的是CDN节点IP<br/>
	<b>你可以从中选择一个能显示你真实地址的IP，优先选下方的选项。</b>
	</div>
	</div>
	<script>
	$(document).ready(function() {
		$.ajax({
			type : "GET",
			url : "ajax.php?act=iptype",
			dataType : 'json',
			async: true,
			success : function(data) {
				$("select[name='ip_type']").empty();
				var defaultv = $("select[name='ip_type']").attr('default');
				$.each(data, function(k, item) {
					$("select[name='ip_type']").append('<option value="'+k+'" '+(defaultv==k?'selected':'')+'>'+ item.name +' - '+ item.ip +' '+ item.city +'</option>');
				});
			}
		});
	});
	</script>
	<?php
} elseif($mod=='file') {
	?>
	<div class="info-card">
	<div class="info-head">文件上传设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
	    <div class="form-group">
		  <label class="col-sm-3 control-label">存储类型</label>
		  <div class="col-sm-9"><select class="form-control" name="storage" default="<?php echo $conf['storage']?>"><option value="local">本地存储</option><option value="oss">阿里云OSS</option><option value="qcloud">腾讯云COS</option><option value="obs">华为云OBS</option><option value="upyun">又拍云</option><option value="webdav">WebDAV存储</option><?php if (defined('SAE_ACCESSKEY')) {
			?><option value="sae">SaeStorage</option><?php
		}
		?></select></div>
		</div>
		<div id="oss_info" style="<?php echo $conf['storage']!='oss'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云AccessKey Id</label>
		  <div class="col-sm-9"><input type="text" name="oss_ak" value="<?php echo $conf['oss_ak']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云AccessKey Secret</label>
		  <div class="col-sm-9"><input type="text" name="oss_sk" value="<?php echo $conf['oss_sk']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云OSS EndPoint</label>
		  <div class="col-sm-9"><input type="text" name="oss_endpoint" value="<?php echo $conf['oss_endpoint']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云OSS Bucket</label>
		  <div class="col-sm-9"><input type="text" name="oss_bucket" value="<?php echo $conf['oss_bucket']; ?>" class="form-control"/></div>
		</div>
		</div>
		<div id="qcloud_info" style="<?php echo $conf['storage']!='qcloud'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">腾讯云SecretId</label>
		  <div class="col-sm-9"><input type="text" name="qcloud_id" value="<?php echo $conf['qcloud_id']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">腾讯云SecretKey</label>
		  <div class="col-sm-9"><input type="text" name="qcloud_key" value="<?php echo $conf['qcloud_key']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">COS存储桶地域</label>
		  <div class="col-sm-9"><input type="text" name="qcloud_region" value="<?php echo $conf['qcloud_region']; ?>" class="form-control" placeholder="填写英文名称，例如：ap-shanghai"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">COS存储桶名称</label>
		  <div class="col-sm-9"><input type="text" name="qcloud_bucket" value="<?php echo $conf['qcloud_bucket']; ?>" class="form-control" placeholder="格式：BucketName-APPID"/></div>
		</div>
		</div>
		<div id="obs_info" style="<?php echo $conf['storage']!='obs'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">华为云AccessKeyId</label>
		  <div class="col-sm-9"><input type="text" name="obs_ak" value="<?php echo $conf['obs_ak']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">华为云SecretAccessKey</label>
		  <div class="col-sm-9"><input type="text" name="obs_sk" value="<?php echo $conf['obs_sk']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">OBS EndPoint</label>
		  <div class="col-sm-9"><input type="text" name="obs_endpoint" value="<?php echo $conf['obs_endpoint']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">OBS桶名称</label>
		  <div class="col-sm-9"><input type="text" name="obs_bucket" value="<?php echo $conf['obs_bucket']; ?>" class="form-control"/></div>
		</div>
		</div>
		<div id="upyun_info" style="<?php echo $conf['storage']!='upyun'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">云存储服务名称</label>
		  <div class="col-sm-9"><input type="text" name="upyun_name" value="<?php echo $conf['upyun_name']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">操作员名称</label>
		  <div class="col-sm-9"><input type="text" name="upyun_user" value="<?php echo $conf['upyun_user']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">操作员密码</label>
		  <div class="col-sm-9"><input type="text" name="upyun_pwd" value="<?php echo $conf['upyun_pwd']; ?>" class="form-control"/></div>
		</div>
		</div>
		<div id="local_info" style="<?php echo $conf['storage']!='local'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">本地存储路径</label>
		  <div class="col-sm-9"><input type="text" name="filepath" value="<?php echo $conf['filepath']; ?>" class="form-control" placeholder="默认存储在网站file目录"/><font color="green">不填写则默认存储在网站file目录下，只能填写以服务器根目录/开始的绝对路径。</font></div>
		</div>
		</div>
		<div id="webdav_info" style="<?php echo $conf['storage']!='webdav'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">WebDAV服务地址</label>
		  <div class="col-sm-9"><input type="text" name="webdav_uri" value="<?php echo $conf['webdav_uri']; ?>" class="form-control" placeholder="例如：https://demo.webdav.com/remote.php/dav/files/用户名/"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">WebDAV账号</label>
		  <div class="col-sm-9"><input type="text" name="webdav_user" value="<?php echo $conf['webdav_user']; ?>" class="form-control" placeholder="WebDAV登录账号"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">WebDAV密码</label>
		  <div class="col-sm-9"><input type="password" name="webdav_pwd" value="<?php echo $conf['webdav_pwd']; ?>" class="form-control" placeholder="WebDAV登录密码"/></div>
		</div>
		</div>
		<div id="sae_info" style="<?php echo $conf['storage']!='sae'?'display:none;':null; ?>">
		<div class="form-group">
		  <label class="col-sm-3 control-label">SAE Storage名称</label>
		  <div class="col-sm-9"><input type="text" name="storagename" value="<?php echo $conf['storagename']; ?>" class="form-control"/></div>
		</div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">图片文件类型</label>
		  <div class="col-sm-9"><input type="text" name="type_image" value="<?php echo $conf['type_image']; ?>" class="form-control" placeholder="多个文件类型用|隔开"/><font color="green">在文件预览页面，以上文件类型将以图片的形式展示</font></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">音频文件类型</label>
		  <div class="col-sm-9"><input type="text" name="type_audio" value="<?php echo $conf['type_audio']; ?>" class="form-control" placeholder="多个文件类型用|隔开"/><font color="green">在文件预览页面，以上文件类型将以音频的形式展示</font></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">视频文件类型</label>
		  <div class="col-sm-9"><input type="text" name="type_video" value="<?php echo $conf['type_video']; ?>" class="form-control" placeholder="多个文件类型用|隔开"/><font color="green">在文件预览页面，以上文件类型将以视频的形式展示</font></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">禁止上传的文件类型</label>
		  <div class="col-sm-9"><input type="text" name="type_block" value="<?php echo $conf['type_block']; ?>" class="form-control" placeholder="多个文件类型用|隔开"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">文件名屏蔽关键词</label>
		  <div class="col-sm-9"><input type="text" name="name_block" value="<?php echo $conf['name_block']; ?>" class="form-control" placeholder="多个关键词用|隔开"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">每IP每天限制上传数量</label>
		  <div class="col-sm-9"><input type="text" name="upload_limit" value="<?php echo $conf['upload_limit']; ?>" class="form-control" placeholder="0或留空为不限制"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">视频文件需要审核</label>
		  <div class="col-sm-9"><select class="form-control" name="videoreview" default="<?php echo $conf['videoreview']?>"><option value="0">关闭</option><option value="1">开启</option></select></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-3 col-sm-9"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	</div>
	<script>
	$("select[name='storage']").change(function() {
		if($(this).val() == 'sae') {
			show_form("sae_info")
		} else if($(this).val() == 'oss') {
			show_form("oss_info")
		} else if($(this).val() == 'qcloud') {
			show_form("qcloud_info")
		} else if($(this).val() == 'obs') {
			show_form("obs_info")
		} else if($(this).val() == 'upyun') {
			show_form("upyun_info")
		} else if($(this).val() == 'webdav') {
			show_form("webdav_info")
		} else {
			show_form("local_info")
		}
	});
	function show_form(name) {
		$("#qcloud_info").hide();
		$("#oss_info").hide();
		$("#sae_info").hide();
		$("#local_info").hide();
		$("#upyun_info").hide();
		$("#obs_info").hide();
		$("#webdav_info").hide();
		$("#"+name).show();
	}
	</script>
	<?php
} elseif($mod=='green') {
	$green_label_porn = explode(',', $conf['green_label_porn']);
	$green_label_terrorism = explode(',', $conf['green_label_terrorism']);
	?>
	<div class="info-card">
	<div class="info-head">内容安全设置</div>
	<div class="info-body">
	  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
	    <div class="form-group">
		  <label class="col-sm-3 control-label">图片违规检测</label>
		  <div class="col-sm-9"><select class="form-control" name="green_check" default="<?php echo $conf['green_check']?>"><option value="0">关闭</option><option value="1">开启</option></select><font color="green">开启后下面的选项才会生效</font></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云AccessKey Id</label>
		  <div class="col-sm-9"><input type="text" name="aliyun_ak" value="<?php echo $conf['aliyun_ak']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">阿里云AccessKey Secret</label>
		  <div class="col-sm-9"><input type="text" name="aliyun_sk" value="<?php echo $conf['aliyun_sk']; ?>" class="form-control"/></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">图片检测接入区域</label>
		  <div class="col-sm-9"><select class="form-control" name="green_check_region" default="<?php echo $conf['green_check_region']?>"><option value="cn-beijing">华北2（北京）</option><option value="cn-shanghai">华东2（上海）</option><option value="cn-shenzhen">华南1（深圳）</option><option value="ap-southeast-1">新加坡</option><option value="us-west-1">美西</option></select><font color="green">你可以选择一个离本站服务器最近的以提升检测速度</font></div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">图片智能鉴黄</label>
		  <div class="col-sm-9"><select class="form-control" name="green_check_porn" default="<?php echo $conf['green_check_porn']?>"><option value="0">关闭</option><option value="1">开启</option></select></div>
		</div>
		<div class="form-group" id="green_check_porn_" style="<?php echo $conf['green_check_porn']!=1?'display:none;':null; ?>">
		  <label class="col-sm-3 control-label">图片智能鉴黄屏蔽类型</label>
		  <div class="col-sm-9">
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_porn[]" value="porn" <?php echo in_array('porn',$green_label_porn)?'checked':null;
		?>> 色情图片（porn）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_porn[]" value="sexy" <?php echo in_array('sexy',$green_label_porn)?'checked':null;
		?>> 性感图片（sexy）</label>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">图片暴恐涉政识别</label>
		  <div class="col-sm-9"><select class="form-control" name="green_check_terrorism" default="<?php echo $conf['green_check_terrorism']?>"><option value="0">关闭</option><option value="1">开启</option></select></div>
		</div>
		<div class="form-group" id="green_check_terrorism_" style="<?php echo $conf['green_check_terrorism']!=1?'display:none;':null; ?>">
		  <label class="col-sm-3 control-label">图片暴恐涉政识别屏蔽类型</label>
		  <div class="col-sm-9">
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="bloody" <?php echo in_array('bloody',$green_label_terrorism)?'checked':null;
		?>> 血腥（bloody）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="explosion" <?php echo in_array('explosion',$green_label_terrorism)?'checked':null;
		?>> 爆炸烟光（explosion）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="outfit" <?php echo in_array('outfit',$green_label_terrorism)?'checked':null;
		?>> 特殊装束（outfit）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="logo" <?php echo in_array('logo',$green_label_terrorism)?'checked':null;
		?>> 特殊标识（logo）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="weapon" <?php echo in_array('weapon',$green_label_terrorism)?'checked':null;
		?>> 武器（weapon）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="politics" <?php echo in_array('politics',$green_label_terrorism)?'checked':null;
		?>> 涉政（politics）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="violence" <?php echo in_array('violence',$green_label_terrorism)?'checked':null;
		?>> 打斗（violence）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="crowd" <?php echo in_array('crowd',$green_label_terrorism)?'checked':null;
		?>> 聚众（crowd）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="parade" <?php echo in_array('parade',$green_label_terrorism)?'checked':null;
		?>> 游行（parade）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="carcrash" <?php echo in_array('carcrash',$green_label_terrorism)?'checked':null;
		?>> 车祸现场（carcrash）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="flag" <?php echo in_array('flag',$green_label_terrorism)?'checked':null;
		?>> 旗帜（flag）</label>
		  <label class="checkbox-inline"><input type="checkbox" name="green_label_terrorism[]" value="location" <?php echo in_array('location',$green_label_terrorism)?'checked':null;
		?>> 地标（location）</label>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-sm-3 control-label">图片检测访问网址</label>
		  <div class="col-sm-9"><input type="text" name="apiurl" value="<?php echo $conf['apiurl']; ?>" class="form-control" placeholder="不填写则默认使用当前网址"/><font color="green">此处是图片检测的时候阿里云访问本站的网址，不填写则默认使用当前网址，如果填写必需以http://开头，以/结尾</font></div>
		</div>
		<div class="form-group">
		  <div class="col-sm-offset-3 col-sm-9"><fluent-button type="submit" appearance="accent">修改</fluent-button></div>
		</div>
	  </form>
	</div>
	<div class="info-foot">
	<i class="fa fa-info-circle"></i>
	阿里云图片违规检测：<a href="https://yundun.console.aliyun.com/?p=cts#/api/statistics" target="_blank" rel="noreferrer">点此进入</a>｜<a href="https://usercenter.console.aliyun.com/#/manage/ak" target="_blank" rel="noreferrer">获取AK/SK</a>
	</div>
	</div>
	<script>
	$("select[name='green_check_porn']").change(function() {
		if($(this).val() == 1) {
			$("#green_check_porn_").show();
		} else {
			$("#green_check_porn_").hide();
		}
	});
	$("select[name='green_check_terrorism']").change(function() {
		if($(this).val() == 1) {
			$("#green_check_terrorism_").show();
		} else {
			$("#green_check_terrorism_").hide();
		}
	});
	</script>
	<?php
}
?>
  </div>
<script src="../assets/js/admin-fluent.js"></script>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
function saveSetting(obj) {
	FluentUI.loading();
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=set',
		data : $(obj).serialize(),
		dataType : 'json',
		success : function(data) {
			FluentUI.closeLoading();
			if(data.code == 0) {
				FluentUI.success('设置保存成功！', function() {
					window.location.reload();
				});
			} else {
				FluentUI.error(data.msg);
			}
		},
		error:function(data) {
			FluentUI.closeLoading();
			FluentUI.toast('服务器错误', 'error');
			return false;
		}
	});
	return false;
}
</script>
<?php include './footer.php';?>
