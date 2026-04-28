<?php
$base_path = (basename($_SERVER['SCRIPT_NAME']) === 'index.php') ? '' : '<?php echo $base_path; ?>';

// đảm bảo có biến $p
if(!isset($p)) return;

/* =============================
   FIX DISCOUNT AN TOÀN
============================= */
$original_price = (int)$p['price'];

// ép discount về số hợp lệ 0 → 90
$discount = isset($p['discount']) ? (int)$p['discount'] : 0;
$discount = max(0, min($discount, 90)); 

$sale_price = $original_price - ($original_price * $discount / 100);

// không cho giá = 0
if($sale_price <= 0) $sale_price = 1000;

/* =============================
   FIX LINK ẢNH
============================= */
$img_src = !empty($p['image']) && strpos($p['image'], 'http') === 0
    ? $p['image']
    : '<?php echo $base_path; ?>' . ($p['image'] ?? 'img/no-image.png');
?>

<div class="card">

    <?php if($discount > 0): ?>
        <div class="badge-discount">-<?php echo $discount; ?>%</div>
    <?php endif; ?>

    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
    <h4><?php echo htmlspecialchars($p['name']); ?></h4>

    <div class="price-container">
        <?php if($discount > 0): ?>
            <span class="old-price"><?php echo number_format($original_price,0,',','.'); ?>đ</span>
            <span class="new-price"><?php echo number_format($sale_price,0,',','.'); ?>đ</span>
        <?php else: ?>
            <span class="new-price"><?php echo number_format($original_price,0,',','.'); ?>đ</span>
        <?php endif; ?>
    </div>

    <button onclick="location.href='<?php echo $base_path; ?>pages/product.php?id=<?php echo $p['id']; ?>'">
        Xem chi tiết
    </button>

</div>