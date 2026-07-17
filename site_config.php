<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
if(!is_admin()){
    redirect('login.php');
}

//读取原有配置
$row = $pdo->query("SELECT * FROM nameandlogo LIMIT 1")->fetch();
$blog_name = $row['blog_name'] ?? '个人博客';
$logo_url  = $row['logo_url'] ?? '';
$id = $row['id'] ?? 0;

$msg = '';
$msg_type = '';

//表单提交保存
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $new_name = trim($_POST['blog_name']);
    $new_logo = $logo_url;

    //上传logo图片处理
    if(!empty($_FILES['logo_file']['name'])){
        $allow_ext = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['logo_file']['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,$allow_ext)){
            $msg = '图片格式仅支持 jpg png gif webp';
            $msg_type = 'danger';
        }else{
            $save_name = 'logo_'.time().'.'.$ext;
            $save_path = 'uploads/'.$save_name;
            if(move_uploaded_file($_FILES['logo_file']['tmp_name'],$save_path)){
                //删除旧logo文件
                if(!empty($logo_url) && file_exists(ltrim($logo_url,'/'))){
                    @unlink(ltrim($logo_url,'/'));
                }
                $new_logo = '/uploads/'.$save_name;
            }else{
                $msg = '图片上传失败，请检查uploads目录权限';
                $msg_type = 'danger';
            }
        }
    }
    //清空logo
    if(isset($_POST['clear_logo']) && $_POST['clear_logo'] ==1){
        if(!empty($logo_url) && file_exists(ltrim($logo_url,'/'))){
            @unlink(ltrim($logo_url,'/'));
        }
        $new_logo = '';
    }

    if($msg === ''){
        if($id>0){
            $stmt = $pdo->prepare("UPDATE nameandlogo SET blog_name=?,logo_url=? WHERE id=?");
            $stmt->execute([$new_name,$new_logo,$id]);
        }else{
            $stmt = $pdo->prepare("INSERT INTO nameandlogo(blog_name,logo_url) VALUES (?,?)");
            $stmt->execute([$new_name,$new_logo]);
        }
        $msg = '保存成功！';
        $msg_type = 'success';
        $blog_name = $new_name;
        $logo_url = $new_logo;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>站点名称Logo设置</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .top-bar{background:#28a745;color:white;padding:14px 20px;}
        .preview-logo{max-height:60px;max-width:260px;}
    </style>
</head>
<body>
<div class="top-bar d-flex justify-content-between align-items-center">
    <h5 class="m-0">博客管理后台</h5>
    <div>
        <a href="admin.php" class="text-white me-3">文章管理</a>
        <a href="index.php" class="text-white me-3">首页</a>
        <a href="joinus_list.php" class="text-white me-3">加入我们管理</a>
        <a href="icp_config.php" class="text-white me-3">ICP备案管理</a>
        <a href="logout.php" class="text-white">退出登录</a>
    </div>
</div>
<div class="container mt-5" style="max-width:700px;">
    <h4 class="mb-4">站点名称 & Logo 设置</h4>
    <?php if(!empty($msg)):?>
    <div class="alert alert-<?=$msg_type?>"><?=xss_echo($msg)?></div>
    <?php endif;?>
    <div class="card p-4 shadow">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">博客名称</label>
                <input type="text" name="blog_name" class="form-control" value="<?=xss_echo($blog_name)?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">上传Logo图片</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*">
                <div class="form-text">建议透明PNG，高度40‑45px最佳，不上传则显示文字名称</div>
            </div>
            <?php if(!empty($logo_url)):?>
            <div class="mb-3">
                <label class="form-label">当前Logo预览</label>
                <div>
                    <img src="<?=xss_echo($logo_url)?>" class="preview-logo">
                </div>
                <div class="mt-2">
                    <button type="submit" name="clear_logo" value="1" class="btn btn-sm btn-outline-danger" onclick="return confirm('确定清空Logo？')">清空Logo</button>
                </div>
            </div>
            <?php endif;?>
            <div class="mt-4">
                <button type="submit" class="btn btn-success">保存设置</button>
                <a href="admin.php" class="btn btn-secondary ms-2">返回</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
