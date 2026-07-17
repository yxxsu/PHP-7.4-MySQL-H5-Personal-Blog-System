<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}
$icp_no = trim($_POST['icp_no'] ?? '');

//判断是否已有记录
$row = $pdo->query("SELECT id FROM icprecordno LIMIT 1")->fetch();
if($row){
    //更新已有数据
$stmt = $pdo->prepare("UPDATE icprecordno SET `no` = ? WHERE id = ?");
$stmt->execute([$icp_no,$row['id']]);
}else{
    //首次插入
$stmt = $pdo->prepare("INSERT INTO icprecordno(`no`) VALUES (?)");
$stmt->execute([$icp_no]);
}
redirect('icp_config.php?ok=1');
?>
