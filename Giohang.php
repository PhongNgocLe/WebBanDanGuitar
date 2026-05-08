<?php
    $currentPage = 'Giohang';
    include 'header.php';
    include_once 'QLSP.php';
    
    $cart = $_SESSION['cart'] ?? [];
    $total_price = 0;
?>

<div class="container my-5">
    <div class="content-head text-center mb-4">
        <h2 style="color: rgb(173, 157, 78);"><i class="fa-solid fa-bag-shopping"></i> Giỏ Hàng Của Bạn</h2>
    </div>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5">
            <p class="lead">Chưa có sản phẩm nào trong giỏ hàng của bạn.</p>
            <a href="index.php" class="btn auth-button btn-lg mt-3">Quay lại mua sắm</a>
        </div>

    <?php else: ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col" colspan="2" class="ps-3">Sản phẩm</th>
                        <th scope="col">Đơn giá</th>
                        <th scope="col" class="text-center">Số lượng</th>
                        <th scope="col" class="text-end">Thành tiền</th>
                        <th scope="col" class="text-center">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <?php 
                            $subtotal = $item['price'] * $item['quantity']; 
                            $total_price += $subtotal; 
                        ?>
                        <tr>
                            <td style="width: 100px;">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="img-fluid rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            </td>
                            <td>
                                <a href="product-detail.php?id=<?php echo $item['id']; ?>" class="text-dark text-decoration-none fw-bold">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </td>
                            <td><?php echo format_price($item['price']); ?></td>
                            
                            <td class="text-center" style="width: 150px;">
                                <div class="input-group justify-content-center" style="max-width: 140px;">
                                    <a href="cart-handler.php?action=decrease&id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-minus"></i>
                                    </a>
                                    <input type="text" class="form-control text-center px-0" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           readonly 
                                           style="max-width: 40px; background-color: #fff;">
                                    <a href="cart-handler.php?action=increase&id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="text-end">
                                <strong><?php echo format_price($subtotal); ?></strong>
                            </td>
                            <td class="text-center">
                                <a href="cart-handler.php?action=remove&id=<?php echo $item['id']; ?>" 
                                   class="btn btn-outline-danger btn-sm" 
                                   title="Xóa sản phẩm">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div> 
        
        <div class="row mt-4 justify-content-between align-items-center">
             <div class="col-md-6 text-start mb-3 mb-md-0">
                 <a href="cart-handler.php?action=clear" class="btn btn-outline-danger">
                    <i class="fa-solid fa-trash"></i> Xóa tất cả giỏ hàng
                </a>
                <a href="index.php" class="btn btn-outline-secondary ms-2">
                    <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <h4>
                            Tổng tiền:
                            <strong class="ms-3" style="color: rgb(173, 157, 78);">
                                <?php echo format_price($total_price); ?>
                            </strong>
                        </h4>
                        <a href="Checkout.php" class="btn auth-button w-100 mt-3">
                            Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
    include 'footer.php';
?>