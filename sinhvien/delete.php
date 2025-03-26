<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$masv = $_GET['id'];

// Kiểm tra xem sinh viên có tồn tại không
$sqlCheck = "SELECT Hinh FROM SinhVien WHERE MaSV = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $masv);
$stmtCheck->execute();
$result = $stmtCheck->get_result();


if ($result->num_rows == 0) {
    header("Location: index.php?error=Sinh+viên+không+tồn+tại");
    exit();
}

$sv = $result->fetch_assoc();

// Xóa ảnh đại diện nếu có
if ($sv['Hinh'] && file_exists('..' . $sv['Hinh'])) {
    unlink('..' . $sv['Hinh']);
}

// Xóa sinh viên
$sql = "DELETE FROM SinhVien WHERE MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masv);

if ($stmt->execute()) {
    header("Location: index.php?success=Đã+xóa+sinh+viên+thành+công");
} else {
    header("Location: index.php?error=Lỗi+khi+xóa+sinh+viên");
}
exit();
?>