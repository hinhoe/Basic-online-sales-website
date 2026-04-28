<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Lấy ID người dùng (nếu đã đăng nhập) hoặc Session ID (nếu là khách)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $session_id = session_id();

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng của user/session này chưa
    if ($user_id) {
        $sql_check = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$user_id, $product_id]);
    } else {
        $sql_check = "SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$session_id, $product_id]);
    }
    
    $item = $stmt_check->fetch();

    if ($item) {
        // Đã có -> Cộng dồn số lượng
        $new_quantity = $item['quantity'] + $quantity;
        $sql_update = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$new_quantity, $item['id']]);
    } else {
        // Chưa có -> Thêm dòng mới vào Database
        $sql_insert = "INSERT INTO cart_items (user_id, session_id, product_id, quantity) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([$user_id, $session_id, $product_id, $quantity]);
    }

    header("Location: /basic_seller_web/pages/cart.php");
    exit();
}