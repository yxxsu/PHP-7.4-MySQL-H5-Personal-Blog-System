<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}

//查询原有配置
$row = $pdo->query("SELECT * FROM Sidebar LIMIT 1")->fetch();
if(!$row){
    //没有记录则新建一条空数据
    $pdo->exec("INSERT INTO Sidebar(avatar_url,introduction,bulletinboard) VALUES('','','')");
    $row = $pdo->query("SELECT * FROM Sidebar LIMIT 1")->fetch();
}

//保存提交
if($_POST){
    $avatar_url = $_POST['avatar_url']??'';
    $introduction = $_POST['introduction']??'';
    $bulletinboard = $_POST['bulletinboard']??'';
    $stmt = $pdo->prepare("UPDATE Sidebar SET avatar_url=?,introduction=?,bulletinboard=? WHERE id=?");
    $stmt->execute([$avatar_url,$introduction,$bulletinboard,$row['id']]);
    echo "<script>alert('保存成功');location.href='sidebar_config.php'</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>侧边栏配置</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .top-bar{background:#28a745;color:white;padding:14px 20px;}
    </style>
</head>
<body>
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="m-0">博客管理后台</h5>
    <div>
        <a href="index.php" class="text-white me-3">首页</a>
        <a href="admin.php" class="text-white me-3">文章管理</a>
        <a href="site_config.php" class="text-white me-3">站点名称Logo设置</a>
        <a href="sidebar_config.php" class="text-white me-3">侧边栏配置</a>
        <a href="category_list.php" class="text-white me-3">分类管理</a>
        <a href="tag_list.php" class="text-white me-3">标签管理</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-4">
    <h4>侧边栏博主信息 & 公告设置</h4>
    <div class="card p-4 shadow mt-3">
        <form method="post">
            <div class="mb-3">
                <label>博主头像地址（URL）</label>
                <input type="text" class="form-control" name="avatar_url" value="<?=xss_echo($row['avatar_url'])?>">
                <?php if(!empty($row['avatar_url'])):?>
                    <img src="<?=xss_echo($row['avatar_url'])?>" style="width:100px;height:100px;border-radius:50%;margin-top:10px">
                <?php endif;?>
            </div>
            <div class="mb-3">
                <label>博主简介</label>
                <textarea class="form-control" rows="5" name="introduction"><?=xss_echo($row['introduction'])?></textarea>
            </div>
            <div class="mb-3">
                <label>站点公告</label>
                <textarea class="form-control" rows="5" name="bulletinboard"><?=xss_echo($row['bulletinboard'])?></textarea>
            </div>
            <button class="btn btn-success" type="submit">保存设置</button>
        </form>
    </div>
</div>
</body>
</html>
