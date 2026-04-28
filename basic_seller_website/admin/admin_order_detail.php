<?php
session_start();
require_once __DIR__.'/../config/db.php';

// KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../misc/ey.php");
    exit();
}

// Kiểm tra ID đơn hàng
if (!isset($_GET['id'])) {
    header("Location: admin_orders.php");
    exit();
}
$order_id = $_GET['id'];

// Lấy thông tin đơn hàng
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch();

if (!$order) {
    die("<div style='padding:50px; text-align:center;'>Đơn hàng không tồn tại. <a href='admin_orders.php'>Quay lại</a></div>");
}

// Lấy chi tiết các sản phẩm trong đơn hàng
$sql_items = "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll();

function getStatusLabel($status) {
    switch($status) {
        case 'pending': return '<span style="color:#f39c12; font-weight:bold;">⏳ Chờ duyệt</span>';
        case 'processing': return '<span style="color:#3498db; font-weight:bold;">📦 Đang chuẩn bị</span>';
        case 'shipped': return '<span style="color:#9b59b6; font-weight:bold;">🚚 Đang giao hàng</span>';
        case 'completed': return '<span style="color:#27ae60; font-weight:bold;">✅ Đã hoàn thành</span>';
        case 'cancelled': return '<span style="color:#e74c3c; font-weight:bold;">❌ Đã hủy</span>';
        default: return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?> - Admin</title>
    <style>
        /* CSS dùng chung của Admin */
        body { font-family: 'Segoe UI', Arial; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 240px; background: #d1d6dc; color: white; height: 100vh; padding: 20px; position: fixed; border-left: 3px solid #f0f2f5; }
        .sidebar a { color: #01080d; text-decoration: none; display: block; padding: 10px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; }
        .main { flex: 1; margin-left: 240px; padding: 40px; border-left: 15px solid #f0f2f5; }
        .logot{ background-color: #b198e1; border-radius: 10px;}
        .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
        .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}

        /* CSS cho chi tiết đơn hàng */
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .header-action { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .btn-back { padding: 8px 15px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-back:hover { background: #7f8c8d; }
        
        .customer-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 25px;}
        .customer-info p { margin: 5px 0; color: #333; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #eee; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #f4f6f8; color: #333; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid #ddd;}
        
        .total-box { text-align: right; padding: 20px 0; font-size: 18px; color: #333; }
        .total-box .price { font-size: 26px; color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
<div class="sum">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logot">
            <a href="../index.php" class="logo">
                <img src="../img/pickle_meow_logo.png">                <br>PickleMeow Shop
            </a>    
        </div>
        
        <nav>
            <a href="../index.php" style="color: #000000 ">🏠 Trở về Shop</a>
            <a href="admin.php" style="color: #000000">🛍️ Quản lý Sản phẩm</a>
            <a href="admin_orders.php" class="active">📦 Quản lý Đơn hàng</a>
            <a href="../auth/logout.php" style="color: #000000; margin-top: 50px;">🚪 Đăng xuất</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="card">
            <div class="header-action">
                <h1 style="margin: 0; color: #2c3e50;">Chi tiết đơn hàng #<?php echo $order['id']; ?></h1>
                <a href="admin_orders.php" class="btn-back">⬅ Quay lại danh sách</a>
            </div>

            <div class="customer-info">
                <div>
                    <h3 style="margin-top: 0; color: #2980b9;">Thông tin khách hàng</h3>
                    <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                </div>
                <div>
                    <h3 style="margin-top: 0; color: #2980b9;">Thông tin giao hàng</h3>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p><strong>Ngày đặt:</strong> <?php echo date('H:i - d/m/Y', strtotime($order['created_at'])); ?></p>
                    <p><strong>Trạng thái:</strong> <?php echo getStatusLabel($order['status']); ?></p>
                </div>
            </div>

            <h3 style="color: #2c3e50;">Sản phẩm đã mua</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?php echo (strpos($item['image'], 'http') === 0) ? $item['image'] : '../' . $item['image']; ?>">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            </div>
                        </td>
                        <td style="color: #e74c3c; font-weight: bold;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                        <td>x<?php echo $item['quantity']; ?></td>
                        <td style="color: #e74c3c; font-weight: bold;"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-box">
                Tổng giá trị đơn hàng: <span class="price"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>