<?php
include("./includes/common.php");
$title = "关于 - " . $conf['title'];
include SYSTEM_ROOT.'header.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
?>

<main id="main-container">"<div class="bg-body-extra-light">
<!-- 以下是可编辑区域 -->
<!-- EDITABLE_START -->
<h2 style="text-align:center;">LittlePan介绍</h2><p style="text-align:center;">这是一个外链网盘管理系统，根据<a href="https://github.com/C4rpeDime/Oneui_Pan">C4rpeDime/Oneui_Pan</a>进行二改</p><p style="text-align:center;">更多详细请看<a href="https://littlepan.netlify.app/" style="font-family: inherit; font-size: 24px; background-color: rgb(255, 255, 255);">使用文档</a></p><p style="text-align:center;">原来的API不够完善，后来自己改了几遍，才能正常使用<br/><br/>原来的程序也比较多漏洞，我估摸着可能原作者是用AI写的😭<br/><br/>如果你有什么问题的话，欢迎去<a href="https://github.com/FirgtZhong/LittlePan/issues">Issues</a>进行详细说明<br/><br/><img src="https://firgt.eu.org/images/wechatpay.png" alt="没米了，呜呜😭"/><br/><br/>给点米吧😭😭</p>
<!-- EDITABLE_END -->
<!-- 以上是可编辑区域 -->
</div></main>

<?php include SYSTEM_ROOT . 'footer.php'; ?>
            
                <script src="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.js"></script>
                <link href="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.css" rel="stylesheet">
              
            </div>
        </div>
    
