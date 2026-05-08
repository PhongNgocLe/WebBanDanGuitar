<?php
// Bắt đầu session để làm việc với $_SESSION['cart']
session_start();
// Gọi file QLSP để có hàm find_product_by_id()
include_once 'QLSP.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Lấy hành động và ID (ép kiểu int để bảo mật)
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Nếu không có ID hợp lệ (cho các action cần ID)
if ($id <= 0 && in_array($action, ['add', 'remove', 'increase', 'decrease'])) {
    header('Location: index.php');
    exit;
}

switch ($action) {
    /**
     * HÀNH ĐỘNG THÊM SẢN PHẨM (ĐÃ SỬA)
     */
    case 'add':
        // SỬA LỖI: Lấy sản phẩm từ CSDL
        $product = find_product_by_id($id);
        
        if ($product) {
            $discount_percent = get_sale_percent($product['id']);
            $sale_price = get_discounted_price($product['price'], $discount_percent);

            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'id'              => $product['id'],
                    'name'            => $product['name'],
                    'image'           => $product['image'],
                    'price'           => $sale_price,
                    'original_price'  => $product['price'],
                    'discount_percent'=> $discount_percent,
                    'quantity'        => 1
                ];
            }
        }
        // Chuyển hướng về trang giỏ hàng
        header('Location: Giohang.php');
        exit;

    /**
     * HÀNH ĐỘNG MỚI: TĂNG SỐ LƯỢNG
     */
    case 'increase':
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        }
        header('Location: Giohang.php');
        exit;
        
    /**
     * HÀNH ĐỘNG MỚI: GIẢM SỐ LƯỢNG
     */
    case 'decrease':
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']--;
            // Nếu giảm về 0 thì tự động xóa
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        header('Location: Giohang.php');
        exit;

    /**
     * HÀNH ĐỘNG XÓA 1 SẢN PHẨM (Giữ nguyên)
     */
    case 'remove':
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: Giohang.php');
        exit;

    /**
     * HÀNH ĐỘNG XÓA TẤT CẢ (Giữ nguyên)
     */
    case 'clear':
        unset($_SESSION['cart']);
        header('Location: Giohang.php');
        exit;
}

// Nếu không có hành động gì, về trang chủ
header('Location: index.php');
exit;
?>