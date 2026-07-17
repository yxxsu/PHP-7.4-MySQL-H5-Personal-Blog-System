<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()) redirect('login.php');
$id = intval($_GET['id']??0);
$row = $pdo->query("SELECT * FROM blog_article WHERE id={$id}")->fetch();
if(!$row){
    exit('文章不存在');
}
if($_POST){
    if(!check_csrf($_POST['csrf_token']??'')){
        echo '令牌错误';exit;
    }
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $stmt = $pdo->prepare("UPDATE blog_article set title=?,content=? WHERE id=?");
    $stmt->execute([$title,$content,$id]);
    redirect('admin.php');
}
$token = create_csrf_token();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑文章</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-4" style="max-width:900px;">
    <div class="card shadow p-4">
        <h4 class="mb-3 text-success">编辑文章</h4>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?=xss_echo($token)?>">
            <div class="mb-3">
                <label>文章标题</label>
                <input class="form-control" name="title" value="<?=xss_echo($row['title'])?>" required>
            </div>
            <div class="mb-3">
                <label>文章正文</label>
                <textarea class="form-control" name="content" rows="12" required><?=xss_echo($row['content'])?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success">保存修改</button>
                <a href="admin.php" class="btn btn-outline-secondary">返回</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
