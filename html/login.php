<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-wrapper { display: flex; gap: 40px; width: 100%; max-width: 900px; padding: 20px; }
        .info-panel { flex: 1; background: white; padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
        .login-panel { width: 350px; background: white; padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
        
        .login-tabs { display: flex; margin-bottom: 20px; background: var(--bg-color); border-radius: 8px; padding: 5px; }
        .login-tab { flex: 1; text-align: center; padding: 10px; cursor: pointer; border-radius: 6px; font-weight: 600; color: var(--text-muted); transition: 0.3s;}
        .login-tab.active { background: var(--white); color: var(--primary); box-shadow: var(--shadow-sm); }
        
        .form-control { width: 100%; padding: 12px 15px; margin-bottom: 15px; border: 1px solid var(--border-color); border-radius: 8px; outline: none; }
        .form-control:focus { border-color: var(--primary); }
        
        .notice-item { padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;}
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="info-panel">
        <h2 style="color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">THÔNG BÁO CHUNG</h2>
        <div class="notice-item">
            <div>
                <h4 style="color: var(--text-main);">Bảo trì hệ thống định kỳ</h4>
                <span style="font-size: 13px; color: var(--text-muted);">06/03/2026</span>
            </div>
            <a href="#" style="color: var(--primary); font-size: 14px;">Xem chi tiết</a>
        </div>
        <div class="notice-item">
            <div>
                <h4 style="color: var(--text-main);">Cập nhật chính sách hoàn trả</h4>
                <span style="font-size: 13px; color: var(--text-muted);">09/02/2026</span>
            </div>
            <a href="#" style="color: var(--primary); font-size: 14px;">Xem chi tiết</a>
        </div>
    </div>

    <div class="login-panel">
        <div class="login-tabs">
            <div class="login-tab active" onclick="setRole('user', this)">User</div>
            <div class="login-tab" onclick="setRole('admin', this)">Admin</div>
        </div>

        <input type="text" id="username" class="form-control" placeholder="Tên đăng nhập / Email">
        <input type="password" id="password" class="form-control" placeholder="Mật khẩu">
        
        <button class="btn" style="width: 100%; margin-top: 10px;" onclick="executeLogin()">Đăng nhập</button>
        <div id="errorMsg" style="color: var(--danger); font-size: 13px; margin-top: 15px; text-align: center;"></div>
    </div>
</div>

<script src="..\\js\\main.js"></script>
<script>
    let currentRole = 'user';
    
    function setRole(role, btn) {
        currentRole = role;
        document.querySelectorAll('.login-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }

    function executeLogin() {
        const u = document.getElementById("username").value;
        const p = document.getElementById("password").value;
        if(u === "" || p === "") {
            document.getElementById("errorMsg").innerText = "Vui lòng nhập đầy đủ thông tin!";
            return;
        }
        window.location.href = currentRole === 'admin' ? 'admin.php' : 'user.php';
    }
</script>
</body>
</html>