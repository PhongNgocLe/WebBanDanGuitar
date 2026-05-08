<?php
    include 'admin_header.php';
    include_once '../QLSP.php';
    
    // Mặc định là form "Thêm mới"
    $form_action = 'create';
    $form_title = "Thêm Danh mục mới";
    $category = [
        'id' => '',
        'name' => '',
        'slug' => ''
    ];

    // Kiểm tra nếu là hành động "Sửa"
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $found_cat = find_category_by_id($id);
        
        if ($found_cat) {
            $category = $found_cat;
            $form_action = 'update';
            $form_title = "Sửa Danh mục";
        }
    }

    // Hàm tạo slug đơn giản (để tránh lỗi tiếng Việt trong URL)
    function create_slug($string) {
        $string = str_replace('đ', 'd', str_replace('Đ', 'D', $string));
        $string = preg_replace('/[^a-z0-9\s-]/u', '', mb_strtolower($string));
        $string = preg_replace('/[\s-]+/', '-', $string);
        return trim($string, '-');
    }
?>

<div class="main-header">
    <div>
        <h1 class="h2"><?php echo $form_title; ?></h1>
        <?php if($form_action == 'update'): ?>
            <h6 class="text-muted">ID: <?php echo htmlspecialchars($category['id']); ?></h6>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <form action="category_handler.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $form_action; ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Tên Danh mục</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($category['name']); ?>" 
                       oninput="document.getElementById('slug').value = create_slug(this.value)"
                       required>
            </div>
            
            <div class="mb-3">
                <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                <input type="text" class="form-control" id="slug" name="slug" 
                       value="<?php echo htmlspecialchars($category['slug']); ?>" required>
                <div class="form-text">Slug được tạo tự động từ Tên Danh mục.</div>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu Danh mục</button>
            <a href="categories.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<script>
    // Hàm JS để tạo slug (giống hàm PHP ở trên)
    function create_slug(str) {
        str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        str = str.toLowerCase();
        str = str.replace(/đ/g, 'd').replace(/Đ/g, 'D');
        str = str.replace(/[^a-z0-9\s-]/g, '').trim();
        str = str.replace(/[\s-]+/g, '-');
        return str;
    }
</script>

<?php
    include 'admin_footer.php';
?>