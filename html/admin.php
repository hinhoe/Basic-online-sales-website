<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-box { background: white; padding: 25px; border-radius: var(--radius-lg); text-align: center; box-shadow: var(--shadow-sm); }
        .stat-box h3 { font-size: 28px; color: var(--success); margin-bottom: 5px; }
        
        .task-list { background: white; padding: 25px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-bottom: 20px;}
        .task-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color); }
        .task-item:last-child { border-bottom: none; }
        .badge { background: var(--danger); color: white; border-radius: 20px; padding: 4px 10px; font-size: 13px; font-weight: bold;}
    </style>
</head>
<body>

<div class="dashboard-layout">
    <div class="sidebar">
        <div style="text-align:center; margin-bottom: 30px;">
            <img src="https://picsum.photos/60" style="border-radius:50%; margin-bottom: 10px;">
            <div style="font-size: 20px; font-weight: bold; color: var(--primary);">GagShop Admin</div>
        </div>
        <div class="sidebar-menu">
            <div class="active">🏠 Trang chủ</div>
            <div>🛍️ Sản phẩm</div>
            <div>📦 Đơn hàng</div>
            <div>💬 Trò chuyện</div>
            <div>⚙️ Cài đặt</div>
            <div onclick="openModal('logoutModal')" style="color: var(--danger); margin-top: 50px;">🚪 Đăng xuất</div>
        </div>
    </div>

    <div class="main-content">
        <h2 style="margin-bottom: 20px;">Tổng quan hàng ngày</h2>
        
        <div class="stats-grid">
            <div class="stat-box">
                <h3>9,999M</h3>
                <p>Doanh thu</p>
            </div>
            <div class="stat-box">
                <h3>999</h3>
                <p>Đơn hàng</p>
            </div>
            <div class="stat-box">
                <h3>99</h3>
                <p>Khách truy cập</p>
            </div>
        </div>

        <div class="task-list">
            <h3 style="margin-bottom: 15px;">📋 Nhiệm vụ cần xử lý</h3>
            <div class="task-item">
                <span>Đơn hàng phải vận chuyển trong 24 giờ</span>
                <span class="badge">67</span>
            </div>
            <div class="task-item">
                <span>Đơn hàng sẵn sàng vận chuyển</span>
                <span class="badge">12</span>
            </div>
            <div class="task-item">
                <span>Hàng trả lại đang chờ xử lý</span>
                <span class="badge">5</span>
            </div>
        </div>
    </div>
</div>

<div id="logoutModal" class="overlay">
    <div class="modal-box">
        <h3 style="margin-bottom: 20px;">Bạn có chắc muốn đăng xuất?</h3>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button class="btn btn-danger" onclick="confirmLogout()">Đăng xuất</button>
            <button class="btn" style="background: #ccc; color: #333;" onclick="closeModal('logoutModal')">Hủy</button>
        </div>
    </div>
</div>

<script src="..\\js\\main.js"></script>
</body>
</html>