<?php
// Luôn bắt đầu session ở file header để mọi trang đều có thể truy cập
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Cần QLSP.php để lấy danh sách categories
include_once __DIR__ . '/QLSP.php'; 

// === THÊM CODE ĐẾM GIỎ HÀNG ===
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}
// ===============================

// === LẤY DANH SÁCH CATEGORIES CHO MENU ===
$menu_categories = get_all_categories();

// === XÁC ĐỊNH TRANG HIỆN TẠI ===
$currentPage = $currentPage ?? ''; // Dùng biến này để xác định active menu
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="P-Guitar - Shop đàn guitar và phụ kiện chất lượng với dịch vụ tận tâm, hỗ trợ 24/7 và giao hàng nhanh chóng.">
    <link rel="icon" type="image/png" href="/images/image-removebg-preview.png">
    <title>P-Guitar</title>
    
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

    <style>
        .profile-wrapper {
            position: relative;
            display: inline-block;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-left: 15px;
        }
        .avatar-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #b5923d;
            color: white;
            font-size: 20px;
            font-weight: bold;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .avatar-btn:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.65);
        }
        .profile-dropdown {
            display: none; 
            position: absolute;
            top: 55px;
            right: 0;
            width: 320px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            padding: 20px 0 10px;
            z-index: 1000;
            border: 1px solid rgba(181,146,61,0.14);
        }
        .user-header {
            display: flex;
            align-items: center;
            padding: 0 24px 16px;
        }
        .avatar-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #b5923d;
            color: white;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
        }
        .user-details {
            display: flex;
            flex-direction: column;
        }
        .user-name {
            font-weight: 600;
            font-size: 17px;
            color: #202124;
        }
        .user-role {
            font-size: 13px;
            color: #5f6368;
            margin-top: 4px;
            background: #f8f4ed;
            padding: 4px 10px;
            border-radius: 10px;
            display: inline-block;
            width: fit-content;
        }
        .divider {
            height: 1px;
            background-color: #e8eaed;
            margin: 8px 0;
        }
        .menu-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .menu-links li a {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: #3c4043;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .menu-links li a i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
            color: #5f6368;
        }
        .menu-links li a:hover {
            background-color: #f8f9fa;
        }
        .logout-text {
            color: #d93025 !important;
        }
        .logout-text i {
            color: #d93025 !important;
        }
    </style>
</head>
<body>
    <div id="Container" class="container-lg">
        <header id="header">
            <div class="header-top">
                <a href="index.php" class="logo">
                    <img class="img" src="/images/image-removebg-preview.png" alt="P-Guitar Logo">
                </a>
                
                <div class="head-mid">
                    <form class="search-bar" action="search.php" method="GET">
                        <input class="tim" type="text" name="query" placeholder="Tìm kiếm sản phẩm...">
                        <button class="aicon" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    
                    <img class="hotline-img" src="./images/hostline.png" alt="Hotline">
                    
                    <div class="d-flex align-items-center">
                        <?php if (isset($_SESSION['username'])): ?>
                            <?php 
                                // Lấy chữ cái đầu tiên của username làm Avatar
                                $username = htmlspecialchars($_SESSION['username']);
                                $avatarLetter = strtoupper(substr($username, 0, 1));
                                $isAdmin = (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin');
                            ?>
                            
                            <div class="profile-wrapper">
                                <button type="button" class="avatar-btn" aria-expanded="false" onclick="toggleProfileMenu(event)">
                                    <?php echo $avatarLetter; ?>
                                </button>

                                <div class="profile-dropdown" id="profileDropdown">
                                    <div class="user-header">
                                        <div class="avatar-large"><?php echo $avatarLetter; ?></div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo $username; ?></span>
                                            <span class="user-role"><?php echo $isAdmin ? 'Quản trị viên' : 'Thành viên P-Guitar'; ?></span>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <ul class="menu-links">
                                        <?php if ($isAdmin): ?>
                                            <li><a href="admin/index.php"><i class="fa-solid fa-gauge-high"></i> Bảng quản trị</a></li>
                                        <?php endif; ?>
                                        <li><a href="profile.php"><i class="fa-solid fa-user-pen"></i> Thông tin cá nhân</a></li>
                                        <li><a href="order_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử đơn hàng</a></li>
                                    </ul>
                                        <div class="divider"></div>
                                    <ul class="menu-links">
                                        <li><a href="logout.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php else: ?>
                            <a href="Login.php" class="login-link"><i class="fa-regular fa-user"></i> Đăng Nhập</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>    
           
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
            
                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($currentPage == 'Index') ? 'active' : ''; ?>" href="index.php">TRANG CHỦ</a>
                            </li>
                            
                            <?php foreach ($menu_categories as $cat): ?>
                                <?php
                                    $cat_link = 'products_by_cat.php?slug=' . htmlspecialchars($cat['slug']);
                                    $is_active = (isset($_GET['slug']) && $_GET['slug'] == $cat['slug']);
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $is_active ? 'active' : ''; ?>" 
                                       href="<?php echo $cat_link; ?>">
                                        <?php echo htmlspecialchars(mb_strtoupper($cat['name'], 'UTF-8')); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($currentPage == 'Blog') ? 'active' : ''; ?>" href="Blog.php">BLOG</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?php echo ($currentPage == 'Giohang') ? 'active' : ''; ?>" href="Giohang.php">
                                    <i class="fa-solid fa-bag-shopping"></i> GIỎ HÀNG
                                    <?php if ($cart_count > 0): ?>
                                        <span class="badge bg-danger ms-1 rounded-pill"><?php echo $cart_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>                
        </header>
        
        <main id="content" class="container-fluid">
           <div class="divider-heading"></div>

<script>
    function toggleProfileMenu(event) {
        event.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
        const dropdown = document.getElementById("profileDropdown");
        const avatarBtn = event.currentTarget;
        const isOpen = dropdown.style.display === "block";
        dropdown.style.display = isOpen ? "none" : "block";
        avatarBtn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    }

    // Đóng menu khi click ra ngoài
    window.addEventListener('click', function(event) {
        const dropdown = document.getElementById("profileDropdown");
        const avatarBtn = document.querySelector('.avatar-btn');
        if (dropdown && event.target !== dropdown && event.target !== avatarBtn && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
            if (avatarBtn) {
                avatarBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>