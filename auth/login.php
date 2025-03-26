<?php
require_once '../config/db.php';
require_once '../config/session.php';
require_once '../includes/header.php';

// Nếu đã đăng nhập thì chuyển hướng
if (isset($_SESSION['masv'])) {
    header("Location: ../dangky");
    exit();
}

// Xử lý đăng nhập
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masv = $conn->real_escape_string($_POST['masv']);
    
    // Kiểm tra sinh viên có tồn tại không
    $sql = "SELECT * FROM SinhVien WHERE MaSV = '$masv'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $_SESSION['masv'] = $masv;
        header("Location: ../dangky");
        exit();
    } else {
        $error = "Mã sinh viên không tồn tại trong hệ thống";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Đăng nhập hệ thống</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="masv" class="form-label">Mã Sinh Viên</label>
                        <input type="text" class="form-control" id="masv" name="masv" required 
                               placeholder="Nhập mã sinh viên">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập 
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="../">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>