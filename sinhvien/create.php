<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';
require_once '../includes/header.php';

// Lấy danh sách ngành học
$nganhHoc = $conn->query("SELECT * FROM NganhHoc");
?>

<h2 class="mb-4">Thêm Sinh Viên Mới</h2>

<form action="process.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="MaSV" class="form-label">Mã Sinh Viên</label>
            <input type="text" class="form-control" id="MaSV" name="MaSV" required maxlength="10">
        </div>
        <div class="col-md-6">
            <label for="HoTen" class="form-label">Họ và Tên</label>
            <input type="text" class="form-control" id="HoTen" name="HoTen" required>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="GioiTinh" class="form-label">Giới Tính</label>
            <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                <option value="">Chọn giới tính</option>
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="NgaySinh" class="form-label">Ngày Sinh</label>
            <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" required>
        </div>
        <div class="col-md-4">
            <label for="MaNganh" class="form-label">Ngành Học</label>
            <select class="form-select" id="MaNganh" name="MaNganh" required>
                <option value="">Chọn ngành học</option>
                <?php while($row = $nganhHoc->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['MaNganh']) ?>"><?= htmlspecialchars($row['TenNganh']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="Hinh" class="form-label">Hình ảnh</label>
        <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
    </div>
    
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
        <button type="submit" class="btn btn-primary">Thêm Sinh Viên</button>
    </div>
</form>

<?php require_once '../includes/footer.php'; ?>