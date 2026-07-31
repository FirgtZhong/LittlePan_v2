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
    $type=substr($url,strpos($url,".")+1);
}else{
    exit;
}

$row = $DB->getRow("SELECT * FROM `pre_file` WHERE `hash`=:hash limit 1", [':hash'=>$hash]);
if (!$row) {
    exit('File Not Found');
}

// ========== 第一步：先检查封禁状态（独立于存储检查） ==========
$block_status = intval($row['block']);
if($block_status >= 1){
    // 文件已封禁(block=1)或待审核(block=2)
    $type_image = explode('|',$conf['type_image']);
    $is_image_type = in_array($type, $type_image);
    
    if ($is_image_type) {
        // 图片类型：显示对应的封禁/审核占位图
        $block_img = $block_status == 2 ? 'block2.gif' : 'block.gif';
        header("Content-type: ".minetype('gif'));
        readfile(ROOT.'assets/img/'.$block_img);
        exit;
    } else {
        // 非图片类型（音频、视频）：返回 403 JSON
        @header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        $msg = $block_status == 2
            ? '文件待审核，暂时无法预览'
            : '文件已被封禁，无法预览';
        exit(json_encode(['code' => -1, 'msg' => $msg, 'block' => $block_status]));
    }
}

// ========== 第二步：正常文件，检查存储并输出 ==========
if ($stor->exists($row['hash']) && is_view($type)) {
    $DB->exec("UPDATE `pre_file` SET `lasttime`=NOW(),`count`=`count`+1 WHERE `id`='{$row['id']}'");
    header("Content-Length: {$row['size']}");
    header("Content-type: ".minetype($type));
    $stor->downfile($row['hash'], $row['size']);
}