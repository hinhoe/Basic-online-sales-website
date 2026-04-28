<?php
session_start();
require_once __DIR__.'/../config/db.php';

// KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../misc/ey.php");
    exit();
}

// XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$new_status, $order_id]);
    
    header("Location: admin_orders.php?msg=status_updated");
    exit();
}

// XỬ LÝ XÓA ĐƠN HÀNG (Tùy chọn, thường đơn hàng chỉ nên Hủy chứ không nên Xóa hẳn)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
    header("Location: admin_orders.php?msg=deleted");
    exit();
}

// TRUY VẤN DANH SÁCH ĐƠN HÀNG
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

// Hàm dịch trạng thái sang tiếng Việt cho đẹp
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
    <title>Quản lý Đơn Hàng - PickleMeow Shop</title>
    <style>
        /* Giữ nguyên toàn bộ CSS từ admin.php của bạn */
        body { font-family: 'Segoe UI', Arial; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 240px; background: #d1d6dc; color: white;  height: 100vh; padding: 20px; position: fixed; border-left: 3px solid #f0f2f5; }
        .sidebar a { color: #01080d; text-decoration: none; display: block; padding: 10px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; }
        .main { flex: 1; margin-left: 240px; padding: 40px; border-left: 15px solid #f0f2f5; }
        .logot{ background-color: #b198e1; border-radius: 10px;}
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #2f6fd6; color: white; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; color: white; text-decoration: none; font-size: 14px; margin-right: 5px; display: inline-block;}
        .btn-update { background: #3498db; }
        .btn-delete { background: #e74c3c; }
        
        .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
        .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
        .alert { padding: 15px; background-color: #4CAF50; color: white; margin-bottom: 20px; border-radius: 5px; }
        
        select.status-dropdown { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="sum">
    <div class="sidebar">
        <div class="logot">
            <a href="../index.php" class="logo">
                <img src="../img/pickle_meow_logo.png">                <br>PickleMeow Shop
            </a>    
        </div>
        
        <nav>
            <a href="../index.php" style="color: #000000 ">🏠 Trở về Shop</a>
            <a href="../admin/admin.php" style="color: #000000">🛍️ Quản lý Sản phẩm</a>
            <a href="../admin/admin_orders.php" class="active">📦 Quản lý Đơn hàng</a>
            <a href="../auth/logout.php" style="color: #000000; margin-top: 50px;">🚪 Đăng xuất</a>
        </nav>
    </div>

    <div class="main">
        <h1>Danh sách Đơn hàng</h1>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'status_updated'): ?>
            <div class="alert">Cập nhật trạng thái đơn hàng thành công!</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><strong>#<?php echo $o['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($o['full_name']); ?></td>
                    <td>
                        SĐT: <?php echo htmlspecialchars($o['phone']); ?><br>
                        <small><?php echo htmlspecialchars($o['address']); ?></small>
                    </td>
                    <td style="color: #e74c3c; font-weight: bold;">
                        <?php echo number_format($o['total_amount'], 0, ',', '.'); ?>đ
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:flex; gap:5px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status" class="status-dropdown">
                                <option value="pending" <?php echo $o['status'] == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                <option value="processing" <?php echo $o['status'] == 'processing' ? 'selected' : ''; ?>>Đang chuẩn bị</option>
                                <option value="shipped" <?php echo $o['status'] == 'shipped' ? 'selected' : ''; ?>>Đang giao</option>
                                <option value="completed" <?php echo $o['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                <option value="cancelled" <?php echo $o['status'] == 'cancelled' ? 'selected' : ''; ?>>Hủy đơn</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-update" style="padding: 6px 10px;">Lưu</button>
                        </form>
                        <div style="margin-top: 8px;"><?php echo getStatusLabel($o['status']); ?></div>
                    </td>
                    <td>
                        <button class="btn" style="background:#7f8c8d;" onclick="alert('Tính năng xem chi tiết sản phẩm trong đơn sẽ cập nhật sau!')">Chi tiết</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($orders)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px;">Chưa có đơn hàng nào!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>