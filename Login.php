<?php
// Bắt đầu session NGAY LẬP TỨC
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; // Gọi file kết nối CSDL

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. TÌM NGƯỜI DÙNG BẰNG TÊN ĐĂNG NHẬP
    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();

        // 2. KIỂM TRA MẬT KHẨU (QUAN TRỌNG)
        if (password_verify($password, $hashed_password)) {
            // Mật khẩu chính xác!
            $_SESSION['username'] = $username;
            
            // === THÊM ĐOẠN NÀY ĐỂ FIX LỖI ===
            $_SESSION['user'] = [
                'username' => $username,
                'role' => $role
            ];
            // ================================
            
            // 3. CHUYỂN HƯỚNG DỰA TRÊN ROLE
            if ($role === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;

        } else {
            // Sai mật khẩu
            $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    } else {
        // Không tìm thấy tên đăng nhập
        $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
    $stmt->close();
    //$conn->close();
}

// Định nghĩa trang hiện tại (không active mục nào)
$currentPage = '';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="auth-box text-center">
            <form class="text-start" action="Login.php" method="POST">
                <?php if (!empty($error_message)): ?>
                    <div class="alert error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Tên Đăng Nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật Khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="show-password" onclick="togglePassword()">
                    <label class="form-check-label" for="show-password">Hiển thị mật khẩu</label>
                </div>
                <button type="submit" class="btn auth-button w-100">Đăng Nhập</button>
            </form>
             <div class="auth-link mt-3">
                Bạn chưa có tài khoản? 
                <a href="Register.php">Đăng kí tại đây</a>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        var passField = document.getElementById("password");
        if (passField.type === "password") {
            passField.type = "text";
        } else {
            passField.type = "password";
        }
    }
</script>

<?php
include 'footer.php';
?>