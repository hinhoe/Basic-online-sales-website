<?php
// Tự động nạp session, DB, avatar, logic menu, HTML head, topbar...
require_once 'includes/header.php'; 

// 2. Lấy danh sách sản phẩm mới nhất (tối đa 8 cái) dùng riêng cho index
$sql_products = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
$stmt_prod = $conn->prepare($sql_products);
$stmt_prod->execute();
$products = $stmt_prod->fetchAll();

// 3. Lấy ảnh ngẫu nhiên làm hero banner dùng riêng cho index
$sql_random = "SELECT image FROM products ORDER BY RAND() LIMIT 1";
$stmt_rand = $conn->prepare($sql_random);
$stmt_rand->execute();
$random_banner = $stmt_rand->fetch();
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
    /* CSS CƠ BẢN */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f3f5f7}    
    /* LAYOUT CHÍNH */
    .container{display:flex;padding:20px 30px;gap:20px;}
    .sidebar{width:250px;background:white;padding:20px;border-radius:12px; position:relative}
    .sidebar h3{margin-bottom:15px}
    .sidebar ul{list-style:none}
    .sidebar li{padding:10px 0;border-bottom:1px solid #eee; position:relative;}
    .sidebar a{text-decoration:none; color:#333;}
    .main{flex:1;}
    .hero {background: white; padding: 15px; border-radius: 15px; margin-bottom: 20px;}
    .hero-img {width: 100%; height: 350px; object-fit: contain; border-radius: 10px; background-color: #ffffff;}
    .section-title{margin:25px 0 15px;font-size:22px;font-weight:700;}
    
    /* ===== MEGA MENU SẢN PHẨM ===== */
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
    .mega-price{color:red; font-weight:600; font-size:14px;}

    /* ===== LƯỚI SẢN PHẨM VÀ CARD ===== */
    .products{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
    .card{background:white;border-radius:15px;padding:15px;text-align:center;transition:.2s; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
    .card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
    .card img {width: 100%; height: 180px; object-fit: contain; border-radius: 10px; background-color: #ffffff;}
    .card h4 {font-size: 16px; margin: 10px 0 5px; color: #333; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}
    
    /* CSS Giá & Khuyến mãi */
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
        <h3> <a href="/basic_seller_web/pages/full_product.php"> Danh mục </a> </h3>
        <ul>
        <?php foreach($categories as $cat): ?>
            <li>
                <a href="/basic_seller_web/pages/category.php?id=<?php echo $cat['id']; ?>">
                    <?php echo $cat['name']; ?>
                </a>

                <div class="mega-menu">
                    <?php if(isset($category_products[$cat['id']])): ?>
                    <div class="mega-products">
                        <?php 
                        $count = 0;
                        foreach($category_products[$cat['id']] as $p_mega):
                            if($count == 6) break;
                        ?>
                            <a class="mega-item" href="/basic_seller_web/pages/product.php?id=<?php echo $p_mega['id']; ?>">
                                <img src="<?php echo $p_mega['image']; ?>">
                                <div>
                                    <div style="font-size: 14px; margin-bottom: 4px;"><?php echo $p_mega['name']; ?></div>
                                    <div class="mega-price">
                                        <?php echo number_format($p_mega['price'],0,',','.'); ?>đ
                                    </div>
                                </div>
                            </a>
                        <?php 
                            $count++;
                        endforeach; 
                        ?>
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
        <div class="hero">
            <img src="<?php echo $banner_url; ?>" class="hero-img" alt="Banner Khuyến Mãi">
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

<?php
// Nạp phần footer và thẻ đóng
require_once 'includes/footer.php'; 
?>

</body>
</html>