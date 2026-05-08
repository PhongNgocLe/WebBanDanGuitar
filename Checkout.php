<?php
    $currentPage = 'Checkout';
    include 'header.php';
    include_once 'QLSP.php';
    
    $cart = $_SESSION['cart'] ?? [];
    $total_price = 0;

    // Kiểm tra giỏ hàng
    if (empty($cart)) {
        echo '<div class="container my-5 text-center"><p class="lead">Giỏ hàng trống. Không thể thanh toán.</p><a href="index.php" class="btn auth-button btn-lg mt-3">Quay lại mua sắm</a></div>';
        include 'footer.php';
        exit;
    }

    // Tính tổng tiền
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
    
    // Lấy thông báo lỗi nếu có
    $order_error = $_SESSION['order_error'] ?? '';
    unset($_SESSION['order_error']);
?>

<div class="container my-5">
    <div class="content-head text-center mb-5">
        <h2 style="color: rgb(173, 157, 78);"><i class="fa-solid fa-credit-card"></i> Tiến Hành Thanh Toán</h2>
    </div>

    <?php if (!empty($order_error)): ?>
        <div class="alert error-message"><?php echo $order_error; ?></div>
    <?php endif; ?>

    <form action="Order-handler.php" method="POST">
        <div class="row">
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-bold">
                        1. Thông Tin Khách Hàng
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số Điện Thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa Chỉ Nhận Hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi Chú Đơn Hàng (Nếu có)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-bold">
                        2. Tóm Tắt Đơn Hàng
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-4">
                            <?php foreach ($cart as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="text-truncate" style="max-width: 70%;">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                        <small class="text-muted">x <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <span class="fw-bold"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-warning-subtle text-dark px-2 mt-2 rounded">
                                <strong>Tổng Cộng:</strong>
                                <strong class="fs-4" style="color: rgb(173, 157, 78);">
                                    <?php echo format_price($total_price); ?>
                                </strong>
                            </li>
                        </ul>
                        
                        <h5 class="mt-4 mb-3 fw-bold" style="color: rgb(173, 157, 78);">3. Phương Thức Thanh Toán</h5>
                        <div class="card mb-4 shadow-sm border-0" style="background-color: #fcfcfc;">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                                    <label class="form-check-label fw-bold" for="pay_cod" style="cursor: pointer;">
                                        <i class="fa-solid fa-money-bill-wave text-success"></i> Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_momo" value="MoMo">
                                    <label class="form-check-label fw-bold" for="pay_momo" style="cursor: pointer;">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" style="height: 24px; margin-right: 5px;"> Thanh toán qua Ví MoMo
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_vnpay" value="VNPAY">
                                    <label class="form-check-label fw-bold" for="pay_vnpay" style="cursor: pointer;">
                                        <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418186387.png" alt="VNPAY" style="height: 24px; margin-right: 5px;"> Thanh toán qua VNPAY
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="Chuyển khoản">
                                    <label class="form-check-label fw-bold" for="pay_bank" style="cursor: pointer;">
                                        <i class="fa-solid fa-building-columns text-primary"></i> Chuyển khoản ngân hàng
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn auth-button btn-lg w-100 mt-2 py-3 fw-bold shadow-sm">
                            <i class="fa-solid fa-bag-shopping"></i> HOÀN TẤT ĐẶT HÀNG
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>

<?php
    include 'footer.php';
?>