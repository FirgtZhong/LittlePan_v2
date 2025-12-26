<?php
include("./includes/common.php");

$title = '文件查看 - ' . $conf['title'];
$is_file = true;

include("./includes/header1.php")
?>

    <main class="container mx-auto px-4 py-6">
        <?php
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

        <?php if ($row['pwd'] && $row['pwd'] != $pwd) { ?>
            <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <title>请输入密码下载文件</title>
            <script type="text/javascript">
                var pwd = prompt("请输入密码", "");
                if (pwd) window.location.href = "./file.php?hash=<?php echo $row['hash'] ?>&pwd=" + pwd;
            </script>
            <div class="text-center py-10">
                请刷新页面，或[ <a href="javascript:history.back();" class="text-windows-color hover:underline">返回上一页</a> ]
            </div>
        <?php exit; } ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <!-- 文件预览区块 -->
                <div class="bg-white rounded-lg shadow-windows p-6 mb-6 card-hover">
                    <div class="border-b border-gray-200 pb-3 mb-4">
                        <h2 class="text-xl font-semibold"><?php echo $title ?></h2>
                    </div>
                    <div class="text-center py-4">
                        <?php
                        if ($filetype == 1) {
                            echo '<div class="image_view"><a href="' . $viewurl . '" title="点击查看原图"><img alt="' . htmlspecialchars($name) . '" src="' . $viewurl . '" class="max-w-full h-auto mx-auto rounded shadow-sm" style="max-height: 600px;"></a></div>';
                        } elseif ($filetype == 2) {
                            echo '<audio controls src="' . $viewurl . '" class="audio-player w-full max-w-lg mx-auto"></audio>';
                        } elseif ($filetype == 3) {
                            echo $row['block'] == 0
                                ? '<video src="' . $viewurl . '" controls class="video-player w-full max-w-4xl mx-auto rounded shadow-sm"></video>'
                                : '<p class="text-center py-10 text-windows-gray">视频文件需审核通过后才能在线播放和下载，请等待审核通过！</p>';
                        } else {
                            echo '<a href="' . $downurl . '" class="bg-windows-color hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md inline-flex items-center btn-hover">
                                    <i class="fa fa-download mr-2"></i> 下载文件
                                  </a>';
                        }
                        ?>
                    </div>
                </div>

                <!-- 文件信息和管理区块 -->
                <div class="bg-white rounded-lg shadow-windows overflow-hidden card-hover">
                    <!-- 标签页导航 -->
                    <div class="border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fileTab" role="tablist">
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 border-windows-color text-windows-color rounded-t-lg" 
                                        id="link-tab" data-tabs-target="#link" type="button" role="tab" 
                                        aria-selected="true">
                                    <i class="fa fa-link mr-1"></i> 文件外链
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg" 
                                        id="info-tab" data-tabs-target="#info" type="button" role="tab" 
                                        aria-selected="false">
                                    <i class="fa fa-info-circle mr-1"></i> 文件详情
                                </button>
                            </li>
                            <?php if ($is_mine) { ?>
                            <li role="presentation">
                                <button class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg" 
                                        id="manager-tab" data-tabs-target="#manager" type="button" role="tab" 
                                        aria-selected="false">
                                    <i class="fa fa-cog mr-1"></i> 管理
                                </button>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    
                    <!-- 标签页内容 -->
                    <div id="fileTabContent" class="p-6">
                        <!-- 外链标签页 -->
                        <div class="tab-pane active" id="link" role="tabpanel" aria-labelledby="link-tab">
                            <div class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <label for="link1" class="text-windows-dark font-medium w-full sm:w-24">查看链接：</label>
                                    <div class="flex-1 flex">
                                        <input type="text" class="flex-1 border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-windows-color focus:border-transparent" 
                                               id="link1" readonly value="<?php echo $viewurl_all ?>">
                                        <button class="bg-windows-color hover:bg-blue-500 text-white px-4 py-2 rounded-r-md btn-hover" 
                                                onclick="copyToClipboard('<?php echo $viewurl_all ?>')">
                                            复制
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <label for="link2" class="text-windows-dark font-medium w-full sm:w-24">下载链接：</label>
                                    <div class="flex-1 flex">
                                        <input type="text" class="flex-1 border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-windows-color focus:border-transparent" 
                                               id="link2" readonly value="<?php echo $downurl_all ?>">
                                        <button class="bg-windows-color hover:bg-blue-500 text-white px-4 py-2 rounded-r-md btn-hover" 
                                                onclick="copyToClipboard('<?php echo $downurl_all ?>')">
                                            复制
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 详情标签页 -->
                        <div class="tab-pane hidden" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <th scope="row" class="px-6 py-4 text-left text-sm font-medium text-windows-dark w-1/3">上传者IP</th>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo preg_replace('/\d+$/', '*', $row['ip']); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="px-6 py-4 text-left text-sm font-medium text-windows-dark w-1/3">上传时间</th>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $row['addtime']; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="px-6 py-4 text-left text-sm font-medium text-windows-dark w-1/3">下载次数</th>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $row['count']; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="px-6 py-4 text-left text-sm font-medium text-windows-dark w-1/3">文件大小</th>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo size_format($row['size']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- 管理标签页 -->
                        <?php if ($is_mine) { ?>
                        <div class="tab-pane hidden" id="manager" role="tabpanel" aria-labelledby="manager-tab">
                            <div class="flex space-x-4">
                                <button onclick="deleteConfirm('<?php echo $row['hash']; ?>')" 
                                        class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-6 rounded-md inline-flex items-center btn-hover">
                                    <i class="fa fa-trash mr-2"></i> 删除文件
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <!-- 提示区块 -->
                <div class="bg-white rounded-lg shadow-windows p-6 card-hover">
                    <div class="border-b border-gray-200 pb-3 mb-4">
                        <h3 class="font-semibold text-lg flex items-center">
                            <i class="fa fa-exclamation-circle text-amber-500 mr-2"></i> 提示
                        </h3>
                    </div>
                    <div class="text-gray-700 text-sm">
                        <?php echo $conf['gg_file'] ?>
                    </div>
                </div>

                <!-- 二维码区块 -->
                <div class="bg-white rounded-lg shadow-windows p-6 card-hover">
                    <div class="border-b border-gray-200 pb-3 mb-4">
                        <h3 class="font-semibold text-lg flex items-center">
                            <i class="fa fa-qrcode text-windows-color mr-2"></i> 当前页面二维码
                        </h3>
                    </div>
                    <div class="text-center">
                        <img alt="当前页面二维码" src="//api.2dcode.biz/v1/create-qr-code?size=180x180&margin=10&data=<?php echo urlencode($thisurl); ?>" 
                             class="mx-auto rounded shadow-sm">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include("./includes/footer1.php")?>

    <script>

        // 确保外部链接在新窗口打开
        document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])').forEach(link => {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        });

        // 平滑滚动到锚点
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                    
                    // 如果是移动菜单，点击后关闭
                    const mobileNav = document.getElementById('mobileNav');
                    if (!mobileNav.classList.contains('hidden')) {
                        mobileNav.classList.add('hidden');
                    }
                }
            });
        });

        // 标签页切换功能
        document.querySelectorAll('#fileTab button').forEach(button => {
            button.addEventListener('click', () => {
                // 移除所有标签页的活动状态
                document.querySelectorAll('#fileTab button').forEach(btn => {
                    btn.classList.remove('border-windows-color', 'text-windows-color');
                    btn.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // 隐藏所有标签页内容
                document.querySelectorAll('#fileTabContent .tab-pane').forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });
                
                // 激活当前标签页
                button.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                button.classList.add('border-windows-color', 'text-windows-color');
                button.setAttribute('aria-selected', 'true');
                
                // 显示当前标签页内容
                const target = button.getAttribute('data-tabs-target');
                const targetPane = document.querySelector(target);
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
            });
        });

        // 复制到剪贴板功能
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // 显示复制成功提示
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                notification.textContent = '复制成功！';
                document.body.appendChild(notification);
                
                // 3秒后移除提示
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, 3000);
            }).catch(err => {
                console.error('无法复制文本: ', err);
                alert('复制失败，请手动复制');
            });
        }

        // 删除文件确认
        function deleteConfirm(hash) {
            var csrf_token = "<?php echo $csrf_token; ?>";
            if (confirm("确定要删除此文件吗？删除后将无法恢复！")) {
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

        // 设置当前年份
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</body>
</html>
