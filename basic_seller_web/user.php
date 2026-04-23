<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// XỬ LÝ CẬP NHẬT THÔNG TIN VÀ ẢNH
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = $_POST['fullname'];
    $new_email = $_POST['email'];
    $avatar_name = ""; 
    $update_avatar_sql = "";

    // 1. Kiểm tra xem người dùng có chọn upload file không
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        
        // Lấy đuôi mở rộng của file (jpg, png...)
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        // Kiểm tra định dạng và dung lượng (giới hạn 2MB = 2000000 bytes)
        if (in_array($ext, $allowed_ext) && $file_size < 2000000) {
            
            // Đặt tên file mới để tránh trùng lặp (vd: user_1_17000000.jpg)
            $avatar_name = "user_" . $user_id . "_" . time() . "." . $ext;
            $upload_path = "img/avatars/" . $avatar_name;

            // Di chuyển file từ bộ nhớ tạm vào thư mục dự án
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $update_avatar_sql = ", avatar = '$avatar_name'";
                // Cập nhật lại Session để giao diện đổi ảnh ngay
                $_SESSION['avatar'] = $avatar_name; 
            } else {
                $error_msg = "Lỗi: Không thể lưu file ảnh vào thư mục.";
            }
        } else {
            $error_msg = "Lỗi: Chỉ chấp nhận ảnh JPG/PNG/GIF và dung lượng < 2MB.";
        }
    }

    if (empty($new_name) || empty($new_email)) {
        $error_msg = "Vui lòng không để trống Họ tên và Email!";
    } elseif(empty($error_msg)) {
        try {
            // Cập nhật Database (nếu có ảnh mới thì ghép thêm đoạn sql cập nhật ảnh)
            $sql = "UPDATE users SET fullname = ?, email = ? $update_avatar_sql WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$new_name, $new_email, $user_id]);

            $_SESSION['fullname'] = $new_name;
            $success_msg = "Cập nhật hồ sơ thành công!";
        } catch (PDOException $e) {
            $error_msg = "Lỗi: Email này có thể đã được sử dụng!";
        }
    }
}

// LẤY THÔNG TIN MỚI NHẤT TỪ DATABASE
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Xác định đường dẫn ảnh hiển thị
$display_avatar = !empty($user['avatar']) ? "img/avatars/" . $user['avatar'] : "img/avatars/default.png";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tài khoản của tôi - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS giữ nguyên từ bản trước */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f4f6f8}
    .topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;}
    .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 22px;font-weight: bold;}
    .logo img {height: 45px;width: 45px;object-fit: cover;border-radius: 50%;}
    .header-links a{color:white; text-decoration:none; margin-left: 20px;}

    .container{max-width:1000px; margin:40px auto; display:flex; gap:25px; padding:0 20px;}
    .sidebar{width:280px; background:white; border-radius:15px; padding:30px; text-align:center; height: fit-content; box-shadow: 0 4px 10px rgba(0,0,0,0.05);}
    .avatar{width:120px; height:120px; border-radius:50%; margin-bottom:15px; object-fit: cover; border: 3px solid #e8f0fe;}
    .menu{margin-top:20px; text-align:left;}
    .menu-item{padding:12px; border-radius:8px; cursor:pointer; color:#555; text-decoration:none; display:block;}
    .menu-item:hover{background:#f0f3f8; color:#2f6fd6;}
    .active{background:#e8f0fe; color:#2f6fd6; font-weight:600;}

    .content{flex:1; background:white; padding:40px; border-radius:15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);}
    h2{margin-bottom:25px; color:#333;}
    label{display:block; margin-bottom:8px; font-weight:600; color:#555;}
    input[type="text"], input[type="email"]{width:100%; padding:12px; margin-bottom:20px; border:1px solid #ddd; border-radius:8px; font-size:15px;}
    
    /* Style riêng cho nút chọn file */
    input[type="file"] { margin-bottom: 20px; padding: 10px; background: #f9f9f9; width: 100%; border-radius: 8px; border: 1px dashed #ccc; }
    
    .btn-save{padding:12px 30px; background:#2f6fd6; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:15px;}
    .btn-save:hover {background:#1f5bb5;}
    .msg{padding:15px; border-radius:8px; margin-bottom:20px; font-size:14px;}
    .success{background: #d4edda; color: #155724;}
    .error{background: #f8d7da; color: #721c24;}
</style>
</head>
<body>

<div class="topbar">
    <a href="index.php" class="logo">
        <img src="img/pickle_meow_logo.png"> PickleMeow Shop
    </a>
    <div class="header-links">
        <a href="index.php">Trang chủ</a>
        <a href="cart.php">Giỏ hàng</a>
        <a href="logout.php">Đăng xuất</a>
    </div>
</div>

<div class="container">
    <div class="sidebar">
        <img class="avatar" src="<?php echo $display_avatar; ?>" alt="Avatar">
        <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
        <p style="color:#777; font-size:14px;"><?php echo htmlspecialchars($user['email']); ?></p>

        <div class="menu">
            <a href="user.php" class="menu-item active">👤 Thông tin cá nhân</a>
            <a href="#" class="menu-item">📦 Đơn hàng của tôi</a>
            <a href="logout.php" class="menu-item" style="color: #e53935;">🚪 Đăng xuất</a>
        </div>
    </div>

    <div class="content">
        <h2>Chỉnh sửa hồ sơ</h2>

        <?php if($success_msg): ?>
            <div class="msg success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="msg error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="user.php" method="POST" enctype="multipart/form-data">
            
            <label>Ảnh đại diện mới (Tùy chọn)</label>
            <input type="file" name="avatar" accept="image/png, image/jpeg, image/gif">

            <label>Họ và tên</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label>Địa chỉ Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <button type="submit" name="update_profile" class="btn-save">Lưu cập nhật</button>
        </form>
    </div>
</div>

</body>
</html>