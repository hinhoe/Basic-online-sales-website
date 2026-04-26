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
    
    /* LƯỚI SẢN PHẨM */
    .products{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
    .card{background:white;border-radius:15px;padding:15px;text-align:center;transition:.2s;}
    .card:hover{transform:translateY(-5px)}
    .card img {width: 100%; height: 180px; object-fit: contain; border-radius: 10px; background-color: #ffffff;}
    .price{color:#e53935;font-weight:700;margin-top:8px}
    button{margin-top:10px;padding:8px 15px;border:none;background:#1f6ed4;color:white;border-radius:8px;cursor:pointer;}

   
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
                        foreach($category_products[$cat['id']] as $p):
                            if($count == 6) break;
                        ?>
                            <a class="mega-item" href="/basic_seller_web/pages/product.php?id=<?php echo $p['id']; ?>">
                                <img src="<?php echo $p['image']; ?>">
                                <div>
                                    <div><?php echo $p['name']; ?></div>
                                    <div class="mega-price">
                                        <?php echo number_format($p['price'],0,',','.'); ?>đ
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
                    <div class="card">
                        <img src="<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
                        <h4><?php echo $p['name']; ?></h4>
                        <div class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</div>
                        <button onclick="location.href='/basic_seller_web/pages/product.php?id=<?php echo $p['id']; ?>'">Xem chi tiết</button>
                    </div>
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