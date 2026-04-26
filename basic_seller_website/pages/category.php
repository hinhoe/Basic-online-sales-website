<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// 1. Kiểm tra xem có truyền ID danh mục trên URL không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category_id = $_GET['id'];

    // 2. Lấy thông tin của danh mục này (để in ra tiêu đề)
    $sql_cat = "SELECT * FROM categories WHERE id = :id";
    $stmt_cat = $conn->prepare($sql_cat);
    $stmt_cat->execute(['id' => $category_id]);
    $category = $stmt_cat->fetch();

    // Nếu khách nhập sai ID (danh mục không tồn tại), đẩy về trang chủ
    if (!$category) {
        header("Location: /basic_seller_web/index.php");        exit();
    }

    // 3. Lấy tất cả sản phẩm thuộc danh mục này
    $sql_prods = "SELECT * FROM products WHERE category_id = :cat_id ORDER BY id DESC";
    $stmt_prods = $conn->prepare($sql_prods);
    $stmt_prods->execute(['cat_id' => $category_id]);
    $products = $stmt_prods->fetchAll();

} else {
    // Không có ID thì về trang chủ
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh mục <?php echo $category['name']; ?> - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* CSS giữ nguyên từ bản thiết kế của bạn */
*{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
body{background:#f3f5f7}


.container{padding:30px; max-width: 1200px; margin: 0 auto;}
.category-title {margin-bottom: 20px; font-size: 24px; color: #333; border-bottom: 2px solid #2f6fd6; padding-bottom: 10px; display: inline-block;}
.products{display:grid;grid-template-columns:repeat(5,1fr);gap:20px}
.card{background:white;padding:15px;border-radius:12px;text-align:center; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
.card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
.card img {
    width: 100%;
    height: 180px;
    object-fit: contain; /* Thu nhỏ ảnh sản phẩm cho vừa vặn */
    border-radius: 10px;
    background-color: #ffffff; 
}.card h4 {font-size: 16px; margin-bottom: 5px; color: #333; height: 38px; overflow: hidden;}
.price{color:red;font-weight:700; font-size: 18px;}
button{margin-top:10px;padding:8px 12px;border:none;background:#1f6ed4;color:white;border-radius:8px; cursor: pointer; width: 100%;}
button:hover {background: #1557a6;}
</style>
</head>

<div class="container">
    <h2 class="category-title">Sản phẩm thuộc: <?php echo $category['name']; ?></h2>

    <div class="products">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $p): ?>
                <div class="card">
                    <img src="<?php echo (strpos($p['image'], 'http') === 0) 
                        ? $p['image'] 
                        : '/basic_seller_web/' . $p['image']; ?>">
                        <h4><?php echo $p['name']; ?></h4>
                    <div class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</div>
                    <button onclick="location.href='product.php?id=<?php echo $p['id']; ?>'">Xem chi tiết</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 10px;">
                <h3 style="color: #666;">Chưa có sản phẩm nào trong danh mục này.</h3>
                <br>
                <a href="index.php" style="color: #1f6ed4; text-decoration: none;">&larr; Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>