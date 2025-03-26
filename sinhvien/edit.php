<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';


$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['csrf_token_expire'] = time() + 3600; // Token có hiệu lực 1 giờ


if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$masv = $_GET['id'];

// Lấy thông tin sinh viên
$sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masv);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?error=Sinh+viên+không+tồn+tại");
    exit();
}

$sv = $result->fetch_assoc();

// Lấy danh sách ngành học
$nganhHoc = $conn->query("SELECT * FROM NganhHoc");
?>

<div class="container">
    <h2 class="my-4">Sửa Thông Tin Sinh Viên</h2>
    
    <form action="process.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="MaSV" value="<?= htmlspecialchars($sv['MaSV']) ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="HoTen" class="form-label">Họ và Tên</label>
                <input type="text" class="form-control" id="HoTen" name="HoTen" 
                       value="<?= htmlspecialchars($sv['HoTen']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="GioiTinh" class="form-label">Giới Tính</label>
                <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                    <option value="Nam" <?= $sv['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                    <option value="Nữ" <?= $sv['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                    <option value="Khác" <?= $sv['GioiTinh'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="NgaySinh" class="form-label">Ngày Sinh</label>
                <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" 
                       value="<?= htmlspecialchars($sv['NgaySinh']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="MaNganh" class="form-label">Ngành Học</label>
                <select class="form-select" id="MaNganh" name="MaNganh" required>
                    <?php while($row = $nganhHoc->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['MaNganh']) ?>" 
                        <?= $row['MaNganh'] == $sv['MaNganh'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['TenNganh']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="Hinh" class="form-label">Hình ảnh</label>
            <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
            
            <?php if (!empty($sv['Hinh'])): ?>
            <div class="mt-3">
                <p>Ảnh hiện tại:</p>
                <img src="../<?= ltrim($sv['Hinh'], '/') ?>" class="img-thumbnail" style="max-width: 200px;" alt="Ảnh hiện tại">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="xoaAnh" name="xoaAnh">
                    <label class="form-check-label" for="xoaAnh">Xóa ảnh hiện tại</label>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="detail.php?id=<?= $sv['MaSV'] ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>