<?php
// 1. Bắt đầu (hoặc tiếp tục) phiên làm việc hiện tại
session_start();

// 2. Xóa toàn bộ dữ liệu đang lưu trong Session (như user_id, fullname, role)
session_unset();

// 3. Phá hủy hoàn toàn Session này (như việc xé vé sau khi xem xong phim)
session_destroy();

// 4. Điều hướng người dùng về lại trang đăng nhập (hoặc trang chủ)
header("Location: ../auth/login.php");
exit();
?>