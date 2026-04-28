<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php'; // Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn

// 1. Lấy tất cả sản phẩm có khuyến mãi (discount > 0) kèm theo tên danh mục
$sql = "SELECT p.*, c.name as cat_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.discount > 0 
        ORDER BY c.name ASC, p.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$discounted_products = $stmt->fetchAll();

// 2. Gom nhóm sản phẩm theo danh mục để dễ hiển thị
$grouped_products = [];
foreach ($discounted_products as $p) {
    $grouped_products[$p['cat_name']][] = $p;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sản Phẩm Khuyến Mãi - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* CSS giữ nguyên từ bản thiết kế của bạn */
*{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
body{background:#f3f5f7}

.container{padding:30px; max-width: 1200px; margin: 0 auto;}
.category-title {margin-bottom: 20px; font-size: 24px; color: #e74c3c; border-bottom: 2px solid #e74c3c; padding-bottom: 10px; display: inline-block; font-weight: bold;}
.products{display:grid;grid-template-columns:repeat(5,1fr);gap:20px; margin-bottom: 40px;}

/* Thêm position: relative để đặt badge % giảm giá */
.card{background:white;padding:15px;border-radius:12px;text-align:center; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: relative;}
.card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
.card img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    border-radius: 10px;
    background-color: #ffffff; 
}
.card h4 {font-size: 16px; margin-bottom: 5px; color: #333; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}

/* CSS cho phần giá khuyến mãi */
.price-container {height: 45px; display: flex; flex-direction: column; justify-content: center; margin-bottom: 5px;}
.old-price {color:#95a5a6; font-size: 13px; text-decoration: line-through;}
.new-price {color:#e74c3c; font-weight:700; font-size: 18px;}

/* CSS Nhãn giảm giá */
.badge-discount {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #e74c3c;
    color: white;
    font-size: 13px;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(231, 76, 60, 0.5);
    z-index: 2;
}

button{margin-top:10px;padding:8px 12px;border:none;background:#1f6ed4;color:white;border-radius:8px; cursor: pointer; width: 100%;}
button:hover {background: #1557a6;}
</style>
</head>
<body>

<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2c3e50; font-size: 32px;">🔥 SĂN SALE KHỦNG BỞI PICKLEMEOW 🔥</h1>
        <p style="color: #7f8c8d; margin-top: 10px;">Tổng hợp các deal hời nhất theo từng danh mục</p>
    </div>

    <?php if(!empty($grouped_products)): ?>
        
        <?php foreach($grouped_products as $cat_name => $products_in_cat): ?>
            
            <h2 class="category-title">► Danh mục: <?php echo htmlspecialchars($cat_name); ?></h2>
            
            <div class="products">
                <?php foreach($products_in_cat as $p): 
                    // Tính toán giá sau khi giảm
                    $sale_price = $p['price'] - ($p['price'] * $p['discount'] / 100);
                ?>
                    <div class="card">
                        <div class="badge-discount">-<?php echo $p['discount']; ?>%</div>
                        
                        <img src="<?php echo (strpos($p['image'], 'http') === 0) 
                            ? $p['image'] 
                            : '../' . $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        
                        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                        
                        <div class="price-container">
                            <span class="old-price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</span>
                            <span class="new-price"><?php echo number_format($sale_price, 0, ',', '.'); ?>đ</span>
                        </div>
                        
                        <button onclick="location.href='product.php?id=<?php echo $p['id']; ?>'">Xem chi tiết</button>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="color: #666; font-size: 20px;">Hiện tại Shop chưa có chương trình khuyến mãi nào 😭</h3>
            <p style="color: #999; margin-top: 10px;">Bạn hãy quay lại sau nhé!</p>
            <br>
            <a href="../index.php" style="display: inline-block; padding: 10px 20px; background: #1f6ed4; color: white; border-radius: 8px; text-decoration: none; font-weight: bold;">&larr; Khám phá sản phẩm khác</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>