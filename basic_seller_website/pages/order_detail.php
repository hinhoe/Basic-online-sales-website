<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kiểm tra xem có truyền id đơn hàng lên URL không
if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}
$order_id = $_GET['id'];

// Lấy thông tin user để hiển thị Sidebar
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();
$display_avatar = !empty($user['avatar']) ? "../img/avatars/" . $user['avatar'] : "../img/avatars/default.png";

// 1. LẤY THÔNG TIN CHUNG CỦA ĐƠN HÀNG (Phải đảm bảo đơn hàng này của user đang đăng nhập)
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt_order->execute([$order_id, $user_id]);
$order = $stmt_order->fetch();

if (!$order) {
    die("<div style='text-align:center; padding:50px;'>Đơn hàng không tồn tại hoặc bạn không có quyền xem! <br><a href='my_orders.php'>Quay lại</a></div>");
}

// 2. LẤY CHI TIẾT SẢN PHẨM TRONG ĐƠN HÀNG (JOIN bảng order_items và products)
$sql_items = "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll();

// Hàm dịch trạng thái
function getStatusLabel($status) {
    switch($status) {
        case 'pending': return '<span class="badge badge-warning">⏳ Chờ duyệt</span>';
        case 'processing': return '<span class="badge badge-info">📦 Đang chuẩn bị</span>';
        case 'shipped': return '<span class="badge badge-purple">🚚 Đang giao</span>';
        case 'completed': return '<span class="badge badge-success">✅ Hoàn thành</span>';
        case 'cancelled': return '<span class="badge badge-danger">❌ Đã hủy</span>';
        default: return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết đơn hàng #<?php echo $order_id; ?> - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS Sidebar giữ nguyên */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f4f6f8}

    .container{max-width:1000px; margin:40px auto; display:flex; gap:25px; padding:0 20px;}
    .sidebar{width:280px; background:white; border-radius:15px; padding:30px; text-align:center; height:fit-content; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    .avatar{width:120px; height:120px; border-radius:50%; margin-bottom:15px; object-fit:cover; border:3px solid #e8f0fe;}
    .menu-item{padding:12px; border-radius:8px; cursor:pointer; color:#555; text-decoration:none; display:block; text-align: left; margin-top:10px;transition: all 0.25s ease;}
    .menu-item:hover{background:#f1f5ff; color:#2f6fd6; transform: translateX(3px);}
    .active{background:#e8f0fe; color:#2f6fd6; font-weight:600;}
    
    .content{flex:1; background:white; padding:40px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    
    /* CSS cho phần chi tiết đơn hàng */
    .header-content { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
    .btn-back { padding: 8px 15px; background: #e0e0e0; color: #333; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s;}
    .btn-back:hover { background: #d0d0d0; }
    
    .info-box { background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #eee;}
    .info-box p { margin-bottom: 8px; color: #555; }
    .info-box strong { color: #333; }
    
    .item-list { width: 100%; border-collapse: collapse; }
    .item-list th { text-align: left; padding: 12px; border-bottom: 2px solid #ddd; color: #555; }
    .item-list td { padding: 15px 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
    .product-info { display: flex; align-items: center; gap: 15px; }
    .product-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid #ddd;}
    .price { color: #e53935; font-weight: bold; }
    
    .total-row { text-align: right; padding-top: 20px; font-size: 18px; }
    .total-row .price { font-size: 24px; }

    /* Badge trạng thái */
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; display: inline-block; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-info { background: #d1ecf1; color: #0c5460; }
    .badge-purple { background: #e2d9f3; color: #5a32a3; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <img class="avatar" src="<?php echo $display_avatar; ?>">
        <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
        <p style="color:#777; font-size:14px;"><?php echo htmlspecialchars($user['email']); ?></p>
        <div style="margin-top:20px;">
            <a href="user.php" class="menu-item">👤 Thông tin cá nhân</a>
            <a href="/cart.php" class="menu-item">🛒 Giỏ hàng của tôi</a>
            <a href="/my_orders.php" class="menu-item active">📦 Đơn hàng của tôi</a>
            <a href="../auth/logout.php" class="menu-item">🚪 Đăng xuất</a>
        </div>
    </div>

    <div class="content">
        <div class="header-content">
            <h2>Chi tiết đơn hàng #<?php echo $order['id']; ?></h2>
            <a href="my_orders.php" class="btn-back">⬅ Quay lại</a>
        </div>

        <div class="info-box">
            <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Ngày đặt hàng:</strong> <?php echo date('H:i - d/m/Y', strtotime($order['created_at'])); ?></p>
            <p style="margin-top: 10px;"><strong>Trạng thái:</strong> <?php echo getStatusLabel($order['status']); ?></p>
        </div>

        <h3>Danh sách sản phẩm</h3>
        <table class="item-list">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_products as $p) {
            // Sử dụng $final_prices đã tính ở trên thay vì $p['price'] gốc
            $stmt_item->execute([$order_id, $p['id'], $p['cart_qty'], $final_prices[$p['id']]]);
        }
                ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="<?php echo (strpos($item['image'], 'http') === 0) ? $item['image'] : '../' . $item['image']; ?>" alt="Hình sản phẩm">
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                        </div>
                    </td>
                    <td class="price"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                    <td>x<?php echo $item['quantity']; ?></td>
                    <td class="price"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">
            Tổng cộng: <span class="price"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
        </div>
    </div>
</div>

</body>
</html>