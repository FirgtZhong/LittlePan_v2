<?php
include("./includes/common.php");

$title = '上传文件 - ' . $conf['title'];

include("./includes/header1.php");

$maxfilesize = ini_get('upload_max_filesize');
$csrf_token = md5(mt_rand(0, 999) . time());
$_SESSION['csrf_token'] = $csrf_token;
?>


<style>
            /* 上传区域拖拽样式 */
        #upload_block.drag-over {
            border-color: #0078D7;
            background-color: rgba(0, 120, 215, 0.05);
        }
</style>
    <!-- 主内容区 -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- 主上传区域 -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-windows p-6 transition-windows hover:shadow-lg">
                        <!-- 进度条容器 -->
                        <div id="progressBar" class="w-100 h-16 mb-6 h-1.5 bg-gray-200 rounded-lg overflow-hidden">
                            <!-- 进度条将由JS动态生成 -->
                        </div>
                        
                        <h3 class="text-center text-xl font-semibold mb-6 text-windows-dark">选择一个文件开始上传</h3>

                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $csrf_token ?>">
                        
                        <!-- 上传区域 -->
                        <div id="upload_block" class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-windows-blue transition-windows">
                            <i class="fa fa-cloud-upload text-5xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">点击或拖拽文件到此处上传</p>
                        </div>

                        <div id="upload_frame">
                            <button id="uploadFile" class="w-full bg-windows-blue hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md flex items-center justify-center transition-windows mb-4">
                                <i class="fa fa-upload mr-2"></i> 立即上传
                            </button>

                            <!-- 文件在首页显示复选框 -->
                            <div class="form-group mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="show" checked class="form-checkbox h-5 w-5 text-windows-blue rounded border-gray-300">
                                    <span class="ml-2 text-gray-700">在首页文件列表显示</span>
                                </label>
                            </div>

                            <!-- 文件加密复选框和密码输入框 -->
                            <div class="form-group mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="ispwd" class="form-checkbox h-5 w-5 text-windows-blue rounded border-gray-300">
                                    <span class="ml-2 text-gray-700">设定密码</span>
                                </label>
                            </div>

                            <div class="form-group" id="pwd_frame" style="display: none;">
                                <input type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-windows-blue focus:border-transparent transition-windows" 
                                       id="pwd" placeholder="请输入密码" autocomplete="off">
                                <small class="text-gray-500 text-sm mt-1 block">密码只能为字母或数字</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 右侧的上传提示块 -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-windows p-6 transition-windows hover:shadow-lg">
                        <div class="border-b border-gray-200 pb-3 mb-4">
                            <h3 class="font-semibold text-lg flex items-center">
                                <i class="fa fa-exclamation-circle text-amber-500 mr-2"></i> 上传提示
                            </h3>
                        </div>
                        <div class="text-gray-700">
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <span class="fa-li mt-1 mr-2"><i class="fa fa-info-circle text-amber-500"></i></span>
                                    <span>您的IP是 <?php echo $clientip ?>，请不要上传违规文件！</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="fa-li mt-1 mr-2"><i class="fa fa-info-circle text-amber-500"></i></span>
                                    <span>上传无格式限制，当前服务器单个文件上传最大支持 <b><?php echo $maxfilesize ?></b>！</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="fa-li mt-1 mr-2"><i class="fa fa-info-circle text-amber-500"></i></span>
                                    <span>上传违规文件会被服务器拦截，提示上传失败！</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="fa-li mt-1 mr-2"><i class="fa fa-info-circle text-amber-500"></i></span>
                                    <span>文件名不要带有英文特殊符号或者emoji字符！</span>
                                </li>
                                <?php if ($conf['videoreview'] == 1) { ?>
                                    <li class="flex items-start">
                                        <span class="fa-li mt-1 mr-2"><i class="fa fa-info-circle text-amber-500"></i></span>
                                        <span>当前网站已开启视频文件审核，如上传视频需审核通过后才能下载和播放。</span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <?php include("./includes/footer1.php"); ?>
    <script>


        // 密码框显示/隐藏控制
        document.getElementById('ispwd').addEventListener('change', function() {
            document.getElementById('pwd_frame').style.display = this.checked ? 'block' : 'none';
        });

        // 拖拽上传支持
        const uploadBlock = document.getElementById('upload_block');
        
        // 阻止默认拖拽行为
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBlock.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // 高亮拖拽区域
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBlock.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadBlock.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadBlock.classList.add('drag-over');
        }
        
        function unhighlight() {
            uploadBlock.classList.remove('drag-over');
        }
        
        // 处理文件拖放
        uploadBlock.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length) {
                // 创建临时文件输入框处理拖放的文件
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.id = 'file';
                fileInput.style.display = 'none';
                document.body.appendChild(fileInput);
                
                // 使用DataTransfer模拟文件选择
                const dataTransfer = new DataTransfer();
                for (let i = 0; i < files.length; i++) {
                    dataTransfer.items.add(files[i]);
                }
                fileInput.files = dataTransfer.files;
                
                // 触发上传
                if (typeof UploadFile === 'function') {
                    UploadFile();
                }
                
                // 清理临时元素
                setTimeout(() => {
                    document.body.removeChild(fileInput);
                }, 1000);
            }
        }
    </script>

    <!-- 引入上传处理脚本 -->
    <script src="assets/js/upload.js"></script>
</body>
</html>
