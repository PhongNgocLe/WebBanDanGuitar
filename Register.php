<?php
// Bắt đầu session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // Kết nối CSDL

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password)) {
        $error_message = 'Vui lòng nhập đầy đủ thông tin!';
    } else if ($password != $password_confirm) {
        $error_message = 'Mật khẩu nhập lại không khớp!';
    } else {
        // Kiểm tra user tồn tại
        // Lưu ý: Đảm bảo biến $conn lấy từ db.php
        if ($stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?")) {
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error_message = 'Tên đăng nhập này đã được sử dụng!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = ($username === 'admin') ? 'admin' : 'user';

                if ($stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)")) {
                    $stmt_insert->bind_param("sss", $username, $hashed_password, $role);
                    
                    if ($stmt_insert->execute()) {
                        $success_message = 'Đăng ký thành công! Đang chuyển hướng...';
                    } else {
                        $error_message = 'Lỗi khi lưu: ' . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                     $error_message = 'Lỗi chuẩn bị INSERT: ' . $conn->error;
                }
            }
            $stmt_check->close();
        } else {
             $error_message = 'Lỗi chuẩn bị SELECT: ' . $conn->error;
        }
    }
    // --- QUAN TRỌNG: ĐÃ XÓA DÒNG $conn->close() Ở ĐÂY ---
    // Để header.php bên dưới vẫn dùng được kết nối database
}

// Include header (Header sẽ dùng lại kết nối $conn ở trên)
$currentPage = '';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="auth-box text-center">
            <form class="text-start" action="Register.php" method="POST">
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success success-message"><?php echo $success_message; ?></div>
                    <script>
                        setTimeout(function() { window.location.href = 'Login.php'; }, 2000);
                    </script>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Tên Đăng Nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật Khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Nhập Lại Mật Khẩu</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn auth-button w-100">Đăng Ký</button>
            </form>
            <div class="auth-link mt-3">
                Bạn đã có tài khoản? <a href="Login.php">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>