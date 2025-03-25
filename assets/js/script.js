// Hiển thị confirm khi xóa
document.addEventListener('DOMContentLoaded', function() {
    // Hiệu ứng fade cho các thông báo
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 1s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 1000);
        }, 5000);
    });
    
    // Xử lý form thêm/sửa sinh viên
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            }
        });
    });
    
    // Kiểm tra ngày sinh hợp lệ
    const ngaySinhInput = document.getElementById('NgaySinh');
    if (ngaySinhInput) {
        ngaySinhInput.max = new Date().toISOString().split('T')[0];
    }
});