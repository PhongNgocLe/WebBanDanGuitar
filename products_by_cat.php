<?php
    // Định nghĩa trang hiện tại
    $currentPage = 'category';
    
    include 'header.php';
    include_once 'QLSP.php';
    $slug = $_GET['slug'] ?? '';
    $category_name = 'Sản phẩm';
    $filtered_products = [];
    
    if (!empty($slug)) {
        // Tìm tên danh mục dựa trên slug (cần truy vấn CSDL, nhưng QLSP hiện chưa có hàm này)
        // GIẢI PHÁP TẠM: Lấy tên Category dựa trên slug từ danh sách menu_categories
        $all_categories = get_all_categories(); // Lấy lại danh sách categories
        foreach ($all_categories as $cat) {
            if ($cat['slug'] === $slug) {
                $category_name = $cat['name'];
                break;
            }
        }
        
        // Lọc sản phẩm theo TÊN category
        $filtered_products = get_products_by_category($category_name);
    } else {
        header('Location: index.php'); // Nếu không có slug, quay về trang chủ
        exit;
    }
?>
            <div class="content-banner">
                <img src="/images/BANNER.png" alt="Banner Guitar">
            </div>
            
            <div class="divider-heading"></div>
          
            <div class="content-head">
                <h2><?php echo htmlspecialchars(mb_strtoupper($category_name, 'UTF-8')); ?></h2>
            </div>
            <div class="divider-heading"></div>

            <section class="product-grid">
                <div class="row">
                    <?php if (!empty($filtered_products)): ?>
                        <?php foreach( $filtered_products as $product): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm sanpham-card">
                                    <?php $discount_percent = get_sale_percent($product['id']); $sale_price = get_discounted_price($product['price'], $discount_percent); ?>
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
                                        <a href="cart-handler.php?action=add&id=<?php echo $product['id']; ?>" class="btn add-to-cart-btn">Thêm vào giỏ hàng</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <p class="lead">Hiện tại không có sản phẩm nào trong danh mục này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
           
            <div class="divider-heading"></div>

<?php
    include 'footer.php';
?>