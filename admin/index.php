<?php
    include 'admin_header.php'; // Đã có $conn
    include_once '../QLSP.php';
    
    // Đếm sản phẩm
    $all_products = get_all_products();
    $product_count = count($all_products);
    
    // Đếm số lượng đơn hàng thực tế
    $order_query = $conn->query("SELECT COUNT(id) as total FROM orders");
    $order_data = $order_query->fetch_assoc();
    $order_count = $order_data['total'];
    
    // Đếm người dùng
    $user_result = $conn->query("SELECT COUNT(id) as count FROM users");
    $user_count = $user_result->fetch_assoc()['count'];
    
    // Thống kê doanh thu hôm nay (Chỉ tính đơn 'Đã giao')
    $today = date('Y-m-d');
    $sql_today = "SELECT COUNT(*) as count, SUM(total_money) as total 
                  FROM orders WHERE DATE(created_at) = '$today' AND status = 'Đã giao'";
    $res_today = $conn->query($sql_today)->fetch_assoc();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-header mb-4">
    <h1 class="h2">Dashboard</h1>
</div>

<p class="lead">Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>

<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card text-white bg-primary h-100 card-admin shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <i class="fa fa-guitar fa-3x opacity-75"></i>
                    <div class="text-end">
                        <div class="fs-1 fw-bold"><?php echo $product_count; ?></div>
                        <h5 class="card-title mb-0">Sản phẩm</h5>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pb-3">
                <a href="products.php" class="text-white d-block text-decoration-none">
                    Xem chi tiết <i class="fa fa-arrow-circle-right float-end mt-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card text-white bg-success h-100 card-admin shadow-sm">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <i class="fa fa-file-invoice-dollar fa-3x opacity-75"></i>
                    <div class="text-end">
                        <div class="fs-1 fw-bold"><?php echo $order_count; ?></div>
                        <h5 class="card-title mb-0">Đơn hàng</h5>
                    </div>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 pb-3">
                <a href="orders.php" class="text-white d-block text-decoration-none">
                    Xem chi tiết <i class="fa fa-arrow-circle-right float-end mt-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card text-white bg-warning h-100 card-admin shadow-sm">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <i class="fa fa-users fa-3x opacity-75"></i>
                    <div class="text-end">
                        <div class="fs-1 fw-bold"><?php echo $user_count; ?></div>
                        <h5 class="card-title mb-0">Người dùng</h5>
                    </div>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 pb-3">
                <a href="users.php" class="text-white d-block text-decoration-none">
                    Xem chi tiết <i class="fa fa-arrow-circle-right float-end mt-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary fw-bold"><i class="fa fa-chart-line me-2"></i>Biểu đồ Doanh thu</h5>
                <select id="timeFilter" class="form-select form-select-sm w-auto shadow-none" onchange="updateRevenueChart()">
                    <option value="week">7 ngày qua</option>
                    <option value="month">Theo tháng (Năm nay)</option>
                </select>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm mb-4 border-0 border-start border-success border-5">
            <div class="card-body py-4">
                <h6 class="text-muted text-uppercase fw-bold mb-2">Doanh thu hôm nay</h6>
                <h3 class="fw-bold text-success mb-1">
                    <?php echo number_format($res_today['total'] ?? 0, 0, ',', '.'); ?> đ
                </h3>
                <p class="mb-0 text-muted">Số đơn hoàn thành: <strong class="text-dark"><?php echo $res_today['count']; ?></strong></p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-secondary fw-bold"><i class="fa fa-chart-pie me-2"></i>Tỷ lệ đơn hàng</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 250px;">
                <canvas id="statusChart" style="max-height: 250px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Cấu hình Biểu đồ Doanh thu (Bar Chart)
    let revenueChartInstance = null;

    async function updateRevenueChart() {
        const type = document.getElementById('timeFilter').value;
        const action = type === 'week' ? 'revenue_week' : 'revenue_month';
        
        try {
            const response = await fetch(`stats_api.php?action=${action}`);
            if (!response.ok) return;

            const data = await response.json();
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            revenueChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data.values,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error("Lỗi vẽ biểu đồ doanh thu:", error);
        }
    }

    // 2. Cấu hình Biểu đồ Trạng thái (Doughnut Chart)
    async function loadStatusChart() {
        try {
            const response = await fetch('stats_api.php?action=order_status');
            const data = await response.json();

            const ctx = document.getElementById('statusChart').getContext('2d');
            const backgroundColors = [
                '#198754', // Success (Thành công/Đã giao)
                '#dc3545', // Danger (Hủy)
                '#ffc107', // Warning (Đang xử lý)
                '#0d6efd', // Primary (Đang giao)
                '#6c757d'  // Secondary (Khác)
            ];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 15, usePointStyle: true }
                        }
                    }
                }
            });
        } catch (error) {
            console.error("Lỗi vẽ biểu đồ trạng thái:", error);
        }
    }

    // Chạy các hàm khi tải trang xong
    document.addEventListener('DOMContentLoaded', function() {
        updateRevenueChart();
        loadStatusChart();
    });
</script>

<?php
    include 'admin_footer.php';
?>