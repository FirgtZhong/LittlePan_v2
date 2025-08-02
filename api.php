<?php
$nosession = true;
$nosecu = true;
include("./includes/common.php");

// 添加CORS头 - 允许所有域名跨域访问（生产环境建议限制为特定域名）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 处理预检请求（Preflight Request）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function showresult($arr, $format='json'){
    $format = isset($_POST['format'])?$_POST['format']:'json';
    if($format == 'json'){
        @header('Content-Type: application/json; charset=UTF-8');
        exit(json_encode($arr));
    }elseif($format == 'jsonp'){
        $callback = isset($_POST['callback'])?$_POST['callback']:'callback';
        @header('Content-Type: application/javascript; charset=UTF-8');
        exit($callback.'('.json_encode($arr).')');
    }else{
        @header('Content-Type: text/html; charset=UTF-8');
        if($arr['code']==0){
            $backurl = isset($_POST['backurl'])?$_POST['backurl']:$_SERVER['HTTP_REFERER'];
echo '<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width">
<title>文件操作结果</title>
</head>
<body>
<form action="'.$backurl.'" method="post">
<input name="result" type="hidden" value="'.$arr['msg'].'" />
<input name="submit" type="submit" value="完成" />
</form>
</body></html>';
exit;
        }else{
            sysmsg($arr['msg']);
        }
    }
}

// 设置安全头
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

if(!$conf['api_open'])showresult(['code'=>-4, 'msg'=>'当前站点未开启上传API']);

// 仅当存在Referer头时才进行验证
if(!empty($conf['api_referer']) && !empty($_SERVER['HTTP_REFERER'])){
    $referers = explode('|',$conf['api_referer']);
    $url_arr = parse_url($_SERVER['HTTP_REFERER']);
    
    // 验证Referer域名是否在白名单中
    if(!isset($url_arr['host']) || !in_array($url_arr['host'], $referers)) {
        showresult(['code'=>-4, 'msg'=>'来源地址不正确']);
    }
}

// 处理DELETE请求 - 文件删除功能
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // 解析DELETE请求参数
    parse_str(file_get_contents("php://input"), $delete_data);
    
    // 验证必要参数
    if (!isset($delete_data['hash']) || empty($delete_data['hash'])) {
        showresult(['code'=>-1, 'msg'=>'缺少文件哈希参数']);
    }
    
    // 查询文件信息
    $hash = $delete_data['hash'];
    $file = $DB->getRow("SELECT * FROM pre_file WHERE hash = :hash", [':hash' => $hash]);
    
    if (!$file) {
        showresult(['code'=>-1, 'msg'=>'文件不存在']);
    }
    
    // 验证文件密码（如果文件设置了密码）
    if (!empty($file['pwd'])) {
        if (!isset($delete_data['file_pwd']) || $delete_data['file_pwd'] !== $file['pwd']) {
            showresult(['code'=>-1, 'msg'=>'文件密码错误']);
        }
    }

    
    // 执行文件删除
    try {
        // 开始事务
        $DB->beginTransaction();
        
        // 从存储系统删除文件
        $delete_result = $stor->delete($hash, $file['type']);
        
        if (!$delete_result) {
            throw new Exception('存储系统删除失败');
        }
        
        // 从数据库删除记录
        $delete_db = $DB->exec("DELETE FROM pre_file WHERE hash = :hash", [':hash' => $hash]);
        
        if (!$delete_db) {
            throw new Exception('数据库删除失败');
        }
        
        // 提交事务
        $DB->commit();
        
        showresult(['code'=>0, 'msg'=>'文件删除成功']);
        
    } catch (Exception $e) {
        // 回滚事务
        $DB->rollBack();
        showresult(['code'=>-1, 'msg'=>'文件删除失败: ' . $e->getMessage()]);
    }
}

