<?php
    include 'admin_header.php';
    include 'auth_check.php';
    global $conn;

    // Lấy danh sách đơn hàng, mới nhất lên đầu
    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    // Hàm hiển thị màu trạng thái (Dựa trên value trong DB của bạn)
    function get_status_badge($status) {
        // Chuẩn hóa chuỗi về chữ thường để so sánh cho chính xác
        $s = mb_strtolower($status, 'UTF-8');
        
        if (strpos($s, 'xử lý') !== false) return '<span class="badge bg-warning text-dark">Đang xử lý</span>';
        if (strpos($s, 'đang giao') !== false) return '<span class="badge bg-primary">Đang giao hàng</span>';
        if (strpos($s, 'hoàn thành') !== false || strpos($s, 'thành công') !== false) return '<span class="badge bg-success">Thành công</span>';
        if (strpos($s, 'hủy') !== false) return '<span class="badge bg-danger">Đã hủy</span>';
        
        return '<span class="badge bg-secondary">'.$status.'</span>';
    }
?>

<div class="main-header mb-4">
    <h1 class="h2">Quản lý Đơn hàng</h1>
</div>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" class="text-center">Chưa có đơn hàng nào.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="fw-bold">#<?php echo $order['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($order['fullname']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($order['phone']); ?></small>
                    </td>
                    <td class="text-danger fw-bold">
                        <?php echo number_format($order['total_money'], 0, ',', '.'); ?> đ
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                    <td><?php echo get_status_badge($order['status']); ?></td>
                    <td class="text-center">
                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm text-white">
                            <i class="fa fa-eye"></i> Xem
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>