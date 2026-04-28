<?php
// Tự động nạp session, DB, avatar, logic menu, HTML head, topbar...
require_once  'includes/header.php'; 

// 1. Lấy danh sách sản phẩm mới nhất (tối đa 8 cái)
$sql_products = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
$stmt_prod = $conn->prepare($sql_products);
$stmt_prod->execute();
$products = $stmt_prod->fetchAll();

// 2. LẤY DANH SÁCH BANNER (Các sản phẩm được Admin set is_banner = 1)
$sql_banners = "SELECT id, image, name FROM products WHERE is_banner = 1 ORDER BY id DESC LIMIT 4";
$stmt_banners = $conn->prepare($sql_banners);
$stmt_banners->execute();
$banners = $stmt_banners->fetchAll();

// NẾU ADMIN CHƯA CHỌN BANNER NÀO -> Lấy tạm 1 ảnh logo hoặc ảnh ngẫu nhiên để chống cháy
if (count($banners) == 0) {
    $sql_random = "SELECT id, image, name FROM products ORDER BY RAND() LIMIT 1";
    $stmt_rand = $conn->prepare($sql_random);
    $stmt_rand->execute();
    $random = $stmt_rand->fetch();
    if($random) {
        $banners[] = $random;
    } else {
        $banners[] = ['id' => 0, 'image' => 'img/pickle_meow_logo.png', 'name' => 'PickleMeow Shop'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PickleMeow Shop - Home</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS CƠ BẢN */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f3f5f7}    
    
    /* LAYOUT CHÍNH */
    .container{display:flex;padding:20px 30px;gap:20px; max-width: 1300px; margin: 0 auto;}
    .sidebar{width:250px;background:white;padding:20px;border-radius:12px; position:relative}
    .sidebar h3{margin-bottom:15px}
    .sidebar ul{list-style:none}
    .sidebar li{padding:10px 0;border-bottom:1px solid #eee; position:relative;}
    .sidebar a{text-decoration:none; color:#333;}
    .main{flex:1; min-width: 0;} /* min-width: 0 giúp chống tràn grid */
    .section-title{margin:25px 0 15px;font-size:22px;font-weight:700;}
    
    /* ===== CSS BANNER SLIDER TỰ ĐỘNG ===== */
    .hero-carousel { position: relative; width: 100%; border-radius: 15px; overflow: hidden; margin-bottom: 20px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .carousel-track { display: flex; transition: transform 0.5s ease-in-out; height: 350px; }
    .carousel-slide { min-width: 100%; height: 100%; position: relative; cursor: pointer; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: contain; background-color: #ffffff; padding: 10px;}
    
    /* Nút bấm Trái/Phải */
    .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.3); color: white; border: none; width: 40px; height: 40px; cursor: pointer; border-radius: 50%; font-size: 18px; z-index: 10; display: flex; align-items: center; justify-content: center; transition: 0.3s;}
    .carousel-btn:hover { background: rgba(0,0,0,0.7); }
    .btn-prev { left: 15px; }
    .btn-next { right: 15px; }
    
    /* Dấu chấm điều hướng (Dots) */
    .carousel-dots { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10; }
    .dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(200,200,200,0.6); cursor: pointer; transition: 0.3s; }
    .dot.active { background: #e74c3c; width: 25px; border-radius: 5px; }

    /* ===== MEGA MENU ===== */
    .mega-menu{
        position:absolute; top:0; left:100%; width:720px; background:white;
        border-radius:15px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.15);
        display:none; z-index:999;
    }
    .sidebar li:hover .mega-menu{display:block;}
    .mega-products{display:grid; grid-template-columns:repeat(3,1fr); gap:15px;}
    .mega-item{display:flex; gap:10px; align-items:center; text-decoration:none; color:#333; padding:8px; border-radius:10px; transition:.2s;}
    .mega-item:hover{background:#f5f7ff;}
    .mega-item img{width:60px; height:60px; object-fit:contain;}

    /* ===== LƯỚI SẢN PHẨM VÀ CARD DÙNG CHUNG ===== */
    .products{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
    .card{background:white;border-radius:15px;padding:15px;text-align:center;transition:.2s; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
    .card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
    .card img {width: 100%; height: 180px; object-fit: contain; border-radius: 10px; background-color: #ffffff;}
    .card h4 {font-size: 16px; margin: 10px 0 5px; color: #333; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}
    
    .price-container {height: 45px; display: flex; flex-direction: column; justify-content: center; margin-bottom: 5px;}
    .old-price {color:#95a5a6; font-size: 13px; text-decoration: line-through;}
    .new-price {color:#e74c3c; font-weight:700; font-size: 18px;}
    .badge-discount {
        position: absolute; top: 10px; right: 10px; background-color: #e74c3c; color: white;
        font-size: 13px; font-weight: bold; padding: 4px 8px; border-radius: 8px;
        box-shadow: 0 2px 5px rgba(231, 76, 60, 0.5); z-index: 2;
    }
    button{margin-top:10px;padding:8px 15px;border:none;background:#1f6ed4;color:white;border-radius:8px;cursor:pointer; width: 100%;}
    button:hover{background: #1557a6;}
</style>
</head>

<body>
<div class="container">
    <div class="sidebar">
        <h3> <a href="pages/full_product.php"> Danh mục </a> </h3>
        <ul>
        <?php foreach($categories as $cat): ?>
            <li>
                <a href="pages/category.php?id=<?php echo $cat['id']; ?>">
                    <?php echo $cat['name']; ?>
                </a>
                <div class="mega-menu">
                    <?php if(isset($category_products[$cat['id']])): ?>
                    <div class="mega-products">
                        <?php 
                        $count = 0;
                        foreach($category_products[$cat['id']] as $p_mega):
                            if($count == 6) break;
                            $discount_mega = isset($p_mega['discount']) ? (int)$p_mega['discount'] : 0;
                            $original_price_mega = $p_mega['price'];
                            $sale_price_mega = $original_price_mega - ($original_price_mega * $discount_mega / 100);
                            $img_mega_src = (strpos($p_mega['image'], 'http') === 0) 
                                ? $p_mega['image'] 
                                : $p_mega['image'];                        ?>
                            <a class="mega-item" href="pages/product.php?id=<?php echo $p_mega['id']; ?>">
                                <div style="position: relative;">
                                    <?php if($discount_mega > 0): ?>
                                        <div style="position: absolute; top: -5px; left: -5px; background: #e74c3c; color: white; font-size: 10px; font-weight: bold; padding: 2px 4px; border-radius: 5px; z-index: 2;">-<?php echo $discount_mega; ?>%</div>
                                    <?php endif; ?>
                                    <img src="<?php echo $img_mega_src; ?>">
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-size: 13px; margin-bottom: 2px; color: #333; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $p_mega['name']; ?></div>
                                    <?php if($discount_mega > 0): ?>
                                        <div style="text-decoration: line-through; color: #95a5a6; font-size: 11px;"><?php echo number_format($original_price_mega, 0, ',', '.'); ?>đ</div>
                                        <div style="color: #e74c3c; font-weight: 700; font-size: 14px;"><?php echo number_format($sale_price_mega, 0, ',', '.'); ?>đ</div>
                                    <?php else: ?>
                                        <div style="color: #e74c3c; font-weight: 700; font-size: 14px; margin-top: 14px;"><?php echo number_format($original_price_mega, 0, ',', '.'); ?>đ</div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php $count++; endforeach; ?>
                    </div>
                    <?php else: ?>
                        <p>Chưa có sản phẩm</p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="main">
        
        <div class="hero-carousel" id="heroCarousel" onmouseenter="pauseTimer()" onmouseleave="resumeTimer()">
            <div class="carousel-track" id="carouselTrack">
                <?php foreach($banners as $banner): ?>
                    <?php 
                        // Xử lý đường dẫn ảnh
                        $img_src = (strpos($banner['image'], 'http') === 0) 
                            ? $banner['image'] 
                            : $banner['image'];                        // Link tới sản phẩm (nếu id = 0 thì không link)
                        $link = ($banner['id'] > 0) ? "pages/product.php?id=" . $banner['id'] : "javascript:void(0)";
                    ?>
                    <div class="carousel-slide" onclick="location.href='<?php echo $link; ?>'">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($banner['name']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if(count($banners) > 1): ?>
                <button class="carousel-btn btn-prev" onclick="moveSlide(-1)">&#10094;</button>
                <button class="carousel-btn btn-next" onclick="moveSlide(1)">&#10095;</button>
                <div class="carousel-dots">
                    <?php for($i=0; $i<count($banners); $i++): ?>
                        <div class="dot <?php echo $i==0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $i; ?>)"></div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="section-title">Sản phẩm nổi bật</div>

        <div class="products">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $p): ?>
                    <?php include 'includes/product_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Hiện chưa có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
    const track = document.getElementById('carouselTrack');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = <?php echo count($banners); ?>;
    let currentIndex = 0;
    let slideInterval;

    // Hàm cập nhật vị trí ảnh và nút dots
    function updateCarousel() {
        if(totalSlides <= 1) return; // Nếu chỉ có 1 ảnh thì không cần lướt
        
        // Dịch chuyển track ảnh
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Cập nhật màu cho dots
        dots.forEach(dot => dot.classList.remove('active'));
        if(dots[currentIndex]) {
            dots[currentIndex].classList.add('active');
        }
    }

    // Hàm khi bấm nút Trái/Phải
    function moveSlide(step) {
        currentIndex += step;
        if (currentIndex >= totalSlides) currentIndex = 0;
        if (currentIndex < 0) currentIndex = totalSlides - 1;
        updateCarousel();
    }

    // Hàm khi bấm thẳng vào dấu chấm
    function currentSlide(index) {
        currentIndex = index;
        updateCarousel();
    }

    // Tự động lướt sau mỗi 3 giây (3000ms)
    function startTimer() {
        if(totalSlides > 1) {
            slideInterval = setInterval(() => moveSlide(1), 3000);
        }
    }

    // Tạm dừng khi rê chuột vào banner (để khách dễ click xem)
    function pauseTimer() {
        clearInterval(slideInterval);
    }

    // Tiếp tục lướt khi đưa chuột ra ngoài
    function resumeTimer() {
        startTimer();
    }

    // Khởi động khi tải xong trang
    startTimer();
</script>

</body>
</html>