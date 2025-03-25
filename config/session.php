<?php
// Kiểm tra nếu session chưa được khởi động
if (session_status() === PHP_SESSION_NONE) {
    // Thiết lập các thông số session trước khi khởi động
    ini_set('session.gc_maxlifetime', 3600); // 1 giờ
    session_set_cookie_params(3600);
    
    // Khởi động session
    session_start();
}
?>