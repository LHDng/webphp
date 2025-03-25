<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';
require_once '../includes/auth_check.php';

if (!isset($_GET['madk']) || !isset($_GET['mahp'])) {
    header("Location: index.php");
    exit();
}

$madk = $_GET['madk'];
$mahp = $_GET['mahp'];
$masv = $_SESSION['masv'];

// Kiểm tra quyền xóa (chỉ được xóa đăng ký của chính mình)
$sqlCheck = "SELECT dk.MaDK 
             FROM DangKy dk 
             JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK 
             WHERE dk.MaDK = ? AND dk.MaSV = ? AND ctdk.MaHP = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("iss", $madk, $masv, $mahp);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows == 0) {
    header("Location: index.php?error=Không+có+quyền+xóa+đăng+ký+này");
    exit();
}

// Xóa chi tiết đăng ký
$sqlDelete = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("is", $madk, $mahp);

if ($stmtDelete->execute()) {
    // Tăng số lượng đăng ký dự kiến
    $sqlUpdate = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien + 1 WHERE MaHP = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("s", $mahp);
    $stmtUpdate->execute();
    
    header("Location: index.php?success=Đã+hủy+đăng+ký+học+phần");
} else {
    header("Location: index.php?error=Lỗi+khi+hủy+đăng+ký");
}
exit();
?>