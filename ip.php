<?php
function expandIPv6($ipv6) {
    // 预处理：移除空格并转为小写
    $ipv6 = trim(strtolower($ipv6));
    
    // 特殊处理：分组数<8且没有::的压缩地址
    $partCount = count(explode(':', str_replace('::', '', $ipv6)));
    $hasDoubleColon = strpos($ipv6, '::') !== false;
    
    // 如果分组数小于8且没有::，自动添加::表示省略了后续零组
    if ($partCount < 8 && !$hasDoubleColon) {
        $ipv6 .= '::';
        $hasDoubleColon = true;
    }
    
    // 处理单独的::情况
    if ($ipv6 === '::') {
        return '0000:0000:0000:0000:0000:0000:0000:0000';
    }
    
    // 分割地址
    $parts = explode('::', $ipv6);
    $left = isset($parts[0]) && $parts[0] !== '' ? explode(':', $parts[0]) : [];
    $right = isset($parts[1]) && $parts[1] !== '' ? explode(':', $parts[1]) : [];
    
    // 计算需要填充的零组
    $totalParts = count($left) + count($right);
    $zeroPartsNeeded = 8 - $totalParts;
    
    // 填充零组
    $zeroParts = array_fill(0, $zeroPartsNeeded, '0000');
    $fullParts = array_merge($left, $zeroParts, $right);
    
    // 补全每组为4位十六进制
    $expandedParts = [];
    foreach ($fullParts as $part) {
        // 确保不会处理空字符串（边缘情况）
        $cleanPart = $part === '' ? '0' : $part;
        $expandedParts[] = str_pad($cleanPart, 4, '0', STR_PAD_LEFT);
    }
    
    // 确保最终是8组
    if (count($expandedParts) > 8) {
        $expandedParts = array_slice($expandedParts, 0, 8);
    }
    
    return implode(':', $expandedParts);
}

function isValidIPv6($ipv6) {
    // 检查是否只包含有效的字符
    if (!preg_match('/^[0-9a-fA-F:]+$/', $ipv6)) {
        return false;
    }
    
    // 检查::出现次数
    if (substr_count($ipv6, '::') > 1) {
        return false;
    }
    
    // 分割后检查每组长度
    $parts = explode(':', str_replace('::', '', $ipv6));
    foreach ($parts as $part) {
        if ($part !== '' && (strlen($part) > 4 || !preg_match('/^[0-9a-fA-F]+$/', $part))) {
            return false;
        }
    }
    
    return true;
}

/**
 * 将IPv4地址转换为两种IPv6格式
 * 1. IPv4映射地址（::ffff:w.x.y.z）
 * 2. 6to4隧道地址（2002:xxxx:yyyy::）
 */
function ipv4ToIPv6Formats($ipv4) {
    if (!filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return false;
    }
    
    $parts = explode('.', $ipv4);
    $hex = [];
    foreach ($parts as $p) {
        $hex[] = str_pad(dechex((int)$p), 2, '0', STR_PAD_LEFT);
    }
    
    // 1. IPv4映射地址（::ffff:w.x.y.z 格式）
    $mappedFormat = "::ffff:{$parts[0]}.{$parts[1]}.{$parts[2]}.{$parts[3]}";
    $mappedFull = "0000:0000:0000:0000:0000:ffff:{$hex[0]}{$hex[1]}:{$hex[2]}{$hex[3]}";
    
    // 2. 6to4隧道地址（2002:xxxx:yyyy:: 格式）
    $sixToFour = "2002:{$hex[0]}{$hex[1]}:{$hex[2]}{$hex[3]}::";
    
    return [
        'mapped_short' => $mappedFormat,
        'mapped_full' => $mappedFull,
        'sixtofour' => $sixToFour
    ];
}

// 处理URL参数和表单提交
$original = '';
$result = [];
$type = '';

// 优先处理URL参数 (GET请求)
if (isset($_GET['ip']) && !empty($_GET['ip'])) {
    $original = trim($_GET['ip']);
    $autoProcess = true; // 自动处理URL参数
}
// 处理表单提交 (POST请求)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ip'])) {
    $original = trim($_POST['ip']);
    $autoProcess = true;
} else {
    $autoProcess = false;
}

