<?php
if (!defined('IN_BLOG')) {
    exit('Access Denied');
}
//强制HTTPS
function force_https()
{
    //本地调试 关闭https跳转
    /*
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        if($_SERVER['SERVER_PORT'] != 443){
            header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    */
}

//CSRF令牌生成
function create_csrf_token()
{
    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

//校验CSRF
function check_csrf($token)
{
    if(empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']){
        return false;
    }
    //使用后销毁，一次性令牌
    unset($_SESSION['csrf_token']);
    return true;
}

//XSS过滤输出
function xss_echo($str)
{
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

//简单权限判断 是否管理员
function is_admin()
{
    if(!isset($_SESSION['admin_id']) || intval($_SESSION['admin_id']) !== 1){
        return false;
    }
    return true;
}

//跳转
function redirect($url){
    header('Location:'.$url);
    exit;
}

//上传安全校验
function check_upload_file($file)
{
    $allow_ext = ['jpg','jpeg','png','gif','webp'];
    $max_size = 5*1024*1024;
    $name = $file['name'];
    $size = $file['size'];
    $tmp = $file['tmp_name'];
    if($size>$max_size){
        return ['ok'=>false,'msg'=>'文件不能大于5M'];
    }
    $ext = strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if(!in_array($ext,$allow_ext)){
        return ['ok'=>false,'msg'=>'仅允许jpg,png,gif,webp图片'];
    }
    //检测图片真实类型
    $mime = mime_content_type($tmp);
    $allow_mime = ['image/jpeg','image/png','image/gif','image/webp'];
    if(!in_array($mime,$allow_mime)){
        return ['ok'=>false,'msg'=>'不是合法图片文件'];
    }
    return ['ok'=>true,'ext'=>$ext];
}
?>
