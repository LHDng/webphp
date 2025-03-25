<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';

if (!isset($_SESSION['masv'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error'] = "Vui lòng đăng nhập để truy cập trang này";
    header("Location: " . ROOT_PATH . "/auth/login.php");
    exit();
}

// Kiểm tra sinh viên có tồn tại không
$masv = $_SESSION['masv'];
$sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masv);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: " . ROOT_PATH . "/auth/login.php?error=sinh_vien_khong_ton_tai");
    exit();
}
?>