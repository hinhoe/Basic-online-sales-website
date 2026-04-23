<?php
// 1. Khai báo các thông số kết nối
$host = 'localhost';
$port = '3307'; // Cổng SQL của bạn
$dbname = 'picklemeow_shop';
$username = 'root';
$password = ''; // Nếu vẫn lỗi 1045, hãy thử mật khẩu là 'root' hoặc để trống

try {
    // 2. Thiết lập chuỗi DSN với Port 3307
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    // 3. Khởi tạo kết nối
    $conn = new PDO($dsn, $username, $password);
    
    // Thiết lập báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /*=== xóa // của echo để xem có chạy hay không, nếu chạy thì để làm ghi chú lại ===*/
    // echo "Kết nối thành công với cổng 3307!"; 
    
} catch(PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>