<?php
include("./includes/common.php");
$title = "关于 - " . $conf['title'];
include SYSTEM_ROOT.'header.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");

?>

    <main id="main-container">
        <div class="bg-body-extra-light">
            <center><h3>
                <p>这个网站自从今年4月份就发布了，当时用的是白猫云，后来这个白猫云跑路了<br>之前的即时备份找到了，在Github上找了一个外链网盘源码</p>
                <p>不会跑路，不过因为只有5GB的存储，所以设置了50MB上传限制<br>平时拿来当个图床也不错，如果可以的话，请我喝杯奶茶呗～</p>
                <p><img src="//firgt.eu.org/images/wechatpay.png" height="180" width="180"></p>
            </h3></center>
        </div>
    </main>
                <?php include SYSTEM_ROOT . 'footer.php'; ?>
            
                <script src="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.js"></script>
                <link href="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.css" rel="stylesheet">
              
            </div>
        </div>
    
