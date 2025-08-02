<?php
include("./includes/common.php");

$title = '文件查看 - ' . $conf['title'];
$is_file = true;
include SYSTEM_ROOT . 'header.php';

$csrf_token = md5(mt_rand(0, 999) . time());
$_SESSION['csrf_token'] = $csrf_token;

$hash = isset($_GET['hash']) ? $_GET['hash'] : exit("<script language='javascript'>window.location.href='./';</script>");
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : null;
$row = $DB->getRow("SELECT * FROM pre_file WHERE hash=:hash", [':hash' => $hash]);
if (!$row) exit("<script language='javascript'>alert('文件不存在');window.location.href='./';</script>");
$name = $row['name'];
$type = $row['type'];

$downurl = 'down.php/' . $row['hash'] . '.' . $type;
if (!empty($row['pwd'])) $downurl .= '&' . $row['pwd'];
$viewurl = 'view.php/' . $row['hash'] . '.' . $type;

$downurl_all = $siteurl . $downurl;
$viewurl_all = $siteurl . $viewurl;

$thisurl = $siteurl . 'file.php?hash=' . $row['hash'];
if (!empty($pwd)) $thisurl .= '&pwd=' . $pwd;

if (isset($_SESSION['fileids']) && in_array($row['id'], $_SESSION['fileids']) && strtotime($row['addtime']) > strtotime("-7 days")) {
    $is_mine = true;
}

$type_image = explode('|', $conf['type_image']);
$type_audio = explode('|', $conf['type_audio']);
$type_video = explode('|', $conf['type_video']);

if (in_array($type, $type_image)) {
    $filetype = 1;
    $title = '<i class="fa fa-picture-o"></i> 图片查看器';
} elseif (in_array($type, $type_audio)) {
    $filetype = 2;
    $title = '<i class="fa fa-music"></i> 音乐播放器';
} elseif (in_array($type, $type_video)) {
    $filetype = 3;
    $title = '<i class="fa fa-video-camera"></i> 视频播放器';
} else {
    $filetype = 0;
    $title = '<i class="fa fa-file"></i> 文件查看';
}
?>

    <main id="main-container">
<div class="container mt-4">
    <div class="row">
        <?php if ($row['pwd'] && $row['pwd'] != $pwd) { ?>
            <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <title>请输入密码下载文件</title>
            <script type="text/javascript">
                var pwd = prompt("请输入密码", "");
                if (pwd) window.location.href = "./file.php?hash=<?php echo $row['hash'] ?>&pwd=" + pwd;
            </script>
            请刷新页面，或[ <a href="javascript:history.back();">返回上一页</a> ]
        <?php exit; } ?>

        <div class="col-sm-9">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><?php echo $title ?></h3>
                </div>
                 <div class="block-content text-center">
                    <?php
                    if ($filetype == 1) {
                        echo '<div class="image_view"><a href="' . $viewurl . '" title="点击查看原图"><img alt="loading" src="' . $viewurl . '" class="img-fluid"  width="35%"></a></div>';
                    } elseif ($filetype == 2) {
                        echo '<audio controls src="' . $viewurl . '" class="audio-player"></audio>';
                    } elseif ($filetype == 3) {
                        echo $row['block'] == 0
                            ? '<video src="' . $viewurl . '" controls class="video-player w-100"></video>'
                            : '<p>视频文件需审核通过后才能在线播放和下载，请等待审核通过！</p>';
                    } else {
                        echo '<a href="' . $downurl . '" class="btn btn-primary"><i class="fa fa-download"></i> 下载文件</a>';
                    }
                    ?>
                </div>
            </div>

            <div class="block block-rounded block-bordered">
                <div class="block-content p-0">
                    <ul class="nav nav-tabs nav-tabs-alt" style="margin-bottom: 15px;">
                        <li class="nav-item"><a class="nav-link active" href="#link" data-toggle="tab"><i class="fa fa-link"></i> 文件外链</a></li>
                        <li class="nav-item"><a class="nav-link" href="#info" data-toggle="tab"><i class="fa fa-info-circle"></i> 文件详情</a></li>
                        <?php if ($is_mine) { ?><li class="nav-item"><a class="nav-link" href="#manager" data-toggle="tab"><i class="fa fa-cog"></i> 管理</a></li><?php } ?>
                    </ul>

                    <div class="tab-content p-3">
                        <div class="tab-pane fade show active" id="link">
                            <div class="form-group row">
                                <label for="link1" class="col-md-2 col-form-label">查看链接：</label>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="link1" readonly value="<?php echo $viewurl_all ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $viewurl_all ?>')">复制</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="link2" class="col-md-2 col-form-label">下载链接：</label>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="link2" readonly value="<?php echo $downurl_all ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $downurl_all ?>')">复制</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="info">
                            <table class="table table-bordered">
                                <tr><th>上传者IP</th><td><?php echo preg_replace('/\d+$/', '*', $row['ip']); ?></td></tr>
                                <tr><th>上传时间</th><td><?php echo $row['addtime']; ?></td></tr>
                                <tr><th>下载次数</th><td><?php echo $row['count']; ?></td></tr>
                                <tr><th>文件大小</th><td><?php echo size_format($row['size']); ?></td></tr>
                            </table>
                        </div>

                        <?php if ($is_mine) { ?>
                        <div class="tab-pane fade" id="manager">
                            <button onclick="deleteConfirm('<?php echo $row['hash']; ?>')" class="btn btn-danger">删除文件</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default"><h3 class="block-title"><i class="fa fa-exclamation-circle"></i> 提示</h3></div>
                <div class="block-content"><?php echo $conf['gg_file'] ?></div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default"><h3 class="block-title"><i class="fa fa-qrcode"></i> 当前页面二维码</h3></div>
                <div class="block-content text-center">
                    <img alt="二维码" src="//api.2dcode.biz/v1/create-qr-code?size=180x180&margin=10&data=<?php echo urlencode($thisurl); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert("复制成功！");
        });
    }

    function deleteConfirm(hash) {
    var csrf_token = "<?php echo $csrf_token; ?>";
    if (confirm("确定要删除此文件吗？")) {
        fetch("ajax.php?act=deleteFile", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `hash=${encodeURIComponent(hash)}&csrf_token=${encodeURIComponent(csrf_token)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.code === 0) {
                alert("删除成功");
                window.location.href = "./";
            } else {
                alert(data.msg);
            }
        })
        .catch(error => {
            alert("删除失败，服务器错误");
        });
    }
}

</script>
</main>
<?php include SYSTEM_ROOT . 'footer.php'; ?>
