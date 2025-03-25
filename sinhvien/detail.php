<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Kiểm tra có ID sinh viên không
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$masv = $_GET['id'];

// Lấy thông tin chi tiết sinh viên
$sql = "SELECT sv.*, nh.TenNganh 
        FROM SinhVien sv 
        JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
        WHERE sv.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masv);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?error=Sinh+viên+không+tồn+tại");
    exit();
}

$sv = $result->fetch_assoc();

// Xử lý đường dẫn ảnh
$imagePath = !empty($sv['Hinh']) ? '../' . ltrim($sv['Hinh'], '/') : '';
$imageExists = !empty($imagePath) && file_exists(__DIR__ . '/' . $imagePath);
?>

<div class="container">
    <h2 class="my-4">Thông tin Chi tiết Sinh viên</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Thông tin cá nhân</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Cột ảnh -->
                <div class="col-md-4 text-center">
                    <?php if ($imageExists): ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             class="img-thumbnail mb-3" 
                             style="max-width: 250px;" 
                             alt="Ảnh sinh viên">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 250px; height: 250px; margin: 0 auto;">
                            <i class="fas fa-user fa-5x text-secondary"></i>
                        </div>
                        <p class="text-muted mt-2">Không có ảnh</p>
                    <?php endif; ?>
                </div>
                
                <!-- Cột thông tin -->
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($sv['MaSV']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Họ và tên:</strong> <?= htmlspecialchars($sv['HoTen']) ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Giới tính:</strong> <?= htmlspecialchars($sv['GioiTinh']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($sv['NgaySinh'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Ngành học:</strong> <?= htmlspecialchars($sv['TenNganh']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Mã ngành:</strong> <?= htmlspecialchars($sv['MaNganh']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Nút điều hướng -->
    <div class="mb-4">
        <a href="edit.php?id=<?= $sv['MaSV'] ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Sửa thông tin
        </a>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>