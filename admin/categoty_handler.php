<?php
include 'auth_check.php';
global $conn;

// Hàm helper để chuyển hướng
function redirect_with_message($page, $message, $type = 'message') {
    $_SESSION[$type] = $message;
    header("Location: $page");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    redirect_with_message('categories.php', 'Truy cập không hợp lệ.', 'error');
}

$action = $_POST['action'];

switch ($action) {
    
    case 'create':
    case 'update':
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $id = (int)($_POST['id'] ?? 0);

        if (empty($name) || empty($slug)) {
             redirect_with_message('categories.php', 'Tên và Slug không được để trống.', 'error');
        }

        // Kiểm tra trùng lặp Tên hoặc Slug (trừ chính nó khi update)
        $sql_check = "SELECT id FROM categories WHERE (name = ? OR slug = ?) " . ($id ? " AND id != ?" : "");
        $stmt_check = $conn->prepare($sql_check);
        
        if ($id) {
            $stmt_check->bind_param("ssi", $name, $slug, $id);
        } else {
            $stmt_check->bind_param("ss", $name, $slug);
        }
        
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            redirect_with_message('categories.php', 'Tên hoặc Slug danh mục đã tồn tại!', 'error');
        }
        $stmt_check->close();

        if ($action == 'create') {
            $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $slug);
            $stmt->execute();
            redirect_with_message('categories.php', 'Thêm danh mục thành công!');
            
        } elseif ($action == 'update') {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $slug, $id);
            $stmt->execute();
            redirect_with_message('categories.php', 'Cập nhật danh mục thành công!');
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];

        // Cập nhật các sản phẩm có category này về NULL (hoặc một giá trị mặc định)
        // Lưu ý: Do ta đang dùng category (VARCHAR) trong bảng products, ta cần tìm tên category:
        $cat_to_delete = find_category_by_id($id);
        if ($cat_to_delete) {
            $cat_name = $cat_to_delete['name'];
            $stmt_update_products = $conn->prepare("UPDATE products SET category = NULL WHERE category = ?");
            $stmt_update_products->bind_param("s", $cat_name);
            $stmt_update_products->execute();
            $stmt_update_products->close();
        }

        // Tiến hành xóa danh mục
        $stmt_delete = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        
        redirect_with_message('categories.php', 'Đã xóa danh mục thành công. Các sản phẩm liên quan đã được đặt lại category.');
        break;

    default:
        redirect_with_message('categories.php', 'Hành động không xác định.', 'error');
}
?>