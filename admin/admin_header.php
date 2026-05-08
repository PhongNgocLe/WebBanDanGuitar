<?php
// Bảo vệ trang này
include 'auth_check.php';
// Xác định trang hiện tại (để làm active menu)
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/images/image-removebg-preview.png">
    <title>Admin P-Guitar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
            <div class="position-sticky pt-3">
                <h4 class="px-3 text-white mb-3">P-Guitar Admin</h4>
                
                <h6 class="sidebar-heading px-3 mt-4 mb-1">
                    <span>Quản lý Nội dung</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">
                            <i class="fa fa-tachometer-alt fa-fw"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'products.php' || $current_page == 'product_edit.php') ? 'active' : ''; ?>" href="products.php">
                            <i class="fa fa-guitar fa-fw"></i> Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link <?php echo ($current_page == 'categories.php' || $current_page == 'category_edit.php') ? 'active' : ''; ?>" href="categories.php">
                            <i class="fa fa-folder fa-fw"></i> Danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
                            <i class="fa fa-file-invoice-dollar fa-fw"></i> Đơn hàng
                        </a>
                    </li>
                    
                </ul>
                
                <h6 class="sidebar-heading px-3 mt-4 mb-1">
                    <span>Quản trị Hệ thống</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'users.php' || $current_page == 'user_edit.php') ? 'active' : ''; ?>" href="users.php">
                            <i class="fa fa-users fa-fw"></i> Người dùng
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">
                           <i class="fa fa-globe fa-fw"></i> Xem Website
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fa fa-sign-out-alt fa-fw"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">