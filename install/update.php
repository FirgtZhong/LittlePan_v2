<?php
// LittlePan_v2 增量更新程序
// 优先读取 config.php，若配置为空或连接失败则允许手动输入
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@header('Content-Type: text/html; charset=UTF-8');

$configFile = '../config.php';
$lockFile = 'install.lock';
$updateSqlFile = 'update.sql';

// 检查安装锁
if (!file_exists($lockFile)) {
    exit('<div style="text-align:center;padding:50px;">
        <h3>未检测到安装锁</h3>
        <p>请先完成安装后再进行更新操作。</p>
        <p><a href="index.php">前往安装</a></p>
    </div>');
}

// 读取数据库配置（可能为空）
$dbconfig = array('host'=>'localhost', 'port'=>3306, 'user'=>'', 'pwd'=>'', 'dbname'=>'');
if (file_exists($configFile)) {
    require $configFile;
}

$action = isset($_POST['action']) ? $_POST['action'] : null;

// 执行更新
if ($action == 'update') {
    // 优先使用表单提交的数据库信息，否则用 config.php 的
    $db_host = isset($_POST['db_host']) && $_POST['db_host'] ? $_POST['db_host'] : $dbconfig['host'];
    $db_port = isset($_POST['db_port']) && $_POST['db_port'] ? $_POST['db_port'] : $dbconfig['port'];
    $db_user = isset($_POST['db_user']) && $_POST['db_user'] ? $_POST['db_user'] : $dbconfig['user'];
    $db_pwd  = isset($_POST['db_pwd']) ? $_POST['db_pwd'] : $dbconfig['pwd'];
    $db_name = isset($_POST['db_name']) && $_POST['db_name'] ? $_POST['db_name'] : $dbconfig['dbname'];

    // 如果用户名或密码为空，显示输入表单
    if (empty($db_user) || empty($db_name)) {
        $needManual = true;
        $errorMsg = '数据库配置不完整（用户名或数据库名为空），请手动填写数据库信息。';
    } else {
        try {
            $db = new PDO("mysql:host={$db_host};dbname={$db_name};port={$db_port}", $db_user, $db_pwd);
        } catch (PDOException $e) {
            $needManual = true;
            $errorMsg = '链接数据库失败: ' . $e->getMessage() . '<br>请检查数据库信息是否正确。';
        }
    }

    if (empty($needManual)) {
        $success = 0;
        $error = 0;
        $errorMsg = null;
        $details = array();

        // 如果手动填写的配置与 config.php 不同，则写回 config.php
        if ($db_user != $dbconfig['user'] || $db_pwd != $dbconfig['pwd'] || $db_name != $dbconfig['dbname'] || $db_host != $dbconfig['host'] || $db_port != $dbconfig['port']) {
            $configContent = '<?php'."\n".'/*数据库配置*/'."\n".'$dbconfig=array('."\n"
                ."\t".'"host" => "'.$db_host.'", //数据库服务器'."\n"
                ."\t".'"port" => '.$db_port.', //数据库端口'."\n"
                ."\t".'"user" => "'.$db_user.'", //数据库用户名'."\n"
                ."\t".'"pwd" => "'.$db_pwd.'", //数据库密码'."\n"
                ."\t".'"dbname" => "'.$db_name.'" //数据库名'."\n"
                .');'."\n".'?>'."\n";
            if (is_writable($configFile) || is_writable(dirname($configFile))) {
                file_put_contents($configFile, $configContent);
                $details[] = '数据库配置已写入 config.php';
            } else {
                $errorMsg .= 'config.php 不可写，请手动修改 config.php 填入数据库信息。<br>';
            }
        }

        $db->exec("set names utf8");
        $db->exec("set sql_mode = ''");

        // ========== 第一部分：执行 update.sql ==========
        if (file_exists($updateSqlFile)) {
            $sqls = file_get_contents($updateSqlFile);
            $sqls = explode(';', $sqls);
            foreach ($sqls as $value) {
                $value = trim($value);
                if (empty($value) || strpos($value, '--') === 0) continue;
                $lines = explode("\n", $value);
                $cleanLines = array();
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (strpos($line, '--') === 0 || empty($line)) continue;
                    $cleanLines[] = $line;
                }
                $value = implode(' ', $cleanLines);
                if (empty($value)) continue;

                if ($db->exec($value) === false) {
                    $error++;
                    $dberror = $db->errorInfo();
                    if ($dberror[1] != 1060 && $dberror[1] != 1050) {
                        $errorMsg .= $dberror[2] . "<br>";
                    }
                } else {
                    $success++;
                }
            }
        }

        // ========== 第二部分：PHP判断并添加缺失的字段 ==========
        $columns = $db->query("SHOW COLUMNS FROM `pre_file` LIKE 'block'");
        if ($columns && $columns->fetch() === false) {
            $db->exec("ALTER TABLE `pre_file` ADD COLUMN `block` int(1) NOT NULL DEFAULT '0'");
            $success++;
            $details[] = 'pre_file 表新增 block 字段';
        }

        $columns = $db->query("SHOW COLUMNS FROM `pre_file` LIKE 'hide'");
        if ($columns && $columns->fetch() === false) {
            $db->exec("ALTER TABLE `pre_file` ADD COLUMN `hide` int(1) NOT NULL DEFAULT '0'");
            $success++;
            $details[] = 'pre_file 表新增 hide 字段';
        }

        $columns = $db->query("SHOW COLUMNS FROM `pre_file` LIKE 'count'");
        if ($columns && $columns->fetch() === false) {
            $db->exec("ALTER TABLE `pre_file` ADD COLUMN `count` int(11) unsigned NOT NULL DEFAULT '0'");
            $success++;
            $details[] = 'pre_file 表新增 count 字段';
        }

        $index = $db->query("SHOW INDEX FROM `pre_file` WHERE Key_name = 'hash'");
        if ($index && $index->fetch() === false) {
            $db->exec("ALTER TABLE `pre_file` ADD INDEX `hash` (`hash`)");
            $success++;
            $details[] = 'pre_file 表新增 hash 索引';
        }

        // 更新版本号
        $date = date("Y-m-d");
        $db->exec("INSERT INTO `pre_config` VALUES ('build', '{$date}') ON DUPLICATE KEY UPDATE `v`='{$date}'");
        $success++;

        $step = 3;
    }
}

