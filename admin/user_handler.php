<?php
include 'auth_check.php'; // Đã có session_start() và $conn
global $conn;

// 0. Hàm helper để chuyển hướng
function redirect_with_message($page, $message, $type = 'message') {
    $_SESSION[$type] = $message;
    header("Location: $page");
    exit;
}

// 1. Lấy hành động
$action = $_POST['action'] ?? '';

switch ($action) {
    /**
     * HÀNH ĐỘNG TẠO MỚI (CREATE)
     */
    case 'create':
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $role = $_POST['role'];

        if ($password != $password_confirm) {
            redirect_with_message('user_edit.php?action=add', 'Mật khẩu xác nhận không khớp!', 'error');
        }

        // Kiểm tra username đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            redirect_with_message('user_edit.php?action=add', 'Tên đăng nhập đã tồn tại!', 'error');
        }
        $stmt->close();

        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm vào CSDL
        // CẦN CỘT created_at trong bảng users (nên đặt mặc định là CURRENT_TIMESTAMP trong CSDL)
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $username, $hashed_password, $role);
        $stmt_insert->execute();
        $stmt_insert->close();

        redirect_with_message('users.php', 'Đã thêm người dùng mới thành công!');
        break;

    /**
     * HÀNH ĐỘNG CẬP NHẬT (UPDATE)
     */
    case 'update':
        $id = (int)$_POST['id'];
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // Kiểm tra username mới (nếu đổi) có bị trùng không
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            redirect_with_message('user_edit.php?action=edit&id=' . $id, 'Tên đăng nhập đã tồn tại!', 'error');
        }
        $stmt->close();

        // Xử lý cập nhật mật khẩu (chỉ khi được nhập)
        if (!empty($password)) {
            if ($password != $password_confirm) {
                redirect_with_message('user_edit.php?action=edit&id=' . $id, 'Mật khẩu xác nhận không khớp!', 'error');
            }
            // Cập nhật cả username, role và password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?");
            $stmt_update->bind_param("sssi", $username, $role, $hashed_password, $id);
        } else {
            // Chỉ cập nhật username và role
            $stmt_update = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $username, $role, $id);
        }

        $stmt_update->execute();
        $stmt_update->close();

        redirect_with_message('users.php', 'Cập nhật người dùng thành công!');
        break;

    /**
     * HÀNH ĐỘNG XÓA (DELETE)
     */
    case 'delete':
        $id = (int)$_POST['id'];

        // Kiểm tra để không tự xóa
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($username_to_delete);
        $stmt->fetch();
        $stmt->close();

        if ($username_to_delete == $_SESSION['username']) {
            redirect_with_message('users.php', 'Không thể tự xóa tài khoản của bạn!', 'error');
        }

        // Tiến hành xóa
        $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        $stmt_delete->close();

        redirect_with_message('users.php', 'Đã xóa người dùng thành công.');
        break;

    // Mặc định
    default:
        header('Location: users.php');
        exit;
}
?>