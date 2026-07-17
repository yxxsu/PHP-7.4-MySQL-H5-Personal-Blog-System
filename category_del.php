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
$pdo->exec("DELETE FROM category WHERE id={$id}");
redirect('category_list.php');
?>