// 判断是否需要显示手动输入表单
$needManual = isset($needManual) ? $needManual : (empty($dbconfig['user']) || empty($dbconfig['dbname']));
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <title>LittlePan外链网盘 - 更新程序</title>
    <link href="//cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container"><br>
    <div class="row">
        <div class="col-xs-12">
            <pre><h4>LittlePan外链网盘 - 增量更新程序</h4></pre>
        </div>
        <div class="col-xs-12">
            <div class="panel panel-info">
                <?php if (isset($errorMsg) && $errorMsg) { ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php } ?>

                <?php if (isset($step) && $step == 3) { ?>
                    <!-- 更新完成页面 -->
                    <div class="panel-heading text-center">更新完成</div>
                    <div class="panel-body">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-success">成功执行 <?php echo $success; ?> 条SQL语句，失败 <?php echo $error; ?> 条</li>
                            <?php if (!empty($details)) { ?>
                            <li class="list-group-item">
                                <b>字段检查结果：</b>
                                <ul>
                                    <?php foreach ($details as $detail) { ?>
                                        <li><?php echo $detail; ?></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if (empty($details) && $error == 0) { ?>
                            <li class="list-group-item">数据库已是最新版本，无需更新</li>
                            <?php } ?>
                            <li class="list-group-item">更新日期：<?php echo $date; ?></li>
                            <a href="../" class="btn btn-success list-group-item">进入网站首页</a>
                            <a href="../admin/" class="btn list-group-item">进入管理后台</a>
                        </ul>
                    </div>
                <?php } elseif ($needManual) { ?>
                    <!-- 手动输入数据库信息 -->
                    <div class="panel-heading text-center">请填写数据库信息</div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            未从 config.php 读取到完整的数据库配置，请手动填写。<br>
                            <b>此信息仅用于本次更新，不会保存。</b>
                        </div>
                        <form class="form-horizontal" action="#" method="post">
                            <input type="hidden" name="action" value="update">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">数据库地址</label>
                                <div class="col-sm-9">
                                    <input type="text" name="db_host" class="form-control" value="<?php echo htmlspecialchars($dbconfig['host']); ?>" placeholder="localhost">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">数据库端口</label>
                                <div class="col-sm-9">
                                    <input type="text" name="db_port" class="form-control" value="<?php echo htmlspecialchars($dbconfig['port']); ?>" placeholder="3306">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">数据库用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" name="db_user" class="form-control" value="<?php echo htmlspecialchars($dbconfig['user']); ?>" placeholder="root" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">数据库密码</label>
                                <div class="col-sm-9">
                                    <input type="password" name="db_pwd" class="form-control" value="<?php echo htmlspecialchars($dbconfig['pwd']); ?>" placeholder="数据库密码">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">数据库名称</label>
                                <div class="col-sm-9">
                                    <input type="text" name="db_name" class="form-control" value="<?php echo htmlspecialchars($dbconfig['dbname']); ?>" placeholder="数据库名" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary btn-block">填写完毕，执行更新</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <a href="index.php" class="btn btn-default btn-block">返回安装首页</a>
                    </div>
                <?php } else { ?>
                    <!-- 默认更新确认页面 -->
                    <div class="panel-heading text-center">更新说明</div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <b>增量更新说明：</b><br>
                            此操作将检查并添加新版本的数据库表和配置项，<br>
                            <b>不会修改或删除已有的数据和配置</b>。<br><br>
                            更新内容包括：<br>
                            1. 新增访问统计表（pre_views）<br>
                            2. 新增第三方统计配置项<br>
                            3. 新增首页公告配置项<br>
                            4. 检查并补充缺失的数据库字段
                        </div>
                        <form class="form-horizontal" action="#" method="post">
                            <input type="hidden" name="action" value="update">
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary btn-block">确认执行增量更新</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <a href="index.php" class="btn btn-default btn-block">返回安装首页</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <footer class="footer">
        <pre><center>Powered by <a href="https://github.com/FirgtZhong">FirgtZhong</a>. 源码版本：<a href="https://github.com/FirgtZhong/LittlePan_v2">LittlePan_v2-v1.9.0-RC</a></center></pre>
    </footer>
</div>
</body>
</html>
