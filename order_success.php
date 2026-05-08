<?php
    $currentPage = '';
    include 'header.php';
    include_once 'QLSP.php'; 
    
    if (!isset($_SESSION['order_success'])) {
        echo "<script>window.location.href='index.php';</script>";
        exit;
    }

    $order_id = $_SESSION['order_id'] ?? '';
    $payment_method = $_SESSION['payment_method'] ?? 'COD';
    $total_money = $_SESSION['total_money'] ?? 0;
    $discount_msg = $_SESSION['discount_msg'] ?? '';

    // Xóa session sau khi lấy thông tin để F5 không bị lặp lại
    unset($_SESSION['order_success']); 
    unset($_SESSION['order_id']);
    unset($_SESSION['payment_method']);
    unset($_SESSION['total_money']);
    unset($_SESSION['discount_msg']);
?>

<div class="container my-5 text-center">
    <i class="fa-solid fa-circle-check fa-4x mb-3" style="color: #28a745;"></i>
    <h1 class="display-5 fw-bold" style="color: rgb(173, 157, 78);">Đặt Hàng Thành Công!</h1>
    
    <div class="alert alert-success mx-auto mt-4" style="max-width: 600px;">
         <h5 class="mb-2">Mã đơn hàng: <strong>#<?php echo htmlspecialchars($order_id); ?></strong></h5>
         <?php if($discount_msg): ?>
            <p class="mb-0 text-danger fw-bold"><i class="fa-solid fa-gift"></i> <?php echo $discount_msg; ?></p>
         <?php endif; ?>
    </div>

    <div class="card mx-auto shadow-sm border-0 mt-4" style="max-width: 600px; background-color: #fcfcfc;">
        <div class="card-body p-4">
            <h4 class="mb-4">Tổng thanh toán: <strong class="text-danger fs-3"><?php echo format_price($total_money); ?></strong></h4>
            
            <?php if ($payment_method == 'COD'): ?>
                <div class="alert alert-info border-0">
                    <i class="fa-solid fa-truck-fast fa-2x mb-2 text-info"></i>
                    <h5>Thanh toán khi nhận hàng (COD)</h5>
                    <p class="mb-0">Vui lòng chuẩn bị sẵn tiền mặt khi shipper giao hàng đến nhé!</p>
                </div>

            <?php elseif ($payment_method == 'MoMo'): ?>
                <div style="background-color: #a50064; color: white; border-radius: 8px; padding: 20px;">
                    <img src="/images/" alt="MoMo" style="height: 40px; margin-bottom: 15px;">
                    <h5>Chuyển khoản qua MoMo</h5>
                    <p>Quét mã QR bên dưới hoặc chuyển vào SĐT: <strong>0334090425</strong></p>
                    <p>Nội dung chuyển khoản: <strong>THANHTOAN <?php echo $order_id; ?></strong></p>
                    <img src="/images/qr.png" alt="QR MoMo" style="width: 200px; border-radius: 10px; border: 2px solid white;">
                </div>

            <?php elseif ($payment_method == 'VNPAY'): ?>
                <div style="background-color: #005baa; color: white; border-radius: 8px; padding: 20px;">
                    <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418186387.png" alt="VNPAY" style="height: 30px; margin-bottom: 15px; filter: brightness(0) invert(1);">
                    <h5>Thanh toán qua VNPAY</h5>
                    <p>Mở ứng dụng ngân hàng có hỗ trợ VNPAY để quét mã</p>
                    <p>Nội dung thanh toán: <strong>THANHTOAN <?php echo $order_id; ?></strong></p>
                    <img src="/images/qr.png" alt="QR VNPAY" style="width: 200px; border-radius: 10px; border: 2px solid white;">
                </div>

            <?php elseif ($payment_method == 'Chuyển khoản'): ?>
                <div class="alert alert-secondary border-0 text-start">
                    <h5 class="text-center text-primary mb-3"><i class="fa-solid fa-building-columns"></i> Thông tin chuyển khoản</h5>
                    <p class="mb-1"><strong>Ngân hàng:</strong> Vietcombank</p>
                    <p class="mb-1"><strong>Chủ tài khoản:</strong> LÊ NGỌC PHONG</p>
                    <p class="mb-1"><strong>Số tài khoản:</strong> 1234567890</p>
                    <p class="mb-1"><strong>Số tiền:</strong> <strong class="text-danger"><?php echo format_price($total_money); ?></strong></p>
                    <p class="mb-0"><strong>Nội dung:</strong> THANHTOAN <?php echo $order_id; ?></p>
                </div>
            <?php endif; ?>
            
            <p class="text-muted small mt-4">
                *Đơn hàng của bạn sẽ được xử lý ngay sau khi chúng tôi xác nhận thanh toán.
            </p>
        </div>
    </div>
   
    <div class="mt-4">
        <a href="order_history.php" class="btn btn-outline-secondary btn-lg me-2">Xem đơn hàng</a>
        <a href="index.php" class="btn auth-button btn-lg" style="background-color: rgb(173, 157, 78); color: white;">Tiếp tục mua sắm</a>
    </div>
</div>

<?php
    include 'footer.php';
?>