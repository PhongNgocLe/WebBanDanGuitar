<?php
    $currentPage = 'profile';
    include 'header.php';
    include_once 'db.php';
    include_once 'QLSP.php';

    if (!isset($_SESSION['username'])) {
        echo "<script>window.location.href='Login.php';</script>";
        exit;
    }

    $username = $_SESSION['username'];
    $success_msg = '';
    $error_msg = '';

    // ==========================================
    // KIỂM TRA QUYỀN ADMIN (Bạn có thể điều chỉnh logic này theo hệ thống của bạn)
    // ==========================================
    // Ví dụ: Nếu bạn dùng $_SESSION['role'] == 'admin' thì thay điều kiện ở dưới
    $isAdmin = ($username === 'admin'); 

    // ==========================================
    // XỬ LÝ FORM SUBMIT (CẬP NHẬT & ĐỔI MẬT KHẨU)
    // ==========================================
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // 1. Xử lý cập nhật thông tin (Chỉ áp dụng nếu không phải admin)
        if (isset($_POST['update_profile']) && !$isAdmin) {
            $fullname = trim($_POST['fullname']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);

            $stmt_update = $conn->prepare("UPDATE users SET fullname=?, phone=?, address=? WHERE username=?");
            $stmt_update->bind_param("ssss", $fullname, $phone, $address, $username);
            
            if ($stmt_update->execute()) {
                $success_msg = "Cập nhật thông tin thành công!";
            } else {
                $error_msg = "Lỗi cập nhật thông tin. Vui lòng thử lại!";
            }
            $stmt_update->close();
        }

        // 2. Xử lý đổi mật khẩu
        if (isset($_POST['change_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Lấy mật khẩu hiện tại từ DB để kiểm tra
            $stmt_pass = $conn->prepare("SELECT password FROM users WHERE username=?");
            $stmt_pass->bind_param("s", $username);
            $stmt_pass->execute();
            $db_pass = $stmt_pass->get_result()->fetch_assoc()['password'];
            $stmt_pass->close();

            if (!password_verify($old_password, $db_pass) && $old_password !== $db_pass) { 
                $error_msg = "Mật khẩu hiện tại không đúng!";
            } elseif ($new_password !== $confirm_password) {
                $error_msg = "Mật khẩu xác nhận không khớp!";
            } else {
                // Đổi mật khẩu
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); 
                
                $stmt_upd_pass = $conn->prepare("UPDATE users SET password=? WHERE username=?");
                $stmt_upd_pass->bind_param("ss", $hashed_password, $username);
                
                if ($stmt_upd_pass->execute()) {
                    $success_msg = "Đổi mật khẩu thành công!";
                } else {
                    $error_msg = "Lỗi khi đổi mật khẩu!";
                }
                $stmt_upd_pass->close();
            }
        }
    }

    // ==========================================
    // LẤY THÔNG TIN USER (Lấy sau khi xử lý POST để hiển thị data mới nhất)
    // ==========================================
    $stmt_get = $conn->prepare("SELECT id, fullname, email, phone, address FROM users WHERE username=?");
    $stmt_get->bind_param("s", $username);
    $stmt_get->execute();
    $user_info = $stmt_get->get_result()->fetch_assoc();
    $user_id = $user_info['id'];
    $stmt_get->close();

    // --- DÙNG HÀM TỪ QLSP.PHP ---
    $discount = get_user_discount_percent($user_id);
    
    // Xác định tên hạng để hiển thị
    $tier_name = 'Đồng'; $tier_color = '#cd7f32'; $icon = 'fa-medal';
    if ($discount == 15) { $tier_name = 'Kim Cương'; $tier_color = '#00bcd4'; $icon = 'fa-gem'; }
    elseif ($discount == 10) { $tier_name = 'Vàng'; $tier_color = '#ffc107'; $icon = 'fa-medal'; }
    elseif ($discount == 5) { $tier_name = 'Bạc'; $tier_color = '#9e9e9e'; $icon = 'fa-medal'; }

    // Tính tổng chi tiêu để hiển thị
    $stmt_spent = $conn->prepare("SELECT SUM(total_money) as total FROM orders WHERE user_id=? AND status='Đã giao'");
    $stmt_spent->bind_param("i", $user_id);
    $stmt_spent->execute();
    $spent_res = $stmt_spent->get_result()->fetch_assoc();
    $total_spent = $spent_res['total'] ?? 0;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, #2b2b2b 0%, #1a1a1a 100%); color: white;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase mb-1" style="letter-spacing: 1px; font-size: 0.85rem; color: #aaa;">P-Guitar Member</p>
                        <h3 class="fw-bold mb-0" style="color: <?php echo $tier_color; ?>;">
                            <i class="fa-solid <?php echo $icon; ?> me-2"></i>Thành viên <?php echo $tier_name; ?>
                        </h3>
                        <p class="mt-2 mb-0">Ưu đãi giảm <strong class="fs-5 text-warning"><?php echo $discount; ?>%</strong> vào giá trị đơn hàng!</p>
                    </div>
                    <div class="text-md-end">
                        <p class="mb-1 text-muted">Tổng tích lũy</p>
                        <h4 class="fw-bold mb-0 text-white"><?php echo number_format($total_spent, 0, ',', '.'); ?> đ</h4>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <?php if($success_msg): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if($error_msg): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!$isAdmin): ?>
                        <div class="col-md-6 mb-4 mb-md-0 pe-md-4" style="border-right: 1px solid #eee;">
                            <h5 class="mb-4 fw-bold" style="color: rgb(173, 157, 78);">Thông tin cá nhân</h5>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Email đăng nhập</label>
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Họ và tên</label>
                                    <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user_info['fullname'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Địa chỉ giao hàng</label>
                                    <textarea name="address" class="form-control" rows="2" required><?php echo htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" name="update_profile" class="btn w-100 fw-bold" style="background-color: rgb(173, 157, 78); color: white;">
                                    Cập nhật thông tin
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>

                        <div class="<?php echo $isAdmin ? 'col-md-6 mx-auto' : 'col-md-6 ps-md-4'; ?>">
                            <h5 class="mb-4 fw-bold" style="color: rgb(173, 157, 78);">Đổi mật khẩu</h5>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Mật khẩu hiện tại</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-dark w-100 fw-bold">
                                    Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>