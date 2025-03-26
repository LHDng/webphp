<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';
require_once '../includes/header.php';
require_once '../includes/auth_check.php';

$masv = $_SESSION['masv'];

// Lấy thông tin sinh viên
$sqlSV = "SELECT * FROM SinhVien WHERE MaSV = '$masv'";
$sv = $conn->query($sqlSV)->fetch_assoc();

// Lấy danh sách học phần đã đăng ký
$sql = "SELECT dk.MaDK, hp.MaHP, hp.TenHP, hp.SoTinChi 
        FROM DangKy dk 
        JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK 
        JOIN HocPhan hp ON ctdk.MaHP = hp.MaHP 
        WHERE dk.MaSV = '$masv'";
$result = $conn->query($sql);

$tongTinChi = 0;
$soHocPhan = $result->num_rows;
$hocPhanDK = [];
while ($row = $result->fetch_assoc()) {
    $hocPhanDK[] = $row;
    $tongTinChi += $row['SoTinChi'];
}
?>

<h2 class="mb-4">Đăng Ký Học Phần - <?= htmlspecialchars($sv['HoTen']) ?></h2>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Học phần đã đăng ký</h5>
    </div>
    
    <div class="card-body">
        <?php if ($soHocPhan > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-info">
                        <th>Mã HP</th>
                        <th>Tên Học Phần</th>
                        <th>Số TC</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hocPhanDK as $hp): ?>
                    <tr>
                        <td><?= htmlspecialchars($hp['MaHP']) ?></td>
                        <td><?= htmlspecialchars($hp['TenHP']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($hp['SoTinChi']) ?></td>
                        <td class="text-center">
                            <a href="delete.php?madk=<?= $hp['MaDK'] ?>&mahp=<?= $hp['MaHP'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Bạn có chắc muốn hủy đăng ký học phần này?')">
                                <i class="fas fa-trash-alt"></i> Hủy
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">Bạn chưa đăng ký học phần nào</div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-info mb-0">
                    <strong>Số học phần:</strong> <?= $soHocPhan ?><br>
                    <strong>Tổng số tín chỉ:</strong> <?= $tongTinChi ?>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <a href="../hocphan" class="btn btn-primary">
                    <i class="fas fa-book"></i> Đăng ký thêm
                </a>
                <?php if ($soHocPhan > 0): ?>
                <a href="save.php" class="btn btn-success">
                    <i class="fas fa-save"></i> Lưu đăng ký
                </a>
                <a href="clear.php" class="btn btn-danger" 
                   onclick="return confirm('Bạn có chắc muốn xóa tất cả đăng ký?')">
                    <i class="fas fa-trash"></i> Xóa tất cả
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>