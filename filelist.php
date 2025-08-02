<?php
include("./includes/common.php");

$title = $conf['title'];
include SYSTEM_ROOT.'header.php';
?>
<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">文件列表</h3>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>操作</th>
                            <th>文件名</th>
                            <th>文件大小</th>
                            <th>文件格式</th>
                            <th>上传时间</th>
                            <th>上传者IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
                        $pagesize = 15;
                        $pages = ceil($numrows / $pagesize);
                        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                        $offset = $pagesize * ($page - 1);

                        $rs = $DB->query("SELECT * FROM pre_file WHERE hide=0 ORDER BY id DESC LIMIT $offset, $pagesize");
                        $i = 1;
                        while ($res = $rs->fetch()) {
                            $fileurl = './down.php/' . $res['hash'] . '.' . ($res['type'] ? $res['type'] : 'file');
                            $viewurl = './file.php?hash=' . $res['hash'];
                            echo '<tr><td><b>' . $i++ . '</b></td><td><a href="' . $fileurl . '">下载</a>｜<a href="' . $viewurl . '" target="_blank">查看</a></td><td><i class="fa ' . type_to_icon($res['type']) . ' fa-fw"></i>' . $res['name'] . '</td><td>' . size_format($res['size']) . '</td><td><font color="blue">' . ($res['type'] ? $res['type'] : '未知') . '</font></td><td>' . $res['addtime'] . '</td><td>' . preg_replace('/\d+$/', '*', $res['ip']) . '</b></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <br>共有 <?php echo $numrows; ?> 个文件&nbsp;&nbsp;当前第 <?php echo $page; ?> 页，共 <?php echo $pages; ?> 页
                </div>
                <div class="col-md-6">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm float-right">
                            <?php
                            $first = 1;
                            $prev = $page - 1;
                            $next = $page + 1;
                            $last = $pages;
                            if ($page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $first . '">首页</a></li>';
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $prev . '">&laquo;</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><a class="page-link">首页</a></li>';
                                echo '<li class="page-item disabled"><a class="page-link">&laquo;</a></li>';
                            }
                            $start = $page - 5 > 1 ? $page - 5 : 1;
                            $end = $page + 5 < $pages ? $page + 5 : $pages;
                            for ($i = $start; $i < $page; $i++) {
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $i . '">' . $i . '</a></li>';
                            }
                            echo '<li class="page-item active"><a class="page-link">' . $page . '</a></li>';
                            for ($i = $page + 1; $i <= $end; $i++) {
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $i . '">' . $i . '</a></li>';
                            }
                            if ($page < $pages) {
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $next . '">&raquo;</a></li>';
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $last . '">尾页</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><a class="page-link">&raquo;</a></li>';
                                echo '<li class="page-item disabled"><a class="page-link">尾页</a></li>';
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include SYSTEM_ROOT . 'footer.php'; ?>
<?php if (!empty($conf['gonggao'])) : ?>
<script src="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.js"></script>
<link href="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.css" rel="stylesheet">
<script>
    $(function() {
        $.snackbar({content: "<?php echo htmlspecialchars($conf['gonggao']); ?>", timeout: 10000});
    });
</script>
<?php endif; ?>
