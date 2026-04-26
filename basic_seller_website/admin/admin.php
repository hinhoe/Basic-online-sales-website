<?php
session_start();
require_once __DIR__.'/../config/db.php';

// KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /basic_seller_web/misc/ey.php");
    exit();
}

// 1. XỬ LÝ THÊM SẢN PHẨM
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat_id = $_POST['category_id'];
    $desc = $_POST['description'];
    
    // --- XỬ LÝ ẢNH (FILE HOẶC URL) ---
        $img_path = 'img/products/' . $file_name;    
    // Ưu tiên 1: Nếu có Upload File
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = "prod_" . time() . "_" . rand(100,999) . "." . strtolower($ext);
        $target_path = __DIR__ . '/../img/products/' . $file_name;        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $img_path = $target_path;
        }
    } 
    // Ưu tiên 2: Nếu không Upload File nhưng có dán Link URL
    elseif (!empty($_POST['image_url'])) {
        $img_path = trim($_POST['image_url']);
    }

    $sql = "INSERT INTO products (name, price, category_id, description, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $cat_id, $desc, $img_path]);
    header("Location: admin.php?msg=added");
}

// 2. XỬ LÝ XÓA SẢN PHẨM
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin.php?msg=deleted");
}

// 3. XỬ LÝ CẬP NHẬT (SỬA) SẢN PHẨM
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat_id = $_POST['category_id'];
    $desc = $_POST['description'];
    
    // Khởi tạo bằng ảnh cũ
    $img_path = $_POST['old_image']; 

    // Ưu tiên 1: Nếu có Upload File MỚI
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = "prod_" . time() . "_" . rand(100,999) . "." . strtolower($ext);
        $target_path = "img/products/" . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $img_path = $target_path; 
        }
    }
    // Ưu tiên 2: Nếu không Upload File nhưng có dán Link URL MỚI
    elseif (!empty($_POST['image_url'])) {
        $img_path = trim($_POST['image_url']);
    }

    $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $cat_id, $desc, $img_path, $id]);
    header("Location: admin.php?msg=updated");
}

// TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ
$products = $conn->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $conn->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị PickleMeow Shop</title>
    <style>

        body { font-family: 'Segoe UI', Arial; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 240px; background: #d1d6dc; color: white;  height: 100vh; padding: 20px; position: fixed; border-left: 3px solid #f0f2f5; }
        .sidebar a { color: #01080d; text-decoration: none; display: block; padding: 10px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; }
        .main { flex: 1; margin-left: 240px; padding: 40px; border-left: 15px solid #f0f2f5; }
        .logot{ background-color: #b198e1; border-radius: 10px;}
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #2f6fd6; color: white; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; color: white; text-decoration: none; font-size: 14px; }
        .btn-add { background: #27ae60; margin-bottom: 20px; display: inline-block; font-weight: bold; }
        .btn-edit { background: #f1c40f; color: #2c3e50; font-weight: bold; }
        .btn-delete { background: #e74c3c; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; width: 450px; max-height: 90vh; overflow-y: auto; }
        input[type="text"], input[type="number"], select, textarea { width: 100%; padding: 10px; margin: 8px 0 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        
        /* CSS cho phần chọn ảnh */
        .image-choice { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px dashed #ccc; margin-bottom: 15px; }
        .image-choice input[type="file"] { width: 100%; margin-bottom: 10px; }
        .image-choice input[type="text"] { margin-bottom: 0; }
        .divider { text-align: center; color: #888; font-size: 12px; font-weight: bold; margin: 10px 0; }

        h2 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        label { font-weight: bold; color: #555; }
        
        .logo {color: white;text-decoration: none;display: flex;align-items: center;gap: 10px;font-size: 24px;font-weight: bold;}
        .logo img {height: 60px;width: 60px;object-fit: cover;border-radius: 50%;}
    </style>
</head>
<body>
<div class="sum">
    <div class="sidebar">
        <div class="logot">
            <a href="/basic_seller_web/index.php" class="logo">
                <img src="/basic_seller_web/img/pickle_meow_logo.png">                </br>PickleMeow Shop
            </a>    
        </div>
        
        <nav>
            <a href="/basic_seller_web/index.php" style="color: #000000 ">🏠 Trở về Shop</a>
            <a href="/basic_seller_web/admin/admin.php" class="active">🛍️ Quản lý Sản phẩm</a>
            <a href="/basic_seller_web/auth/logout.php" style="color: #000000; margin-top: 50px;">🚪 Đăng xuất</a>
        </nav>
    </div>

    <div class="main">
        <h1>Danh sách sản phẩm</h1>
        
        <button class="btn btn-add" onclick="openModal('addModal')">+ Thêm sản phẩm mới</button>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Danh mục</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td>#<?php echo $p['id']; ?></td>
                    <td>
                        <img src="<?php echo (strpos($p['image'], 'http') === 0) 
                            ? $p['image'] 
                            : '/basic_seller_web/' . $p['image']; ?>" width="60" height="60" style="border-radius:5px; object-fit: contain; background: white;">
                    </td>
                    <td><strong><?php echo $p['name']; ?></strong></td>
                    <td style="color: #e74c3c; font-weight: bold;"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                    <td><?php echo $p['cat_name']; ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($p)); ?>)">Sửa</button>
                        <a href="?delete_id=<?php echo $p['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Thêm sản phẩm mới</h2>
            <form method="POST" enctype="multipart/form-data">
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" required>
                
                <label>Giá tiền:</label>
                <input type="number" name="price" required>
                
                <label>Danh mục:</label>
                <select name="category_id">
                    <?php foreach($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Ảnh sản phẩm:</label>
                <div class="image-choice">
                    <input type="file" name="image" accept="image/*">
                    <div class="divider">--- HOẶC ---</div>
                    <input type="text" name="image_url" placeholder="Dán link ảnh (URL) vào đây...">
                </div>
                
                <label>Mô tả:</label>
                <textarea name="description" rows="3"></textarea>
                
                <button type="submit" name="add_product" class="btn btn-add" style="width: 100%;">Lưu sản phẩm</button>
                <button type="button" onclick="closeModal('addModal')" style="width: 100%; background: #95a5a6;" class="btn">Hủy</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Chỉnh sửa sản phẩm</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="old_image" id="edit_old_img">
                
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" id="edit_name" required>
                
                <label>Giá tiền:</label>
                <input type="number" name="price" id="edit_price" required>
                
                <label>Danh mục:</label>
                <select name="category_id" id="edit_cat">
                    <?php foreach($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Thay đổi ảnh (Bỏ trống để giữ nguyên ảnh cũ):</label>
                <div class="image-choice">
                    <input type="file" name="image" accept="image/*">
                    <div class="divider">--- HOẶC ---</div>
                    <input type="text" name="image_url" placeholder="Dán link ảnh (URL) MỚI vào đây...">
                </div>
                
                <label>Mô tả:</label>
                <textarea name="description" id="edit_desc" rows="3"></textarea>
                
                <button type="submit" name="edit_product" class="btn btn-add" style="width: 100%; background: #2980b9;">Cập nhật thay đổi</button>
                <button type="button" onclick="closeModal('editModal')" style="width: 100%; background: #95a5a6;" class="btn">Đóng</button>
            </form>
        </div>
    </div>   
</div>


<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openEditModal(product) {
        document.getElementById('edit_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_cat').value = product.category_id;
        document.getElementById('edit_desc').value = product.description;
        document.getElementById('edit_old_img').value = product.image; 
        
        openModal('editModal');
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>

</body>
</html>
