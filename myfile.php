<?php
include("./includes/common.php");

$title = '我的文件 - ' . $conf['title'];
include SYSTEM_ROOT . 'header1.php';
?>

    <main id="main-container">
<div class="content">
    <h2 style="text-align:center;">我上传的文件 <small>（根据浏览器缓存记录）</small></h2>
    
    <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <?php if (isset($_SESSION['fileids']) && count($_SESSION['fileids']) > 0): ?>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th style="width: 5%;" class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">#</th>
                            <th style="width: 20%;" class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">操作</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">文件名</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">文件大小</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">文件格式</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">上传时间</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-windows-dark">上传者IP</th>
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
                            echo '                                                <td class="px-6 py-4">
                                                    <div class="flex gap-2">
                                                        <button class="p-1.5 text-windows-gray hover:text-windows-blue hover:bg-blue-50 rounded transition-windows" title="下载">
                                                            <a href="' . htmlspecialchars($fileurl) . '"><i class="fa fa-download"></i></a>
                                                        </button> 
                                                        <button class="p-1.5 text-windows-gray hover:text-green-500 hover:bg-green-50 rounded transition-windows" title="详细">
                                                            <a href="' . htmlspecialchars($viewurl) . '"><i class="fa fa-info-circle"></i></a>
                                                        </button>
                                                    </div>
                                                </td>';
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
                <h1 style="text-align:center;">你还没有上传过文件</h1>
            <?php endif; ?>
        </div    </div>

                <!-- 分页导航控件 -->
                <?php if ($pages > 1) { ?>
                <div class="flex justify-center items-center gap-2 mt-6 py-4">
                    <!-- 上一页 -->
                    <?php 
                    $prevUrl = $search ? "?search=" . urlencode($search) . "&page=" . ($page - 1) : "?page=" . ($page - 1);
                    ?>
                    <?php if ($page > 1) { ?>
                        <a href="<?php echo $prevUrl; ?>" class="pagination-link">
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
                        $firstUrl = $search ? "?search=" . urlencode($search) . "&page=1" : "?page=1";
                        echo '<a href="' . $firstUrl . '" class="pagination-link ' . ($page == 1 ? 'active' : '') . '">1</a>';
                        if ($startPage > 2) echo '<span class="px-2">...</span>';
                    }
                    
                    // 显示中间页码
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        $pageUrl = $search ? "?search=" . urlencode($search) . "&page={$i}" : "?page={$i}";
                        echo '<a href="' . $pageUrl . '" class="pagination-link ' . ($page == $i ? 'active' : '') . '">' . $i . '</a>';
                    }
                    
                    // 显示尾页
                    if ($endPage < $pages) {
                        if ($endPage < $pages - 1) echo '<span class="px-2">...</span>';
                        $lastUrl = $search ? "?search=" . urlencode($search) . "&page={$pages}" : "?page={$pages}";
                        echo '<a href="' . $lastUrl . '" class="pagination-link ' . ($page == $pages ? 'active' : '') . '">' . $pages . '</a>';
                    }
                    ?>

                    <!-- 下一页 -->
                    <?php 
                    $nextUrl = $search ? "?search=" . urlencode($search) . "&page=" . ($page + 1) : "?page=" . ($page + 1);
                    ?>
                    <?php if ($page < $pages) { ?>
                        <a href="<?php echo $nextUrl; ?>" class="pagination-link">
                            下一页 <i class="fa fa-angle-right"></i>
                        </a>
                    <?php } else { ?>
                        <span class="pagination-link text-gray-400 cursor-not-allowed">
                            下一页 <i class="fa fa-angle-right"></i>
                        </span>
                    <?php } ?>
                </div>
                <?php } ?>
    
</div>
</main>
<?php include SYSTEM_ROOT . 'footer1.php'; ?>
