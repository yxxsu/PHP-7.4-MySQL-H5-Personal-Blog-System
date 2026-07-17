<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}
$page = intval($_GET['p']??1);
$pagesize=10;
$offset = ($page-1)*$pagesize;
$list = $pdo->query("SELECT * FROM joinus ORDER BY sort ASC,id DESC limit {$offset},{$pagesize}")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加入我们管理</title>
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
        <a href="admin.php" class="text-white me-3">文章管理</a>
        <a href="index.php" class="text-white me-3">首页</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>加入我们 - 岗位列表</h4>
        <a href="joinus_add.php" class="btn btn-success">新增岗位</a>
    </div>
    <div class="card p-3 shadow">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>排序</th>
                    <th>是否显示</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($list as $v):?>
                <tr>
                    <td><?=xss_echo($v['id'])?></td>
                    <td><?=xss_echo($v['title'])?></td>
                    <td><?=xss_echo($v['sort'])?></td>
                    <td><?=$v['is_show']==1?'✅显示':'❌隐藏'?></td>
                    <td>
                        <a href="joinus_edit.php?id=<?=$v['id']?>" class="btn btn-sm btn-outline-success">编辑</a>
                        <a href="joinus_del.php?id=<?=$v['id']?>" onclick="return confirm('确定删除这条岗位？')" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
