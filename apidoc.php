<?php
include("./includes/common.php");
$title = "API文档 - " . $conf['title'];
include SYSTEM_ROOT.'header1.php';

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';
?>

<main class="flex-1">
  <div class="max-w-6xl mx-auto px-4 md:px-6 py-8 space-y-6">

    <!-- API 状态卡 -->
    <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-windows-color/10 flex items-center justify-center">
              <i class="fa fa-code text-windows-color text-xl"></i>
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-800">API 说明文档</h2>
              <p class="text-sm text-gray-500 mt-0.5">上传接口地址：<code class="bg-gray-100 px-2 py-0.5 rounded text-windows-color font-mono text-xs"><?php echo $siteurl?>api.php</code></p>
            </div>
          </div>
          <?php if ($conf['api_open'] == 1) { ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
              <i class="fa fa-check-circle"></i> 当前 API 已启用
            </span>
          <?php } else { ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
              <i class="fa fa-exclamation-circle"></i> 当前 API 未启用（请联系管理员）
            </span>
          <?php } ?>
        </div>
      </div>
      <div class="px-6 py-5 text-sm text-gray-600 leading-relaxed">
        支持 <span class="font-medium text-gray-800">JSON</span>、<span class="font-medium text-gray-800">JSONP</span>、<span class="font-medium text-gray-800">FORM</span> 三种返回方式，支持 Web 跨域调用，也可在程序中直接调用。请求方式：<code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono text-xs">POST multipart/form-data</code>。
      </div>
    </div>

    <!-- 请求参数 -->
    <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2"><i class="fa fa-list text-windows-color"></i> 请求参数说明</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-gray-700">
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">字段名</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">变量名</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">必填</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">示例值</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">描述</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">文件</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">file</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-red-50 border border-red-200 text-red-600 text-xs">是</span></td><td class="px-5 py-3 text-gray-500">-</td><td class="px-5 py-3 text-gray-600">multipart 格式文件</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">是否首页显示</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">show</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">1</td><td class="px-5 py-3 text-gray-600">默认为是</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">是否设置密码</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">ispwd</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">0</td><td class="px-5 py-3 text-gray-600">默认为否</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">下载密码</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">pwd</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">123456</td><td class="px-5 py-3 text-gray-600">默认留空</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">返回格式</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">format</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">json</td><td class="px-5 py-3 text-gray-600">json / jsonp / form，默认 json</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">跳转页面 URL</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">backurl</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">http://...</td><td class="px-5 py-3 text-gray-600">仅 form 格式有效</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">Callback</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">callback</td><td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">否</span></td><td class="px-5 py-3 text-gray-500">callback</td><td class="px-5 py-3 text-gray-600">仅 jsonp 格式有效</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 返回参数 -->
    <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2"><i class="fa fa-reply text-windows-color"></i> 返回参数说明</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-gray-700">
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">字段名</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">变量名</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">类型</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">示例值</th>
              <th class="px-5 py-3 text-left font-semibold border-b border-gray-100">描述</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">上传状态</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">code</td><td class="px-5 py-3 text-gray-600">Int</td><td class="px-5 py-3 text-gray-500 font-mono text-xs">0</td><td class="px-5 py-3 text-gray-600">0 成功，其他失败</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">提示信息</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">msg</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500">上传成功！</td><td class="px-5 py-3 text-gray-600">失败时返回错误描述</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">文件 MD5</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">hash</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500 font-mono text-xs text-[11px]">f1e807cb…</td><td class="px-5 py-3 text-gray-600">-</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">文件名称</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">name</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500 font-mono text-xs">example.jpg</td><td class="px-5 py-3 text-gray-600">-</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">文件大小</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">size</td><td class="px-5 py-3 text-gray-600">Int</td><td class="px-5 py-3 text-gray-500 font-mono text-xs">58937</td><td class="px-5 py-3 text-gray-600">单位：字节</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">文件格式</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">type</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500 font-mono text-xs">jpg</td><td class="px-5 py-3 text-gray-600">-</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">下载地址</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">downurl</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500">http://...</td><td class="px-5 py-3 text-gray-600">-</td></tr>
            <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-medium text-gray-800">预览地址</td><td class="px-5 py-3 font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded">viewurl</td><td class="px-5 py-3 text-gray-600">String</td><td class="px-5 py-3 text-gray-500">http://...</td><td class="px-5 py-3 text-gray-600">仅图/音/视有值</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 删除 API -->
    <div class="bg-white rounded-xl shadow-windows border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2"><i class="fa fa-trash text-red-500"></i> 文件删除接口（DELETE）</h3>
      </div>
      <div class="px-6 py-5 space-y-4 text-sm">
        <div class="text-gray-600 leading-relaxed">请求方式：<code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono text-xs">DELETE application/x-www-form-urlencoded</code>，必要参数 <code class="bg-gray-50 px-1.5 py-0.5 rounded font-mono text-xs">hash</code>，若文件设置了密码还需提交 <code class="bg-gray-50 px-1.5 py-0.5 rounded font-mono text-xs">file_pwd</code>。</div>
        <div class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto">
<pre class="font-mono text-xs leading-relaxed"><code>curl -X DELETE \
  -d "hash=3a78c73f88489c8f33c0e430683b5d5&file_pwd=123456" \
  <?php echo $siteurl?>api.php</code></pre>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-gray-500 mb-1.5 font-medium">成功响应</div>
            <div class="bg-gray-900 text-gray-100 rounded-lg p-3 overflow-x-auto">
<pre class="font-mono text-xs leading-relaxed"><code>{
  "code": 0,
  "msg": "文件删除成功"
}</code></pre>
            </div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1.5 font-medium">失败响应</div>
            <div class="bg-gray-900 text-gray-100 rounded-lg p-3 overflow-x-auto">
<pre class="font-mono text-xs leading-relaxed"><code>{
  "code": -1,
  "msg": "文件密码错误"
}</code></pre>
            </div>
          </div>
        </div>
        <div class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 flex gap-2 items-start">
          <i class="fa fa-lightbulb-o mt-0.5"></i>
          <span>若文件未设密码，只能通过后台管理界面删除。</span>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include SYSTEM_ROOT . 'footer1.php'; ?>
