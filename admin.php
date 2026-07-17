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

//获取总条数用于分页
$total = $pdo->query("SELECT COUNT(*) FROM blog_article")->fetchColumn();
$total_page = ceil($total / $pagesize);

$list = $pdo->query("SELECT * FROM blog_article ORDER BY add_time DESC limit {$offset},{$pagesize}")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博客后台管理</title>
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
        <a href="site_config.php" class="text-white me-3">站点名称Logo设置</a>
        <a href="sidebar_config.php" class="text-white me-3">侧边栏配置</a>
        <a href="category_list.php" class="text-white me-3">分类管理</a>
        <a href="tag_list.php" class="text-white me-3">标签管理</a>
        <a href="joinus_list.php" class="text-white me-3">加入我们管理</a>
        <a href="sociallinks_list.php" class="text-white me-3">社交链接管理</a>
        <a href="icp_config.php" class="text-white me-3">ICP备案管理</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>文章列表</h4>
        <a href="add_article.php" class="btn btn-success">新增文章</a>
    </div>
    <div class="card p-3 shadow">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>发布时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($list as $v):?>
                <tr>
                    <td><?=xss_echo($v['id'])?></td>
                    <td><?=xss_echo($v['title'])?></td>
                    <td><?=xss_echo(date('Y‑m‑d H:i',$v['add_time']))?></td>
                    <td>
                        <a href="edit_article.php?id=<?=$v['id']?>" class="btn btn-sm btn-outline-success">编辑</a>
                        <a href="del_article.php?id=<?=$v['id']?>" onclick="return confirm('确定删除？')" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <!--分页-->
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <li class="page-item <?= $page<=1 ? 'disabled':'' ?>">
                    <a class="page-link" href="admin.php?p=<?=$page-1?>">上一页</a>
                </li>
                <?php for($i=1;$i<=$total_page;$i++):?>
                <li class="page-item <?= $page==$i ? 'active':'' ?>">
                    <a class="page-link" href="admin.php?p=<?=$i?>"><?=$i?></a>
                </li>
                <?php endfor;?>
                <li class="page-item <?= $page>=$total_page ? 'disabled':'' ?>">
                    <a class="page-link" href="admin.php?p=<?=$page+1?>">下一页</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
</body>
</html>
