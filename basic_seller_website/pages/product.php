    <?php
    session_start();
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../includes/header.php'; 

    // 1. Kiểm tra xem có ID sản phẩm trên URL không
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        // 2. Truy vấn lấy thông tin chi tiết sản phẩm và tên danh mục (JOIN)
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        // 3. Nếu không tìm thấy sản phẩm, quay về trang chủ
        if (!$product) {
            header("Location: ../index.php");
            exit();
        }
    } else {
        header("Location: ../index.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> - PickleMeow Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS cơ bản cho trang chi tiết */
        *{margin:0;padding:0;box-sizing:border-box;font-family:Inter}
        body{background:#f4f6f8}
        
        .container{max-width:1200px;margin:auto;padding:40px 20px;}
        .product-flex{display:flex;gap:40px;background:white;padding:30px;border-radius:15px;box-shadow: 0 5px 15px rgba(0,0,0,0.05);}
        .product-img img{width:450px;border-radius:15px;object-fit: cover;}
        .product-info{flex:1}
        .badge{background:#e8f0fe;color:#2f6fd6;padding:6px 12px;border-radius:6px;display:inline-block;margin-bottom:10px;font-size:14px;font-weight:600;}
        .price{color:#e53935;font-size:32px;font-weight:700;margin:15px 0;}
        .add-cart{background:#e53935;color:white;padding:15px 30px;border:none;border-radius:10px;font-size:18px;font-weight:700;cursor:pointer;width:100%;}
        .desc{background:white;padding:30px;border-radius:15px;margin-top:25px;line-height:1.8;color:#444;}
        .desc h2{margin-bottom:15px;border-bottom:2px solid #f0f0f0;padding-bottom:10px;}
    </style>
    </head>

    <body>

    <div class="container">
        <div class="product-flex">
            <div class="product-img">
                <img src="<?php echo (strpos($product['image'], 'http') === 0) 
        ? $product['image'] 
        : '../' . $product['image']; ?>" 
        alt="<?php echo $product['name']; ?>">
            </div>

            <div class="product-info">
                <span class="badge"><?php echo $product['category_name']; ?></span>
                <h1 style="font-size:36px; color:#333;"><?php echo $product['name']; ?></h1>
                
                <p style="color:#777; margin-top:10px;">Mã sản phẩm: <b>#PM-<?php echo $product['id']; ?></b></p>
                
                <div class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</div>

                <form action="../auth/add_to_cart.php" method="POST">
                    <div style="margin:20px 0;">
                        <label>Số lượng: </label>
                        <input type="number" name="quantity" value="1" min="1" style="padding:8px; width:60px; border-radius:5px; border:1px solid #ddd;">
                    </div>

                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="add-cart">🛒 THÊM VÀO GIỎ HÀNG</button>
                </form>

                <ul style="margin-top:25px; list-style:none; color:#555;">
                    <li style="margin-bottom:8px;">✅ Bảo hành chính hãng 12 tháng</li>
                    <li style="margin-bottom:8px;">✅ Miễn phí vận chuyển toàn quốc</li>
                    <li style="margin-bottom:8px;">✅ Đổi trả trong 7 ngày nếu có lỗi</li>
                </ul>
            </div>
        </div>

        <div class="desc">
            <h2>Mô tả sản phẩm</h2>
            <p>
                <?php 
                    // nl2br giúp giữ nguyên các dấu xuống dòng khi nhập từ database
                    echo nl2br($product['description']); 
                ?>
            </p>
        </div>
    </div>

    </body>
    </html>