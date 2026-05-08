<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../db.php'; // Kết nối CSDL

$is_admin = false;

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Truy vấn CSDL để lấy role
    $stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    
    if ($role === 'admin') {
        $is_admin = true;
    }
}

// Nếu không phải admin, đá về trang đăng nhập
if ($is_admin === false) {
    header('Location: ../Login.php');
    exit;
}
?>