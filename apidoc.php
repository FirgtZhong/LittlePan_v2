<?php
include("./includes/common.php");
$title = "API文档 - " . $conf['title'];
include SYSTEM_ROOT.'header1.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
$api_referer1 = $conf['api_referer'];
?>

<main id="main-container"><div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden mx-5"> 
<h2 style="text-align:center;">API说明文档</h2><p style="text-align:center;">
检测 : <?php
$t = $api_referer1;
if ($t = "1") {
echo "当前API已启用！";
} else {
echo "当前API未启用！请联系管理员设置";
}
?>
<pre>
<span style="text-decoration: underline;"><strong><span style="font-size: 18px;">API接口地址：<?php echo $siteurl?>api.php</span></strong></span>

当前API支持JSON、JSONP、FORM 3种返回方式，支持Web跨域调用，也支持程序中直接调用。

请求方式：POST multipart/form-data

请求参数说明：
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>字段名</th>
<th>变量名</th>
<th>是否必填</th>
<th>示例值</th>
<th>描述</th>
</tr>
</thead>
<tbody>
<tr>
<td>文件</td>
<td>file</td>
<td>是</td>
<td></td>
<td>multipart格式文件</td>
</tr>
<tr>
<td>是否首页显示</td>
<td>show</td>
<td>否</td>
<td>1</td>
<td>默认为是</td>
</tr>
<tr>
<td>是否设置密码</td>
<td>ispwd</td>
<td>否</td>
<td>0</td>
<td>默认为否</td>
</tr>
<tr>
<td>下载密码</td>
<td>pwd</td>
<td>否</td>
<td>123456</td>
<td>默认留空</td>
</tr>
<tr>
<td>返回格式</td>
<td>format</td>
<td>否</td>
<td>json</td>
<td>有json、jsonp、form三种选择 默认为json</td>
</tr>
<tr>
<td>跳转页面url</td>
<td>backurl</td>
<td>否</td>
<td>http://...</td>
<td>上传成功后的跳转地址 只在form格式有效</td>
</tr>
<tr>
<td>callback</td>
<td>callback</td>
<td>否</td>
<td>callback</td>
<td>只在jsonp格式有效</td>
</tr>
</tbody>
</table>
返回参数说明：
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>字段名</th>
<th>变量名</th>
<th>类型</th>
<th>示例值</th>
<th>描述</th>
</tr>
</thead>
<tbody>
<tr>
<td>上传状态</td>
<td>code</td>
<td>Int</td>
<td>0</td>
<td>0为成功，其他为失败</td>
</tr>
<tr>
<td>提示信息</td>
<td>msg</td>
<td>String</td>
<td>上传成功！</td>
<td>如果上传失败会有错误提示</td>
</tr>
<tr>
<td>文件MD5</td>
<td>hash</td>
<td>String</td>
<td>f1e807cb0d6ba52d71bdb02864e6bda8</td>
<td></td>
</tr>
<tr>
<td>文件名称</td>
<td>name</td>
<td>String</td>
<td>exapmle1.jpg</td>
<td></td>
</tr>
<tr>
<td>文件大小</td>
<td>size</td>
<td>Int</td>
<td>58937</td>
<td>单位：字节</td>
</tr>
<tr>
<td>文件格式</td>
<td>type</td>
<td>String</td>
<td>jpg</td>
<td></td>
</tr>
<tr>
<td>下载地址</td>
<td>downurl</td>
<td>String</td>
<td>http://.....</td>
<td></td>
</tr>
<tr>
<td>预览地址</td>
<td>viewurl</td>
<td>String</td>
<td>http://.....</td>
<td>只有图片、音乐、视频文件才有</td>
</tr>
</tbody>
</table>
<h2><span style="font-size: 24px;">使用教程</span></h2>
调用本API可以使用多种方法，以下是几种常见的实现方式，附带示例代码：
<h3>方法一：使用cURL（推荐）</h3>
cURL是PHP中处理HTTP请求的标准扩展，功能强大且灵活：
<pre class="language-php"><code>&lt;?php
// 要上传的本地文件路径
$filePath = '/path/to/local/file.jpg';

