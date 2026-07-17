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
    $title = $_POST['title']??'';
    $content = $_POST['content']??'';
    $sort = intval($_POST['sort']??0);
    $is_show = intval($_POST['is_show']??1);
    if(empty($title)){
        echo "<script>alert('标题不能为空');history.back();</script>";
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO joinus(title,content,sort,is_show) VALUES (?,?,?,?)");
    $stmt->execute([$title,$content,$sort,$is_show]);
    redirect('joinus_list.php');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增岗位</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .top-bar{background:#28a745;color:white;padding:14px 20px;}
    </style>
</head>
<body>
<div class="top-bar d-flex justify-content-between align-items-center">
    <h5 class="m-0">博客管理后台</h5>
    <div>
        <a href="joinus_list.php" class="text-white me-3">返回列表</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-4">
    <div class="card p-4 shadow" style="max-width:700px;">
        <h4 class="mb-3">新增岗位</h4>
        <form method="post">
            <div class="mb-3">
                <label>岗位标题</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>岗位介绍内容</label>
                <textarea name="content" class="form-control" rows="5"></textarea>
            </div>
            <div class="mb-3">
                <label>排序(数字越小越靠前)</label>
                <input type="number" name="sort" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label>是否显示</label>
                <select name="is_show" class="form-select">
                    <option value="1">显示</option>
                    <option value="0">隐藏</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">提交保存</button>
        </form>
    </div>
</div>
</body>
</html>
