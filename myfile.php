<?php
include("./includes/common.php");

$title = '我的文件 - ' . $conf['title'];
include SYSTEM_ROOT . 'header.php';
?>

    <main id="main-container">
<div class="content">
    <h2 class="content-heading">我上传的文件 <small>（根据浏览器缓存记录）</small></h2>
    
    <div class="block block-rounded block-bordered">
        <div class="block-content">
            <?php if (isset($_SESSION['fileids']) && count($_SESSION['fileids']) > 0): ?>
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">操作</th>
                            <th>文件名</th>
                            <th>文件大小</th>
                            <th>文件格式</th>
                            <th>上传时间</th>
                            <th>上传者IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ids = implode(',', $_SESSION['fileids']);
                        $numrows = $DB->getColumn("SELECT count(*) from pre_file WHERE id IN($ids)");
                        $pagesize = 15;
                        $pages = ceil($numrows / $pagesize);
                        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                        $offset = $pagesize * ($page - 1);

                        $rs = $DB->query("SELECT * FROM pre_file WHERE id IN($ids) ORDER BY id DESC LIMIT $offset, $pagesize");
                        $i = 1;
                        while ($res = $rs->fetch()) {
                            $fileurl = './down.php/' . $res['hash'] . '.' . ($res['type'] ? $res['type'] : 'file');
                            $viewurl = './file.php?hash=' . $res['hash'];
                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td><a class="btn btn-sm btn-primary" href="' . $fileurl . '">下载</a> ｜ <a class="btn btn-sm btn-secondary" href="' . $viewurl . '" target="_blank">管理</a></td>';
                            echo '<td ' . ($res['hide'] == 1 ? 'style="color:#7d94a9"' : '') . '><i class="fa ' . type_to_icon($res['type']) . ' fa-fw"></i> ' . htmlspecialchars($res['name']) . '</td>';
                            echo '<td>' . size_format($res['size']) . '</td>';
                            echo '<td><span class="badge badge-info">' . ($res['type'] ? htmlspecialchars($res['type']) : '未知') . '</span></td>';
                            echo '<td>' . htmlspecialchars($res['addtime']) . '</td>';
                            echo '<td>' . htmlspecialchars(preg_replace('/\d+$/', '*', $res['ip'])) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">你还没有上传过文件</div>
            <?php endif; ?>
        </div    </div>

    <?php if ($pages > 1): ?>
    <div class="block block-rounded block-bordered">
        <div class="block-content block-content-full clearfix">
            <div class="float-left">
                共有 <?php echo $numrows ?> 个文件&nbsp;&nbsp;当前第 <?php echo $page ?> 页，共 <?php echo $pages ?> 页
            </div>
            <div class="float-right">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
                        <?php
                        $first = 1;
                        $prev = $page - 1;
                        $next = $page + 1;
                        $last = $pages;

                        if ($page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="myfile.php?page=' . $first . '">首页</a></li>';
                            echo '<li class="page-item"><a class="page-link" href="myfile.php?page=' . $prev . '">&laquo;</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link">首页</span></li>';
                            echo '<li class="page disabled"><span class="page-link">&laquo;</span></li>';
                        }

                        $start = max(1, $page - 5);
                        $end = min($pages, $page + 5);
                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $page) {
                                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="myfile.php?page=' . $i . '">' . $i . '</a></li>';
                            }
                        }

                        if ($page < $pages) {
                            echo '<li class="page-item"><a class="page-link" href="myfile.php?page=' . $next . '">&raquo;</a></li>';
                            echo '<li class="page-item"><a class="page-link" href="myfile.php?page=' . $last . '">尾页</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
                            echo '<li class="page-item disabled"><span class="page-link">尾页</span></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</main>
<?php include SYSTEM_ROOT . 'footer.php'; ?>
