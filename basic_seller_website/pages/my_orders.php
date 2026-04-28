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

// Lấy thông tin user để hiển thị Sidebar
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$display_avatar = !empty($user['avatar']) ? "../img/avatars/" . $user['avatar'] : "../img/avatars/default.png";

// Lấy danh sách đơn hàng của user này
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$my_orders = $stmt_orders->fetchAll();

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
<title>Đơn hàng của tôi - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS Sidebar (giữ nguyên của bạn) */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f4f6f8}

    .container{max-width:1000px; margin:40px auto; display:flex; gap:25px; padding:0 20px;}
    .sidebar{width:280px; background:white; border-radius:15px; padding:30px; text-align:center; height:fit-content; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    .avatar{width:120px; height:120px; border-radius:50%; margin-bottom:15px; object-fit:cover; border:3px solid #e8f0fe;}
    .menu-item{padding:12px; border-radius:8px; cursor:pointer; color:#555; text-decoration:none; display:block; text-align: left; margin-top:10px;transition: all 0.25s ease;}
    .menu-item:hover{background:#f1f5ff; color:#2f6fd6; transform: translateX(3px);}
    .active{background:#e8f0fe; color:#2f6fd6; font-weight:600;}
    
    .content{flex:1; background:white; padding:40px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    h2 {border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px;}
    
    /* CSS Bảng đơn hàng */
    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th { text-align: left; padding: 12px; background: #f8f9fa; border-bottom: 2px solid #ddd; color: #555;}
    .order-table td { padding: 15px 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    /* Badge trạng thái */
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; display: inline-block; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-info { background: #d1ecf1; color: #0c5460; }
    .badge-purple { background: #e2d9f3; color: #5a32a3; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    
    .price-text { color: #e53935; font-weight: bold; }
    .btn-detail { padding: 6px 12px; background: #e0e0e0; color: #333; text-decoration: none; border-radius: 5px; font-size: 13px; transition: 0.2s;}
    .btn-detail:hover { background: #d0d0d0; }
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
            <a href="cart.php" class="menu-item">🛒 Giỏ hàng của tôi</a>
            <a href="my_orders.php" class="menu-item active">📦 Đơn hàng của tôi</a>
            <a href="../auth/logout.php" class="menu-item">🚪 Đăng xuất</a>
        </div>
    </div>

    <div class="content">
        <h2>Lịch sử đơn hàng</h2>
        
        <?php if (empty($my_orders)): ?>
            <div style="text-align: center; padding: 40px 0; color: #777;">
                <p>Bạn chưa có đơn hàng nào.</p>
                <a href="../index.php" style="color: #2f6fd6; text-decoration: none; font-weight: bold; display:inline-block; margin-top: 10px;">Bắt đầu mua sắm ngay!</a>
            </div>
        <?php else: ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_orders as $o): ?>
                        <tr>
                            <td><strong>#<?php echo $o['id']; ?></strong></td>
                            <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                            <td class="price-text"><?php echo number_format($o['total_amount'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo getStatusLabel($o['status']); ?></td>
                            <td>
                                <a href="order_detail.php?id=<?php echo $o['id']; ?>" class="btn-detail">Chi tiết</a>                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>