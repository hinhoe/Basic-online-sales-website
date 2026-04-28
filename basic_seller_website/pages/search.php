<?php
session_start();
require_once __DIR__. '/../config/db.php';
require_once __DIR__. '/../includes/header.php'; 

$keyword = "";
$products = [];

// Kiểm tra xem người dùng có gửi từ khóa tìm kiếm lên không
if (isset($_GET['q'])) {
    $keyword = trim($_GET['q']);

    if (!empty($keyword)) {
        // Sử dụng LIKE để tìm kiếm trong tên sản phẩm HOẶC mô tả sản phẩm
        $sql = "SELECT * FROM products WHERE name LIKE :keyword OR description LIKE :keyword ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        
        // Thêm dấu % vào trước và sau từ khóa để tìm chuỗi có chứa từ này
        $stmt->execute(['keyword' => "%" . $keyword . "%"]);
        $products = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả tìm kiếm cho "<?php echo htmlspecialchars($keyword); ?>" - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS CƠ BẢN */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f3f5f7}

    .container{padding:30px; max-width: 1200px; margin: 0 auto;}
    .page-title {margin-bottom: 20px; font-size: 24px; color: #333; border-bottom: 2px solid #2f6fd6; padding-bottom: 10px; display: inline-block;}
    .keyword-highlight { color: #e53935; }
    
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
    
    .no-results { grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 12px; }
    .no-results h3 { color: #555; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="container">
    <h2 class="page-title">Kết quả tìm kiếm cho: <span class="keyword-highlight">"<?php echo htmlspecialchars($keyword); ?>"</span></h2>

    <div class="products">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $p): ?>
            
                <?php include __DIR__ . '/../includes/product_card.php'; ?>
                
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <h3>Rất tiếc, không tìm thấy sản phẩm nào phù hợp với từ khóa này.</h3>
                <p>Vui lòng thử lại bằng các từ khóa khác ngắn gọn hoặc chung chung hơn (Ví dụ: "Điện thoại", "Tai nghe").</p>
                <br>
                <a href="../index.php" style="color: #2f6fd6; text-decoration: none; font-weight: bold;">&larr; Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Tiện thể gọi luôn footer để trang hoàn thiện
if (file_exists(__DIR__ . '/../includes/footer.php')) {
    require_once __DIR__ . '/../includes/footer.php'; 
}
?>

</body>
</html>