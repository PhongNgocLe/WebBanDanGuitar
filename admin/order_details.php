<?php
    include 'admin_header.php';
    include 'auth_check.php';
    global $conn;

    if (!isset($_GET['id'])) {
        header('Location: orders.php');
        exit;
    }

    $order_id = (int)$_GET['id'];

    // 1. Lấy thông tin đơn hàng (Dùng đúng cột fullname, address...)
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        echo "<div class='alert alert-danger'>Không tìm thấy đơn hàng.</div>";
        include 'admin_footer.php';
        exit;
    }

    // 2. Lấy chi tiết sản phẩm (JOIN bảng order_details với products)
    // Lưu ý: Dùng LEFT JOIN để lỡ sản phẩm bị xóa thì vẫn hiện thông tin giá/số lượng
    $sql_items = "SELECT od.*, p.name as product_name, p.image 
                  FROM order_details od 
                  LEFT JOIN products p ON od.product_id = p.id 
                  WHERE od.order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Chi tiết đơn hàng #<?php echo $order['id']; ?></h1>
    <a href="orders.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Thông tin khách hàng</div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
                <p><strong>SĐT:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['note']); ?></p>
                <p><strong>Ngày tạo:</strong> <?php echo $order['created_at']; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-dark">Cập nhật trạng thái</div>
            <div class="card-body">
                <form action="order_handler.php" method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                        <select name="status" class="form-select">
                            <option value="Đang xử lý" <?php echo ($order['status']=='Đang xử lý')?'selected':''; ?>>Đang xử lý</option>
                            <option value="Đang giao hàng" <?php echo ($order['status']=='Đang giao hàng')?'selected':''; ?>>Đang giao hàng</option>
                            <option value="Đã giao" <?php echo ($order['status']=='Đã giao')?'selected':''; ?>>Đã giao</option>
                            <option value="Đã hủy" <?php echo ($order['status']=='Đã hủy')?'selected':''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Danh sách sản phẩm</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_calc = 0;
                        while($item = $items_result->fetch_assoc()): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total_calc += $item_total;
                            $p_name = $item['product_name'] ?? 'Sản phẩm đã bị xóa';
                            $p_img = $item['image'] ?? '/images/no-image.png';
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../<?php echo htmlspecialchars($p_img); ?>" width="50" height="50" style="object-fit:cover" class="me-2 border rounded">
                                    <span><?php echo htmlspecialchars($p_name); ?></span>
                                </div>
                            </td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="fw-bold"><?php echo number_format($item_total, 0, ',', '.'); ?> đ</td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <tr class="table-active">
                            <td colspan="3" class="text-end fw-bold">Tổng tiền (DB lưu):</td>
                            <td class="text-danger fw-bold fs-5">
                                <?php echo number_format($order['total_money'], 0, ',', '.'); ?> đ
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>