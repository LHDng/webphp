<?php

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="jumbotron bg-light p-5 rounded-lg m-3">
    <h1 class="display-4">Chào mừng đến với Hệ thống Đăng ký Học phần</h1>
    <p class="lead">Hệ thống quản lý đăng ký học phần cho sinh viên</p>
    <hr class="my-4">
    <?php if (isset($_SESSION['masv'])): ?>
        <p>Bạn đã đăng nhập với mã sinh viên: <?= htmlspecialchars($_SESSION['masv']) ?></p>
        <a class="btn btn-primary btn-lg" href="dangky" role="button">Đến trang đăng ký</a>
    <?php else: ?>
        <p>Vui lòng đăng nhập để sử dụng hệ thống</p>
        <a class="btn btn-primary btn-lg" href="auth/login.php" role="button">Đăng nhập ngay</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Quản lý Sinh viên</h5>
                <p class="card-text">Thêm, sửa, xóa thông tin sinh viên trong hệ thống.</p>
                <a href="sinhvien" class="btn btn-outline-primary">Truy cập</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Danh sách Học phần</h5>
                <p class="card-text">Xem danh sách các học phần có thể đăng ký.</p>
                <a href="hocphan" class="btn btn-outline-primary">Truy cập</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Đăng ký Học phần</h5>
                <p class="card-text">Đăng ký các học phần cho sinh viên.</p>
                <?php if (isset($_SESSION['masv'])): ?>
                    <a href="dangky" class="btn btn-outline-primary">Truy cập</a>
                <?php else: ?>
                    <button class="btn btn-outline-secondary" disabled>Vui lòng đăng nhập</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>