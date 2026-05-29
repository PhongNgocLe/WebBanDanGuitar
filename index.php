<?php
$currentPage = 'Index';
include 'header.php';
include_once 'QLSP.php';
$featured_products = get_all_products();
$top_categories = get_all_categories();
?>

<div class="hero-section">
    <div class="hero-content">
        <span class="hero-badge"><i class="fa-solid fa-guitar"></i> P-Guitar</span>
        <h1 class="hero-title">Âm nhạc thăng hoa cùng cây đàn hoàn hảo</h1>
        <p class="hero-description">Khám phá bộ sưu tập guitar acoustic, guitar điện và phụ kiện cao cấp. Mua ngay để nhận ưu đãi đặc biệt, giao nhanh và bảo hành tận tâm.</p>
        <div class="hero-actions">
            <a href="#featuredProducts" class="hero-btn">Mua ngay</a>
            <a href="phukien.php" class="hero-btn hero-btn-secondary">Phụ kiện hot</a>
        </div>
    </div>
</div>

<section class="promo-strip">
    <div class="promo-card">
        <h2>ƯU ĐÃI FLASH SALE</h2>
        <p>Giảm ngay tới 25% cho những cây đàn guitar hot nhất. Mua sớm để săn giá tốt, quà tặng hấp dẫn và giao nhanh trong 24h.</p>
    </div>
</section>

<?php if (!empty($top_categories)): ?>
<section class="category-shelf">
    <div class="section-heading">
        <h2>Danh mục nổi bật</h2>
    </div>
    <div class="row g-4">
        <?php foreach (array_slice($top_categories, 0, 4) as $cat): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="products_by_cat.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card h-100 d-block">
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p>Khám phá bộ sưu tập <?php echo htmlspecialchars(mb_strtolower($cat['name'], 'UTF-8')); ?> phong cách.</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<section id="featuredProducts" class="product-grid">
    <div class="content-head">
        <h2>SẢN PHẨM MỚI</h2>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-3">
        <?php foreach ($featured_products as $product): 
            $discount_percent = get_sale_percent($product['id']);
            $sale_price = get_discounted_price($product['price'], $discount_percent);
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm sanpham-card">
                    <?php if ($discount_percent > 0): ?>
                        <div class="sale-badge">Giảm <?php echo $discount_percent; ?>%</div>
                    <?php endif; ?>
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                            class="card-img-top picture"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title product-name">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h5>
                        <p class="card-text product-price mt-auto">
                            <?php if ($discount_percent > 0): ?>
                                <span class="price-new"><?php echo format_price($sale_price); ?></span>
                                <span class="price-old"><?php echo format_price($product['price']); ?></span>
                            <?php else: ?>
                                <?php echo format_price($product['price']); ?>
                            <?php endif; ?>
                        </p>
                        <a href="cart-handler.php?action=add&id=<?php echo $product['id']; ?>" class="btn add-to-cart-btn">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
include 'footer.php';
?>
