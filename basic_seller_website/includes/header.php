<?php

$base_path = (basename($_SERVER['SCRIPT_NAME']) === 'index.php') ? '' : '../';
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// 0. Lấy Avatar
$avatar_top = $base_path .  "img/avatars/default.png";
if (isset($_SESSION['user_id'])) {
    $stmt_u = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt_u->execute([$_SESSION['user_id']]);
    $current_user = $stmt_u->fetch();
    
    if ($current_user && !empty($current_user['avatar'])) {
        $avatar_top = $base_path .  "img/avatars/" . $current_user['avatar'];
    }
}

// 1. Lấy danh sách danh mục (dùng cho cả mega menu ở sidebar nếu các trang khác cũng có sidebar)
$sql_categories = "SELECT * FROM categories";
$category_products = [];
$sql_all_products = "SELECT * FROM products ORDER BY id DESC";
$stmt_all = $conn->prepare($sql_all_products);
$stmt_all->execute();
$all_products = $stmt_all->fetchAll();

foreach($all_products as $prod){
    $category_products[$prod['category_id']][] = $prod;
}
$stmt_cat = $conn->prepare($sql_categories);
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    /* Bê nguyên toàn bộ phần CSS của bạn vào đây (tôi rút gọn hiển thị ở đây để tránh dài dòng) */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f3f5f7}
/* ===== TOPBAR ===== */
    .topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;}
    .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
    .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
    .search-box{flex:1;margin:0 40px;}
    .search-box input{width:100%;padding:10px 15px;border:none;border-radius:20px;outline:none;}
    
    /* ===== HEADER LINKS ===== */
    .header-links{display:flex;align-items:center;gap:20px;}
    .header-links a{color:white;text-decoration:none;font-weight:500;}
    
    .user-menu {position: relative; display: flex; align-items: center;}
    .avatar-img-top {width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid white; cursor: pointer; background: white;}
    .user-dropdown {
        position: absolute; top: 100%; right: 0; background: white; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15); display: none; flex-direction: column;
        min-width: 160px; overflow: hidden; z-index: 1000; margin-top: 10px;
    }
    .user-menu:hover .user-dropdown {display: flex;}
    .user-dropdown a {color: #333; padding: 12px 15px; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 500;}
    .user-dropdown a:hover {background: #f5f7ff;}
    .user-dropdown a:last-child {border-bottom: none; color: #e53935;}
        /* MENU DƯỚI TOPBAR */
    .menu{background:white;padding:15px 30px;display:flex;gap:30px;font-weight:600;}
    .menu a{color:#333;text-decoration:none}

</style>
</head>
<body>

<div class="topbar">
    <a href="<?php echo $base_path; ?>index.php" class="logo">
        <img src="<?php echo $base_path; ?>img/pickle_meow_logo.png" alt="Logo">
        PickleMeow Shop
    </a>

    <div class="search-box">
        <form action="<?php echo $base_path; ?>pages/search.php" method="GET">
            <input type="text" name="q" placeholder="Tìm kiếm sản phẩm... (Nhấn Enter)" required>
        </form>
    </div>

    <div class="header-links">
        <a href="<?php echo $base_path; ?>pages/cart.php">Giỏ hàng</a>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_path; ?>admin/admin.php">Chỉnh sửa sản phẩm</a>
            <?php endif; ?>

            <div class="user-menu">
                <a href="<?php echo $base_path; ?>pages/user.php">
                    <img src="<?php echo $avatar_top; ?>" alt="User Avatar" class="avatar-img-top">
                </a>
                <div class="user-dropdown">
                    <a href="<?php echo $base_path; ?>pages/user.php">Hồ sơ cá nhân</a>
                    <a href="<?php echo $base_path; ?>auth/logout.php">Đăng xuất</a>
                </div>
            </div>

        <?php else: ?>
            <a href="<?php echo $base_path; ?>auth/login.php">Đăng nhập</a>
        <?php endif; ?>
    </div>
</div>

<div class="menu">
    <a href="#">Sản phẩm mới</a>
    <a href="<?php echo $base_path; ?>pages/sale_off.php">Khuyến mãi</a>
</div>