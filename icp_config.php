<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}
//读取备案号
$row = $pdo->query("SELECT * FROM icprecordno LIMIT 1")->fetch();
$icp_no = $row['no'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICP备案管理</title>
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
        <a href="index.php" class="text-white me-3">首页</a>
        <a href="admin.php" class="text-white me-3">文章管理</a>
        <a href="joinus_list.php" class="text-white me-3">加入我们管理</a>
        <a href="icp_config.php" class="text-white me-3">ICP备案管理</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-5" style="max-width:650px;">
    <div class="card shadow p-4">
        <h4 class="mb-4">ICP备案号设置</h4>
        <form action="save_icp.php" method="post">
            <div class="mb-3">
                <label class="form-label">备案号</label>
                <input type="text" name="icp_no" class="form-control" value="<?=xss_echo($icp_no)?>" placeholder="例：粤ICP备12345678号">
                <div class="form-text">留空则前台页面不显示备案号</div>
            </div>
            <button class="btn btn-success" type="submit">保存设置</button>
            <a href="admin.php" class="btn btn-outline-secondary ms-2">返回</a>
        </form>
    </div>
</div>
</body>
</html>
