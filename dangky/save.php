<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';

session_start();
if (!isset($_SESSION['masv'])) {
    header("Location: ../auth/login.php");
    exit();
}

$masv = $_SESSION['masv'];

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Tạo bản ghi đăng ký mới
    $sql = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), '$masv')";
    $conn->query($sql);
    $madk = $conn->insert_id;

    // Lấy tất cả học phần đã chọn từ session
    if (isset($_SESSION['hocphan_dachon'])) {
        foreach ($_SESSION['hocphan_dachon'] as $mahp) {
            // Thêm vào chi tiết đăng ký
            $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES ($madk, '$mahp')";
            $conn->query($sql);
            
            // Giảm số lượng đăng ký dự kiến
            $sql = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = '$mahp'";
            $conn->query($sql);
        }
    }

    // Xóa học phần đã chọn trong session
    unset($_SESSION['hocphan_dachon']);
    
    $conn->commit();
    $_SESSION['thanhcong'] = "Đăng ký học phần thành công!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['loi'] = "Có lỗi xảy ra: " . $e->getMessage();
}

header("Location: index.php");
exit();
?>