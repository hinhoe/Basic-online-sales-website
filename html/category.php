<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh mục sản phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\\css\\style.css">
    <style>
        .products-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
        .page-title { margin-bottom: 30px; font-size: 24px; border-left: 4px solid var(--primary); padding-left: 10px;}
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">Danh mục sản phẩm</div>
    <div class="header-actions">
        <a href="index.php">Về trang chủ</a>
    </div>
</div>

<div class="container">
    <h2 class="page-title">Tất cả sản phẩm</h2>
    <div class="products-grid">
        <script>
            for(let i=1; i<=15; i++){
                document.write(`
                <div class="card">
                    <img src="https://picsum.photos/200?${i}" alt="SP">
                    <h4>Sản phẩm Demo ${i}</h4>
                    <div class="price">${100 + i * 20}.000đ</div>
                    <button class="btn" onclick="location.href='product.php'" style="width:100%; margin-top:10px;">Xem chi tiết</button>
                </div>`);
            }
        </script>
    </div>
</div>

<script src="..\\js\\main.js"></script>
</body>
</html>