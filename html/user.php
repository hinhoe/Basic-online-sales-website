<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản của tôi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .user-layout { display: flex; gap: 25px; }
        .user-sidebar { width: 280px; background: white; border-radius: var(--radius-lg); padding: 30px 20px; text-align: center; box-shadow: var(--shadow-sm); height: fit-content; }
        .user-sidebar img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid var(--border-color); }
        
        .user-content { flex: 1; background: white; padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main); }
        .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; outline: none; }
        .form-control:focus { border-color: var(--primary); }
        
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">Quản lý tài khoản</div>
    <div style="font-weight: 600; gap: 20px; display: flex;">
        <a href="index.php">Trang chủ</a>
        <a href="cart.php">Giỏ hàng</a>
    </div>
</div>

<div class="container">
    <div class="user-layout">
        <div class="user-sidebar">
            <img src="https://picsum.photos/200" alt="Avatar">
            <h3 id="displayName">User Demo</h3>
            <p id="displayEmail" style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">demo@email.com</p>

            <div class="sidebar-menu" style="text-align: left;">
                <div class="user-menu-item active" onclick="switchTab('tab-content', 'profileTab', 'user-menu-item', this)">👤 Thông tin cá nhân</div>
                <div class="user-menu-item" onclick="switchTab('tab-content', 'ordersTab', 'user-menu-item', this)">📦 Đơn hàng</div>
                <div class="user-menu-item" onclick="switchTab('tab-content', 'passwordTab', 'user-menu-item', this)">🔒 Đổi mật khẩu</div>
                <div style="color: var(--danger); margin-top: 20px;" onclick="openModal('logoutModal')">🚪 Đăng xuất</div>
            </div>
        </div>

        <div class="user-content">
            
            <div id="profileTab" class="tab-content active">
                <h2 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Chỉnh sửa thông tin</h2>
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" id="name" class="form-control" placeholder="Nhập họ tên">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Nhập email">
                </div>
                <button class="btn" onclick="saveProfile()">Lưu thay đổi</button>
            </div>

            <div id="ordersTab" class="tab-content">
                <h2 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Lịch sử đơn hàng</h2>
                <div style="padding: 40px; text-align: center; color: var(--text-muted); background: var(--bg-color); border-radius: var(--radius-md);">
                    <p>Bạn chưa có đơn hàng nào.</p>
                    <button class="btn" style="margin-top: 15px;" onclick="location.href='index.php'">Mua sắm ngay</button>
                </div>
            </div>

            <div id="passwordTab" class="tab-content">
                <h2 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Đổi mật khẩu</h2>
                <div class="form-group">
                    <label>Mật khẩu cũ</label>
                    <input type="password" class="form-control" placeholder="Nhập mật khẩu hiện tại">
                </div>
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" class="form-control" placeholder="Nhập mật khẩu mới">
                </div>
                <button class="btn btn-danger">Cập nhật mật khẩu</button>
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
<script>
    // Xử lý load/lưu dữ liệu cá nhân demo
    window.onload = function() {
        let name = localStorage.getItem("name") || "User Demo";
        let email = localStorage.getItem("email") || "demo@email.com";
        
        document.getElementById("displayName").innerText = name;
        document.getElementById("displayEmail").innerText = email;
        document.getElementById("name").value = name;
        document.getElementById("email").value = email;
    }

    function saveProfile() {
        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        localStorage.setItem("name", name);
        localStorage.setItem("email", email);
        alert("Đã lưu thông tin thành công!");
        location.reload();
    }
</script>
</body>
</html>