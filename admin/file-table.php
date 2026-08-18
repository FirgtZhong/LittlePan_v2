<?php
/**
 * 文件管理 - 表格部分（AJAX 返回）
**/
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

function display_status($status, $id){
	if($status == 2){
		return '<a href="javascript:setBlock('.$id.',0)" class="status-tag status-pending">待审</a>';
	}elseif($status == 1){
		return '<a href="javascript:setBlock('.$id.',0)" class="status-tag status-block">封禁</a>';
	}else{
		return '<a href="javascript:setBlock('.$id.',1)" class="status-tag status-normal">正常</a>';
	}
}

$sqls = '';
$links = '';
if (isset($_GET['dstatus']) && $_GET['dstatus']>0) {
	if($_GET['dstatus']==3){
		$sqls = " AND `block`=2";
		$links = "&dstatus=".$_GET['dstatus'];
	}elseif($_GET['dstatus']==2){
		$sqls = " AND `block`=1";
		$links = "&dstatus=".$_GET['dstatus'];
	}elseif($_GET['dstatus']==1){
		$sqls = " AND `block`=0";
		$links = "&dstatus=".$_GET['dstatus'];
	}
}

if(isset($_GET['kw']) && !empty($_GET['kw'])) {
	$type = intval($_GET['type']);
	$kw = trim(daddslashes($_GET['kw']));
	if($type == 1){
		$sql=" `name` LIKE '%{$kw}%'";
	}elseif($type == 2){
		$sql=" `hash`='{$kw}'";
	}elseif($type == 3){
		$sql=" `type`='{$kw}'";
	}elseif($type == 4){
		$sql=" `ip`='{$kw}'";
	}
	$sql.=$sqls;
	$numrows=$DB->getColumn("SELECT count(*) from pre_file WHERE{$sql}");
	$con='包含 '.htmlspecialchars($kw).' 的共有 <b>'.$numrows.'</b> 个记录';
	$link='&type='.$type.'&kw='.urlencode($kw).$links;
}else{
	$sql=" 1".$sqls;
	$numrows=$DB->getColumn("SELECT count(*) from pre_file WHERE{$sql}");
	$con='系统共有 <b>'.$numrows.'</b> 条记录';
	$link=$links;
}
?>
<div class="toolbar-count" style="margin-bottom:10px;color:#6b7280;font-size:13px;"><?=$con?></div>
<form name="form1" id="form1">
	<table class="file-grid" id="grid">
		<thead>
			<tr>
				<th scope="col" style="width:42px;">ID</th>
				<th scope="col">文件名</th>
				<th scope="col">文件大小</th>
				<th scope="col">格式</th>
				<th scope="col">上传日期/上次下载</th>
				<th scope="col">上传IP/下载量</th>
				<th scope="col">状态</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
<?php
$pagesize=15;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
if($page<1)$page=1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM pre_file WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
	$pwd_ext1='';$pwd_ext2='';
	if(!empty($res['pwd'])){
		$pwd_ext1='&'.$res['pwd'];
		$pwd_ext2='&pwd='.$res['pwd'];
	}
	$fileurl = '/down.php/'.$res['hash'].'.'.($res['type']?$res['type']:'file').$pwd_ext1;
	$viewurl = '/file.php?hash='.$res['hash'].$pwd_ext2;
	$imgurl = '/view.php/'.$res['hash'].'.'.$res['type']; // 图片原始地址（供图片浏览器）
	$dlurl = $fileurl; // 下载链接（图片预览时复用）
	if (is_image($res['type'])) {
		// 用 data-* 属性传递参数，避免文件名含引号/特殊字符破坏 onclick JS 字符串
		$preview = ' [<a href="javascript:void(0)" class="img-preview" '.
			'data-url="'.htmlspecialchars($imgurl, ENT_QUOTES).'" '.
			'data-name="'.htmlspecialchars($res['name'], ENT_QUOTES).'" '.
			'data-dl="'.htmlspecialchars($dlurl, ENT_QUOTES).'" '.
			'onclick="showImageFrom(this)">预览</a>]';
	} elseif (is_view($res['type'])) {
		$preview = ' [<a href="javascript:showfile('.$res['id'].')">预览</a>]';
	} else {
		$preview = '';
	}
	echo '<tr>';
	echo '<td><div class="grid-id-cell"><input type="checkbox" class="row-check" name="checkbox[]" value="'.$res['id'].'" onclick="syncMaster()"><b>'.$res['id'].'</b></div></td>';
	echo '<td><a href="'.$fileurl.'" title="点击下载"><i class="fa '.type_to_icon($res['type']).' fa-fw"></i>'.htmlspecialchars($res['name']).'</a>'.$preview.'</td>';
	echo '<td>'.size_format($res['size']).'</td>';
	echo '<td><font color="blue">'.($res['type']?htmlspecialchars($res['type']):'未知').'</font></td>';
	echo '<td>'.htmlspecialchars($res['addtime']).'<br/>'.htmlspecialchars($res['lasttime']).'</td>';
	echo '<td><a href="/ip.php?ip='.urlencode($res['ip']).'" target="_blank" rel="noreferrer">'.htmlspecialchars($res['ip']).'</a><br/><b>'.intval($res['count']).'</b></td>';
	echo '<td>'.display_status($res['block'], $res['id']).'</td>';
	echo '<td class="grid-actions">';
	echo '<a href="javascript:editframe('.$res['id'].')" class="grid-btn btn-edit">编辑</a>';
	echo '<a href="'.$viewurl.'" class="grid-btn btn-view" target="_blank">查看</a>';
	echo '<a href="javascript:delFile('.$res['id'].')" class="grid-btn btn-del">删除</a>';
	echo '</td>';
	echo '</tr>';
}
?>
		</tbody>
	</table>
	<div class="batch-bar">
		<label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
			<input type="checkbox" id="chkAll1" onclick="toggleAll(this)"> 全选
		</label>
		<fluent-button appearance="accent" onclick="operation()"><i class="fa fa-trash"></i> 删除选中</fluent-button>
	</div>
</form>
<?php
echo '<ul class="pagination">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1){
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$first.$link.'\')">首页</a></li>';
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$prev.$link.'\')">&laquo;</a></li>';
} else {
	echo '<li class="disabled"><span>首页</span></li>';
	echo '<li class="disabled"><span>&laquo;</span></li>';
}
$start=$page-10>1?$page-10:1;
$end=$page+10<$pages?$page+10:$pages;
for ($i=$start;$i<$page;$i++)
echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$i.$link.'\')">'.$i.'</a></li>';
echo '<li class="active"><span>'.$page.'</span></li>';
for ($i=$page+1;$i<=$end;$i++)
echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$i.$link.'\')">'.$i.'</a></li>';
if ($page<$pages){
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$next.$link.'\')">&raquo;</a></li>';
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page='.$last.$link.'\')">尾页</a></li>';
} else {
	echo '<li class="disabled"><span>&raquo;</span></li>';
	echo '<li class="disabled"><span>尾页</span></li>';
}
echo '</ul>';
