<?php
session_start();
require_once 'db.php';

// 1. Lấy danh sách danh mục để hiển thị ở Sidebar
$sql_categories = "SELECT * FROM categories";
$stmt_cat = $conn->prepare($sql_categories);
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll();

// 2. Lấy danh sách sản phẩm mới nhất (tối đa 8 cái)
$sql_products = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
$stmt_prod = $conn->prepare($sql_products);
$stmt_prod->execute();
$products = $stmt_prod->fetchAll();
//3. Lấy ảnh ngẫu nhiên làm hero
// LẤY NGẪU NHIÊN 1 ẢNH SẢN PHẨM LÀM BANNER
$sql_random = "SELECT image FROM products ORDER BY RAND() LIMIT 1";
$stmt_rand = $conn->prepare($sql_random);
$stmt_rand->execute();
$random_banner = $stmt_rand->fetch();

// Nếu trong database có ảnh thì lấy ảnh đó, nếu rỗng thì dùng ảnh mặc định
$banner_url = ($random_banner) ? $random_banner['image'] : "img/pickle_meow_logo.png";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PickleMeow Shop - Home</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    /* Giữ nguyên phần CSS cũ của bạn */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f3f5f7}
    .topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;}
    .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
    .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
    .search-box{flex:1;margin:0 40px;}
    .search-box input{width:100%;padding:10px 15px;border:none;border-radius:20px;outline:none;}
    .header-links{display:flex;gap:20px;}
    .header-links a{color:white;text-decoration:none;font-weight:500;}
    .menu{background:white;padding:15px 30px;display:flex;gap:30px;font-weight:600;}
    .menu a{color:#333;text-decoration:none}
    .container{display:flex;padding:20px 30px;gap:20px;}
    .sidebar{width:250px;background:white;padding:20px;border-radius:12px;}
    .sidebar h3{margin-bottom:15px}
    .sidebar ul{list-style:none}
    .sidebar li{padding:10px 0;border-bottom:1px solid #eee;}
    .sidebar a{text-decoration:none; color:#333;}
    .main{flex:1;}
    .hero {
        background: white;
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 20px;
    }

.hero-img {
    width: 100%;
    height: 350px; 
    object-fit: contain; /* Đổi từ cover sang contain để ảnh tự thu nhỏ vừa khung */
    border-radius: 10px;
    background-color: #ffffff; /* Thêm nền trắng đề phòng ảnh nhỏ hơn khung sẽ bị lộ nền */
}
    .section-title{margin:25px 0 15px;font-size:22px;font-weight:700;}
    .products{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
    .card{background:white;border-radius:15px;padding:15px;text-align:center;transition:.2s;}
    .card:hover{transform:translateY(-5px)}
.card img {
    width: 100%;
    height: 180px;
    object-fit: contain; /* Thu nhỏ ảnh sản phẩm cho vừa vặn */
    border-radius: 10px;
    background-color: #ffffff; 
}
    .price{color:#e53935;font-weight:700;margin-top:8px}
    button{margin-top:10px;padding:8px 15px;border:none;background:#1f6ed4;color:white;border-radius:8px;cursor:pointer;}
    footer{margin-top:40px;padding:20px;background:#1f6ed4;color:white;text-align:center;}
</style>
</head>

<body>

<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png">
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

<div class="menu">
    <a href="#">Sản phẩm mới</a>
    <a href="#">Khuyến mãi</a>
    <a href="#">Tin tức</a>
</div>

<div class="container">
    <div class="sidebar">
        <h3>Danh mục</h3>
        <ul>
            <?php foreach($categories as $cat): ?>
                <li>
                    <a href="category.php?id=<?php echo $cat['id']; ?>">
                        <?php echo $cat['name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main">
        <div class="hero">
            <img src="<?php echo $banner_url; ?>" class="hero-img" alt="Banner Khuyến Mãi">
        </div>

        <div class="section-title">Sản phẩm nổi bật</div>

        <div class="products">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $p): ?>
                    <div class="card">
                        <img src="<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
                        <h4><?php echo $p['name']; ?></h4>
                        <div class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</div>
                        <button onclick="location.href='product.php?id=<?php echo $p['id']; ?>'">Xem chi tiết</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Hiện chưa có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    © 2026 PickleMeow Shop - Design by thg Huyngu 
</footer>

</body>
</html>