<?php
    $currentPage = ''; 
    include 'header.php';
    include_once 'QLSP.php'; 

    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
    $product = null;
    $extra_images = [];

    if ($product_id > 0) {
        $product = find_product_by_id($product_id); 
        // Lấy danh sách ảnh phụ
        $extra_images = $product ? get_product_images($product_id) : [];
    }

    // --- LOGIC giảm giá sản phẩm ---
    $product_discount_percent = $product ? get_sale_percent($product['id']) : 0;
    $product_sale_price = $product ? get_discounted_price($product['price'], $product_discount_percent) : 0;

    // --- LOGIC: Lấy 4 phụ kiện ngẫu nhiên để cross-selling ---
    $phukien_list = get_products_by_category('Phụ kiện');
    $phukien_list = array_filter($phukien_list, function($p) use ($product_id) {
        return $p['id'] != $product_id;
    });
    shuffle($phukien_list);
    $suggested_products = array_slice($phukien_list, 0, 4);
?>

<div class="container my-5">
    <?php if ($product): ?>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="main-img-container shadow-sm border rounded p-3 mb-3 bg-white text-center">
                    <img id="mainImage" src="<?php echo htmlspecialchars($product['image']); ?>" 
                         class="img-fluid rounded" 
                         style="max-height: 450px; width: 100%; object-fit: contain;"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <?php if(!empty($extra_images)): ?>
                    <div class="d-flex gap-2 overflow-auto py-2">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             class="thumb-img border rounded shadow-sm" 
                             onclick="changeImg(this.src)" 
                             style="width: 80px; height: 80px; cursor: pointer; object-fit: cover;">
                        
                        <?php foreach($extra_images as $img): ?>
                            <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 class="thumb-img border rounded shadow-sm" 
                                 onclick="changeImg(this.src)" 
                                 style="width: 80px; height: 80px; cursor: pointer; object-fit: cover;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <h1 class="product-title fw-bold" style="color: rgb(173, 157, 78);">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                <p class="text-muted fs-5">Danh mục: <?php echo htmlspecialchars($product['category']); ?></p>

                <div class="product-price-detail my-3">
                    <?php if ($product_discount_percent > 0): ?>
                        <span class="fs-2 fw-bold text-danger">
                            <?php echo format_price($product_sale_price); ?>
                        </span>
                        <span class="price-old ms-3 d-inline-block">
                            <?php echo format_price($product['price']); ?>
                        </span>
                        <span class="badge bg-danger text-white ms-3">Giảm <?php echo $product_discount_percent; ?>%</span>
                    <?php else: ?>
                        <span class="fs-2 fw-bold text-danger">
                            <?php echo format_price($product['price']); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <a href="cart-handler.php?action=add&id=<?php echo $product['id']; ?>" 
                       class="btn auth-button btn-lg w-100 py-3 fw-bold">
                        <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                    </a>
                </div>
                
               <div class="product-description mt-5">
                    <h5 class="fw-bold"><i class="fa-solid fa-circle-info text-warning"></i> Mô tả sản phẩm</h5>
                    <hr>
                    <?php if (!empty($product['description'])): ?>
                        <p style="white-space: pre-wrap; line-height: 1.8;"><?php echo htmlspecialchars($product['description']); ?></p>
                    <?php else: ?>
                        <p class="text-muted">Mô tả cho sản phẩm này đang được cập nhật...</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($suggested_products)): ?>
        <div class="mt-5 pt-4 border-top">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold" style="color: rgb(173, 157, 78);">
                    <i class="fa-solid fa-tags"></i> Thường được mua kèm
                </h4>
                <a href="phukien.php" class="text-decoration-none text-muted">Xem tất cả <i class="fa-solid fa-angle-right"></i></a>
            </div>
            
            <div class="row">
                <?php foreach( $suggested_products as $sp): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="card h-100 shadow-sm sanpham-card">
                        <?php $suggest_discount = get_sale_percent($sp['id']); $suggest_sale_price = get_discounted_price($sp['price'], $suggest_discount); ?>
                        <?php if ($suggest_discount > 0): ?>
                            <div class="sale-badge">Giảm <?php echo $suggest_discount; ?>%</div>
                        <?php endif; ?>
                        <a href="product-detail.php?id=<?php echo $sp['id']; ?>">
                            <img src="<?php echo htmlspecialchars($sp['image']); ?>" 
                                 class="card-img-top picture p-2" 
                                 alt="<?php echo htmlspecialchars($sp['name']); ?>"
                                 style="height: 180px; object-fit: contain;">
                        </a>
                        <div class="card-body d-flex flex-column text-center p-2">
                            <h6 class="card-title product-name mb-1" style="font-size: 0.95rem;">
                                <a href="product-detail.php?id=<?php echo $sp['id']; ?>"><?php echo htmlspecialchars($sp['name']); ?></a>
                            </h6>
                            <p class="card-text product-price mt-auto mb-2" style="font-size: 1rem;">
                                <?php if ($suggest_discount > 0): ?>
                                    <span class="price-new"><?php echo format_price($suggest_sale_price); ?></span>
                                    <span class="price-old"><?php echo format_price($sp['price']); ?></span>
                                <?php else: ?>
                                    <?php echo format_price($sp['price']); ?>
                                <?php endif; ?>
                            </p>
                            <a href="cart-handler.php?action=add&id=<?php echo $sp['id']; ?>" class="btn add-to-cart-btn btn-sm">Thêm giỏ</a>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center py-5">
            <h1 class="display-4" style="color: rgb(173, 157, 78);">Không tìm thấy sản phẩm</h1>
            <p class="lead">Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
            <a href="index.php" class="btn auth-button btn-lg mt-3">Quay về trang chủ</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function changeImg(src) {
        document.getElementById('mainImage').src = src;
    }
</script>

<?php include 'footer.php'; ?>