<?php
session_start();
require_once 'db.php';

// Lấy toàn bộ sản phẩm
$sql = "SELECT * FROM products ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tất cả sản phẩm - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
body{background:#f3f5f7}
.topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;}
.logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
.logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
.search-box{flex:1;margin:0 40px;}
.search-box input{width:100%;padding:10px 15px;border:none;border-radius:20px;outline:none;}
.header-links{display:flex;gap:20px;}
.header-links a{color:white;text-decoration:none;font-weight:500;}
.header-links a:hover{opacity:0.8;}

.container{padding:30px; max-width: 1200px; margin: 0 auto;}
.category-title {margin-bottom: 20px; font-size: 24px; color: #333; border-bottom: 2px solid #2f6fd6; padding-bottom: 10px; display: inline-block;}
.products{display:grid;grid-template-columns:repeat(5,1fr);gap:20px}
.card{background:white;padding:15px;border-radius:12px;text-align:center; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
.card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
.card img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    border-radius: 10px;
    background-color: #ffffff; 
}
.card h4 {font-size: 16px; margin-bottom: 5px; color: #333; height: 38px; overflow: hidden;}
.price{color:red;font-weight:700; font-size: 18px;}
button{margin-top:10px;padding:8px 12px;border:none;background:#1f6ed4;color:white;border-radius:8px; cursor: pointer; width: 100%;}
button:hover {background: #1557a6;}
</style>
</head>

<body>

<!-- TOPBAR GIỮ NGUYÊN -->
<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png" alt="Logo">
        PickleMeow Shop
    </a>

    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Tìm kiếm sản phẩm... (Nhấn Enter)" required>
        </form>
    </div>

    <div class="header-links">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="user.php">Chào, <?php echo $_SESSION['fullname']; ?></a>
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a>
        <?php endif; ?>
        <a href="cart.php">Giỏ hàng</a>
    </div>
</div>

<div class="container">
    <h2 class="category-title">Tất cả sản phẩm</h2>

    <div class="products">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $p): ?>
                <div class="card">
                    <img src="<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
                    <h4><?php echo $p['name']; ?></h4>
                    <div class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</div>
                    <button onclick="location.href='product.php?id=<?php echo $p['id']; ?>'">
                        Xem chi tiết
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 10px;">
                <h3 style="color: #666;">Chưa có sản phẩm nào.</h3>
                <br>
                <a href="index.php" style="color: #1f6ed4; text-decoration: none;">← Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>