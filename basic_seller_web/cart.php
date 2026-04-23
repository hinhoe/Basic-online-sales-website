<?php
session_start();
require_once 'db.php';

// Xử lý Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $id_remove = $_GET['remove'];
    unset($_SESSION['cart'][$id_remove]);
    header("Location: cart.php");
    exit();
}

$cart_products = [];
$total_all = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Lấy danh sách ID để truy vấn 1 lần duy nhất (tối ưu hiệu năng)
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);
    $cart_products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - PickleMeow Shop</title>
    <style>
        .topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;}
        .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
        .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
        .search-box{flex:1;margin:0 40px;}
        .search-box input{width:100%;padding:10px 15px;border:none;border-radius:20px;outline:none;}
        .header-links{display:flex;gap:20px;}
        .header-links a{color:white;text-decoration:none;font-weight:500;}
        body { font-family: 'Inter', sans-serif; background: #f4f6f8; margin: 0; }
        .topbar { background: #2f6fd6; padding: 15px 30px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #555; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
        .price { color: #e53935; font-weight: bold; }
        .total-section { text-align: right; margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
        .btn-checkout { background: #2f6fd6; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-remove { color: #e53935; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>


<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png">
        PickleMeow Shop
    </a>

    <div class="search-box">
        <input type="text" placeholder="Tìm kiếm sản phẩm...">
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

<div class="container">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($cart_products)): ?>
        <p style="text-align:center; padding: 40px;">Giỏ hàng trống. <a href="index.php">Mua sắm ngay!</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_products as $p): 
                    $qty = $_SESSION['cart'][$p['id']];
                    $subtotal = $p['price'] * $qty;
                    $total_all += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="<?php echo $p['image']; ?>">
                            <span><?php echo $p['name']; ?></span>
                        </div>
                    </td>
                    <td class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                    <td><?php echo $qty; ?></td>
                    <td class="price"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                    <td><a href="?remove=<?php echo $p['id']; ?>" class="btn-remove">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <h3>Tổng cộng: <span class="price" style="font-size: 24px;"><?php echo number_format($total_all, 0, ',', '.'); ?>đ</span></h3>
            <br>
            <button class="btn-checkout" onclick="alert('Tính năng thanh toán sẽ kết nối với bảng Orders ở bước sau!')">TIẾN HÀNH THANH TOÁN</button>
        </div>
    <?php endif; ?>
</div>

</body>
</html>