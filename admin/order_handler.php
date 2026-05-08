<?php
include 'auth_check.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'update_status') {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['status'];

        // Cập nhật trạng thái
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Đã cập nhật trạng thái đơn hàng #$order_id thành công!";
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật trạng thái.";
        }
        
        // Quay lại trang danh sách
        header("Location: orders.php");
        exit;
    }
}

// Nếu truy cập trực tiếp file này mà không post
header("Location: orders.php");
exit;
?>