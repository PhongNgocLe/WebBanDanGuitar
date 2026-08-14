<?php
    include 'admin_header.php';
    include 'auth_check.php';
    include_once '../QLSP.php';
    global $conn;

    // 1. Thống kê KPI tổng quan
    // Tổng số sản phẩm
    $res_products = $conn->query("SELECT COUNT(*) as total FROM products");
    $total_products = $res_products ? $res_products->fetch_assoc()['total'] : 0;

    // Tổng số đơn hàng & Tổng doanh thu (không tính đơn hủy)
    $res_orders = $conn->query("SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status NOT LIKE '%hủy%' THEN total_money ELSE 0 END) as total_revenue,
        SUM(CASE WHEN status LIKE '%xử lý%' OR status LIKE '%chờ%' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN DATE(created_at) = CURDATE() AND status NOT LIKE '%hủy%' THEN total_money ELSE 0 END) as today_revenue,
        SUM(CASE WHEN DATE(created_at) = CURDATE() AND (status LIKE '%hoàn thành%' OR status LIKE '%thành công%' OR status LIKE '%đã giao%') THEN 1 ELSE 0 END) as today_completed_orders
    FROM orders");
    $order_stats = $res_orders ? $res_orders->fetch_assoc() : ['total_orders' => 0, 'total_revenue' => 0, 'pending_orders' => 0, 'today_revenue' => 0, 'today_completed_orders' => 0];

    // Tổng số người dùng
    $res_users = $conn->query("SELECT COUNT(*) as total FROM users");
    $total_users = $res_users ? $res_users->fetch_assoc()['total'] : 0;

    // 2. Lấy 5 đơn hàng mới nhất
    $res_recent_orders = $conn->query("SELECT id, fullname, phone, total_money, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5");
    $recent_orders = $res_recent_orders ? $res_recent_orders->fetch_all(MYSQLI_ASSOC) : [];

    // 3. Lấy 4 sản phẩm mới nhất
    $res_recent_products = $conn->query("SELECT id, name, price, category, image FROM products ORDER BY id DESC LIMIT 4");
    $recent_products = $res_recent_products ? $res_recent_products->fetch_all(MYSQLI_ASSOC) : [];

    // Hàm badge trạng thái chuẩn
    function render_status_badge($status) {
        $s = mb_strtolower($status, 'UTF-8');
        if (strpos($s, 'xử lý') !== false || strpos($s, 'chờ') !== false) return '<span class="badge bg-warning text-dark">Đang xử lý</span>';
        if (strpos($s, 'đang giao') !== false) return '<span class="badge bg-primary">Đang giao hàng</span>';
        if (strpos($s, 'hoàn thành') !== false || strpos($s, 'thành công') !== false || strpos($s, 'đã giao') !== false) return '<span class="badge bg-success">Đã giao</span>';
        if (strpos($s, 'hủy') !== false) return '<span class="badge bg-danger">Đã hủy</span>';
        return '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
    }
?>

<div class="main-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h2 fw-bold text-dark mb-1">Dashboard Quản trị</h1>
        <p class="text-muted mb-0">Chào mừng trở lại, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong>!</p>
    </div>
    <span class="badge bg-light text-dark border p-2"><i class="fa fa-calendar me-1"></i> <?php echo date('d/m/Y'); ?></span>
</div>

<!-- HÀNG 4 THẺ THỐNG KÊ (KPIs) -->
<div class="row g-3 mb-4">
    <!-- Thẻ 1: Doanh thu tổng -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white h-100 rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.8rem;">Tổng doanh thu</h6>
                    <h4 class="fw-bold mb-0"><?php echo number_format($order_stats['total_revenue'] ?? 0, 0, ',', '.'); ?> đ</h4>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa fa-money-bill-wave fa-lg"></i>
                </div>
            </div>
            <a href="orders.php" class="card-footer bg-black bg-opacity-10 text-white text-decoration-none small d-flex justify-content-between align-items-center border-0 py-2">
                <span>Xem chi tiết</span>
                <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Thẻ 2: Tổng đơn hàng -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-success text-white h-100 rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.8rem;">Tổng đơn hàng</h6>
                    <h4 class="fw-bold mb-0"><?php echo number_format($order_stats['total_orders']); ?></h4>
                    <small class="text-white-50"><?php echo (int)$order_stats['pending_orders']; ?> đơn chờ duyệt</small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa fa-shopping-bag fa-lg"></i>
                </div>
            </div>
            <a href="orders.php" class="card-footer bg-black bg-opacity-10 text-white text-decoration-none small d-flex justify-content-between align-items-center border-0 py-2">
                <span>Quản lý đơn</span>
                <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Thẻ 3: Tổng sản phẩm -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-info text-white h-100 rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.8rem;">Sản phẩm trong kho</h6>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_products); ?></h4>
                    <small class="text-white-50">Đàn & Phụ kiện</small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa fa-guitar fa-lg"></i>
                </div>
            </div>
            <a href="products.php" class="card-footer bg-black bg-opacity-10 text-white text-decoration-none small d-flex justify-content-between align-items-center border-0 py-2">
                <span>Danh mục kho</span>
                <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Thẻ 4: Người dùng -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-warning text-dark h-100 rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-black-50 text-uppercase fw-semibold mb-1" style="font-size: 0.8rem;">Tài khoản khách hàng</h6>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_users); ?></h4>
                    <small class="text-black-50">Đang hoạt động</small>
                </div>
                <div class="bg-black bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa fa-users fa-lg"></i>
                </div>
            </div>
            <a href="users.php" class="card-footer bg-black bg-opacity-10 text-dark text-decoration-none small d-flex justify-content-between align-items-center border-0 py-2">
                <span>Quản lý User</span>
                <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- HÀNG BIỂU ĐỒ & THỐNG KÊ NHANH -->
<div class="row g-3 mb-4">
    <!-- Biểu đồ Doanh thu (Line Chart) -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa fa-chart-line me-2"></i>Biểu đồ Doanh thu</h6>
                <select id="revenueRange" class="form-select form-select-sm w-auto">
                    <option value="week" selected>7 ngày qua</option>
                    <option value="month">Năm nay (12 tháng)</option>
                </select>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tỷ lệ Đơn hàng & Doanh thu ngày -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 mb-3 border-start border-success border-4">
            <div class="card-body py-3">
                <small class="text-muted fw-semibold text-uppercase">Doanh thu hôm nay</small>
                <h4 class="fw-bold text-success mb-1"><?php echo number_format($order_stats['today_revenue'] ?? 0, 0, ',', '.'); ?> đ</h4>
                <small class="text-muted">Đơn hoàn thành: <strong><?php echo (int)($order_stats['today_completed_orders'] ?? 0); ?></strong></small>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-chart-pie me-2"></i>Tỷ lệ trạng thái đơn</h6>
            </div>
            <div class="card-body pt-0">
                <div style="position: relative; height: 180px; width: 100%;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HÀNG 2 BẢNG THÔNG TIN TÓM TẮT DƯỚI CÙNG -->
<div class="row g-3">
    <!-- Bảng 1: Đơn hàng mới nhất -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fa fa-clock me-2 text-primary"></i>Đơn hàng mới nhất</h6>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive px-3 pb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $ro): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $ro['id']; ?></td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($ro['fullname']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($ro['phone']); ?></small>
                                </td>
                                <td class="text-danger fw-bold"><?php echo number_format($ro['total_money'], 0, ',', '.'); ?> đ</td>
                                <td><?php echo render_status_badge($ro['status']); ?></td>
                                <td class="text-center">
                                    <a href="order_details.php?id=<?php echo $ro['id']; ?>" class="btn btn-sm btn-light border" title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bảng 2: Sản phẩm mới thêm gần đây -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fa fa-guitar me-2 text-info"></i>Sản phẩm mới thêm</h6>
                <a href="products.php" class="btn btn-sm btn-outline-info">Quản lý kho</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($recent_products)): ?>
                    <p class="text-center text-muted py-4">Chưa có sản phẩm nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recent_products as $rp): ?>
                        <li class="list-group-item d-flex align-items-center px-0 py-2 border-0">
                            <img src="../<?php echo htmlspecialchars($rp['image'] ?: 'images/default-guitar.jpg'); ?>" 
                                 class="rounded border me-3" style="width: 48px; height: 48px; object-fit: cover;">
                            <div class="flex-grow-1 overflow-hidden me-2">
                                <h6 class="mb-0 text-truncate" style="font-size: 0.9rem;" title="<?php echo htmlspecialchars($rp['name']); ?>">
                                    <?php echo htmlspecialchars($rp['name']); ?>
                                </h6>
                                <small class="text-muted"><?php echo htmlspecialchars($rp['category'] ?? 'Guitar'); ?> - </small>
                                <span class="text-danger fw-semibold small"><?php echo number_format($rp['price'], 0, ',', '.'); ?> đ</span>
                            </div>
                            <a href="product_edit.php?action=edit&id=<?php echo $rp['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-edit"></i>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT VẼ CHART VÀ FIX LỖI TRỤC Y -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart = null;
