<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 ngày
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

// Tạo CSRF token nếu chưa có hoặc đã hết hạn
if (empty($_SESSION['csrf_token']) || (isset($_SESSION['csrf_token_expire']) && time() > $_SESSION['csrf_token_expire'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expire'] = time() + 3600; // Token có hiệu lực 1 giờ
}
?>