<?php
    include 'admin_header.php';
    include_once '../QLSP.php';
    $categories = get_all_categories();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2">Quản lý Danh mục</h1>
    <a href="category_edit.php?action=add" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm danh mục mới
    </a>
</div>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
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
                <th scope="col" style="width: 10%;">ID</th>
                <th scope="col" style="width: 30%;">Tên danh mục</th>
                <th scope="col" style="width: 30%;">Slug (URL)</th>
                <th scope="col" class="text-center" style="width: 20%;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="4" class="text-center">Chưa có danh mục nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="fw-bold align-middle"><?php echo $cat['id']; ?></td>
                        <td class="align-middle"><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td class="align-middle text-muted"><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td class="text-center align-middle action-buttons">
                            <a href="category_edit.php?action=edit&id=<?php echo $cat['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            
                            <form action="category_handler.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Xóa danh mục này có thể ảnh hưởng đến các sản phẩm liên quan. Bạn có chắc chắn?');">
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            </form>
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