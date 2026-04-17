<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .cart-table { width: 100%; background: white; border-radius: var(--radius-lg); border-collapse: collapse; overflow: hidden; box-shadow: var(--shadow-sm); }
        .cart-table th, .cart-table td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); text-align: center; }
        .cart-table th { background: #e8f0fe; font-weight: 700; color: var(--primary); }
        .cart-table tr:last-child td { border-bottom: none; }
        
        .cart-summary { display: flex; justify-content: flex-end; align-items: center; margin-top: 30px; gap: 20px; background: white; padding: 20px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .total-price { font-size: 22px; font-weight: bold; color: var(--danger); }
        
        .qty-input { width: 60px; padding: 8px; text-align: center; border: 1px solid var(--border-color); border-radius: 6px; outline: none;}
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">Giỏ hàng của bạn</div>
    <div class="header-actions">
        <a href="index.php">Tiếp tục mua sắm</a>
    </div>
</div>

<div class="container">
    <table class="cart-table">
        <tr>
            <th style="text-align: left;">Sản phẩm</th>
            <th>Đơn Giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th>Thao tác</th>
        </tr>
        <tr>
            <td style="font-weight: 600; text-align: left;">Premium Account VIP</td>
            <td>299.000đ</td>
            <td><input type="number" class="qty-input" value="1" min="1"></td>
            <td style="color: var(--danger); font-weight: bold;">299.000đ</td>
            <td><button class="btn btn-danger" style="padding: 6px 12px; font-size: 13px;">Xóa</button></td>
        </tr>
    </table>

    <div class="cart-summary">
        <div class="total-price">Tổng thanh toán: 299.000đ</div>
        <button class="btn btn-danger" style="padding: 12px 30px; font-size: 16px;">Thanh toán ngay</button>
    </div>
</div>

<script src="..\\js\\main.js"></script>
</body>
</html>