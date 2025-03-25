<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/session.php';
require_once CONFIG_PATH . '/db.php';
require_once INCLUDES_PATH . '/header.php';

// Lấy danh sách sinh viên
$sql = "SELECT sv.*, nh.TenNganh FROM SinhVien sv JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh";
$result = $conn->query($sql);
?>

<h2 class="mb-4">Danh Sách Sinh Viên</h2>
<a href="create.php" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Thêm Sinh Viên Mới
</a>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Mã SV</th>
            <th>Họ Tên</th>
            <th>Giới Tính</th>
            <th>Ngày Sinh</th>
            <th>Ngành Học</th>
            <th>Hình ảnh</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaSV']) ?></td>
                    <td><?= htmlspecialchars($row['HoTen']) ?></td>
                    <td><?= htmlspecialchars($row['GioiTinh']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['NgaySinh'])) ?></td>
                    <td><?= htmlspecialchars($row['TenNganh']) ?></td>

                    <td class="text-center">
                        <?php
                        $imagePath = '../' . ltrim($row['Hinh'], '/'); // Đảm bảo đường dẫn đúng
                        if (!empty($row['Hinh']) && file_exists(__DIR__ . '/' . $imagePath)):
                            ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" class="img-thumbnail" width="80" alt="Ảnh sinh viên">
                        <?php else: ?>
                            <div class="no-image" style="width:80px;height:80px;background:#eee;display:inline-block;">
                                <i class="fas fa-user" style="font-size:40px;color:#999;line-height:80px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    
                    <td class="text-nowrap">
                        <a href="detail.php?id=<?= $row['MaSV'] ?>" class="btn btn-info btn-sm" title="Xem chi tiết">
                            <i class="fas fa-eye">Xem chi tiết</i>
                        </a>
                        <a href="edit.php?id=<?= $row['MaSV'] ?>" class="btn btn-warning btn-sm" title="Sửa">
                            <i class="fas fa-edit">Sửa</i>
                        </a>
                        <a href="delete.php?id=<?= $row['MaSV'] ?>" class="btn btn-danger btn-sm" title="Xóa"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?')">
                            <i class="fas fa-trash-alt">Xóa</i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">Không có sinh viên nào trong hệ thống</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>