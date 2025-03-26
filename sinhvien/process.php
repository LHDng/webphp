<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Phương thức không hợp lệ!";
    header("Location: index.php");
    exit();
}

// Kiểm tra CSRF token
if (empty($_POST['csrf_token']) || 
    empty($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']) ||
    time() > $_SESSION['csrf_token_expire']) {
    
    $_SESSION['error'] = "Token bảo mật không hợp lệ hoặc đã hết hạn!";
    header("Location: " . (isset($_POST['MaSV']) ? 'edit.php?id='.$_POST['MaSV'] : 'index.php'));
    exit();
}

// Xóa token sau khi đã sử dụng
unset($_SESSION['csrf_token']);
unset($_SESSION['csrf_token_expire']);

// Lấy action từ form
$action = $_POST['action'] ?? '';

// Xử lý thêm mới sinh viên
if ($action === 'create') {
    // Validate input
    $maSV = trim($_POST['MaSV'] ?? '');
    $hoTen = trim($_POST['HoTen'] ?? '');
    $gioiTinh = $_POST['GioiTinh'] ?? '';
    $ngaySinh = $_POST['NgaySinh'] ?? '';
    $maNganh = $_POST['MaNganh'] ?? '';

    // Kiểm tra dữ liệu bắt buộc
    if (empty($maSV) || empty($hoTen) || empty($gioiTinh) || empty($ngaySinh) || empty($maNganh)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        header("Location: create.php");
        exit();
    }

    // Kiểm tra mã sinh viên đã tồn tại chưa
    $checkSql = "SELECT MaSV FROM SinhVien WHERE MaSV = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $maSV);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Mã sinh viên đã tồn tại trong hệ thống!";
        header("Location: create.php");
        exit();
    }

    // Xử lý upload hình ảnh
    $hinhAnh = '';
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
        $targetDir = ROOT_PATH . '/assets/images/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES['Hinh']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileExt, $allowedExts)) {
            $_SESSION['error'] = "Chỉ chấp nhận file ảnh JPG, JPEG, PNG hoặc GIF!";
            header("Location: create.php");
            exit();
        }

        if ($_FILES['Hinh']['size'] > $maxFileSize) {
            $_SESSION['error'] = "File ảnh không được vượt quá 2MB!";
            header("Location: create.php");
            exit();
        }

        $newFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $fileName);
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES['Hinh']['tmp_name'], $targetFile)) {
            $hinhAnh = '/assets/images/' . $newFileName;
        } else {
            $_SESSION['error'] = "Có lỗi khi upload file ảnh!";
            header("Location: create.php");
            exit();
        }
    }

    // Thêm vào database
    try {
        $conn->begin_transaction();
        
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $maSV, $hoTen, $gioiTinh, $ngaySinh, $hinhAnh, $maNganh);
        $stmt->execute();
        
        $conn->commit();
        
        $_SESSION['success'] = "Thêm sinh viên thành công!";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        
        // Xóa file ảnh nếu đã upload nhưng insert thất bại
        if (!empty($hinhAnh) && file_exists(ROOT_PATH . $hinhAnh)) {
            unlink(ROOT_PATH . $hinhAnh);
        }
        
        $_SESSION['error'] = "Lỗi khi thêm sinh viên: " . $e->getMessage();
        header("Location: create.php");
        exit();
    }
}

// Xử lý cập nhật sinh viên
if ($action === 'update') {
    // Validate input
    $maSV = trim($_POST['MaSV'] ?? '');
    $hoTen = trim($_POST['HoTen'] ?? '');
    $gioiTinh = $_POST['GioiTinh'] ?? '';
    $ngaySinh = $_POST['NgaySinh'] ?? '';
    $maNganh = $_POST['MaNganh'] ?? '';
    $xoaAnh = isset($_POST['xoaAnh']);

    // Kiểm tra dữ liệu bắt buộc
    if (empty($maSV) || empty($hoTen) || empty($gioiTinh) || empty($ngaySinh) || empty($maNganh)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        header("Location: edit.php?id=$maSV");
        exit();
    }

    // Lấy thông tin hiện tại
    $currentSql = "SELECT Hinh FROM SinhVien WHERE MaSV = ?";
    $currentStmt = $conn->prepare($currentSql);
    $currentStmt->bind_param("s", $maSV);
    $currentStmt->execute();
    $currentResult = $currentStmt->get_result();
    
    if ($currentResult->num_rows === 0) {
        $_SESSION['error'] = "Sinh viên không tồn tại!";
        header("Location: index.php");
        exit();
    }
    
    $currentData = $currentResult->fetch_assoc();
    $hinhAnh = $currentData['Hinh'];

    // Xử lý xóa ảnh nếu được chọn
    if ($xoaAnh && !empty($hinhAnh)) {
        if (file_exists(ROOT_PATH . $hinhAnh)) {
            unlink(ROOT_PATH . $hinhAnh);
        }
        $hinhAnh = '';
    }

    // Xử lý upload ảnh mới
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
        // Xóa ảnh cũ nếu có
        if (!empty($hinhAnh) && file_exists(ROOT_PATH . $hinhAnh)) {
            unlink(ROOT_PATH . $hinhAnh);
        }

        $targetDir = ROOT_PATH . '/assets/images/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES['Hinh']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileExt, $allowedExts)) {
            $_SESSION['error'] = "Chỉ chấp nhận file ảnh JPG, JPEG, PNG hoặc GIF!";
            header("Location: edit.php?id=$maSV");
            exit();
        }

        if ($_FILES['Hinh']['size'] > $maxFileSize) {
            $_SESSION['error'] = "File ảnh không được vượt quá 2MB!";
            header("Location: edit.php?id=$maSV");
            exit();
        }

        $newFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $fileName);
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES['Hinh']['tmp_name'], $targetFile)) {
            $hinhAnh = '/assets/images/' . $newFileName;
        } else {
            $_SESSION['error'] = "Có lỗi khi upload file ảnh!";
            header("Location: edit.php?id=$maSV");
            exit();
        }
    }

    // Cập nhật database
    try {
        $conn->begin_transaction();
        
        $sql = "UPDATE SinhVien SET 
                HoTen = ?, 
                GioiTinh = ?, 
                NgaySinh = ?, 
                Hinh = ?, 
                MaNganh = ? 
                WHERE MaSV = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $hoTen, $gioiTinh, $ngaySinh, $hinhAnh, $maNganh, $maSV);
        $stmt->execute();
        
        $conn->commit();
        
        $_SESSION['success'] = "Cập nhật thông tin sinh viên thành công!";
        header("Location: detail.php?id=$maSV");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        
        // Xóa file ảnh mới nếu đã upload nhưng update thất bại
        if (isset($newFileName)) {
            $newImagePath = ROOT_PATH . '/assets/images/' . $newFileName;
            if (file_exists($newImagePath)) {
                unlink($newImagePath);
            }
        }
        
        $_SESSION['error'] = "Lỗi khi cập nhật thông tin: " . $e->getMessage();
        header("Location: edit.php?id=$maSV");
        exit();
    }
}

// Xử lý các action khác (delete) có thể thêm ở đây

// Nếu không khớp với action nào
header("Location: index.php");
exit();