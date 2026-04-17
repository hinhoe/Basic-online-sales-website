<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .product-layout { display: flex; gap: 40px; background: white; padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .product-img { flex: 1; }
        .product-img img { width: 100%; max-width: 450px; border-radius: var(--radius-md); }
        .product-info { flex: 1; }
        
        .badge-stock { background: #e8f0fe; color: var(--primary); padding: 6px 12px; border-radius: 6px; display: inline-block; margin-bottom: 10px; font-weight: 600; font-size: 14px;}
        .qty-box { display: flex; align-items: center; gap: 10px; margin: 15px 0; }
        .qty-box input { width: 70px; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: center; font-weight: bold;}
        
        .desc-box { background: white; padding: 30px; border-radius: var(--radius-lg); margin-top: 25px; box-shadow: var(--shadow-sm); line-height: 1.7; }
        .products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">Chi tiết sản phẩm</div>
    <div style="font-weight: 600; gap: 20px; display: flex;">
        <a href="index.php">Trang chủ</a>
        <a href="cart.php">Giỏ hàng</a>
    </div>
</div>

<div class="container">
    <div class="product-layout">
        <div class="product-img">
            <img src="https://picsum.photos/500" alt="Product Image">
        </div>
        <div class="product-info">
            <span class="badge-stock">Còn hàng</span>
            <h1 style="margin-bottom: 10px;">Gói Premium Account VIP</h1>
            <p style="color: var(--text-muted);">Mã sản phẩm: <b>PROD-ID-99</b></p>
            
            <div style="font-size: 32px; color: var(--danger); font-weight: bold; margin: 20px 0;">299.000đ</div>
            
            <div class="qty-box">
                Số lượng:
                <input type="number" value="1" min="1">
            </div>
            
            <button class="btn btn-danger" style="padding: 15px 30px; font-size: 16px; margin-top: 10px;" onclick="location.href='cart.php'">
                🛒 Thêm vào giỏ hàng
            </button>
            
            <ul style="margin-top: 25px; line-height: 1.8; color: var(--text-muted); list-style: none;">
                <li>✅ Bảo hành full thời gian sử dụng</li>
                <li>✅ Nhận tài khoản tự động ngay sau thanh toán</li>
                <li>✅ Hỗ trợ kỹ thuật 24/7</li>
            </ul>
        </div>
    </div>

    <div class="desc-box">
        <h2 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Mô tả sản phẩm</h2>
        <p>Đây là phần mô tả chi tiết của sản phẩm. Cung cấp thông tin cụ thể về quyền lợi tài khoản, tính năng nổi bật, cách kích hoạt và các chính sách hỗ trợ đi kèm để khách hàng an tâm mua sắm.</p>
    </div>

    <h2 style="margin-top: 40px;">Sản phẩm tương tự</h2>
    <div class="products-grid">
        <div class="card">
            <img src="https://picsum.photos/200?1" alt="SP">
            <h4>Gói Cơ bản 1 Tháng</h4>
            <div class="price">120.000đ</div>
            <button class="btn" onclick="location.href='product.php'">Xem ngay</button>
        </div>
        <div class="card">
            <img src="https://picsum.photos/200?2" alt="SP">
            <h4>Gói Gia đình</h4>
            <div class="price">150.000đ</div>
            <button class="btn" onclick="location.href='product.php'">Xem ngay</button>
        </div>
        <div class="card">
            <img src="https://picsum.photos/200?3" alt="SP">
            <h4>Gói Sinh viên</h4>
            <div class="price">99.000đ</div>
            <button class="btn" onclick="location.href='product.php'">Xem ngay</button>
        </div>
        <div class="card">
            <img src="https://picsum.photos/200?4" alt="SP">
            <h4>Phụ kiện đi kèm</h4>
            <div class="price">79.000đ</div>
            <button class="btn" onclick="location.href='product.php'">Xem ngay</button>
        </div>
    </div>
</div>

<script src="..\\js\\main.js"></script>
</body>
</html>