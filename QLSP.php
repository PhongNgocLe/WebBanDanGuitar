<?php
include_once __DIR__ . '/db.php'; 

/**
 * HÀM MỚI: Tính tỷ lệ giảm giá dựa trên hạng thành viên
 * Trả về con số phần trăm (VD: 5, 10, 15)
 */
function get_user_discount_percent($user_id) {
    global $conn;
    
    // Tính tổng tiền các đơn hàng đã hoàn thành (Đã giao)
    $stmt = $conn->prepare("SELECT SUM(total_money) as total FROM orders WHERE user_id = ? AND status = 'Đã giao'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_spent = $result['total'] ?? 0;
    $stmt->close();

    // Logic phân hạng (Khớp với profile.php)
    if ($total_spent >= 20000000) return 15; // Kim cương
    if ($total_spent >= 10000000) return 10; // Vàng
    if ($total_spent >= 3000000)  return 5;  // Bạc
    return 0; // Đồng
}

/**
 * Hàm: Lấy tỷ lệ giảm giá theo sản phẩm
 */
function get_sale_percent($product_id) {
    if ($product_id % 5 === 0) return 25;
    if ($product_id % 4 === 0) return 20;
    if ($product_id % 3 === 0) return 15;
    if ($product_id % 2 === 0) return 10;
    return 0;
}

/**
 * Hàm: Tính giá sau giảm
 */
function get_discounted_price($price, $percent) {
    if ($percent <= 0) {
        return $price;
    }
    return round($price * (100 - $percent) / 100);
}

/**
 * Hàm: Lấy TẤT CẢ sản phẩm
 */
function get_all_products() {
    global $conn; 
    $products = [];
    $sql = "SELECT id, name, price, category, description, image FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC); 
    }
    return $products;
}

/**
 * Hàm: Tìm 1 sản phẩm bằng ID
 */
function find_product_by_id($id) {
    global $conn; 
    $stmt = $conn->prepare("SELECT id, name, price, category, description, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc(); 
    }
    return null; 
}

/**
 * Lấy sản phẩm THEO LOẠI
 */
function get_products_by_category($category) {
    global $conn;
    $products = [];
    $stmt = $conn->prepare("SELECT id, name, price, category, description, image FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $products;
}

/**
 * Tìm kiếm sản phẩm
 */
function search_products($query) {
    global $conn;
    $products = [];
    $search_query = "%" . $query . "%";
    $stmt = $conn->prepare("SELECT id, name, price, category, description, image FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $search_query, $search_query); 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $products;
}

/**
 * Lấy danh sách danh mục
 */
function get_all_categories() {
    global $conn;
    $categories = [];
    $sql = "SELECT id, name, slug FROM categories ORDER BY id ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $categories;
}

/**
 * Định dạng tiền tệ
 */
function format_price($price) {
    return number_format($price, 0, ',', '.') . ' đ';
}
function get_product_images($product_id) {
    global $conn;
    $images = [];
    $stmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $images = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $images;
}
?>