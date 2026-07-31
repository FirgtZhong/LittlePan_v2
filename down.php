<?php
$nosession=true;
$nosecu=true;
include("./includes/common.php");

// 强制禁止缓存，确保封禁状态能实时生效
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

$urlarr=explode('/',$_SERVER['PATH_INFO']);
if (($length = count($urlarr)) > 1) {
$url = $urlarr[$length-1];
}
$extension=explode('&',$url);
if (($length = count($extension)) > 1) {
$pwd = $extension[$length-1];
$url = $extension[0];
}

if(strpos($url,".")){
    $hash=substr($url,0,strpos($url,"."));
}else{
    $hash=$url;
}

$row = $DB->getRow("SELECT * FROM `pre_file` WHERE `hash`=:hash limit 1", [':hash'=>$hash]);
if(!$row)exit('404 Not Found');
// 修正：将 block 转为整数，处理 NULL 值的情况
$block_status = intval($row['block']);
if($block_status >= 1){
    if($block_status == 2){
        exit('文件待审核，暂时无法下载！');
    }else{
        exit('文件已被封禁，无法下载！');
    }
}

if($row['pwd']!=null && $row['pwd']!=$pwd){ ?>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>请输入密码下载文件</title>
    <script type="text/javascript">
    var pwd=prompt("请输入密码","")
    if (pwd!=null && pwd!="")
    {
        window.location.href='<?php echo $siteurl.'down.php/'.$hash?>&'+pwd
    }
    </script>
    请刷新页面，或[ <a href="javascript:history.back();">返回上一页</a> ]
<?php
    exit;
}

// ========== 新增：WebDAV 存在性检查的错误细化 ==========
// 检查文件是否存在，若为WebDAV存储且不存在，返回具体错误
if($stor->exists($hash))
{
    header("Content-Description: File Transfer");
    header("Content-Type:application/force-download");
    header("Content-Length: {$row['size']}");
    header("Content-Disposition:attachment; filename={$row['name']}");
    // 注意：不再设置长缓存，保持开头的禁止缓存设置
    
    $DB->exec("UPDATE `pre_file` SET `lasttime`=NOW(),`count`=`count`+1 WHERE `id`='{$row['id']}'");

    // ========== 新增：WebDAV 下载失败的异常处理 ==========
    // 执行下载并捕获失败状态
    $downloadResult = $stor->downfile($hash, $row['size']);
    // 若下载失败且为WebDAV存储，返回具体错误信息
    if(!$downloadResult && $conf['storage'] == 'webdav'){
        exit('WebDAV文件下载失败：'.$stor->errmsg());
    }
    // ====================================================
}
else{
    // 若为WebDAV存储且文件不存在，返回具体错误
    if($conf['storage'] == 'webdav'){
        exit('WebDAV文件不存在：'.$stor->errmsg());
    }else{
        exit('File Not Found');
    }
}
?>