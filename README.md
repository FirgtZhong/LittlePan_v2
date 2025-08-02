> 本文将详细介绍如何从零构建一个基于PHP和MySQL的文件管理系统，分解项目代码并剖析每个模块的功能。我们将以`index.php`、`config.php`和`api.php`这三个核心文件为例，详细展示如何设计文件列表、数据库配置和文件上传接口，从而实现一个完整的文件管理系统。该文章可以作为学术研究和代码实现的参考。


## 系统架构概述

本系统是一个典型的Web应用，由PHP脚本、MySQL数据库和HTML/CSS前端组件构成。PHP用于处理文件的上传、下载和展示，MySQL用于存储文件的元信息，前端组件提供用户界面。系统主要分为以下几个模块：

1. **主界面** - 用于展示文件列表和操作按钮。
2. **数据库配置** - 配置MySQL数据库连接信息。
3. **文件上传API** - 提供文件上传接口，处理文件存储和防重复上传。

## 数据库设计

数据库的设计主要围绕文件的元信息展开。以下是一个简单的文件表结构：

| 字段       | 类型         | 描述             |
|------------|--------------|------------------|
| id         | INT          | 文件唯一标识     |
| name       | VARCHAR(255) | 文件名称         |
| type       | VARCHAR(10)  | 文件类型         |
| size       | INT          | 文件大小         |
| hash       | VARCHAR(32)  | 文件的MD5哈希值  |
| addtime    | DATETIME     | 文件上传时间     |
| ip         | VARCHAR(15)  | 上传者IP地址     |
| hide       | TINYINT      | 文件是否隐藏     |
| pwd        | VARCHAR(50)  | 文件下载密码     |

该表结构支持存储文件的各种必要信息，例如名称、类型、大小、上传时间等，以便后续实现文件的检索、下载和权限管理。

## 代码实现

接下来，我们逐步剖析每个主要文件的代码实现，解释其中的逻辑和关键点。

