<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';

// Kiểm tra đăng nhập - nếu chưa đăng nhập thì chuyển hướng
if (!isset($_SESSION['masv'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Lưu URL hiện tại để redirect lại sau khi đăng nhập
    $_SESSION['error'] = "Vui lòng đăng nhập để truy cập trang học phần";
    header("Location: ../auth/login.php");
    exit();
}

// Kiểm tra sinh viên có tồn tại trong database không
$sql_check = "SELECT * FROM SinhVien WHERE MaSV = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $_SESSION['masv']);
$stmt->execute();
$result_check = $stmt->get_result();


if ($result_check->num_rows == 0) {
    session_destroy();
    $_SESSION['error'] = "Tài khoản không tồn tại trong hệ thống";
    header("Location:../auth/login.php");
    exit();
}

require_once INCLUDES_PATH . '/header.php';

// Lấy danh sách học phần
$sql = "SELECT * FROM HocPhan ORDER BY MaHP";
$result = $conn->query($sql);
?>

<div class="container">
    <h2 class="my-4"><i class="fas fa-book me-2"></i>Danh Sách Học Phần</h2>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-primary">
                <tr>
                    <th width="15%">Mã HP</th>
                    <th>Tên Học Phần</th>
                    <th width="10%" class="text-center">Số TC</th>
                    <th width="15%" class="text-center">SL còn lại</th>
                    <th width="15%" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MaHP']) ?></td>
                        <td><?= htmlspecialchars($row['TenHP']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['SoTinChi']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['SoLuongDuKien']) ?></td>
                        <td class="text-center">
                            <?php if ($row['SoLuongDuKien'] > 0): ?>
                            <a href="register.php?mahp=<?= $row['MaHP'] ?>" 
                               class="btn btn-sm btn-success"
                               title="Đăng ký học phần này">
                                <i class="fas fa-plus-circle"></i> Đăng ký
                            </a>
                            <?php else: ?>
                            <span class="badge bg-secondary">Hết chỗ</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle me-2"></i>Hiện không có học phần nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>