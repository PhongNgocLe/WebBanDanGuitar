<?php
    // Định nghĩa trang hiện tại
    $currentPage = 'phukien';
    
    include 'header.php';
    include 'QLSP.php'; // Tạm thời dùng chung data sản phẩm
    $featured_products = get_all_products();
?>
            <div class="content-banner">
                 <img src="/images/BANNER.png" alt="Banner Phụ kiện">
            </div>
            
            <div class="divider-heading"></div>
          
            <div class="content-head">
                <h2>PHỤ KIỆN GUITAR</h2>
            </div>
            <div class="divider-heading"></div>

            <section class="product-grid">
                <div class="row">
                    <?php 
                    // Tạm thời chỉ hiển thị 2 sản phẩm đầu cho khác
                    $phukien_products = array_slice($featured_products, 0, 2);
                    foreach( $phukien_products as $product): 
                    ?>
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
                </div>
            </section>
           
            <div class="divider-heading"></div>

<?php
    // Gọi Footer
    include 'footer.php';
?>