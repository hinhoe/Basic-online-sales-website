<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Shop - Trang Chủ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .search-box { flex: 1; margin: 0 40px; }
        .search-box input { width: 100%; padding: 12px; border: none; border-radius: 8px; outline: none; }
        .header-actions a { color: white; margin-left: 20px; font-weight: 600; }
        .menu-bar { background: white; padding: 15px 30px; display: flex; gap: 30px; font-weight: 600; box-shadow: var(--shadow-sm); }
        .home-layout { display: flex; padding: 20px 30px; gap: 20px; }
        .sidebar-categories { width: 250px; background: white; padding: 20px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); height: fit-content;}
        .sidebar-categories li { padding: 12px 0; border-bottom: 1px solid var(--border-color); }
        .sidebar-categories li:last-child { border-bottom: none; }
        .hero { background: white; padding: 15px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);}
        .hero-tabs { display: flex; gap: 10px; margin-bottom: 10px; }
        .hero-tab { background: var(--border-color); padding: 8px 14px; border-radius: 20px; cursor: pointer; font-weight: 600; font-size: 14px;}
        .hero-tab.active { background: var(--primary); color: white; }
        .slider { position: relative; }
        .slider img { width: 100%; border-radius: var(--radius-md); object-fit: cover; height: 350px;}
        .slider-btn { position: absolute; top: 50%; transform: translateY(-50%); background: white; border: none; font-size: 18px; padding: 10px 15px; border-radius: 50%; cursor: pointer; box-shadow: var(--shadow-sm);}
        .slider-btn.prev { left: 10px; }
        .slider-btn.next { right: 10px; }
        .sub-banners { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0; }
        .sub-banners img { width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);}
        .products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        footer { margin-top: 40px; padding: 20px; background: var(--primary); color: white; text-align: center; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">
        <img src="https://picsum.photos/50" alt="Logo"> Demo Shop
    </div>
    <div class="search-box">
        <input type="text" placeholder="Tìm kiếm sản phẩm...">
    </div>
    <div class="header-actions">
        <a href="login.php">Đăng nhập</a>      
        <a href="cart.php">Giỏ hàng</a>
        <a href="user.php">Tài khoản</a>
    </div>
</div>

<div class="menu-bar">
    <a href="#">Sản phẩm mới</a>
    <a href="#">Khuyến mãi</a>
    <a href="#">Tin tức</a>
</div>

<div class="home-layout">
    <div class="sidebar-categories">
        <h3>Danh mục</h3>
        <ul style="list-style:none; margin-top:15px;">
            <li><a href="category.php">Máy tính</a></li>
            <li><a href="category.php">Điện thoại</a></li>
            <li><a href="category.php">Đồng hồ thông minh</a></li>      
            <li><a href="category.php">Âm thanh - Tai nghe</a></li>
            <li><a href="category.php">Phụ kiện</a></li>
        </ul>
    </div>

    <div style="flex: 1;">
        <div class="hero">
            <div class="hero-tabs">
                <div class="hero-tab active" onclick="showSlide(0)">REDMI NOTE 15</div>
                <div class="hero-tab" onclick="showSlide(1)">GALAXY A17</div>
                <div class="hero-tab" onclick="showSlide(2)">MACBOOK M5</div>
                <div class="hero-tab" onclick="showSlide(3)">LAPTOP SALE</div>
            </div>
            <div class="slider">
                <img id="slideImg" src="https://picsum.photos/1200/350?1" alt="Slider">
                <button class="slider-btn prev" onclick="nextSlide(-1)">❮</button>
                <button class="slider-btn next" onclick="nextSlide(1)">❯</button>
            </div>
        </div>

        <div class="sub-banners">
            <img src="https://picsum.photos/400/160?1" alt="Banner 1">
            <img src="https://picsum.photos/400/160?2" alt="Banner 2">
            <img src="https://picsum.photos/400/160?3" alt="Banner 3">
        </div>

        <h2 style="margin: 30px 0 20px;">Sản phẩm nổi bật</h2>
        <div class="products-grid">
            <div class="card">
                <img src="https://picsum.photos/200?1" alt="SP">
                <h4>iPhone 15</h4>
                <div class="price">22.990.000đ</div>
                <button class="btn" onclick="location.href='product.php'">Xem chi tiết</button>
            </div>
            <div class="card">
                <img src="https://picsum.photos/200?2" alt="SP">
                <h4>Galaxy S24</h4>
                <div class="price">18.990.000đ</div>
                <button class="btn" onclick="location.href='product.php'">Xem chi tiết</button>
            </div>
            <div class="card">
                <img src="https://picsum.photos/200?3" alt="SP">
                <h4>Macbook Air M5</h4>
                <div class="price">27.990.000đ</div>
                <button class="btn" onclick="location.href='product.php'">Xem chi tiết</button>
            </div>
            <div class="card">
                <img src="https://picsum.photos/200?4" alt="SP">
                <h4>AirPods Pro</h4>
                <div class="price">4.990.000đ</div>
                <button class="btn" onclick="location.href='product.php'">Xem chi tiết</button>
            </div>
        </div>
    </div>
</div>

<footer>© 2026 Demo Shop</footer>

<script src="..\\js\\main.js"></script>
</body>
</html>