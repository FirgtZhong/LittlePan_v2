<?php
include("../includes/common.php");
$title='文件管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$sel_type = trim($_GET['type'] ?? '');
$sel_kw = trim($_GET['kw'] ?? '');
$type_opts = [['1','文件名'],['2','文件Hash'],['3','文件格式'],['4','上传者IP']];
?>

<!-- 编辑文件弹窗 -->
<fluent-dialog id="modal-store" modal class="flu-dialog flu-dialog-wide" hidden>
  <div class="flu-dialog-title">文件信息修改</div>
  <div class="flu-dialog-body">
    <form id="form-store" class="form-grid">
      <input type="hidden" name="action" id="action"/>
      <input type="hidden" name="id" id="id"/>
      <label>文件名称</label><fluent-text-field name="name" id="name"></fluent-text-field>
      <label>文件类型</label><fluent-text-field name="type" id="type"></fluent-text-field>
      <label>文件大小</label><fluent-text-field id="size" disabled></fluent-text-field>
      <label>文件MD5</label><fluent-text-field id="hash" disabled></fluent-text-field>
      <label>是否隐藏</label>
      <fluent-select id="hide" name="hide">
        <fluent-option value="0">否</fluent-option>
        <fluent-option value="1">是</fluent-option>
      </fluent-select>
      <label>开启密码</label>
      <fluent-select id="ispwd" name="ispwd" onchange="change_ispwd(this)">
        <fluent-option value="0">否</fluent-option>
        <fluent-option value="1">是</fluent-option>
      </fluent-select>
      <div id="pwd_frame">
        <label>下载密码</label><fluent-text-field name="pwd" id="pwd"></fluent-text-field>
      </div>
    </form>
  </div>
  <div class="flu-dialog-footer">
    <fluent-button appearance="stealth" onclick="closeEdit()">关闭</fluent-button>
    <fluent-button appearance="accent" onclick="save()">保存</fluent-button>
  </div>
</fluent-dialog>

<div class="toolbar">
  <form onsubmit="return searchFile()" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
    <fluent-select id="search-type" name="type" style="min-width:130px;">
      <?php foreach($type_opts as $o){ $s = ($o[0]===$sel_type)?' selected':''; ?>
      <fluent-option value="<?=$o[0]?>"<?=$s?>><?=$o[1]?></fluent-option>
      <?php }?>
    </fluent-select>
    <fluent-text-field id="search-kw" name="kw" placeholder="搜索内容" value="<?=htmlspecialchars($sel_kw)?>" style="min-width:220px;"></fluent-text-field>
    <fluent-button type="submit" appearance="accent"><i class="fa fa-search"></i> 搜索</fluent-button>
    <fluent-button type="button" appearance="stealth" onclick="searchClear()"><i class="fa fa-repeat"></i> 重置</fluent-button>
  </form>
  <div class="spacer"></div>
  <fluent-select id="dstatus" style="min-width:140px;">
    <fluent-option value="0">全部文件</fluent-option>
    <fluent-option value="1">正常文件</fluent-option>
    <fluent-option value="2">已屏蔽文件</fluent-option>
    <fluent-option value="3">待审核文件</fluent-option>
  </fluent-select>
</div>

<div id="listTable"></div>

<script src="../assets/js/admin-fluent.js"></script>
<script>
var dstatus = 0;

function listTable(query){
	var url = window.document.location.href.toString();
	var queryString = url.split("?")[1];
	query = query || queryString;
	if(query == 'start' || query == undefined){
		query = '';
		history.replaceState({}, null, './file.php');
	}else if(query != undefined){
		history.replaceState({}, null, './file.php?'+query);
	}
	FluentUI.loading();
	$.ajax({
		type : 'GET',
		url : 'file-table.php?dstatus='+dstatus+'&'+query,
		dataType : 'html',
		cache : false,
		success : function(data) {
			FluentUI.closeLoading();
			$("#listTable").html(data);
		},
		error:function(data){
			FluentUI.closeLoading();
			FluentUI.toast('服务器错误', 'error');
			return false;
		}
	});
}

function change_ispwd(obj){
	if($(obj).val()==1){
		$('#pwd_frame').removeClass('is-hidden');
	}else{
		$('#pwd_frame').addClass('is-hidden');
	}
}

function searchFile(){
	var type=$('#search-type').val();
	var kw=$('#search-kw').val();
	if(kw==''){
		listTable();
	}else{
		listTable('type='+type+'&kw='+encodeURIComponent(kw));
	}
	return false;
}

