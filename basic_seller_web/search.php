<?php
session_start();
require_once 'db.php';

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
    /* CSS giữ nguyên từ giao diện chuẩn của bạn */
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
    .page-title {margin-bottom: 20px; font-size: 24px; color: #333; border-bottom: 2px solid #2f6fd6; padding-bottom: 10px; display: inline-block;}
    
    .products{display:grid;grid-template-columns:repeat(5,1fr);gap:20px}
    .card{background:white;padding:15px;border-radius:12px;text-align:center; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
    .card:hover{transform:translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1);}
.card img {
    width: 100%;
    height: 180px;
    object-fit: contain; /* Thu nhỏ ảnh sản phẩm cho vừa vặn */
    border-radius: 10px;
    background-color: #ffffff; 
}
    .card h4 {font-size: 16px; margin-bottom: 5px; color: #333; height: 38px; overflow: hidden;}
    .price{color:red;font-weight:700; font-size: 18px;}
    button{margin-top:10px;padding:8px 12px;border:none;background:#1f6ed4;color:white;border-radius:8px; cursor: pointer; width: 100%;}
    button:hover {background: #1557a6;}
    
    .no-results { grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 12px; }
    .no-results h3 { color: #555; margin-bottom: 15px; }
    .keyword-highlight { color: #e53935; }
</style>
</head>

<body>
<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png" alt="Logo">
        PickleMeow Shop
    </a>

    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Tìm kiếm sản phẩm... (Nhấn Enter)" value="<?php echo htmlspecialchars($keyword); ?>" required>
        </form>
    </div>

    <div class="header-links">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="user.php">👤 <?php echo $_SESSION['fullname']; ?></a>
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a>
        <?php endif; ?>
        <a href="cart.php">Giỏ hàng</a>
    </div>
</div>

<div class="container">
    <h2 class="page-title">Kết quả tìm kiếm cho: <span class="keyword-highlight">"<?php echo htmlspecialchars($keyword); ?>"</span></h2>

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
            <div class="no-results">
                <h3>Rất tiếc, không tìm thấy sản phẩm nào phù hợp với từ khóa này.</h3>
                <p>Vui lòng thử lại bằng các từ khóa khác ngắn gọn hoặc chung chung hơn (Ví dụ: "Điện thoại", "Tai nghe").</p>
                <br>
                <a href="index.php" style="color: #2f6fd6; text-decoration: none; font-weight: bold;">&larr; Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>