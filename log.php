<?php
include("./includes/common.php");
$title = "更新日志 - " . $conf['title'];
include SYSTEM_ROOT.'header.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");

?>

    <main id="main-container">
        <div class="bg-body-extra-light">
            <center>
                <p><h3>
<ul>
<li>2025.7.20<br>
开通API功能</li>
<li>2025.7.21<br>
为API添加CORS响应头，完成<a href="https://blog.firgt.cn/2025/07/23/firgt-cloud-api-doc/">API文档</a></li>
<li>2025.7.22<br>
为API添加删除功能（如果同IP上传可删除，以及设置密码的文件，使用API进行删除），更新API文档</li>
<li>2025.7.23<br>
主页更新，使用必应壁纸</li>
</ul>
                </h3></p>
            </center>
        </div>
    </main>
                <?php include SYSTEM_ROOT . 'footer.php'; ?>
            
                <script src="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.js"></script>
                <link href="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.css" rel="stylesheet">
              
            </div>
        </div>
    