function searchClear(){
	$('#search-kw').val('');
	listTable('start');
}

function setBlock(id,status){
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=setBlock&id='+id+'&status='+status,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				FluentUI.toast(data.msg, 'success');
				listTable();
			}else{
				FluentUI.toast(data.msg, 'error');
			}
		},
		error:function(data){
			FluentUI.toast('服务器错误，请检查网络连接', 'error');
			return false;
		}
	});
}

function editframe(id){
	FluentUI.loading();
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=getFileInfo&id='+id,
		dataType : 'json',
		success : function(data) {
			FluentUI.closeLoading();
			if(data.code == 0){
				$('#action').val("edit");
				$('#id').val(data.id);
				$('#name').val(data.name);
				$('#type').val(data.type);
				$('#size').val(data.size2+" ("+data.size+" 字节)");
				$('#hash').val(data.hash);
				$('#hide').val(data.hide);
				if(data.pwd==null||data.pwd==""){
					$('#ispwd').val(0);
					$('#pwd').val("");
					$('#pwd_frame').addClass('is-hidden');
				}else{
					$('#ispwd').val(1);
					$('#pwd').val(data.pwd);
					$('#pwd_frame').removeClass('is-hidden');
				}
				var dlg = document.getElementById('modal-store');
				try{ dlg.show(); }catch(e){ dlg.hidden=false; }
			}else{
				FluentUI.error(data.msg);
			}
		},
		error:function(data){
			FluentUI.closeLoading();
			FluentUI.toast('服务器错误', 'error');
			return false;
		}
	});
}

function closeEdit(){
	var dlg = document.getElementById('modal-store');
	try{ dlg.hide(); }catch(e){ dlg.hidden=true; }
}

function save(){
	if($('#name').val()==''){
		FluentUI.alert('请确保各项不能为空！');
		return false;
	}
	FluentUI.loading();
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=saveFileInfo',
		data : {
			id: $('#id').val(),
			name: $('#name').val(),
			type: $('#type').val(),
			hide: $('#hide').val(),
			ispwd: $('#ispwd').val(),
			pwd: $('#pwd').val()
		},
		dataType : 'json',
		success : function(data) {
			FluentUI.closeLoading();
			if(data.code == 0){
				closeEdit();
				FluentUI.success(data.msg, function(){ listTable(); });
			}else{
				FluentUI.error(data.msg);
			}
		},
		error:function(data){
			FluentUI.closeLoading();
			FluentUI.toast('服务器错误', 'error');
			return false;
		}
	});
}

function delFile(id){
	FluentUI.confirm('你确定要删除此文件吗？', function(){
		FluentUI.loading();
		$.ajax({
			type : 'GET',
			url : 'ajax.php?act=delFile&id='+id,
			dataType : 'json',
			success : function(data) {
				FluentUI.closeLoading();
				if(data.code == 0){
					FluentUI.success('删除成功', function(){ listTable(); });
				}else{
					FluentUI.error(data.msg);
				}
			},
			error:function(data){
				FluentUI.closeLoading();
				FluentUI.toast('服务器错误', 'error');
				return false;
			}
		});
	});
}

function operation(){
	FluentUI.confirm('你确定要批量删除文件吗？', function(){
		FluentUI.loading();
		$.ajax({
			type : 'POST',
			url : 'ajax.php?act=operation',
			data : $('#form1').serialize(),
			dataType : 'json',
			success : function(data) {
				FluentUI.closeLoading();
				if(data.code == 0){
					FluentUI.success(data.msg, function(){ listTable(); });
				}else{
					FluentUI.error(data.msg);
				}
			},
			error:function(data){
				FluentUI.closeLoading();
				FluentUI.toast('请求超时', 'error');
				listTable();
			}
		});
	});
}

function showfile(id){
	FluentUI.dialog({
		title: '文件预览',
		wide: true,
		body: '<iframe src="./file-view.php?id='+id+'" style="width:100%;height:70vh;border:0;border-radius:4px;"></iframe>',
		okText: false,
		cancelText: '关闭'
	});
}

function toggleAll(master){
	$('.row-check').prop('checked', master.checked);
}
function syncMaster(){
	var all = $('.row-check');
	var checked = all.filter(':checked').length;
	$('#chkAll1').prop('checked', all.length>0 && checked===all.length);
}

$(document).ready(function(){
	$('#dstatus').change(function () {
		dstatus = $(this).val();
		listTable();
	});
	listTable();
});
</script>
<?php include './footer.php';?>
