<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
header("Content-Type:application/json;charset=utf-8");
if(!is_admin()){
    echo json_encode(['code'=>0,'msg'=>'无权限']);
    exit;
}
if(empty($_FILES['file'])){
    echo json_encode(['code'=>0,'msg'=>'未接收到文件']);
    exit;
}
$check = check_upload_file($_FILES['file']);
if(!$check['ok']){
    echo json_encode(['code'=>0,'msg'=>$check['msg']]);
    exit;
}
$ext = $check['ext'];
$filename = date('YmdHis').mt_rand(1000,9999).'.'.$ext;
$save_path = 'uploads/'.$filename;
move_uploaded_file($_FILES['file']['tmp_name'],$save_path);
echo json_encode([
    'code'=>1,
    'url'=>$save_path
]);
?>
