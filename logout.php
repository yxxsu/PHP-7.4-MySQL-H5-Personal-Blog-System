<?php
define('IN_BLOG',true);
session_start();
session_destroy();
header("Location:login.php");
exit;
?>