// 检查文件是否存在
if (!file_exists($filePath)) {
    die("文件不存在: $filePath");
}

// API请求URL
$apiUrl = '<?php echo $siteurl?>api.php';

// 创建cURL句柄
$ch = curl_init();

// 准备表单数据
$postData = [
    'file' =&gt; new CURLFile($filePath), // PHP 5.5+ 使用CURLFile类
    'format' =&gt; 'json',
    'ispwd' =&gt; 1,
    'pwd' =&gt; 'SecurePass123'
];

// 设置cURL选项
curl_setopt_array($ch, [
    CURLOPT_URL =&gt; $apiUrl,
    CURLOPT_POST =&gt; true,
    CURLOPT_POSTFIELDS =&gt; $postData,
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HEADER =&gt; false,
    CURLOPT_SSL_VERIFYPEER =&gt; true, // 验证SSL证书
    CURLOPT_SSL_VERIFYHOST =&gt; 2,    // 验证主机名
    CURLOPT_TIMEOUT =&gt; 60           // 请求超时时间（秒）
]);

// 执行请求
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 检查是否有错误
if (curl_errno($ch)) {
    die('cURL错误: ' . curl_error($ch));
}

// 关闭cURL句柄
curl_close($ch);

// 处理响应
if ($httpCode === 200) {
    $result = json_decode($response, true);
    if ($result &amp;&amp; $result['code'] === 0) {
        echo "文件上传成功！\n";
        echo "下载URL: {$result['downurl']}\n";
        echo "预览URL: {$result['viewurl']}\n";
    } else {
        echo "上传失败: {$result['msg']}\n";
    }
} else {
    echo "HTTP请求失败，状态码: $httpCode\n";
    echo "响应内容: $response\n";
}
?&gt;
</code></pre>
<h3>方法二：使用file_get_contents（简单场景）</h3>
对于简单的上传需求，可以使用<code>file_get_contents</code>配合<code>stream_context_create</code>：
<pre class="language-php"><code>&lt;?php
$filePath = '/path/to/local/file.jpg';
$apiUrl = '<?php echo $siteurl?>api.php';

// 创建表单数据
$boundary = uniqid();
$delimiter = '--' . $boundary;

// 构建请求体
$fileData = file_get_contents($filePath);
$fileName = basename($filePath);

$postData = "--$boundary\r\n";
$postData .= "Content-Disposition: form-data; name=\"file\"; filename=\"$fileName\"\r\n";
$postData .= "Content-Type: application/octet-stream\r\n\r\n";
$postData .= $fileData . "\r\n";
$postData .= "--$boundary\r\n";
$postData .= "Content-Disposition: form-data; name=\"format\"\r\n\r\n";
$postData .= "json\r\n";
$postData .= "--$boundary\r\n";
$postData .= "Content-Disposition: form-data; name=\"ispwd\"\r\n\r\n";
$postData .= "1\r\n";
$postData .= "--$boundary\r\n";
$postData .= "Content-Disposition: form-data; name=\"pwd\"\r\n\r\n";
$postData .= "SecurePass123\r\n";
$postData .= "--$boundary--\r\n";

// 设置HTTP上下文选项
$options = [
    'http' =&gt; [
        'method' =&gt; 'POST',
        'header' =&gt; "Content-Type: multipart/form-data; boundary=$boundary\r\n" .
                    "Content-Length: " . strlen($postData) . "\r\n",
        'content' =&gt; $postData,
        'timeout' =&gt; 60
    ]
];

// 创建上下文
$context = stream_context_create($options);

// 发送请求
$response = file_get_contents($apiUrl, false, $context);

// 处理响应
if ($response) {
    $result = json_decode($response, true);
    if ($result &amp;&amp; $result['code'] === 0) {
        echo "上传成功: {$result['msg']}\n";
        echo "下载链接: {$result['downurl']}\n";
    } else {
        echo "上传失败: {$result['msg']}\n";
    }
} else {
    echo "请求失败\n";
}
?&gt;
</code></pre>
<h3>方法三：使用HTTP客户端库（如Guzzle）</h3>
对于更复杂的场景，推荐使用第三方库Guzzle：
<pre class="language-php"><code>&lt;?php
require 'vendor/autoload.php'; // 引入Guzzle

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

