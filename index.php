<?php
include("./includes/common.php");
$title = $conf['title'];
include SYSTEM_ROOT.'header1.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
// 添加总空间大小统计
$totalSize = $DB->getColumn("SELECT sum(size) FROM pre_file WHERE hide=0");
$totalSizeFormatted = size_format($totalSize ?: 0);
?>

    <!-- 主内容区 -->
    <main class="container mx-auto px-4 py-8">       
            <!-- 网站公告预览 -->
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <i class="fa fa-bullhorn text-windows-blue mr-2"></i>最新公告
                    </h3>
                </div>
                
                <div class="bg-white rounded-xl shadow-windows border border-gray-100 p-6">
                    <div class="flex items-start gap-4">
                        <div>
                            <h4 class="font-medium mb-1">通知标题</h4>
                            <p class="text-windows-gray text-sm mb-3">没有通知</p>
                            <div class="text-xs text-windows-gray">发布时间: 无</div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
        <br>
        <!-- 文件管理页面 -->
            <!-- 页面标题和操作区 -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold text-windows-dark">所有文件</h4>
                    <p class="text-windows-gray mt-2">共有 <?php echo $numrows; ?> 个公开文件 | 总占用空间：<?php echo $totalSizeFormatted; ?></p>
                </div>
                
                <!-- 操作按钮组 -->
                <div class="flex flex-wrap gap-3">
                    
                    <div class="relative">
                        <input type="text" placeholder="搜索文件..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-windows-blue/30 focus:border-windows-blue transition-windows w-full md:w-64">
                        <i class="fa fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-windows-gray"></i>
                    </div>
                    
                    <button class="flex items-center gap-2 px-4 py-2 bg-windows-blue text-white rounded-lg hover:bg-windows-blue/90 transition-windows btn-hover">
                        <a href="/upload.php">
                        <i class="fa fa-upload"></i>
                        <span>上传文件</span>
                        </a>
                    </button>
                    
                </div>
            </div>
            
            <!-- 文件列表表格 -->
            <section class="mb-10">
                <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">文件名</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">大小</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark"> </th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">上传时间</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $pagesize = 15;
                                    $pages = ceil($numrows / $pagesize);
                                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                                    // 防止页码超出范围
                                    $page = max(1, min($page, $pages));
                                    $offset = $pagesize * ($page - 1);

                                    $rs = $DB->query("SELECT * FROM pre_file WHERE hide=0 ORDER BY id DESC LIMIT $offset, $pagesize");
                                    $i = 1;
                                    while ($res = $rs->fetch()) {
                                    $fileurl = './down.php/' . $res['hash'] . '.' . ($res['type'] ? $res['type'] : 'file');
                                    $viewurl = './file.php?hash=' . $res['hash'];
                                    echo '<tr class="border-b border-gray-100 hover:bg-gray-50 transition-windows"><td class="px-6 py-4"><div class="flex items-center gap-3"><div class="file-icon bg-blue-100 text-blue-600"><i class="fa ' . type_to_icon($res['type']) . ' fa-fw"></i></div><span>' . $res['name'] . '</span></td><td class="px-6 py-4 text-windows-gray">' . size_format($res['size']) . '</td><td><font color="blue">' . '</font></td><td class="px-6 py-4 text-windows-gray">' . $res['addtime'] . '</td><td class="px-6 py-4"><div class="flex gap-2"><button class="p-1.5 text-windows-gray hover:text-windows-blue hover:bg-blue-50 rounded transition-windows" title="下载"><a href="' . $fileurl . '"><i class="fa fa-download"></i></a></button> <button class="p-1.5 text-windows-gray hover:text-green-500 hover:bg-green-50 rounded transition-windows" title="详细"><a href="'. $viewurl . '"><i class="fa fa-info-circle"></i></a></button></div></td></tr>';
                                            }
                                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 分页导航控件 -->
                <?php if ($pages > 1) { ?>
                <div class="flex justify-center items-center gap-2 mt-6 py-4">
                    <!-- 上一页 -->
                    <?php if ($page > 1) { ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="pagination-link">
                            <i class="fa fa-angle-left"></i> 上一页
                        </a>
                    <?php } else { ?>
                        <span class="pagination-link text-gray-400 cursor-not-allowed">
                            <i class="fa fa-angle-left"></i> 上一页
                        </span>
                    <?php } ?>

                    <!-- 页码列表 -->
                    <?php 
                    // 显示当前页前后2页及首尾页
                    $startPage = max(1, $page - 2);
                    $endPage = min($pages, $page + 2);
                    
                    // 显示首页
                    if ($startPage > 1) {
                        echo '<a href="?page=1" class="pagination-link ' . ($page == 1 ? 'active' : '') . '">1</a>';
                        if ($startPage > 2) echo '<span class="px-2">...</span>';
                    }
                    
                    // 显示中间页码
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        echo '<a href="?page=' . $i . '" class="pagination-link ' . ($page == $i ? 'active' : '') . '">' . $i . '</a>';
                    }
                    
                    // 显示尾页
                    if ($endPage < $pages) {
                        if ($endPage < $pages - 1) echo '<span class="px-2">...</span>';
                        echo '<a href="?page=' . $pages . '" class="pagination-link ' . ($page == $pages ? 'active' : '') . '">' . $pages . '</a>';
                    }
                    ?>

                    <!-- 下一页 -->
                    <?php if ($page < $pages) { ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="pagination-link">
                            下一页 <i class="fa fa-angle-right"></i>
                        </a>
                    <?php } else { ?>
                        <span class="pagination-link text-gray-400 cursor-not-allowed">
                            下一页 <i class="fa fa-angle-right"></i>
                        </span>
                    <?php } ?>
                </div>
                <?php } ?>
            </section>
        </main>
    
<?php include SYSTEM_ROOT . 'footer1.php'; ?>