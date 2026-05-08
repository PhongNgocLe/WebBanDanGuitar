<?php
include 'auth_check.php';
include_once '../QLSP.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $upload_dir_server = __DIR__ . '/../images/';
    
    // 1. Xử lý XÓA sản phẩm (Làm ngay đầu tiên, chỉ cần ID)
    if ($action == 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header('Location: products.php');
        exit; // Dừng luôn tại đây, không chạy các dòng code bên dưới nữa
    }

    // 2. Xử lý THÊM (create) và SỬA (update)
    // Lúc này chắc chắn form có gửi name, price... nên ta mới dùng $_POST
    $name = trim($_POST['name'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Biến lưu ảnh đại diện (mặc định là ảnh cũ nếu có)
    $main_image = $_POST['current_image'] ?? '';

    // Xử lý Upload nhiều ảnh
    $uploaded_images = [];
    if (isset($_FILES['image_upload']) && !empty($_FILES['image_upload']['name'][0])) {
        foreach ($_FILES['image_upload']['name'] as $key => $val) {
            if ($_FILES['image_upload']['error'][$key] == 0) {
                $file_name = uniqid() . '-' . basename($_FILES['image_upload']['name'][$key]);
                $target_path = $upload_dir_server . $file_name;
                
                if (move_uploaded_file($_FILES['image_upload']['tmp_name'][$key], $target_path)) {
                    $path_to_save = '/images/' . $file_name;
                    $uploaded_images[] = $path_to_save;
                    
                    // Nếu chưa có ảnh đại diện, lấy ảnh đầu tiên làm ảnh đại diện
                    if (empty($main_image)) {
                        $main_image = $path_to_save;
                    }
                }
            }
        }
    }

    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $name, $price, $category, $description, $main_image);
        $stmt->execute();
        $product_id = $conn->insert_id;
    } elseif ($action == 'update') {
        $product_id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sisssi", $name, $price, $category, $description, $main_image, $product_id);
        $stmt->execute();
    }

    // Lưu các ảnh mới vào bảng product_images (nếu có)
    if (!empty($uploaded_images) && isset($product_id)) {
        $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        foreach ($uploaded_images as $img_path) {
            $stmt_img->bind_param("is", $product_id, $img_path);
            $stmt_img->execute();
        }
        $stmt_img->close();
    }

    header('Location: products.php');
    exit;
}
?>