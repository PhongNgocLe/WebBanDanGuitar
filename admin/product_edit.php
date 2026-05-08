<?php
    include 'admin_header.php';
    include_once '../QLSP.php'; 

    $form_action = 'create';
    $product = ['id' => '', 'name' => '', 'price' => '', 'category' => '', 'description'=>'', 'image' => ''];
    $extra_images = [];

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $found_product = find_product_by_id($_GET['id']);
        if ($found_product) {
            $product = $found_product;
            $form_action = 'update';
            $extra_images = get_product_images($product['id']);
        }
    }
    $categories = ['Classic', 'Acoustic', 'Electric', 'Phụ kiện'];
?>

<div class="main-header mb-4"><h1 class="h2"><?php echo ($form_action == 'update' ? "Sửa" : "Thêm"); ?> Sản Phẩm</h1></div>

<div class="row"><div class="col-lg-8">
    <form action="product_handler.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $form_action; ?>">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">

        <div class="mb-3">
            <label class="form-label fw-bold">Tên sản phẩm</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Loại sản phẩm</label>
            <select class="form-select" name="category" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>" <?php echo ($product['category'] == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Giá (VNĐ)</label>
            <input type="number" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Chọn hình ảnh (Có thể chọn nhiều)</label>
            <input type="file" class="form-control" name="image_upload[]" multiple>
            <div class="form-text">Ảnh đầu tiên sẽ tự động làm ảnh đại diện nếu chưa có.</div>
        </div>

        <?php if(!empty($extra_images)): ?>
            <div class="mb-3 p-3 bg-light border rounded">
                <h6>Bộ sưu tập ảnh hiện tại:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach($extra_images as $img): ?>
                        <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" class="border rounded" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Lưu Sản Phẩm</button>
        <a href="products.php" class="btn btn-secondary btn-lg">Hủy</a>
    </form>
</div></div>
<?php include 'admin_footer.php'; ?>