// 自动处理IP地址
if ($autoProcess && !empty($original)) {
    // 先检查IPv4
    if (filter_var($original, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $result = ipv4ToIPv6Formats($original);
        $type = 'ipv4';
    }
    // 再检查IPv6
    elseif (isValidIPv6($original)) {
        $result['expanded'] = expandIPv6($original);
        $type = 'ipv6';
    }
    // 无效地址
    else {
        $result['error'] = "无效的IP地址格式";
        $type = 'invalid';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>IP转换查询器</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .container {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2d3436;
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #0984e3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0652dd;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background: #dfe6e9;
            border: 1px solid #b2bec3;
        }
        .error {
            background: #ffeaa7;
            border: 1px solid #fdcb6e;
        }
        .example {
            color: #636e72;
            font-style: italic;
            margin-top: 5px;
        }
        .api-info {
            margin-top: 30px;
            padding: 15px;
            background: #e8f4fd;
            border-radius: 4px;
            font-size: 0.95em;
        }
        .ipv6-format {
            margin: 10px 0;
            padding: 10px;
            background: rgba(255,255,255,0.7);
            border-radius: 4px;
            word-break: break-all;
        }
        .format-title {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>IP转换查询器</h1>
        <!-- 修改表单为GET方法，解析按钮点击后跳转 -->
        <form id="ipForm" method="get" action="/ip.php">
            <input type="text" name="ip" placeholder="输入IP地址（如240e:476:8c3:45或156.238.128.10）" 
                   value="<?php echo htmlspecialchars($original); ?>">
            <div class="example">
                IPv6示例：240e:476:8c3:45、::1<br>
                IPv4示例：156.238.128.10、192.168.1.1
            </div>
            <!-- 解析按钮点击后将跳转到https://ip.com，并携带ip参数 -->
            <button type="submit">查询</button>
        </form>
        
        <?php if (!empty($result)): ?>
        <div class="result <?php echo $type === 'invalid' ? 'error' : 'success'; ?>">
            <h3><?php 
                if ($type === 'ipv6') echo 'IPv6查询结果';
                elseif ($type === 'ipv4') echo 'IPv4转IPv6结果';
                else echo '错误';
            ?></h3>
            <p><strong>原始地址：</strong><?php echo htmlspecialchars($original); ?></p>
            <p>以下地址点击后跳转IP详细页</p>
            <?php if ($type === 'ipv4' && $result): ?>
                <div class="ipv6-format">
                    <div class="format-title">1. IPv4映射的IPv6地址（简化版）：</div>
                    <div><a href="https://ping0.cc/ip/<?php echo htmlspecialchars($result['mapped_short']); ?>"><?php echo htmlspecialchars($result['mapped_short']); ?></a></div>
                </div>
                <div class="ipv6-format">
                    <div class="format-title">2. 6to4隧道IPv6地址：</div>
                    <div><a href="https://ping0.cc/ip/<?php echo htmlspecialchars($result['sixtofour']); ?>"><?php echo htmlspecialchars($result['sixtofour']); ?></a></div>
                </div>
            <?php elseif ($type === 'ipv6'): ?>
                <div class="ipv6-format">
                    <div class="format-title">完整IPv6地址：</div>
                    <div><a href="https://ping0.cc/ip/<?php echo htmlspecialchars($result['expanded']); ?>"><?php echo htmlspecialchars($result['expanded']); ?></a></div>
                </div>
            <?php else: ?>
                <p><?php echo $result['error']; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="api-info">
            <h4>API接口使用说明</h4>
            <p>可以直接通过URL参数传递IP地址，格式如下：</p>
            <p><code><?php echo $_SERVER['PHP_SELF']; ?>?ip=需要解析的IP地址</code></p>
            <p>示例：</p>
            <ul>
                <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?ip=240e:476:8c3:45" target="_self">
                    解析IPv6地址</a></li>
                <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?ip=156.238.128.10" target="_self">
                    解析IPv4地址</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
    