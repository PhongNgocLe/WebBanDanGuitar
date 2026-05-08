<?php
    include 'admin_header.php'; // Đã có $conn
    
    // Mặc định là form "Thêm mới"
    $form_action = 'create';
    $form_title = "Thêm người dùng mới";
    $user = [
        'id' => '',
        'username' => '',
        'role' => 'user' // Mặc định là user
    ];
    $password_required = 'required'; // Mật khẩu là bắt buộc khi tạo mới

    // Kiểm tra nếu là hành động "Sửa"
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $form_action = 'update';
            $form_title = "Sửa người dùng";
            $password_required = ''; // Mật khẩu KHÔNG bắt buộc khi sửa
        }
        $stmt->close();
    }
?>

<div class="main-header">
    <div>
        <h1 class="h2"><?php echo $form_title; ?></h1>
        <?php if($form_action == 'update'): ?>
            <h6 class="text-muted">ID: <?php echo htmlspecialchars($user['id']); ?></h6>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="user_handler.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $form_action; ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập (username)</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="role" class="form-label">Quyền (Role)</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>
                        User (Khách hàng)
                    </option>
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>
                        Admin (Quản trị)
                    </option>
                </select>
            </div>
            
            <hr>
            
            <p class="form-text">
                <?php if ($form_action == 'update'): ?>
                    <b>Để trống phần mật khẩu nếu bạn không muốn thay đổi.</b>
                <?php else: ?>
                    Mật khẩu là bắt buộc khi tạo mới.
                <?php endif; ?>
            </p>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" <?php echo $password_required; ?>>
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" <?php echo $password_required; ?>>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu người dùng</button>
            <a href="users.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php
    include 'admin_footer.php';
?>