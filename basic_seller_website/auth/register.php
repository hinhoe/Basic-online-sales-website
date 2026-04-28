<?php
session_start();
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/header.php'; 

// Nếu đã đăng nhập thì đẩy về trang chủ, không cho vào trang đăng ký nữa
if (isset($_SESSION['user_id'])) {
    header("Location: /basic_seller_web/index.php");
    exit();
}

$error_msg = '';
$success_msg = '';

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG KÝ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Kiểm tra không để trống
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_msg = "Vui lòng nhập đầy đủ thông tin!";
    } 
    // 2. Kiểm tra mật khẩu xác nhận có khớp không
    elseif ($password !== $confirm_password) {
        $error_msg = "Mật khẩu xác nhận không khớp!";
    } 
    // 3. Kiểm tra độ dài mật khẩu (bảo mật cơ bản)
    elseif (strlen($password) < 6) {
        $error_msg = "Mật khẩu phải có ít nhất 6 ký tự!";
    } 
    else {
        // 4. Kiểm tra xem Email đã tồn tại trong Database chưa
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->execute([$email]);
        
        if ($stmt_check->rowCount() > 0) {
            $error_msg = "Email này đã được đăng ký. Vui lòng dùng email khác!";
        } else {
            // 5. BĂM MẬT KHẨU (CỰC KỲ QUAN TRỌNG)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 6. Lưu vào Database (Mặc định role là 'user', avatar là 'default.png')
            $sql = "INSERT INTO users (fullname, email, password, role, avatar) VALUES (?, ?, ?, 'user', 'default.png')";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$fullname, $email, $hashed_password])) {
                $success_msg = "Đăng ký thành công! Đang chuyển hướng đến trang Đăng nhập...";
                // Tự động chuyển về trang login sau 2 giây
                header("refresh:2;url=login.php"); 
            } else {
                $error_msg = "Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký tài khoản - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background: #f3f5f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    

    .auth-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
    .logo-container { text-align: center; margin-bottom: 20px; }
    .logo-container img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
    h2 { text-align: center; color: #333; margin-bottom: 20px; }
    
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 14px; }
    .input-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; box-sizing: border-box; }
    .input-group input:focus { border-color: #2f6fd6; outline: none; }
    
    .btn-submit { width: 100%; padding: 12px; background: #2f6fd6; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.2s; }
    .btn-submit:hover { background: #1f5bb5; }
    
    .msg { padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    
    .auth-links { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    .auth-links a { color: #2f6fd6; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>

<div class="auth-container">
    <div class="logo-container">
        <img src="/basic_seller_web/img/pickle_meow_logo.png" alt="PickleMeow Logo">
    </div>
    <h2>Tạo tài khoản mới</h2>

    <?php if($error_msg): ?>
        <div class="msg error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if($success_msg): ?>
        <div class="msg success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form action="/basic_seller_web/auth/register.php" method="POST">
        <div class="input-group">
            <label>Họ và Tên</label>
            <input type="text" name="fullname" placeholder="Nhập họ và tên..." required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Nhập địa chỉ email..." required>
        </div>

        <div class="input-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" placeholder="Nhập mật khẩu (Ít nhất 6 ký tự)" required>
        </div>

        <div class="input-group">
            <label>Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu..." required>
        </div>

        <button type="submit" class="btn-submit">ĐĂNG KÝ NGAY</button>
    </form>

    <div class="auth-links">
        Đã có tài khoản? <a href="../auth/login.php">Đăng nhập tại đây</a><br><br>
        <a href="../index.php" style="color: #888;">&larr; Quay lại Trang chủ</a>
    </div>
</div>

</body>
</html>