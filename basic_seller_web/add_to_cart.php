<?php
session_start();
require_once 'db.php';

if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Nếu giỏ hàng chưa tồn tại, tạo mới một mảng rỗng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Nếu sản phẩm đã có trong giỏ, cộng thêm số lượng
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $quantity;
    } else {
        // Nếu chưa có, thêm mới sản phẩm với số lượng tương ứng
        $_SESSION['cart'][$id] = $quantity;
    }

    header("Location: cart.php");
    exit();
}