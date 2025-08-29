var xhr;
var isBlock = false;
var ot = 0;
var oloaded = 0;

// 安全获取DOM元素的工具函数
function getElementSafe(selector) {
    const element = $(selector);
    if (element.length === 0) {
        console.error(`无法找到元素: ${selector}`);
        return null;
    }
    return element;
}

// 验证依赖是否加载
function validateDependencies() {
    if (typeof $ === 'undefined') {
        show_msg("错误：jQuery未加载，无法执行上传功能", 1);
        return false;
    }
    
    if (typeof XMLHttpRequest === 'undefined') {
        show_msg("错误：您的浏览器不支持XMLHttpRequest，无法上传文件", 1);
        return false;
    }
    
    // 检查必要的DOM元素
    const requiredElements = [
        "#upload_block", "#progressBar", "#uploadFile", 
        "#show", "#ispwd", "#pwd", "#csrf_token"
    ];
    
    for (const selector of requiredElements) {
        if (getElementSafe(selector) === null) {
            show_msg(`错误：页面缺少必要组件（${selector}），请刷新页面重试`, 1);
            return false;
        }
    }
    
    return true;
}

// 上传文件方法（核心逻辑）
function UploadFile() {
    // 初始化检查
    if (!validateDependencies()) {
        return;
    }

    try {
        if (isBlock) {
            show_msg("正在上传中，请不要重复点击", 1);
            return;
        }

        const $fileInput = getElementSafe("#file");
        if (!$fileInput) return;
        
        const fileInput = $fileInput[0];
        if (!fileInput) {
            show_msg("文件选择组件加载失败，请刷新页面重试", 1);
            return;
        }

        // 验证文件数量（仅允许单文件）
        if (!fileInput.files || fileInput.files.length === 0) {
            show_msg("请先选择文件", 1);
            return;
        }
        if (fileInput.files.length > 1) {
            show_msg("一次只能上传一个文件", 1);
            return;
        }

        const fileObj = fileInput.files[0];
        // 验证文件有效性
        if (!fileObj || fileObj.size <= 0) {
            show_msg("无法上传空文件或无效文件", 1);
            return;
        }

        // 验证密码格式（若启用）
        const $ispwd = getElementSafe("#ispwd");
        const $pwd = getElementSafe("#pwd");
        if (!$ispwd || !$pwd) return;
        
        const ispwd = $ispwd.prop('checked');
        const pwd = $pwd.val() || "";
        
        if (ispwd && pwd && !/^[a-zA-Z0-9]+$/.test(pwd)) {
            show_msg("密码只能包含字母或数字", 1);
            return;
        }

        // 显示准备状态
        const $progressBar = getElementSafe("#progressBar");
        if (!$progressBar) return;
        
        $progressBar.html('<div class="text-amber-500">正在准备上传...</div>');
        
        // 计算文件大小并格式化
        let size = fileObj.size;
        let units = 'B';
        if (size / 1024 > 1) {
            size = size / 1024;
            units = 'KB';
        }
        if (size / 1024 > 1) {
            size = size / 1024;
            units = 'MB';
        }
        if (size / 1024 > 1) {
            size = size / 1024;
            units = 'GB';
        }
        const filesize = size.toFixed(2) + units;

        // 验证浏览器兼容性
        if (typeof FormData === "undefined") {
            show_msg("您的浏览器不支持文件上传功能，请升级至现代浏览器", 1);
            return;
        }
        
        try {
            const xhrTest = new XMLHttpRequest();
            if (!xhrTest.upload) {
                show_msg("您的浏览器不支持上传进度显示，但仍可尝试上传", 0);
            }
        } catch (e) {
            show_msg("浏览器上传功能检测失败，请尝试升级浏览器", 1);
            return;
        }

        // 获取CSRF令牌
        const $csrfToken = getElementSafe("#csrf_token");
        if (!$csrfToken) return;
        
        const csrfToken = $csrfToken.val() || "";
        if (!csrfToken) {
            show_msg("安全验证令牌获取失败，请刷新页面重试", 1);
            return;
        }

        // 构建表单数据
        const url = "./ajax.php?act=upload";
        const form = new FormData();
        try {
            form.append("file", fileObj);
            form.append("show", getElementSafe("#show").prop('checked') ? 1 : 0);
            form.append("ispwd", ispwd ? 1 : 0);
            form.append("pwd", pwd);
            form.append("csrf_token", csrfToken);
        } catch (e) {
            show_msg("构建上传数据失败：" + e.message, 1);
            return;
        }

        // 初始化XHR请求
        try {
            xhr = new XMLHttpRequest();
            xhr.open("post", url, true);

            // 超时设置（300秒）
            xhr.timeout = 300000;
            xhr.ontimeout = function() {
                show_msg("上传超时，请检查网络后重试", 1);
                isBlock = false;
            };

            // 上传成功回调
            xhr.onload = function (evt) {
                try {
                    const nt = (new Date().getTime() - ot) / 1000;
                    let data = evt.target.responseText;
                    
                    // 验证响应是否为空
                    if (!data) {
                        show_msg("上传失败：服务器未返回数据", 1);
                        return;
                    }

                    let json;
                    try {
                        json = JSON.parse(data);
                    } catch (e) {
                        show_msg("上传失败：服务器返回格式错误", 1);
                        console.error("服务器返回原始数据：", data);
                        return;
                    }

                    if (json.code === 0) {
                        let jumpurl = "file.php?hash=" + (json.hash || "");
                        if (ispwd && pwd) {
                            jumpurl += '&pwd=' + encodeURIComponent(pwd);
                        }
                        // 验证跳转URL有效性
                        if (!json.hash) {
                            show_msg('上传成功，但无法获取文件信息', 0);
                            return;
                        }
                        
                        show_msg(`上传成功！总用时：${nt.toFixed(2)}秒。正在跳转到文件查看页面...`, 0);
                        setTimeout(function () { 
                            window.location.href = jumpurl; 
                        }, 800);
                    } else {
                        show_msg("上传失败：" + (json.msg || "服务器处理错误"), 1);
                    }
                } catch (e) {
                    show_msg("处理上传结果时出错：" + e.message, 1);
                } finally {
                    isBlock = false;
                }
            };

            // 上传失败回调
            xhr.onerror = function (evt) {
                show_msg('上传失败：网络异常或服务器无响应，请稍后重试', 1);
                isBlock = false;
            };

            // 上传进度更新
            xhr.upload.onprogress = progressFunction;

            // 上传开始回调
            xhr.upload.onloadstart = function () {
                isBlock = true;
                ot = new Date().getTime();
                oloaded = 0;
                // 初始化进度条UI
                $progressBar.html(`
                    <div class="relative overflow-hidden h-2 bg-gray-200 rounded-full mb-3">
                        <div id="progressFill" class="absolute top-0 left-0 h-full bg-windows-blue transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex flex-wrap justify-between items-center text-sm">
                        <div id="percentage" class="text-windows-blue font-medium">0%</div>
                        <div class="filename text-gray-600 truncate max-w-[50%] text-center">${fileObj.name || '未知文件'} (${filesize})</div>
                        <div id="uploadspeed" class="text-gray-500 text-right">0 KB/s</div>
                    </div>
                `);
            };

            // 发送请求
            xhr.send(form);

        } catch (e) {
            show_msg("上传初始化失败：" + e.message, 1);
            isBlock = false;
        }

    } catch (e) {
        show_msg("上传过程发生错误：" + e.message, 1);
        isBlock = false;
        console.error("上传错误详情：", e);
    }
}

