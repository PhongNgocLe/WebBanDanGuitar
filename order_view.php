<?php
    $currentPage = 'history';
    include 'header.php';
    // SỬA: Dùng đường dẫn tương đối hoặc tuyệt đối chính xác
    include_once 'db.php';
    include_once 'QLSP.php';

    if (!isset($_SESSION['username'])) {
        // Chuyển hướng bằng script nếu header đã gửi
        echo "<script>window.location.href='Login.php';</script>";
        exit;
    }

    $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // 1. Lấy thông tin đơn hàng (phải khớp với user đang đăng nhập để bảo mật)
    $username = $_SESSION['username'];
    // Truy vấn join bảng users để kiểm tra quyền sở hữu đơn hàng
    $sql_order = "SELECT o.* FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = ? AND u.username = ?";
    
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("is", $order_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // Nếu không tìm thấy đơn hoặc không phải của user này
    if (!$order) {
        echo '<div class="container my-5 text-center">';
        echo '<i class="fa-solid fa-circle-exclamation fa-3x text-warning mb-3"></i>';
        echo '<h3>Không tìm thấy đơn hàng!</h3>';
        echo '<p>Đơn hàng không tồn tại hoặc bạn không có quyền xem.</p>';
        echo '<a href="order_history.php" class="btn btn-secondary mt-2">Quay lại lịch sử</a>';
        echo '</div>';
        include 'footer.php';
        exit;
    }

    // 2. Lấy chi tiết sản phẩm trong đơn
    $sql_details = "SELECT d.*, p.name, p.image 
                    FROM order_details d 
                    JOIN products p ON d.product_id = p.id 
                    WHERE d.order_id = ?";
    $stmt_d = $conn->prepare($sql_details);
    $stmt_d->bind_param("i", $order_id);
    $stmt_d->execute();
    $details = $stmt_d->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="color: rgb(173, 157, 78);">
            <i class="fa-solid fa-file-invoice"></i> Chi tiết đơn hàng #<?php echo $order['id']; ?>
        </h3>
        <a href="order_history.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại lịch sử
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-user"></i> Thông tin nhận hàng
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
                    <p class="mb-2"><strong>SĐT:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p class="mb-2"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p class="mb-2"><strong>Ghi chú:</strong> <span class="text-muted"><?php echo htmlspecialchars($order['note'] ?? 'Không có'); ?></span></p>
                    <hr>
                    <p class="mb-2"><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    <p class="mb-0"><strong>Trạng thái:</strong> 
                        <?php 
                            $status_class = 'secondary';
                            if($order['status'] == 'Đang xử lý') $status_class = 'warning text-dark';
                            elseif($order['status'] == 'Đang giao') $status_class = 'info text-dark';
                            elseif($order['status'] == 'Hoàn thành') $status_class = 'success';
                            elseif($order['status'] == 'Đã hủy') $status_class = 'danger';
                        ?>
                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-box"></i> Sản phẩm đã mua
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Sản phẩm</th>
                                    <th>Giá</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end pe-3">Tạm tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $item): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px; border: 1px solid #eee;">
                                                <span class="fw-medium"><?php echo htmlspecialchars($item['name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo format_price($item['price']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end pe-3 fw-bold"><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-3 fs-5">Tổng tiền thanh toán:</td>
                                    <td class="text-end pe-3 fw-bold text-danger fs-4 pt-3">
                                        <?php echo format_price($order['total_money']); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>