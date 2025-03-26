<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';
require_once '../includes/auth_check.php';

$masv = $_SESSION['masv'];


// Lấy danh sách đăng ký hiện tại
$sql = "SELECT dk.MaDK, ctdk.MaHP 
        FROM DangKy dk 
        JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK 
        WHERE dk.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masv);
$stmt->execute();
$result = $stmt->get_result();

// Tăng số lượng đăng ký dự kiến cho các học phần
while ($row = $result->fetch_assoc()) {
    $sqlUpdate = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien + 1 WHERE MaHP = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("s", $row['MaHP']);
    $stmtUpdate->execute();
}

// Xóa tất cả đăng ký của sinh viên
$sqlDelete = "DELETE FROM DangKy WHERE MaSV = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("s", $masv);

if ($stmtDelete->execute()) {
    header("Location: index.php?success=Đã+xóa+tất+cả+đăng+ký");
} else {
    header("Location: index.php?error=Lỗi+khi+xóa+đăng+ký");
}
exit();
?>