// 取消上传
function cancleUploadFile() {
    if (xhr) {
        try {
            xhr.abort();
        } catch (e) {
            console.error("取消上传时出错：", e);
        }
        xhr = null;
    }
    
    const $progressBar = getElementSafe("#progressBar");
    if ($progressBar) {
        $progressBar.html("");
    }
    
    isBlock = false;
    show_msg("已中止上传", 0);
}

// 上传进度更新函数
function progressFunction(evt) {
    try {
        // 检查必要元素
        const $progressFill = getElementSafe("#progressFill");
        const $percentage = getElementSafe("#percentage");
        const $uploadspeed = getElementSafe("#uploadspeed");
        
        if (!$progressFill || !$percentage || !$uploadspeed) {
            return;
        }

        if (evt.lengthComputable) {
            const percentage = Math.round((evt.loaded / evt.total) * 100);
            $progressFill.css("width", percentage + "%");
            $percentage.html(percentage + "%");
        }

        // 计算上传速度和剩余时间
        const nt = new Date().getTime();
        let pertime = (nt - ot) / 1000; // 时间差（秒）
        pertime = pertime < 0.01 ? 0.01 : pertime; // 避免除以0

        const perload = evt.loaded - oloaded; // 本次上传字节数
        oloaded = evt.loaded; // 更新已上传总量

        // 计算速度
        let speed = perload / pertime;
        const bspeed = speed; // 原始速度（B/s）
        let units = 'B/s';
        
        if (speed / 1024 > 1) {
            speed = speed / 1024;
            units = 'KB/s';
        }
        if (speed / 1024 > 1) {
            speed = speed / 1024;
            units = 'MB/s';
        }
        speed = speed.toFixed(2);

        // 计算剩余时间
        let resttime = 0;
        if (evt.lengthComputable && bspeed > 0) {
            resttime = (evt.total - evt.loaded) / bspeed;
            resttime = resttime < 0.1 ? 0.1 : resttime; // 避免显示0秒
        }

        // 更新UI
        $uploadspeed.html(`${speed}${units}，剩余约${resttime.toFixed(1)}秒`);
        
        if (evt.loaded === evt.total) {
            $uploadspeed.html("正在处理文件...");
        }

        // 更新时间戳（用于下次计算）
        ot = nt;

    } catch (e) {
        console.error("进度计算错误：", e);
        // 进度计算错误不中断上传，仅在控制台记录
    }
}

