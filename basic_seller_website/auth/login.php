<?php
session_start(); // Bắt đầu phiên làm việc
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$error = "";
$isPost = false; // thêm dòng này

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginBtn'])) {
    $isPost = true; // thêm dòng này
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // 1. Tìm người dùng theo email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // 2. Kiểm tra sự tồn tại và xác thực mật khẩu
        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công, lưu thông tin vào Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // 3. Điều hướng dựa trên quyền hạn
            if ($user['role'] == 'admin') {
                header("Location: /basic_seller_web/admin/admin.php");
            } else {
                header("Location: /basic_seller_web/index.php");
            }
            exit();
        } else {
            $error = "Email hoặc mật khẩu không chính xác!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - PickleMeow Shop</title>
    <style>

/* ===== WRAPPER CĂN GIỮA MÀN HÌNH ===== */
.login-container{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* ===== CARD LOGIN ===== */
.login-box{
    width:380px;
    background:#fff;
    padding:40px 35px;
    border-radius:18px;
    box-shadow:0 25px 60px rgba(0,0,0,0.15);
    text-align:center;
    animation:fadeIn 0.6s ease;
}

/* Hiệu ứng xuất hiện */
@keyframes fadeIn{
    from{ opacity:0; transform:translateY(20px);}
    to{ opacity:1; transform:translateY(0);}
}

.login-box h2{
    margin-bottom:25px;
    color:#2f6fd6;
    font-size:26px;
}

/* INPUT */
.login-box input{
    width:100%;
    padding:13px 15px;
    margin-bottom:18px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:15px;
    transition:0.25s;
}

.login-box input:focus{
    border-color:#2f6fd6;
    box-shadow:0 0 0 4px rgba(47,111,214,0.15);
    outline:none;
}

/* BUTTON */
.login-box button{
    width:100%;
    padding:13px;
    background:linear-gradient(90deg,#2f6fd6,#4f8cff);
    border:none;
    border-radius:10px;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.login-box button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(0,0,0,0.15);
}

/* ERROR */
.error-msg{
    background:#ffe5e5;
    color:#d8000c;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
}

/* LINK ĐĂNG KÝ */
.login-box p{ margin-top:15px; font-size:14px; }
.login-box a{
    color:#2f6fd6;
    text-decoration:none;
    font-weight:600;
}
.login-box a:hover{ text-decoration:underline; }

/* Trang trí nền bằng bóng tròn */
body::before, body::after{
    content:"";
    position:fixed;
    width:350px;
    height:350px;
    border-radius:50%;
    background:rgba(255,255,255,0.25);
    filter:blur(60px);
    z-index:-1;
}
body::before{ top:-80px; left:-80px; }
body::after{ bottom:-80px; right:-80px; }
    </style>
</head>
<body>

<div class="login-container">
    <form action="/basic_seller_web/auth/login.php" method="POST" class="login-box">
        <h2>ĐĂNG NHẬP</h2>
        
        <?php if ($isPost && !empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email (ví dụ: NguyenVanA@gmail.com)" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button type="submit" name="loginBtn">Đăng nhập</button>        
        <p>Chưa có tài khoản? <a href="/basic_seller_web/auth/register.php">Đăng ký ngay</a></p>
    </form>
</div>

</body>
</html>