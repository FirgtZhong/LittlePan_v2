var xhr;
var isBlock = false;
// 全局变量初始化（避免未定义错误）
var ot = 0;
var oloaded = 0;

// 上传文件方法（核心逻辑）
function UploadFile() {
    try {
        if (isBlock) {
            show_msg("正在上传中，请不要重复点击", 1);
            return;
        }

        var fileInput = $("#file")[0];
        // 验证文件输入框状态
        if (!fileInput) {
            show_msg("文件选择组件加载失败，请刷新页面重试", 1);
            return;
        }

        // 验证文件数量（仅允许单文件）
        if (fileInput.files.length === 0) {
            show_msg("请先选择文件", 1);
            return;
        }
        if (fileInput.files.length > 1) {
            show_msg("一次只能上传一个文件", 1);
            return;
        }

        var fileObj = fileInput.files[0];
        // 验证文件有效性
        if (typeof (fileObj) === "undefined" || fileObj.size <= 0) {
            show_msg("无法上传空文件或无效文件", 1);
            return;
        }

        // 验证密码格式（若启用）
        var ispwd = $("#ispwd").prop('checked');
        var pwd = $("#pwd").val();
        if (ispwd && pwd && !/^[a-zA-Z0-9]+$/.test(pwd)) {
            show_msg("密码只能包含字母或数字", 1);
            return;
        }

        // 显示准备状态（避免用户误以为无反应）
        $("#progressBar").html('<div class="text-info">正在准备上传...</div>');
        
        // 计算文件大小并格式化
        var size = fileObj.size;
        var units = 'B';
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
        var filesize = size.toFixed(2) + units;

        // 验证浏览器兼容性
        if (typeof FormData === "undefined") {
            show_msg("您的浏览器不支持文件上传功能，请升级至现代浏览器", 1);
            return;
        }
        var xhrTest = new XMLHttpRequest();
        if (!xhrTest.upload) {
            show_msg("您的浏览器不支持上传进度显示，请升级浏览器", 1);
            return;
        }

        // 构建表单数据
        var url = "./ajax.php?act=upload";
        var form = new FormData();
        form.append("file", fileObj);
        form.append("show", $("#show").prop('checked') ? 1 : 0);
        form.append("ispwd", ispwd ? 1 : 0);
        form.append("pwd", pwd);
        form.append("csrf_token", $("#csrf_token").val() || "");

        // 初始化XHR请求
        xhr = new XMLHttpRequest();
        xhr.open("post", url, true);

        // 上传成功回调
        xhr.onload = function (evt) {
            var nt = (new Date().getTime() - ot) / 1000;
            var data = evt.target.responseText;
            try {
                var json = JSON.parse(data);
                if (json.code === 0) {
                    var jumpurl = "file.php?hash=" + json.hash;
                    if (ispwd && pwd) {
                        jumpurl += '&pwd=' + pwd;
                    }
                    show_msg('上传成功！总用时：' + nt.toFixed(2) + '秒。正在跳转到文件查看页面...', 0);
                    setTimeout(function () { window.location.href = jumpurl; }, 800);
                } else {
                    show_msg("上传失败：" + (json.msg || "服务器处理错误"), 1);
                }
            } catch (e) {
                show_msg("上传失败：服务器返回格式错误，原始数据：" + data, 1);
            }
        };

        // 上传失败回调
        xhr.onerror = function (evt) {
            show_msg('上传失败：网络异常或服务器无响应，请稍后重试', 1);
        };

        // 上传进度更新
        xhr.upload.onprogress = progressFunction;

        // 上传开始回调
        xhr.upload.onloadstart = function () {
            isBlock = true;
            ot = new Date().getTime();
            oloaded = 0;
            // 初始化进度条UI
            $("#progressBar").html(`
                <div class="progress progress-striped active">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <div class="row">
                    <div class="col-xs-3" style="text-align:left;" id="percentage">0%</div>
                    <div class="col-xs-6 filename">${fileObj.name} (${filesize})</div>
                    <div class="col-xs-3" style="text-align:right;" id="uploadspeed">0 KB/s</div>
                </div>
            `);
        };

        // 发送请求
        xhr.send(form);

    } catch (e) {
        show_msg("上传初始化失败：" + e.message, 1);
        isBlock = false;
    }
}

// 取消上传
function cancleUploadFile() {
    if (xhr) {
        xhr.abort();
        xhr = null;
    }
    $("#progressBar").html("");
    isBlock = false;
    show_msg("已中止上传", 0);
}

// 上传进度更新函数
function progressFunction(evt) {
    try {
        if (evt.lengthComputable) {
            var percentage = Math.round((evt.loaded / evt.total) * 100);
            $(".progress-bar").css("width", percentage + "%");
            $("#percentage").html(percentage + "%");
        }

        // 计算上传速度和剩余时间
        var nt = new Date().getTime();
        var pertime = (nt - ot) / 1000; // 时间差（秒）
        pertime = pertime < 0.01 ? 0.01 : pertime; // 避免除以0

        var perload = evt.loaded - oloaded; // 本次上传字节数
        oloaded = evt.loaded; // 更新已上传总量

        // 计算速度
        var speed = perload / pertime;
        var bspeed = speed; // 原始速度（B/s）
        var units = 'B/s';
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
        var resttime = (evt.total - evt.loaded) / bspeed;
        resttime = resttime < 0.1 ? 0.1 : resttime; // 避免显示0秒

        // 更新UI
        $("#uploadspeed").html(`${speed}${units}，剩余约${resttime.toFixed(1)}秒`);
        if (evt.loaded === evt.total) {
            $(".progress-bar").html('正在保存文件...');
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
    $("#progressBar").hide();
    $("#progressBar").html(`
        <div class="alert alert-dismissible alert-${error ? 'danger' : 'success'}">
            <strong>${msg}</strong>
            ${error ? '<button onclick="retryUpload()" class="btn btn-sm btn-primary ml-2">重试</button>' : ''}
        </div>
    `);
    $("#progressBar").fadeIn();
}

// 重试上传（复用已选择的文件）
function retryUpload() {
    var fileInput = $("#file")[0];
    if (fileInput && fileInput.files.length > 0) {
        UploadFile(); // 直接重新上传
    } else {
        // 若文件已丢失，重新触发文件选择
        $("#file").trigger("click");
    }
}

// 页面加载完成后初始化
$(document).ready(function () {
    // 绑定上传按钮点击事件
    $("#uploadFile").click(function () {
        if (isBlock) {
            show_msg("正在上传中，请不要重复点击", 1);
            return;
        }
        // 创建文件输入框（视觉隐藏而非完全隐藏，避免浏览器拦截）
        $("#upload_block").html(`
            <input type="file" id="file" name="myfile" onchange="UploadFile()" 
                   style="position:absolute; top:-9999px; left:-9999px; opacity:0; z-index:100;"/>
        `);
        // 触发文件选择对话框
        $("#file").trigger("click");
    });

    // 绑定密码复选框事件
    $("#ispwd").click(function () {
        if ($(this).prop("checked")) {
            $("#pwd_frame").show();
        } else {
            $("#pwd_frame").hide();
        }
    });

    // 页面卸载时处理未完成的上传
    $(window).on("beforeunload", function () {
        if (isBlock) {
            return "正在上传文件，确定要离开吗？";
        }
    });
});
