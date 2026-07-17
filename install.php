<?php
define('IN_BLOG',true);
session_start();
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$err = '';
$success = '';
if(file_exists('config.php')){
    $err = '系统已经安装完成！如需重装请删除config.php文件';
}
if($_SERVER['REQUEST_METHOD'] == 'POST' && $step ==2 && empty($err)){
    $db_host = trim($_POST['db_host']??'');
    $db_user = trim($_POST['db_user']??'');
    $db_pass = trim($_POST['db_pass']??'');
    $db_name = trim($_POST['db_name']??'');
    $admin_user = trim($_POST['admin_user']??'');
    $admin_pwd = trim($_POST['admin_pwd']??'');
    if(empty($db_host)||empty($db_user)||empty($db_name)||empty($admin_user)||empty($admin_pwd)){
        $err = '所有项目不能为空';
    }else{
        try{
            $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4",$db_user,$db_pass,[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
            ]);
            //创建数据库
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db_name}`");
            //管理员表【原有不动】
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `blog_admin`(
                `id` int primary key auto_increment,
                `username` varchar(64) not null unique,
                `password` varchar(255) not null,
                `create_time` int not null
            )engine=InnoDB default charset=utf8mb4;
            ");
            //文章表【原有不动】
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `blog_article`(
                `id` int primary key auto_increment,
                `title` varchar(255) not null,
                `content` longtext not null,
                `cover` varchar(500) default '',
                `add_time` int not null,
                `is_show` tinyint(1) default 1
            )engine=InnoDB default charset=utf8mb4;
            ");

            //=====================新增SQL开始=====================
            // 侧边栏配置表（博主头像、简介、公告）
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `Sidebar` (
              `id` int NOT NULL AUTO_INCREMENT COMMENT '主键ID',
              `avatar_url` varchar(500) NOT NULL DEFAULT '' COMMENT '头像地址',
              `introduction` text NOT NULL COMMENT '博主简介',
              `bulletinboard` text NOT NULL COMMENT '站点公告',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 文章分类表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `category` (
              `id` int NOT NULL AUTO_INCREMENT COMMENT '分类ID',
              `name` varchar(120) NOT NULL DEFAULT '' COMMENT '分类名称',
              `sort` int NOT NULL DEFAULT 0 COMMENT '排序值，越小越靠前',
              `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1显示 0隐藏',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 文章标签表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `tag` (
              `id` int NOT NULL AUTO_INCREMENT COMMENT '标签ID',
              `name` varchar(120) NOT NULL DEFAULT '' COMMENT '标签名称',
              `sort` int NOT NULL DEFAULT 0 COMMENT '排序值，越小越靠前',
              `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1显示 0隐藏',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 文章表（新版结构，表名不冲突）
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `blog_article` (
              `id` int NOT NULL AUTO_INCREMENT,
              `title` varchar(200) NOT NULL COMMENT '文章标题',
              `content` longtext NOT NULL COMMENT '文章内容',
              `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1显示 0隐藏',
              `add_time` int NOT NULL DEFAULT '0' COMMENT '发布时间戳',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 加入我们表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `joinus` (
              `id` int NOT NULL AUTO_INCREMENT,
              `title` varchar(200) NOT NULL COMMENT '标题',
              `content` text NOT NULL COMMENT '内容',
              `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
              `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // ICP备案号表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `icprecordno` (
              `id` int NOT NULL AUTO_INCREMENT,
              `no` varchar(100) NOT NULL DEFAULT '' COMMENT '备案号',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 【站点名称&Logo配置表 nameandlogo】
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `nameandlogo` (
              `id` int NOT NULL AUTO_INCREMENT,
              `blog_name` varchar(120) NOT NULL DEFAULT '个人博客' COMMENT '博客名称',
              `logo_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo图片路径',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 社交链接表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `sociallinks` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL COMMENT '链接名称',
              `icon` varchar(200) DEFAULT NULL COMMENT '图标地址(预留)',
              `url` varchar(500) NOT NULL COMMENT '跳转链接',
              `sort` int NOT NULL DEFAULT '0' COMMENT '排序数字越小越靠前',
              `is_show` tinyint NOT NULL DEFAULT '1' COMMENT '1前台显示，0隐藏',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            //管理员账号表
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS `admin` (
              `id` int NOT NULL AUTO_INCREMENT,
              `username` varchar(50) NOT NULL COMMENT '管理员账号',
              `password` varchar(100) NOT NULL COMMENT '密码md5',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            //初始化数据：插入默认配置数据（站点名称Logo）
            $pdo->exec("INSERT IGNORE INTO `nameandlogo` (`blog_name`,`logo_url`) VALUES ('个人博客','');");
            //=====================新增SQL结束=====================

            //插入管理员密码bcrypt加密【原有不动】
            $pwd_hash = password_hash($admin_pwd,PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO blog_admin(username,password,create_time) VALUES (?,?,?)");
            $stmt->execute([$admin_user,$pwd_hash,time()]);
            //自动创建上传目录 uploads【原有不动】
            if(!is_dir('uploads')){
                mkdir('uploads',0755,true);
            }
            //写入config.php配置文件【原有不动】
            $config_code = '<?php
$db_host="'.addslashes($db_host).'";
$db_user="'.addslashes($db_user).'";
$db_pass="'.addslashes($db_pass).'";
$db_name="'.addslashes($db_name).'";
try{
$pdo=new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",$db_user,$db_pass,[
PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);
}catch(Exception $e){
die("数据库连接失败:".$e->getMessage());
}
?>';
            file_put_contents('config.php',$config_code);
            $success = '安装成功！<a href="index.php">前往博客首页</a> | <a href="login.php">管理员登录</a>';
            $step = 3;
        }catch(Exception $e){
            $err = '数据库错误：'.$e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人博客 - 安装程序</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body{
        background:#f7fff8;
        background-image: 
        radial-gradient(circle at 10% 20%, rgba(188,235,198,0.42) 0%, transparent 48%),
        radial-gradient(circle at 90% 85%, rgba(162,228,180,0.33) 0%, transparent 55%),
        radial-gradient(ellipse at 50% 10%, rgba(210,247,220,0.25) 0%,transparent 60%),
        radial-gradient(ellipse at 30% 95%, rgba(194,238,207,0.22) 0%,transparent 52%);
        background-attachment: fixed;
        min-height: 100vh;
        margin: 0;
        padding:20px 15px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        overflow-x:hidden;
    }
    body::before{
        content:"";
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background-image: radial-gradient(rgba(82,185,110,0.04) 1px,transparent 1px);
        background-size:28px 28px;
        pointer-events:none;
        z-index:-1;
    }
    .main-card{
        max-width:620px;
        margin:60px auto;
        background: #ffffff;
        padding:42px 48px;
        border-radius:28px;
        box-shadow:
            0 4px 14px rgba(40,167,69,0.07),
            0 14px 36px rgba(34,139,58,0.11),
            0 34px 70px rgba(26,110,45,0.08),
            inset 0 0 0 1px rgba(255,255,255,0.75);
        border:1px solid rgba(40,167,69,0.07);
        transition: all 0.42s cubic‑bezier(0.22, 1, 0.22, 1);
        position:relative;
        overflow:hidden;
    }
    .main-card::before{
        content:"";
        position:absolute;
        top:0;
        left:-120%;
        width:100%;
        height:100%;
        background:linear-gradient(90deg,transparent,rgba(255,255,255,0.42),transparent);
        transition:left 0.7s ease;
    }
    .main-card:hover::before{
        left:120%;
    }
    .main-card:hover{
        transform: translateY(-7px) scale(1.012);
        box-shadow:
            0 8px 22px rgba(40,167,69,0.11),
            0 22px 50px rgba(34,139,58,0.16),
            0 48px 86px rgba(26,110,45,0.12),
            inset 0 0 0 1px rgba(255,255,255,0.85);
        border-color:rgba(40,167,69,0.13);
    }
    .btn-primary{
        background:#28a745;
        border-color:#28a745;
        border-radius:16px;
        padding:13px 32px;
        border-width:2px;
        font-weight:550;
        letter-spacing:0.7px;
        box-shadow: 
        0 4px 16px rgba(40,167,69,0.33),
        0 2px 6px rgba(40,167,69,0.18),
        inset 0 1px 0 rgba(255,255,255,0.28);
        transition: all 0.32s cubic‑bezier(0.25, 0.8, 0.25, 1);
        position:relative;
        overflow:hidden;
    }
    .btn-primary::after{
        content:"";
        position:absolute;
        top:0;
        left:-100%;
        width:100%;
        height:100%;
        background:linear-gradient(90deg,transparent,rgba(255,255,255,0.22),transparent);
        transition:left 0.5s ease;
    }
    .btn-primary:hover::after{
        left:100%;
    }
    .btn-primary:hover{
        background:#218838;
        border-color:#1e7e34;
        transform: translateY(-3px) scale(1.025);
        box-shadow: 
        0 8px 26px rgba(33,136,56,0.42),
        0 3px 10px rgba(33,136,56,0.22),
        inset 0 1px 0 rgba(255,255,255,0.25);
    }
    .btn-primary:active{
        transform: translateY(1.5px) scale(0.985);
        box-shadow: 
        0 3px 10px rgba(33,136,56,0.32),
        inset 0 2px 4px rgba(0,0,0,0.08);
    }
    </style>
</head>
<body>
<div class="container main-card">
    <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
            <h4>个人博客系统 安装向导</h4>
        </div>
        <div class="card-body p-4">
            <?php if($err):?>
                <div class="alert alert-danger"><?php echo xss_echo($err);?></div>
            <?php endif;?>
            <?php if($success):?>
                <div class="alert alert-success"><?php echo $success;?></div>
            <?php else:?>
                <?php if($step===1):?>
                    <h5>第一步：环境检测</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item">PHP版本 ≥7.4：
                            <?php echo (PHP_VERSION_ID >=70400) ? '<span class="text-success">通过</span>' : '<span class="text-danger">不通过，请升级PHP</span>' ?>
                        </li>
                        <li class="list-group-item">PDO-MySQL扩展：
                            <?php echo extension_loaded('pdo_mysql') ? '<span class="text-success">通过</span>' : '<span class="text-danger">未开启</span>' ?>
                        </li>
                        <li class="list-group-item">mime_content_type函数：
                            <?php echo function_exists('mime_content_type') ? '<span class="text-success">通过</span>' : '<span class="text-danger">不可用</span>' ?>
                        </li>
                    </ul>
                    <a href="install.php?step=2" class="btn btn-primary w-100">下一步，配置数据库</a>
                <?php elseif($step===2):?>
                    <h5>第二步：填写数据库与管理员账号</h5>
                    <form method="post">
                        <div class="mb-3">
                            <label>数据库地址</label>
                            <input class="form-control" type="text" name="db_host" value="127.0.0.1" required>
                        </div>
                        <div class="mb-3">
                            <label>数据库账号</label>
                            <input class="form-control" type="text" name="db_user" required>
                        </div>
                        <div class="mb-3">
                            <label>数据库密码</label>
                            <input class="form-control" type="password" name="db_pass">
                        </div>
                        <div class="mb-3">
                            <label>数据库名称（不存在会自动创建）</label>
                            <input class="form-control" type="text" name="db_name" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label>管理员账号(admin后台登录)</label>
                            <input class="form-control" type="text" name="admin_user" required>
                        </div>
                        <div class="mb-3">
                            <label>管理员登录密码</label>
                            <input class="form-control" type="password" name="admin_pwd" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">开始安装</button>
                    </form>
                <?php endif;?>
            <?php endif;?>
        </div>
    </div>
</div>
</body>
</html>
