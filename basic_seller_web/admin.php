<?php
session_start();
require_once 'db.php';

// KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. XỬ LÝ THÊM SẢN PHẨM
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat_id = $_POST['category_id'];
    $desc = $_POST['description'];
    $img = $_POST['image'];

    $sql = "INSERT INTO products (name, price, category_id, description, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $cat_id, $desc, $img]);
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
    $img = $_POST['image'];

    $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $cat_id, $desc, $img, $id]);
    header("Location: admin.php?msg=updated");
}

// TRUY VẤN DỮ LIỆU
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
        .sidebar { width: 240px; background: #2c3e50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .main { flex: 1; margin-left: 240px; padding: 40px; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2f6fd6; color: white; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; color: white; text-decoration: none; font-size: 14px; }
        .btn-add { background: #27ae60; margin-bottom: 20px; display: inline-block; }
        .btn-edit { background: #f1c40f; color: #2c3e50; font-weight: bold; }
        .btn-delete { background: #e74c3c; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; width: 450px; position: relative; }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        h2 { margin-top: 0; color: #2c3e50; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="color: #ecf0f1;">GagShop Admin</h2>
    <p>Admin: <?php echo $_SESSION['fullname']; ?></p>
    <nav>
        <a href="index.php">🏠 Xem Trang chủ</a>
        <a href="admin.php">🛍️ Quản lý Sản phẩm</a>
        <a href="logout.php">🚪 Đăng xuất</a>
    </nav>
</div>

<div class="main">
    <h1>Danh sách sản phẩm</h1>
    
    <button class="btn btn-add" onclick="openModal('addModal')">+ Thêm sản phẩm</button>

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
                <td><img src="<?php echo $p['image']; ?>" width="60" style="border-radius:5px;"></td>
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
        <form method="POST">
            <input type="text" name="name" placeholder="Tên sản phẩm" required>
            <input type="number" name="price" placeholder="Giá tiền" required>
            <select name="category_id">
                <?php foreach($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="description" placeholder="Mô tả sản phẩm" rows="3"></textarea>
            <input type="text" name="image" placeholder="Link ảnh (URL)">
            <button type="submit" name="add_product" class="btn btn-add" style="width: 100%;">Lưu sản phẩm</button>
            <button type="button" onclick="closeModal('addModal')" style="width: 100%; margin-top: 10px; background: #95a5a6;" class="btn">Hủy</button>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <h2>Chỉnh sửa sản phẩm</h2>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            
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
            
            <label>Mô tả:</label>
            <textarea name="description" id="edit_desc" rows="3"></textarea>
            
            <label>Link ảnh:</label>
            <input type="text" name="image" id="edit_img">
            
            <button type="submit" name="edit_product" class="btn btn-add" style="width: 100%; background: #2980b9;">Cập nhật thay đổi</button>
            <button type="button" onclick="closeModal('editModal')" style="width: 100%; margin-top: 10px; background: #95a5a6;" class="btn">Đóng</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Hàm đặc biệt để mở Modal Sửa và điền dữ liệu
    function openEditModal(product) {
        document.getElementById('edit_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_cat').value = product.category_id;
        document.getElementById('edit_desc').value = product.description;
        document.getElementById('edit_img').value = product.image;
        
        openModal('editModal');
    }

    // Đóng modal khi click ra ngoài
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>

</body>
</html>