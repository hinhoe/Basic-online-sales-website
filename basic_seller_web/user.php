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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);
    $new_address = trim($_POST['address']);
    $avatar_sql = "";

    // Xử lý Upload Ảnh Đại Diện (Giữ nguyên tính năng cũ)
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $avatar_name = "user_" . $user_id . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], "img/avatars/" . $avatar_name)) {
                $avatar_sql = ", avatar = '$avatar_name'";
            }
        }
    }

    if (empty($new_name) || empty($new_email)) {
        $error_msg = "Không được để trống Tên và Email!";
    } else {
        try {
            // Cập nhật thêm Phone và Address
            $sql = "UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? $avatar_sql WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$new_name, $new_email, $new_phone, $new_address, $user_id]);

            $_SESSION['fullname'] = $new_name;
            $success_msg = "Cập nhật hồ sơ thành công!";
        } catch (PDOException $e) {
            $error_msg = "Lỗi: Email này đã có người sử dụng!";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$display_avatar = !empty($user['avatar']) ? "img/avatars/" . $user['avatar'] : "img/avatars/default.png";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hồ sơ của tôi - PickleMeow Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    /* CSS y chang bản cũ của bạn */
    *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
    body{background:#f4f6f8}
    .topbar{background:#2f6fd6;padding:12px 30px;display:flex;align-items:center;justify-content:space-between; color:white}
    .topbar a{color:white; text-decoration:none; margin-left: 20px;}
    .container{max-width:1000px; margin:40px auto; display:flex; gap:25px; padding:0 20px;}
    .sidebar{width:280px; background:white; border-radius:15px; padding:30px; text-align:center; height:fit-content; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    .avatar{width:120px; height:120px; border-radius:50%; margin-bottom:15px; object-fit:cover; border:3px solid #e8f0fe;}
    .menu-item{padding:12px; border-radius:8px; cursor:pointer; color:#555; text-decoration:none; display:block; text-align: left; margin-top:10px;}
    .active{background:#e8f0fe; color:#2f6fd6; font-weight:600;}
    .content{flex:1; background:white; padding:40px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    label{display:block; margin-bottom:8px; font-weight:600; color:#555; margin-top: 15px;}
    input[type="text"], input[type="email"]{width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:15px;}
    input[type="file"]{margin-top:5px; margin-bottom: 15px;}
    .btn-save{margin-top:20px; padding:12px 30px; background:#2f6fd6; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600;}
    .msg{padding:15px; border-radius:8px; margin-bottom:20px;}
    .success{background:#d4edda; color:#155724;}
    .error{background:#f8d7da; color:#721c24;}
</style>
</head>
<body>

<div class="topbar">
    <a href="index.php" style="font-size:22px; font-weight:bold;">PickleMeow Shop</a>
    <div>
        <a href="index.php">Trang chủ</a>
        <a href="cart.php">Giỏ hàng</a>
        <a href="logout.php">Đăng xuất</a>
    </div>
</div>

<div class="container">
    <div class="sidebar">
        <img class="avatar" src="<?php echo $display_avatar; ?>">
        <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
        <p style="color:#777; font-size:14px;"><?php echo htmlspecialchars($user['email']); ?></p>
        <div style="margin-top:20px;">
            <a href="user.php" class="menu-item active">👤 Thông tin cá nhân</a>
            <a href="#" class="menu-item">📦 Đơn hàng của tôi</a>
        </div>
    </div>

    <div class="content">
        <h2>Chỉnh sửa hồ sơ</h2>
        <?php if($success_msg) echo "<div class='msg success'>$success_msg</div>"; ?>
        <?php if($error_msg) echo "<div class='msg error'>$error_msg</div>"; ?>

        <form action="user.php" method="POST" enctype="multipart/form-data">
            <label>Ảnh đại diện (Tùy chọn)</label>
            <input type="file" name="avatar" accept="image/png, image/jpeg, image/gif">

            <label>Họ và tên</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label>Địa chỉ Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Số điện thoại</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

            <label>Địa chỉ nhận hàng</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">

            <button type="submit" name="update_profile" class="btn-save">Lưu cập nhật</button>
        </form>
    </div>
</div>
</body>
</html>