### 1. `index.php` - 文件列表和操作主界面
![home.png](https://www.1042.net/usr/uploads/2024/11/1674080678.png)
`index.php`文件是系统的核心界面，展示已上传的文件，并提供文件的下载和预览功能。
#### 代码分析

```php
<?php
include("./includes/common.php");
$title = $conf['title'];
include SYSTEM_ROOT.'header.php';
$numrows = $DB->getColumn("SELECT count(*) FROM pre_file WHERE hide=0");
?>
```
- **引入公共文件**：通过`include`引入`common.php`和`header.php`文件。`common.php`通常包含数据库连接和其他公用函数，`header.php`则用于页面的头部布局和CSS、JS等资源的引用。
- **文件总数查询**：通过数据库查询获取所有公开文件的总数，并保存在变量`$numrows`中，以便在页面上显示文件的数量。

#### 主页面布局

```html
<main id="main-container">
    <div class="bg-image" style="background-image: url('assets/media/photos/photo36@2x.jpg');">
        <div class="bg-primary-dark-op py-9 overflow-hidden">
            <div class="content content-full text-center">
                <h1 class="display-4 fw-semibold text-white mb-2"><?php echo $title; ?></h1>
                <p class="fs-4 fw-normal text-white-50 mb-5">共有 <?php echo $numrows; ?> 个文件</p>
                <div>
                    <a class="btn btn-primary px-3 py-2 m-1" href="/upload.php">
                        <i class="fa fa-fw fa-link opacity-50 me-1"></i> 立即开始
                    </a>
                    <a class="btn btn-dark px-3 py-2 m-1" href="javascript:void(0)">
                        <i class="fa fa-fw fa-link opacity-50 me-1"></i> 联系客服
                    </a>
                </div>
            </div>
        </div>
    </div>
```

- **页面标题和背景**：页面顶部显示系统标题和总文件数量，并采用自定义背景图。
- **按钮设置**：提供“立即开始”按钮，链接到上传页面，方便用户上传新文件。
#### 文件列表展示与分页功能
```php
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
            echo '<tr><td><b>' . $i++ . '</b></td><td><a href="' . $fileurl . '">下载</a>｜<a href="' . $viewurl . '" target="_blank">查看</a></td><td><i class="fa ' . type_to_icon($res['type']) . ' fa-fw"></i>' . $res['name'] . '</td><td>' . size_format($res['size']) . '</td><td><font color="blue">' . ($res['type'] ? $res['type'] : '未知') . '</font></td><td>' . $res['addtime'] . '</td><td>' . preg_replace('/\\d+$/', '*', $res['ip']) . '</td></tr>';
        }
        ?>
    </tbody>
</table>
```
- **分页控制**：使用`$pagesize`定义每页显示15个文件，通过数据库查询和计算偏移量`$offset`，实现文件的分页显示。
- **文件操作按钮**：为每个文件提供“下载”和“查看”按钮，链接由文件的哈希值生成。
- **IP地址模糊处理**：为了保护隐私，使用正则表达式隐藏IP地址的最后几位。

---

### 2. `config.php` - 数据库连接配置

`config.php`文件存储了系统的数据库配置信息，用于连接和访问MySQL数据库。

```php
<?php
/* 数据库配置 */
$dbconfig = array(
    "host" => "localhost", // 数据库服务器地址
    "port" => 3306,        // 数据库端口
    "user" => "",     // 数据库用户名
    "pwd" => "", // 数据库密码
    "dbname" => ""    // 数据库名称
);
?>
```
- **连接参数**：配置数据库的主机地址、端口号、用户名、密码和数据库名称。这些信息在系统初始化时加载，使系统能够与MySQL数据库通信。
- **安全性提示**：在实际应用中，应保护数据库的密码信息，确保配置文件的权限和访问控制。

---

### 3. `api.php` - 文件上传API

`api.php`文件实现了文件的上传功能，是系统的核心接口，支持文件上传、验证和存储。

#### API接口设置与文件验证

```php
<?php
$nosession = true;
$nosecu = true;
include("./includes/common.php");

function showresult($arr, $format='json') {
    ...
}
```

- **接口配置**：`$nosession`和`$nosecu`的设置使接口在无会话和安全检查模式下运行。
- **结果输出函数**：`showresult`函数支持以`JSON`或`JSONP`格式返回数据，确保API在不同客户端的兼容性。

#### 文件上传的核心逻辑

```php
if (!isset($_FILES['file'])) showresult(['code' => -1, 'msg' => '请选择文件']);
$name = trim(htmlspecialchars($_FILES['file']['name']));
$size = intval($_FILES['file']['size']);
$hash = md5_file($_FILES['file']['tmp_name']);

$row = $DB->getRow("SELECT * FROM pre_file WHERE hash=:hash", [':hash' => $hash]);
if ($row) {
    showresult(['code' => 0, 'msg' => '本站已存在该文件', 'exists' => 1, ...]);
}
```

- **文件存在性检查**：生成文件的MD5哈希值，通过数据库查询检查是否已存在同一文件。若文件存在，则返回文件信息，避免重复上传。
- **文件名称过滤**：清理文件名中的无效字符，确保安全性。

#### 文件存储与响应输出

```php
$result = $stor->upload($hash, $_FILES['file']['tmp_name']);
if (!$result) showresult(['code' => -1, 'msg' => '文件上传失败', 'error' => 'stor']);

$id = $DB->lastInsertId();
showresult([
    'code' => 0,
    'msg' => '文件上传成功！',
    'exists' => 0,
    'hash' => $hash,
    ...
]);
```

- **文件保存与数据库插入**：`upload`方法负责将文件存储到服务器的指定目录中，同时将文件的相关信息如名称、大小、哈希值等插入数据库。
- **返回结果**：上传成功后返回包含下载链接的JSON对象，供前端页面使用。

## 总结

本文详细分析了如何从零构建一个基于PHP和MySQL的文件管理系统，涉及文件上传、数据库配置和文件列表展示等关键模块的实现。希望该教程能为读者提供一个开发文件管理系统的思路和实现细节参考，适用于学术研究和实践项目。

此系统在实际应用中还可以进一步扩展，比如添加文件分类、文件搜索、权限管理等功能，以适应更广泛的需求。
