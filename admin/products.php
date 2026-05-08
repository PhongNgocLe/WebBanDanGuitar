<?php
    include 'admin_header.php';
   include_once '../QLSP.php';
    
    // SỬA: Gọi hàm get_all_products()
    $products = get_all_products();
?>

<div class="d-flex justify-content-between align-items-center">
    <h1 class="h2">Quản lý Sản phẩm</h1>
    <a href="product_edit.php?action=add" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm sản phẩm mới
    </a>
</div>
<div class="table-responsive mt-3">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark"> 
            <tr>
                <th scope="col" style="width: 5%;">ID</th>
                <th scope="col" style="width: 10%;">Hình ảnh</th>
                <th scope="col">Tên sản phẩm</th>
                <th scope="col" style="width: 15%;">Loại đàn</th>
                <th scope="col" style="width: 15%;">Giá</th>
                <th scope="col" class="text-center" style="width: 15%;">Hành động</th>
            </tr>
        </thead>
    
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có sản phẩm nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td class="fw-bold align-middle"><?php echo $product['id']; ?></td>
                        <td>
                            <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td class="align-middle"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="align-middle"><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                        <td class="align-middle"><?php echo format_price($product['price']); ?></td>
                        <td class="text-center align-middle action-buttons">
                            <a href="product_edit.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            
                            <form action="product_handler.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
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