// 处理POST请求 - 原有的文件上传功能
if(!isset($_FILES['file']))showresult(['code'=>-1, 'msg'=>'请选择文件']);
$name=trim(htmlspecialchars($_FILES['file']['name']));
$size=intval($_FILES['file']['size']);
$hide = $_POST['show']==1?0:1;
$ispwd = intval($_POST['ispwd']);
$pwd = $ispwd==1?trim(htmlspecialchars($_POST['pwd'])):null;
$name = str_replace(['/','\\',':','*','"','<','>','|','?'],'',$name);
if(empty($name))showresult(['code'=>-1, 'msg'=>'文件名不能为空']);
if($ispwd==1 && !empty($pwd)){
    if (!preg_match('/^[a-zA-Z0-9]+$/', $pwd)) {
        showresult(['code'=>-1, 'msg'=>'文件密码只能为字母和数字']);
    }
}
$extension=explode('.',$name);
if (($length = count($extension)) > 1) {
    $ext = strtolower($extension[$length - 1]);
}
if(strlen($ext)>6)$ext='';
if($conf['type_block']){
    $type_block = explode('|',$conf['type_block']);
    if(in_array($ext,$type_block)){
        showresult(['code'=>-1, 'msg'=>'文件上传失败', 'error'=>'block']);
    }
}
if($conf['name_block']){
    $name_block = explode('|',$conf['name_block']);
    foreach($name_block as $row){
        if(strpos($name,$row)!==false){
            showresult(['code'=>-1, 'msg'=>'文件上传失败', 'error'=>'block']);
        }
    }
}
$hash = md5_file($_FILES['file']['tmp_name']);
$row = $DB->getRow("SELECT * FROM pre_file WHERE hash=:hash", [':hash'=>$hash]);
if($row){
    unset($_SESSION['csrf_token']);
    $downurl = $siteurl.'down.php/'.$row['hash'].'.'.$row['type'];
    if(!empty($row['pwd']))$downurl .= '&'.$row['pwd'];
    $result = ['code'=>0, 'msg'=>'本站已存在该文件', 'exists'=>1, 'hash'=>$hash, 'name'=>$name, 'size'=>$size, 'type'=>$ext, 'id'=>$row['id'], 'downurl'=>$downurl];
    if(is_view($row['type']))$result['viewurl'] = $siteurl.'view.php/'.$hash.'.'.$row['type'];
    showresult($result);
}
$result = $stor->upload($hash, $_FILES['file']['tmp_name']);
if(!$result)showresult(['code'=>-1, 'msg'=>'文件上传失败', 'error'=>'stor']);
$sds = $DB->exec("INSERT INTO `pre_file` (`name`,`type`,`size`,`hash`,`addtime`,`ip`,`hide`,`pwd`) values (:name,:type,:size,:hash,NOW(),:ip,:hide,:pwd)", [':name'=>$name, ':type'=>$ext, ':size'=>$size, ':hash'=>$hash, ':ip'=>$clientip, ':hide'=>$hide, ':pwd'=>$pwd]);
if(!$sds)showresult(['code'=>-1, 'msg'=>'上传失败'.$DB->error(), 'error'=>'database']);
$id = $DB->lastInsertId();

$type_image = explode('|',$conf['type_image']);
$type_video = explode('|',$conf['type_video']);
if($conf['green_check']==1 && in_array($ext,$type_image)){
    $apiurl = $conf['apiurl']?$conf['apiurl']:$siteurl;
    $fileurl = $apiurl.'view.php/'.$hash.'.'.$ext;
    if(checkImage($fileurl)==true){
        $DB->exec("UPDATE `pre_file` SET `block`=1 WHERE `id`=:id LIMIT 1", [':id' => $id]);
    }
}
if($conf['videoreview']==1 && in_array($ext,$type_video)){
    $DB->exec("UPDATE `pre_file` SET `block`=2 WHERE `id`=:id LIMIT 1", [':id' => $id]);
}

$downurl = $siteurl.'down.php/'.$hash.'.'.$ext;
if(!empty($pwd))$downurl .= '&'.$pwd;
$result = ['code'=>0, 'msg'=>'文件上传成功！', 'exists'=>0, 'hash'=>$hash, 'name'=>$name, 'size'=>$size, 'type'=>$ext, 'id'=>$id, 'downurl'=>$downurl];
if(is_view($ext))$result['viewurl'] = $siteurl.'view.php/'.$hash.'.'.$ext;
showresult($result);