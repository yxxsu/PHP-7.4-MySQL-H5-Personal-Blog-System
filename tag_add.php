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
    $sort = intval($_POST['sort']??0);
    $is_show = intval($_POST['is_show']??1);
    $stmt = $pdo->prepare("INSERT INTO tag(name,sort,is_show) VALUES(?,?,?)");
    $stmt->execute([$name,$sort,$is_show]);
    redirect('tag_list.php');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增标签</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.top-bar{background:#28a745;color:white;padding:14px 20px;}</style>
</head>
<body>
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="m-0">博客管理后台</h5>
    <div><a href="tag_list.php" class="text-white me-3">返回标签列表</a><a href="logout.php" class="text-white">退出登录</a></div>
</div>
<div class="container mt-4">
    <h4>新增标签</h4>
    <div class="card p-4 shadow mt-3">
        <form method="post">
            <div class="mb-3">
                <label>标签名称</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>排序数字（越小越靠前）</label>
                <input type="number" name="sort" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label><input type="checkbox" name="is_show" value="1" checked> 前台显示</label>
            </div>
            <button class="btn btn-success">提交保存</button>
        </form>
    </div>
</div>
</body>
</html>