// 消息提示函数
function show_msg(msg, error) {
    isBlock = false; // 无论成功失败，都重置上传状态
    error = error || 0;
    const alertClass = error ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200';
    const buttonHtml = error ? '<button onclick="retryUpload()" class="ml-4 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm transition-windows">重试</button>' : '';
    
    const $progressBar = getElementSafe("#progressBar");
    if ($progressBar) {
        $progressBar.html(`
            <div class="${alertClass} px-4 py-3 rounded-md flex items-center justify-between my-4">
                <strong>${msg}</strong>
                ${buttonHtml}
            </div>
        `);
    } else {
        // 如果找不到进度条容器，使用浏览器默认提示
        alert(msg);
    }
}

// 重试上传
function retryUpload() {
    try {
        const $fileInput = getElementSafe("#file");
        if ($fileInput && $fileInput[0] && $fileInput[0].files && $fileInput[0].files.length > 0) {
            UploadFile(); // 直接重新上传
        } else {
            // 重新创建文件输入框
            const $uploadBlock = getElementSafe("#upload_block");
            if ($uploadBlock) {
                $uploadBlock.html(`
                    <input type="file" id="file" name="myfile" style="position:absolute; top:-9999px; left:-9999px; opacity:0; z-index:100;"/>
                `);
                // 绑定change事件
                $("#file").on("change", UploadFile);
                // 触发文件选择对话框
                $("#file").trigger("click");
            }
        }
    } catch (e) {
        show_msg("重试上传失败：" + e.message, 1);
        console.error("重试上传错误：", e);
    }
}

// 页面加载完成后初始化
$(document).ready(function () {
    // 页面加载完成后先验证依赖
    setTimeout(validateDependencies, 100);

    // 绑定上传按钮点击事件
    const $uploadFile = getElementSafe("#uploadFile");
    if ($uploadFile) {
        $uploadFile.click(function () {
            if (isBlock) {
                show_msg("正在上传中，请不要重复点击", 1);
                return;
            }
            
            try {
                const $uploadBlock = getElementSafe("#upload_block");
                if (!$uploadBlock) return;
                
                // 创建文件输入框
                $uploadBlock.html(`
                    <input type="file" id="file" name="myfile" style="position:absolute; top:-9999px; left:-9999px; opacity:0; z-index:100;"/>
                `);
                
                // 绑定change事件（使用jQuery的on方法确保事件正确绑定）
                $("#file").off("change").on("change", function() {
                    UploadFile();
                });
                
                // 触发文件选择对话框
                const $file = getElementSafe("#file");
                if ($file) {
                    $file.trigger("click");
                }
            } catch (e) {
                show_msg("无法打开文件选择器：" + e.message, 1);
                console.error("文件选择器错误：", e);
            }
        });
    }

    // 绑定密码复选框事件
    const $ispwd = getElementSafe("#ispwd");
    const $pwdFrame = getElementSafe("#pwd_frame");
    if ($ispwd && $pwdFrame) {
        $ispwd.off("click").on("click", function () {
            try {
                if ($(this).prop("checked")) {
                    $pwdFrame.show();
                } else {
                    $pwdFrame.hide();
                }
            } catch (e) {
                console.error("密码框显示/隐藏错误：", e);
            }
        });
    }

    // 页面卸载时处理未完成的上传
    $(window).on("beforeunload", function () {
        if (isBlock) {
            return "正在上传文件，确定要离开吗？";
        }
    });

    // 窗口失去焦点时提示
    $(window).on("blur", function() {
        if (isBlock) {
            console.log("上传正在进行中，请不要关闭页面...");
        }
    });
});
