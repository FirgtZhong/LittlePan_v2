<?php
/**
 * 文件预览
**/
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id'])?intval($_GET['id']):exit();
$row = $DB->getRow("SELECT * FROM pre_file WHERE id=:id", [':id'=>$id]);
if(!$row)exit();
$name = $row['name'];
$type = $row['type'];
$viewurl = '/view.php/'.$row['hash'].'.'.$type;

$type_image = explode('|',$conf['type_image']);
$type_audio = explode('|',$conf['type_audio']);
$type_video = explode('|',$conf['type_video']);

if(in_array($type, $type_image)){
  $filetype = 1;
}elseif(in_array($type, $type_audio)){
  $filetype = 2;
}elseif(in_array($type, $type_video)){
  $filetype = 3;
}else{
  $filetype = 0;
}

@header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo $title ?></title>
  <link href="//cdn.staticfile.org/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="//cdn.staticfile.org/aplayer/1.10.1/APlayer.min.css">
  <link href="../assets/css/style.css" rel="stylesheet"/>
  <script src="//cdn.staticfile.org/jquery/2.1.4/jquery.min.js"></script>
  <style>
    html, body { margin: 0; padding: 0; background: #000; width: 100%; height: 100%; overflow: hidden; }
    .panel-body { padding: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .image_view { max-width: 100%; max-height: 100%; min-height: 0; object-fit: contain; height: auto; }
    .view { width: 100%; max-width: 960px; padding: 16px; box-sizing: border-box; }
    .videosamplex { width: 100%; height: 100%; position: relative; background: #000; display: flex; align-items: center; justify-content: center; }
    #videoplayer { width: 100%; height: 100%; display: block; object-fit: contain; background: #000; }
    .video-fallback {
      color: #f3f4f6; text-align: center; padding: 24px;
      font-size: 14px; line-height: 1.7;
    }
    .video-fallback a { color: #60a5fa; text-decoration: underline; }
    /* APlayer 容器适配：浅底版本 */
    #aplayer { background: #fff; }
  </style>
</head>
<body>
<div class="panel-body" align="center">
<?php
if($filetype==1){
  echo '<a href="'.$viewurl.'" title="点击查看原图" target="_blank" rel="noopener noreferrer"><img alt="loading" src="'.$viewurl.'" class="image_view"></a>';
}elseif($filetype==2){
  echo '<div class="view"><div id="aplayer"></div></div>';
}elseif($filetype==3){
  // 使用 HTML5 video 标签：浏览器原生支持的格式（mp4/webm/ogg）直接播放，
  // 不支持的格式（mpg/mpeg/avi 等）由浏览器自行嗅探；
  // 全部失败时显示下载/新窗口打开链接作为 fallback。
  echo '<div class="videosamplex"><video id="videoplayer" controls autoplay preload="auto" playsinline>'.
       '<source src="'.$viewurl.'" type="video/mp4">'.
       '<source src="'.$viewurl.'" type="video/webm">'.
       '<source src="'.$viewurl.'" type="video/ogg">'.
       '<source src="'.$viewurl.'" type="video/quicktime">'.
       '<source src="'.$viewurl.'">'. // 不指定 type，让浏览器嗅探
       '<p class="video-fallback">您的浏览器无法直接播放此格式，'.
       '<a href="'.$viewurl.'" target="_blank" rel="noopener noreferrer">点击下载或新窗口打开</a></p>'.
       '</video></div>';
}else{
  exit;
}
?>
</div>
<?php if($filetype==2){?>
<script type="text/javascript" src="//cdn.staticfile.org/aplayer/1.10.1/APlayer.min.js"></script>
<script type="text/javascript">
var ap1 = new APlayer({
    element: document.getElementById('aplayer'),
    narrow: false,
    autoplay: false,
    showlrc: false,
    mutex: true,
    theme: '#b2dae6',
    music: {
        title: '<?php echo $name?>',
        author: '',
        url: '<?php echo $viewurl?>',
    }
});
</script>
<?php }?>
</body>
</html>
