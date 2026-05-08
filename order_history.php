<?php
    $currentPage = 'history';
    include 'header.php';
    include_once 'db.php';
    include_once 'QLSP.php';

    // Kiểm tra đăng nhập
    if (!isset($_SESSION['username'])) {
        echo "<script>window.location.href='Login.php';</script>";
        exit;
    }

    // Lấy ID user hiện tại
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $user_id = $user['id'];

    // Lấy danh sách đơn hàng của user này (Mới nhất lên đầu)
    $sql_orders = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_orders = $conn->prepare($sql_orders);
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();
    $orders = $result_orders->fetch_all(MYSQLI_ASSOC);
?>

<div class="container my-5">
    <div class="content-head text-center mb-4">
        <h2 style="color: rgb(173, 157, 78);">LỊCH SỬ ĐƠN HÀNG</h2>
        <p>Theo dõi trạng thái và xem lại các đơn hàng của bạn</p>
    </div>
    <div class="divider-heading"></div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-box-open fa-4x text-muted mb-3"></i>
            <p class="lead">Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" class="btn auth-button mt-2">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Ngày đặt</th>
                        <th>Người nhận</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?php echo $order['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['fullname']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['phone']); ?></small>
                            </td>
                            <td class="fw-bold text-danger"><?php echo format_price($order['total_money']); ?></td>
                            <td>
                                <?php 
                                    $status_color = 'secondary';
                                    if($order['status'] == 'Đang xử lý') $status_color = 'warning text-dark';
                                    if($order['status'] == 'Đang giao') $status_color = 'info text-dark';
                                    if($order['status'] == 'Hoàn thành') $status_color = 'success';
                                    if($order['status'] == 'Đã hủy') $status_color = 'danger';
                                ?>
                                <span class="badge bg-<?php echo $status_color; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                            </td>
                            <td class="text-center">
                                <a href="order_view.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>