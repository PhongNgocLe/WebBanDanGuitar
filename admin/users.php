<?php
    include 'admin_header.php'; // Đã có $conn từ auth_check.php
    
    // Lấy tất cả người dùng
    $result = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
    $users = $result->fetch_all(MYSQLI_ASSOC);
    
    // Lấy tên người dùng đang đăng nhập để kiểm tra (không cho tự xóa)
    $current_admin_username = $_SESSION['username'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2">Quản lý Người dùng</h1>
    <a href="user_edit.php?action=add" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm người dùng mới
    </a>
</div>

<?php
// Hiển thị thông báo nếu có (từ user_handler.php)
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="table-responsive mt-3">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên đăng nhập (username)</th>
                <th scope="col">Quyền (Role)</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col" class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="text-center">Chưa có người dùng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="fw-bold align-middle"><?php echo $user['id']; ?></td>
                        <td class="align-middle">
                            <?php echo htmlspecialchars($user['username']); ?>
                            <?php if ($user['username'] == $current_admin_username)  ?>
                        </td>
                        <td class="align-middle">
                            <?php 
                                $role_class = ($user['role'] == 'admin') ? 'badge bg-danger' : 'badge bg-secondary';
                                echo '<span class="' . $role_class . '">' . htmlspecialchars($user['role']) . '</span>';
                            ?>
                        </td>
                        <td class="align-middle"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                        <td class="text-center align-middle action-buttons">
                            
                            <a href="user_edit.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            
                            <?php 
                            // QUAN TRỌNG: Không cho phép admin tự xóa chính mình
                            if ($user['username'] != $current_admin_username): 
                            ?>
                                <form action="user_handler.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
    include 'admin_footer.php';
?>