let statusChart = null;

// Hàm định dạng số tiền rút gọn cho trục Y
function formatCurrencyAxis(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(1).replace('.0', '') + ' tr';
    if (value >= 1000) return (value / 1000).toFixed(0) + ' k';
    return value + ' đ';
}

// 1. Tải dữ liệu Doanh thu từ stats_api.php
function loadRevenueChart(range = 'week') {
    const action = (range === 'month') ? 'revenue_month' : 'revenue_week';
    fetch(`stats_api.php?action=${action}`)
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            if (revenueChart) revenueChart.destroy();

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data.values,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' ' + Number(ctx.parsed.y).toLocaleString('vi-VN') + ' đ'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5000000,
                            ticks: {
                                callback: value => formatCurrencyAxis(value)
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
}

// 2. Tải dữ liệu Tròn Trạng thái Đơn hàng
function loadStatusChart() {
    fetch('stats_api.php?action=order_status')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('statusChart').getContext('2d');
            
            if (statusChart) statusChart.destroy();

            // Bảng màu trạng thái
            const colorMap = {
                'Đang xử lý': '#ffc107',
                'Chờ duyệt': '#ffc107',
                'Đang giao hàng': '#0d6efd',
                'Đã giao': '#198754',
                'Hoàn thành': '#198754',
                'Đã hủy': '#dc3545'
            };
            const bgColors = data.labels.map(label => colorMap[label] || '#6c757d');

            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
}

document.addEventListener('DOMContentLoaded', () => {
    loadRevenueChart('week');
    loadStatusChart();

    document.getElementById('revenueRange').addEventListener('change', function() {
        loadRevenueChart(this.value);
    });
});
</script>

<?php include 'admin_footer.php'; ?>