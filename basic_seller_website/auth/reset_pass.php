<?php
require_once 'db.php'; // Gọi kết nối database

// Tạo mã băm chuẩn xác cho mật khẩu '123456'
$mat_khau_chuan = password_hash('123456', PASSWORD_DEFAULT);

try {
    // Cập nhật lại toàn bộ mật khẩu trong bảng users thành mã băm chuẩn
    $sql = "UPDATE users SET password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['password' => $mat_khau_chuan]);
    
    echo "<h1>Cập nhật thành công!</h1>";
    echo "<p>Mã băm chuẩn của 123456 là: <br> <b>$mat_khau_chuan</b></p>";
    echo "<p>Bây giờ bạn có thể quay lại <a href='login.php'>Trang đăng nhập</a> để thử lại.</p>";
    
} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>