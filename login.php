<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
$error='';
if(is_admin()){
    redirect('admin.php');
}
if($_POST){
    $username = trim($_POST['username']??'');
    $pwd = trim($_POST['password']??'');
    $csrf = $_POST['csrf_token']??'';
    if(!check_csrf($csrf)){
        $error='表单令牌失效，请刷新重试';
    }else{
        $stmt = $pdo->prepare("SELECT id,password FROM blog_admin WHERE username=?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if($row && password_verify($pwd,$row['password'])){
            $_SESSION['admin_id'] = $row['id'];
            redirect('admin.php');
        }else{
            $error='账号或密码错误';
        }
    }
}
$token = create_csrf_token();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body{background:#f7fff8;}
        .login-box{max-width:420px;margin:80px auto;}
        .btn-success{background:#28a745;}
    </style>
</head>
<body>
<div class="container login-box">
    <div class="card shadow">
        <div class="card-header bg-success text-white text-center"><h5>博客后台登录</h5></div>
        <div class="card-body p-4">
            <?php if($error):?><div class="alert alert-danger"><?=xss_echo($error)?></div><?php endif;?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?=xss_echo($token)?>">
                <div class="mb-3">
                    <label>管理员账号</label>
                    <input class="form-control" type="text" name="username" required>
                </div>
                <div class="mb-3">
                    <label>登录密码</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <button class="btn btn-success w-100">登录后台</button>
                <div class="mt-3 text-center">
                    <a href="index.php">← 返回博客首页</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
