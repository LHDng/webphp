<?php
require_once '../config/session.php';

// Hủy tất cả session
$_SESSION = array();
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: login.php");
exit();
?>