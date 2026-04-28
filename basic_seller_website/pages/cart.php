<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// 1. CHẶN NẾU CHƯA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Vui lòng đăng nhập để xem giỏ hàng của bạn!');
        window.location.href = '../auth/login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Xử lý Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $id_remove = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $id_remove]);
    header("Location: cart.php");
    exit();
}

// 3. Xử lý Cập nhật số lượng sản phẩm
if (isset($_POST['update_cart']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $product_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
        }
    }
    header("Location: cart.php");
    exit();
}

// 4. Lấy dữ liệu giỏ hàng của User này
$sql = "SELECT c.quantity as cart_qty, p.* FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$cart_products = $stmt->fetchAll();

$total_all = 0;
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
        .qty-input { width: 60px; padding: 8px; text-align: center; border: 1px solid #ccc; border-radius: 6px; outline: none;}
        .qty-input:focus { border-color: #2f6fd6; }
        .btn-checkout { background: #2f6fd6; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-checkout:hover { background: #1f5bb8; }
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
        <p style="text-align:center; padding: 40px;">Giỏ hàng trống. <a href="../index.php">Mua sắm ngay!</a></p>
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
                        // Lấy số lượng
                        $qty = $p['cart_qty']; 
                        
                        // LOGIC TÍNH GIẢM GIÁ
                        $original_price = (int)$p['price'];
                        $discount = isset($p['discount']) ? (int)$p['discount'] : 0;
                        $discount = max(0, min($discount, 90)); 
                        
                        // Giá chốt để tính tiền (nếu có giảm giá thì lấy giá sale, ko thì lấy giá gốc)
                        $final_price = $original_price;
                        if ($discount > 0) {
                            $final_price = $original_price - ($original_price * $discount / 100);
                            if($final_price <= 0) $final_price = 1000;
                        }

                        $subtotal = $final_price * $qty;
                        $total_all += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?php echo (strpos($p['image'], 'http') === 0) ? $p['image'] : '/basic_seller_web/' . $p['image']; ?>" alt="Ảnh sản phẩm">
                                <span><?php echo htmlspecialchars($p['name']); ?></span>
                            </div>
                        </td>
                        <td class="price">
                            <?php echo number_format($final_price, 0, ',', '.'); ?>đ
                            <!-- Hiển thị nhỏ giá gốc gạch ngang ở dưới nếu có giảm giá -->
                            <?php if($discount > 0): ?>
                                <br><small style="color:#999; text-decoration:line-through; font-weight:normal;"><?php echo number_format($original_price, 0, ',', '.'); ?>đ</small>
                            <?php endif; ?>
                        </td>
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
                <button type="button" class="btn-checkout" onclick="window.location.href='checkout.php'">TIẾN HÀNH THANH TOÁN</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>