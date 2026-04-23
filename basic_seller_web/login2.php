<?php
session_start(); // Bắt đầu phiên làm việc
require_once 'db.php'; // Kết nối cơ sở dữ liệu

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                header("Location: admin.php");
            } else {
                header("Location: index.php");
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
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Inter;
}

body{
    background:#f3f4f6;
}

/* ===== TOPBAR ===== */
.topbar{
    background:#2f6fd6;
    padding:12px 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

/* LOGO */
.logo {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: bold;
}
.logo img {
    height: 60px;
    width: 60px;
    object-fit: cover;
    border-radius: 50%;
}

/* SEARCH */
.search-box{
    flex:1;
    margin:0 40px;
}
.search-box input{
    width:100%;
    padding:10px 15px;
    border:none;
    border-radius:20px;
    outline:none;
}

/* HEADER RIGHT */
.header-links{
    display:flex;
    gap:20px;
}
.header-links a{
    color:white;
    text-decoration:none;
    font-weight:500;
}
.header-links a:hover{
    opacity:0.8;
}

/* ===== MAIN CENTER ===== */
.main{
    height:calc(100vh - 84px);
    display:flex;
    justify-content:center;
    align-items:center;
}

/* WRAP */
.wrapper{
    display:flex;
    gap:40px;
}

/* ===== NOTICE ===== */
.notice-box{
    width:600px;
    background:white;
    border-radius:15px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.notice-title{
    font-size:20px;
    font-weight:700;
    color:#0f8b8d;
    margin-bottom:15px;
    border-bottom:2px solid #eee;
    padding-bottom:10px;
}

.notice-item{
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.notice-item h4{
    color:#0f8b8d;
    font-size:14px;
}

.notice-item span{
    font-size:12px;
    color:#777;
}

.notice-item a{
    float:right;
    color:red;
    font-size:12px;
    text-decoration:none;
}

/* ===== LOGIN ===== */
.login-box{
    width:320px;
    background:white;
    border-radius:15px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* TAB */
.tabs{
    display:flex;
    margin-bottom:20px;
}

/* TAB */
.tab{
    flex:1;
    text-align:center;
    padding:12px 0;
    cursor:pointer;
    background:#e5e7eb;
    color:#999;
    font-weight:500;
    transition:0.25s;
}

.tab:first-child{
    clip-path:polygon(0 0, 100% 0, 90% 100%, 0% 100%);
}

.tab:last-child{
    clip-path:polygon(10% 0, 100% 0, 100% 100%, 0 100%);
    margin-left:-20px;
}

.tab.active{
    background:white;
    color:#000;
    font-size:18px;
    font-weight:700;
    z-index:2;
    transform:scale(1.05);
    box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

/* INPUT */
input{
    width:100%;
    padding:12px;
    margin-top:12px;
    border-radius:8px;
    border:1px solid #ddd;
}

/* BUTTON */
button{
    width:100%;
    margin-top:15px;
    padding:12px;
    background:#0f8b8d;
    color:white;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}
button:hover{
    background:#0c6f70;
}

/* ERROR */
.error{
    color:red;
    font-size:13px;
    margin-top:10px;
}
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png">
        PickleMeow Shop
    </a>

    <div class="search-box">
        <input type="text" placeholder="Tìm kiếm sản phẩm...">
    </div>

    <div class="header-links">
        <a href="#">Đăng nhập</a>
        <a href="cart.php">Giỏ hàng</a>
        <a href="#">Tài khoản</a>
    </div>
</div>
<!-- MAIN -->
<div class="main">
    <div class="wrapper">

        <!-- LEFT -->
        <div class="notice-box">
            <div class="notice-title">THÔNG BÁO CHUNG</div>

            <div class="notice-item">
                <h4>Thông báo</h4>
                <span>06/03/2026</span>
                <a href="#">Xem chi tiết</a>
            </div>

            <div class="notice-item">
                <h4>Thông báo</h4>
                <span>09/02/2026</span>
                <a href="#">Xem chi tiết</a>
            </div>

            <div class="notice-item">
                <h4>Thông báo</h4>
                <span>23/01/2026</span>
                <a href="#">Xem chi tiết</a>
            </div>

            <div class="notice-item">
                <h4>Thông báo</h4>
                <span>13/01/2026</span>
                <a href="#">Xem chi tiết</a>
            </div>

            <div class="notice-item">
                <h4>Thông báo</h4>
                <span>31/12/2025</span>
                <a href="#">Xem chi tiết</a>
            </div>
        </div>

<div class="login-container">
    <form action="login.php" method="POST" class="login-box">
            <div class="tabs">
                <div class="tab active" onclick="selectTab(event,'user')">User</div>
                <div class="tab" onclick="selectTab(event,'admin')">Admin</div>
            </div>        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email (ví dụ: admin@gmail.com)" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button type="submit">Đăng nhập</button>
        
        <p>Chưa có tài khoản? <a href="#">Đăng ký ngay</a></p>
    </form>
</div>

<script>
let role = "user";

function selectTab(e, r){
    role = r;

    document.querySelectorAll(".tab").forEach(t=>{
        t.classList.remove("active");
    });

    e.target.classList.add("active");
}

function login(){
    let u = document.getElementById("username").value;
    let p = document.getElementById("password").value;
    let err = document.getElementById("error");

    if(u === "" || p === ""){
        err.innerText = "Vui lòng hãy nhập đầy đủ!";
        return;
    }

    if(role === "user"){
        window.location.href = "user.php";
    } else {
        window.location.href = "admin.php";
    }
}
</script>

</body>
</html>