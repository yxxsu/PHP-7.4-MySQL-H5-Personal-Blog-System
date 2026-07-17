<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}
$id=intval($_GET['id']??0);
$stmt = $pdo->prepare("DELETE FROM blog_article WHERE id=?");
$stmt->execute([$id]);
redirect('admin.php');
?>
