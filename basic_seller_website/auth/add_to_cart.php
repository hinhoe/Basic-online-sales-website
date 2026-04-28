<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. CHẶN NẾU CHƯA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng!');
        window.location.href = 'login.php';
    </script>";
    exit();
}

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Vì đã qua bước chặn ở trên, lúc này chắc chắn đã có user_id
    $user_id = $_SESSION['user_id'];

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng của user này chưa
    $sql_check = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$user_id, $product_id]);
    $item = $stmt_check->fetch();

    if ($item) {
        // Đã có -> Cộng dồn số lượng
        $new_quantity = $item['quantity'] + $quantity;
        $sql_update = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$new_quantity, $item['id']]);
    } else {
        // Chưa có -> Thêm dòng mới vào Database
        $sql_insert = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([$user_id, $product_id, $quantity]);
    }

    // Thêm xong thì chuyển về trang Giỏ hàng
    header("Location: ../pages/cart.php");
    exit();
}