<?php
session_start();
include_once 'db.php'; 
include_once 'QLSP.php'; 

// 1. Kiểm tra đăng nhập và giỏ hàng
if (!isset($_SESSION['username'])) {
    header('Location: Login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: Giohang.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Lấy thông tin từ form
    $name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $notes = trim($_POST['notes'] ?? '');
    
    // BẮT THÊM PHƯƠNG THỨC THANH TOÁN
    $payment_method = $_POST['payment_method'] ?? 'COD';
    
    // 3. Lấy User ID
    $username = $_SESSION['username'];
    $stmt_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $user_data = $stmt_user->get_result()->fetch_assoc();
    $user_id = $user_data['id'];
    $stmt_user->close();

    // 4. Tính tổng tiền gốc từ giỏ hàng
    $total_before_discount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_before_discount += $item['price'] * $item['quantity'];
    }

    // --- BƯỚC QUAN TRỌNG: ÁP DỤNG GIẢM GIÁ THÀNH VIÊN ---
    $discount_percent = get_user_discount_percent($user_id);
    $discount_amount = $total_before_discount * ($discount_percent / 100);
    $final_total = $total_before_discount - $discount_amount;

    // 5. BẮT ĐẦU TRANSACTION
    $conn->begin_transaction();

    try {
        // A. Lưu vào bảng ORDERS (THÊM PAYMENT METHOD VÀO CÂU SQL)
        $status = 'Đang xử lý';
        // Lưu ý: Cần đảm bảo bảng orders đã có cột payment_method (Chạy câu lệnh SQL ở Bước 1)
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, fullname, phone, address, note, payment_method, total_money, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_order->bind_param("isssssis", $user_id, $name, $phone, $address, $notes, $payment_method, $final_total, $status);
        
        if (!$stmt_order->execute()) {
            throw new Exception("Lỗi khi tạo đơn hàng.");
        }
        $order_id = $conn->insert_id;
        $stmt_order->close();

        // B. Lưu chi tiết sản phẩm
        $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_detail->bind_param("iiii", $order_id, $item['id'], $item['price'], $item['quantity']);
            if (!$stmt_detail->execute()) {
                throw new Exception("Lỗi khi lưu chi tiết sản phẩm.");
            }
        }
        $stmt_detail->close();

        $conn->commit();

        // 6. Xóa giỏ hàng và lưu thông tin sang trang success để hiển thị mã QR
        unset($_SESSION['cart']);
        
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;
        $_SESSION['payment_method'] = $payment_method;
        $_SESSION['total_money'] = $final_total;
        
        if ($discount_percent > 0) {
            $_SESSION['discount_msg'] = "Bạn đã được giảm " . $discount_percent . "% nhờ hạng thành viên!";
        }
        
        header('Location: order_success.php');
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['order_error'] = "Có lỗi xảy ra: " . $e->getMessage();
        header('Location: Checkout.php');
        exit;
    }

} else {
    header('Location: Checkout.php');
    exit;
}
?>