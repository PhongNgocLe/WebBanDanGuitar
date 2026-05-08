<?php
    $currentPage = 'search';
    
    include 'header.php';
    include_once 'QLSP.php';
    
    $search_query = trim($_GET['query'] ?? '');
    $search_results = [];
    
    if (!empty($search_query)) {
        // Tìm kiếm sản phẩm theo query
        $search_results = search_products($search_query);
    }
?>
            <div class="content-banner">
                <img src="/images/BANNER.png" alt="Banner Guitar">
            </div>
            
            <div class="divider-heading"></div>
          
            <div class="content-head">
                <?php if (!empty($search_query)): ?>
                    <h2>KẾT QUẢ TÌM KIẾM CHO: "<?php echo htmlspecialchars($search_query); ?>"</h2>
                <?php else: ?>
                    <h2>VUI LÒNG NHẬP TỪ KHÓA TÌM KIẾM</h2>
                <?php endif; ?>
            </div>
            <div class="divider-heading"></div>

            <section class="product-grid">
                <div class="row">
                    <?php if (!empty($search_results)): ?>
                        <?php foreach( $search_results as $product): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm sanpham-card">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                             class="card-img-top picture" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title product-name">
                                         
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text product-price mt-auto">
                                            <?php echo format_price($product['price']); ?>
                                        </p>
                                        <a href="cart-handler.php?action=add&id=<?php echo $product['id']; ?>" class="btn add-to-cart-btn">Thêm vào giỏ hàng</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <div class="col-12 text-center py-5">
                            <p class="lead">Không tìm thấy sản phẩm nào phù hợp với từ khóa.</p>
                            <a href="index.php" class="btn auth-button mt-3">Quay lại trang chủ</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
           
            <div class="divider-heading"></div>

<?php
    include 'footer.php';
?>