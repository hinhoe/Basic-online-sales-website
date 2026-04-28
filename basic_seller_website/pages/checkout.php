<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$session_id = session_id();

// 1. Lấy dữ liệu giỏ hàng để hiển thị tóm tắt
if ($user_id) {
    $sql = "SELECT c.quantity as cart_qty, p.* FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
} else {
    $sql = "SELECT c.quantity as cart_qty, p.* FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$session_id]);
}
$cart_products = $stmt->fetchAll();

// Nếu giỏ hàng trống, quay về trang giỏ hàng
if (empty($cart_products)) {
    header("Location: cart.php");
    exit();
}

$total_all = 0;
// Tạo một mảng lưu lại giá cuối cùng của mỗi sản phẩm để lúc lưu Database dùng lại cho chuẩn
$final_prices = []; 

foreach ($cart_products as $p) {
    $original_price = (int)$p['price'];
    $discount = isset($p['discount']) ? (int)$p['discount'] : 0;
    $discount = max(0, min($discount, 90)); 
    
    $final_price = $original_price;
    if ($discount > 0) {
        $final_price = $original_price - ($original_price * $discount / 100);
        if($final_price <= 0) $final_price = 1000;
    }
    
    // Lưu lại giá đã chốt
    $final_prices[$p['id']] = $final_price;
    
    $total_all += $final_price * $p['cart_qty'];
}

// 2. Xử lý khi nhấn "Đặt hàng"
if (isset($_POST['place_order'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
        $conn->beginTransaction();

        // Lưu vào bảng orders
        $sql_order = "INSERT INTO orders (user_id, full_name, email, phone, address, total_amount) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->execute([$user_id, $full_name, $email, $phone, $address, $total_all]);
        $order_id = $conn->lastInsertId();

        // Lưu chi tiết vào bảng order_items
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);
        foreach ($cart_products as $p) {
            $stmt_item->execute([$order_id, $p['id'], $p['cart_qty'], $p['price']]);
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        if ($user_id) {
            $stmt_clear = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt_clear->execute([$user_id]);
        } else {
            $stmt_clear = $conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
            $stmt_clear->execute([$session_id]);
        }

        $conn->commit();
        echo "<script>alert('Đặt hàng thành công! Mã đơn hàng của bạn là: #$order_id'); window.location.href='../index.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        echo "Lỗi khi đặt hàng: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - PickleMeow Shop</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f8; }
        .checkout-container { max-width: 1100px; margin: 40px auto; display: grid; grid-template-columns: 1fr 400px; gap: 30px; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .btn-order { width: 100%; background: #27ae60; color: white; padding: 15px; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        .btn-order:hover { background: #219150; }
        .price { color: #e53935; font-weight: bold; }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="card">
        <h2>Thông tin giao hàng</h2>
        <form method="POST" id="checkout-form">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="full_name" required placeholder="Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="meow@example.com">
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" required placeholder="090xxxxxxx">
            </div>
            <div class="form-group">
                <label>Địa chỉ nhận hàng</label>
                <textarea name="address" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
            </div>
            <button type="submit" name="place_order" class="btn-order">XÁC NHẬN ĐẶT HÀNG</button>
        </form>
    </div>

    <div class="card">
        <h2>Đơn hàng của bạn</h2>
        <?php foreach ($cart_products as $p): ?>
            <div class="summary-item">
                <span><?php echo $p['name']; ?> (x<?php echo $p['cart_qty']; ?>)</span>
                <!-- Dùng $final_prices để hiển thị giá -->
                <span><?php echo number_format($final_prices[$p['id']] * $p['cart_qty'], 0, ',', '.'); ?>đ</span>
            </div>
        <?php endforeach; ?>
        <hr>
        <div class="summary-item" style="font-size: 20px; font-weight: bold;">
            <span>Tổng cộng:</span>
            <span class="price"><?php echo number_format($total_all, 0, ',', '.'); ?>đ</span>
        </div>
        <p style="font-size: 0.9em; color: #777; margin-top: 20px;">* Phương thức: Thanh toán khi nhận hàng (COD)</p>
    </div>
</div>

</body>
</html>