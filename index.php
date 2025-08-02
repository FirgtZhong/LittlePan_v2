<?php
include("./includes/common.php");
$title = $conf['title'];
include SYSTEM_ROOT.'header.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");

?>

    <main id="main-container">
        <div class="bg-image" style="background-image: url('//dailybing.com/api/v1');">
            <div class="bg-primary-dark-op py-9 overflow-hidden">
                <div class="content content-full text-center">
                    <h1 class="display-4 fw-semibold text-white mb-2"><?php echo $title; ?></h1>
                    <p class="fs-4 fw-normal text-white-50 mb-5">共有 <?php echo $numrows; ?> 个公开文件</p>
                    <div>
                        <a class="btn btn-primary px-3 py-2 m-1" href="/upload.php">
                            <i class="fa fa-fw fa-upload opacity-50 me-1"></i> 上传文件
                        </a>
                        <a class="btn btn-dark px-3 py-2 m-1" href="https://firgt.cn">
                            <i class="fa fa-fw fa-link opacity-50 me-1"></i> 导航站
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-body-extra-light">
            <div class="content content-full">
                <div class="py-5 text-center push">
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
                                            $pagesize = 15;
                                            $pages = ceil($numrows / $pagesize);
                                            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                                            $offset = $pagesize * ($page - 1);

                                            $rs = $DB->query("SELECT * FROM pre_file WHERE hide=0 ORDER BY id DESC LIMIT $offset, $pagesize");
                                            $i = 1;
                                            while ($res = $rs->fetch()) {
                                                $fileurl = './down.php/' . $res['hash'] . '.' . ($res['type'] ? $res['type'] : 'file');
                                                $viewurl = './file.php?hash=' . $res['hash'];
                                                echo '<tr><td><b>' . $i++ . '</b></td><td><a href="' . $fileurl . '">下载</a>｜<a href="' . $viewurl . '" target="_blank">查看</a></td><td><i class="fa ' . type_to_icon($res['type']) . ' fa-fw"></i>' . $res['name'] . '</td><td>' . size_format($res['size']) . '</td><td><font color="blue">' . ($res['type'] ? $res['type'] : '未知') . '</font></td><td>' . $res['addtime'] . '</td><td>' . preg_replace('/\d+$/', '*', $res['ip']) . '</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
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
                </div>
                </main>
                <?php include SYSTEM_ROOT . 'footer.php'; ?>
            
                <script src="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.js"></script>
                <link href="//cdn.staticfile.org/snackbarjs/1.1.0/snackbar.min.css" rel="stylesheet">
              
            </div>
        </div>
    
