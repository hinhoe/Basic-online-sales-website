<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Xử lý Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $id_remove = $_GET['remove'];
    unset($_SESSION['cart'][$id_remove]);
    header("Location: cart.php");
    exit();
}

// Thêm đoạn logic này: Xử lý Cập nhật số lượng sản phẩm
if (isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $_SESSION['cart'][$id] = $quantity;
            } else {
                // Nếu người dùng nhập số lượng <= 0, tự động xóa khỏi giỏ
                unset($_SESSION['cart'][$id]);
            }
        }
    }
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
        body { font-family: 'Inter', sans-serif; background: #f4f6f8; margin: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #555; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
        .price { color: #e53935; font-weight: bold; }
        .total-section { text-align: right; margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
        
        /* Chỉnh style cho ô input số lượng */
        .qty-input { width: 60px; padding: 8px; text-align: center; border: 1px solid #ccc; border-radius: 6px; outline: none;}
        .qty-input:focus { border-color: #2f6fd6; }
        
        .btn-checkout { background: #2f6fd6; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-checkout:hover { background: #1f5bb8; }
        
        /* Nút cập nhật giỏ hàng */
        .btn-update { background: #e0e0e0; color: #333; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-right: 15px; transition: 0.2s;}
        .btn-update:hover { background: #d0d0d0; }
        
        .btn-remove { color: #e53935; text-decoration: none; font-size: 14px; }
        .btn-remove:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($cart_products)): ?>
        <p style="text-align:center; padding: 40px;">Giỏ hàng trống. <a href="/basic_seller_web/index.php">Mua sắm ngay!</a></p>
    <?php else: ?>
        <form action="cart.php" method="POST">
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
                                <img src="<?php echo (strpos($p['image'], 'http') === 0) 
                        ? $p['image'] 
                        : '/basic_seller_web/' . $p['image']; ?> " alt="Ảnh sản phẩm">
                                <span><?php echo htmlspecialchars($p['name']); ?></span>
                            </div>
                        </td>
                        <td class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                        <td>
                            <input type="number" name="qty[<?php echo $p['id']; ?>]" value="<?php echo $qty; ?>" min="1" class="qty-input">
                        </td>
                        <td class="price"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                        <td><a href="?remove=<?php echo $p['id']; ?>" class="btn-remove">Xóa</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-section">
                <h3>Tổng cộng: <span class="price" style="font-size: 24px;"><?php echo number_format($total_all, 0, ',', '.'); ?>đ</span></h3>
                <br>
                <button type="submit" name="update_cart" class="btn-update">CẬP NHẬT GIỎ HÀNG</button>
                <button type="button" class="btn-checkout" onclick="alert('Tính năng thanh toán sẽ kết nối với bảng Orders ở bước sau!')">TIẾN HÀNH THANH TOÁN</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>