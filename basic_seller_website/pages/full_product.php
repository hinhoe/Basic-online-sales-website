<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// 1. Truy vấn LẤY TOÀN BỘ SẢN PHẨM kèm theo tên Danh mục
$sql = "SELECT p.*, c.name as cat_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        ORDER BY c.name ASC, p.id DESC"; // Sắp xếp theo tên danh mục trước, rồi tới sản phẩm mới nhất
$stmt = $conn->prepare($sql);
$stmt->execute();
$all_products = $stmt->fetchAll();

// 2. Gom nhóm sản phẩm theo tên danh mục
$grouped_products = [];
foreach ($all_products as $p) {
    $grouped_products[$p['cat_name']][] = $p;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tất cả sản phẩm - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* CSS CƠ BẢN */
*{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
body{background:#f3f5f7; scroll-behavior: smooth; /* Hiệu ứng cuộn mượt khi bấm menu */}

.container{padding:30px; max-width: 1200px; margin: 0 auto;}

/* TIÊU ĐỀ & MENU ĐIỀU HƯỚNG NHANH */
.page-title {text-align: center; font-size: 28px; color: #2c3e50; margin-bottom: 20px;}
.category-nav {display: flex; gap: 10px; margin-bottom: 40px; flex-wrap: wrap; justify-content: center;}
.category-nav a {
    padding: 8px 18px; background: white; border: 1px solid #ddd; 
    border-radius: 20px; text-decoration: none; color: #555; 
    font-weight: 600; font-size: 14px; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.category-nav a:hover {background: #2f6fd6; color: white; border-color: #2f6fd6; transform: translateY(-2px);}

/* TIÊU ĐỀ TỪNG DANH MỤC */
.category-section {margin-bottom: 50px;}
.category-title {margin-bottom: 20px; font-size: 22px; color: #333; border-bottom: 2px solid #2f6fd6; padding-bottom: 10px; display: inline-block;}

/* ===== LƯỚI SẢN PHẨM VÀ CARD DÙNG CHUNG ===== */
.products{display:grid;grid-template-columns:repeat(5,1fr);gap:20px}
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
    <h1 class="page-title">🛍️ Khám phá tất cả sản phẩm</h1>

    <?php if(!empty($grouped_products)): ?>
        <div class="category-nav">
            <?php foreach(array_keys($grouped_products) as $cat_name): ?>
                <a href="#cat_<?php echo md5($cat_name); ?>"><?php echo htmlspecialchars($cat_name); ?></a>
            <?php endforeach; ?>
        </div>

        <?php foreach($grouped_products as $cat_name => $products_in_cat): ?>
            
            <div class="category-section" id="cat_<?php echo md5($cat_name); ?>">
                <h2 class="category-title">► <?php echo htmlspecialchars($cat_name); ?></h2>
                
                <div class="products">
                    <?php foreach($products_in_cat as $p): ?>
                        
                        <?php include __DIR__ . '/../includes/product_card.php'; ?>
                        
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 10px;">
            <h3 style="color: #666;">Cửa hàng hiện chưa có sản phẩm nào.</h3>
            <br>
            <a href="../index.php" style="color: #1f6ed4; text-decoration: none; font-weight: bold;">&larr; Về trang chủ</a>
        </div>
    <?php endif; ?>

</div>

<?php 
// Gọi footer nếu có
if (file_exists(__DIR__ . '/../includes/footer.php')) {
    require_once __DIR__ . '/../includes/footer.php'; 
}
?>

</body>
</html>