<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}
if($_POST){
    $name = $_POST['name']??'';
    $url  = $_POST['url']??'';
    $sort = intval($_POST['sort']??0);
    $is_show = intval($_POST['is_show']??0);
    if(empty($name) || empty($url)){
        $msg = "名称和链接不能为空";
    }else{
        $stmt = $pdo->prepare("INSERT INTO sociallinks(name,url,sort,is_show) VALUES (?,?,?,?)");
        $stmt->execute([$name,$url,$sort,$is_show]);
        redirect('sociallinks_list.php');
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增社交链接</title>
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
        <a href="sociallinks_list.php" class="text-white me-3">返回列表</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-4" style="max-width:650px;">
    <div class="card p-4 shadow">
        <h4 class="mb-3">新增社交链接</h4>
        <?php if(!empty($msg)):?>
        <div class="alert alert-danger"><?=xss_echo($msg)?></div>
        <?php endif;?>
        <form method="post">
            <div class="mb-3">
                <label>名称（如 GitHub、知乎）</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>链接地址</label>
                <input type="url" name="url" class="form-control" placeholder="shturl.cc/NunxT" required>
            </div>
            <div class="mb-3">
                <label>排序（数字越小越靠前）</label>
                <input type="number" name="sort" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label class="me-3">
                    <input type="checkbox" name="is_show" value="1" checked> 是否前台显示
                </label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">提交保存</button>
                <a href="sociallinks_list.php" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
