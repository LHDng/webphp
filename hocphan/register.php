<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (!isset($_GET['mahp'])) {
    header("Location: index.php");
    exit();
}

$mahp = $_GET['mahp'];
$masv = $_SESSION['masv'];

// Kiểm tra học phần có tồn tại và còn chỗ không (sử dụng prepared statement)
$sqlHP = "SELECT * FROM HocPhan WHERE MaHP = ? AND SoLuongDuKien > 0";
$stmtHP = $conn->prepare($sqlHP);
$stmtHP->bind_param("s", $mahp);
$stmtHP->execute();
$resultHP = $stmtHP->get_result();

if ($resultHP->num_rows == 0) {
    header("Location: index.php?error=Học+phần+không+tồn+tại+hoặc+đã+hết+chỗ");
    exit();
}

$hocPhan = $resultHP->fetch_assoc();

// Kiểm tra sinh viên đã đăng ký học phần này chưa (sử dụng prepared statement)
$sqlCheck = "SELECT ctdk.MaHP 
             FROM DangKy dk 
             JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK 
             WHERE dk.MaSV = ? AND ctdk.MaHP = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $masv, $mahp);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    header("Location: index.php?error=Bạn+đã+đăng+ký+học+phần+này");
    exit();
}

// Tạo đăng ký mới hoặc sử dụng đăng ký hiện tại chưa lưu (sửa phần này)
$sqlDK = "SELECT MaDK FROM DangKy WHERE MaSV = ? AND NgayDK = CURDATE()";
$stmtDK = $conn->prepare($sqlDK);
$stmtDK->bind_param("s", $masv);
$stmtDK->execute();
$resultDK = $stmtDK->get_result();

if ($resultDK->num_rows > 0) {
    $maDK = $resultDK->fetch_assoc()['MaDK'];
} else {
    $sqlInsertDK = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)";
    $stmtInsertDK = $conn->prepare($sqlInsertDK);
    $stmtInsertDK->bind_param("s", $masv);
    $stmtInsertDK->execute();
    $maDK = $conn->insert_id;
}

// Thêm vào chi tiết đăng ký (sử dụng prepared statement)
$sqlCTDK = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
$stmtCTDK = $conn->prepare($sqlCTDK);
$stmtCTDK->bind_param("is", $maDK, $mahp);

if ($stmtCTDK->execute()) {
    // Giảm số lượng đăng ký dự kiến (sử dụng prepared statement)
    $sqlUpdate = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("s", $mahp);
    $stmtUpdate->execute();
    
    header("Location: ../dangky?success=Đăng+ký+học+phần+thành+công");
} else {
    header("Location: index.php?error=Lỗi+khi+đăng+ký+học+phần");
}
exit();
?>