$filePath = '/path/to/local/file.jpg';
$apiUrl = '<?php echo $siteurl?>api.php';

// 创建HTTP客户端
$client = new Client();

try {
    // 发送请求
    $response = $client-&gt;post($apiUrl, [
        'multipart' =&gt; [
            [
                'name' =&gt; 'file',
                'contents' =&gt; fopen($filePath, 'r'),
                'filename' =&gt; basename($filePath)
            ],
            [
                'name' =&gt; 'format',
                'contents' =&gt; 'json'
            ],
            [
                'name' =&gt; 'ispwd',
                'contents' =&gt; '1'
            ],
            [
                'name' =&gt; 'pwd',
                'contents' =&gt; 'SecurePass123'
            ]
        ]
    ]);

    // 处理响应
    $result = json_decode($response-&gt;getBody(), true);
    if ($result &amp;&amp; $result['code'] === 0) {
        echo "上传成功: {$result['msg']}\n";
        echo "下载链接: {$result['downurl']}\n";
    } else {
        echo "上传失败: {$result['msg']}\n";
    }
} catch (\Exception $e) {
    echo "请求异常: " . $e-&gt;getMessage() . "\n";
}
?&gt;
</code></pre>
<h3>使用注意事项</h3>
<ol>
 	<li><strong>文件路径</strong>：确保PHP进程有读取本地文件的权限。</li>
 	<li><strong>超时设置</strong>：大文件上传时，建议增加超时时间（如<code>CURLOPT_TIMEOUT</code>）。</li>
 	<li><strong>错误处理</strong>：生产环境中应完善错误日志记录，例如：
<pre class="language-php"><code>// 记录错误日志
error_log("API请求失败: " . json_encode($result));
</code></pre>
</li>
 	<li><strong>SSL验证</strong>：生产环境务必保持<code>CURLOPT_SSL_VERIFYPEER</code>为<code>true</code>，避免中间人攻击。</li>
 	<li><strong>依赖安装</strong>：使用Guzzle时，需先通过Composer安装：
<pre class="language-bash"><code>composer require guzzlehttp/guzzle
</code></pre>
</li>
</ol>
<h3>典型应用场景</h3>
<ol>
 	<li><strong>批量文件上传</strong>：遍历目录中的文件，逐个调用API上传。</li>
 	<li><strong>定时同步</strong>：通过定时任务，将服务器本地文件同步到文件存储系统。</li>
 	<li><strong>CMS集成</strong>：在内容管理系统中，通过后台上传文件到指定存储。</li>
</ol>
根据您的具体需求，选择合适的方法实现即可。
cURL是最通用的方案，Guzzle则提供了更友好的面向对象接口。
<h3>使用API进行文件删除</h3>
我在原有的带密码功能基础上进行了改进，
增加了删除文件时的必要验证：
<ul>
 	<li>DELETE请求参数：</li>
 	<li><code>file_pwd</code>: 文件上传时设置的密码（删除时必选，需要文件设置了密码）</li>
</ul>
使用方法：
<ul>
 	<li>如果系统没有设置全局删除密码，
 	只需提供文件密码：</li>
</ul>
<pre class="language-bash"><code>curl -X DELETE \
  -d "hash=3a78c73f88489c8f33c0e430683b5d5&amp;file_pwd=123456" \
  <?php echo $siteurl?>api.php
</code></pre>
响应示例：
<pre class="language-json"><code>{
  "code": 0,
  "msg": "文件删除成功"
}
</code></pre>
错误响应示例：
<pre class="language-json"><code>{
  "code": -1,
  "msg": "文件密码错误"
}
</code></pre>
这样就实现了对设置了密码的文件进行输入密码删除的功能，
同时保留了系统管理员删除验证。
如果您的文件未设密码，又希望删除的话，
可以在管理界面中删除。
</pre>
</p>
</div></main>

<?php include SYSTEM_ROOT . 'footer1.php'; ?>
