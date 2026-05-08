<?php
// stats_api.php
include 'auth_check.php'; 
global $conn; 

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- 1. Thống kê theo Tuần (7 ngày gần nhất) ---
if ($action == 'revenue_week') {
    $data = [];
    // Tạo mảng 7 ngày từ quá khứ đến hôm nay
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $data[$date] = 0;
    }

    // Query: Lấy đơn hàng KHÔNG bị hủy và nằm trong 7 ngày qua
    $sql = "SELECT DATE(created_at) as order_date, SUM(total_money) as total 
            FROM orders 
            WHERE status IN ('Đã giao', 'Đang giao hàng', 'Đang xử lý', 'Chờ duyệt')
            AND created_at >= DATE(NOW() - INTERVAL 6 DAY)
            GROUP BY DATE(created_at)";
            
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Ép kiểu float để đảm bảo json trả về số
            $data[$row['order_date']] = (float)$row['total'];
        }
    }

    echo json_encode([
        'labels' => array_keys($data),
        'values' => array_values($data)
    ]);
    exit;
}

// --- 2. Thống kê theo Tháng (Trong năm nay) ---
if ($action == 'revenue_month') {
    $data = [];
    for ($i = 1; $i <= 12; $i++) {
        $data["Tháng $i"] = 0;
    }

    $sql = "SELECT MONTH(created_at) as month_num, SUM(total_money) as total 
            FROM orders 
            WHERE status IN ('Đã giao', 'Đang giao hàng', 'Đang xử lý', 'Chờ duyệt')
            AND YEAR(created_at) = YEAR(NOW())
            GROUP BY MONTH(created_at)";

    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data["Tháng " . $row['month_num']] = (float)$row['total'];
        }
    }

    echo json_encode([
        'labels' => array_keys($data),
        'values' => array_values($data)
    ]);
    exit;
}

// --- 3. Thống kê trạng thái (Biểu đồ tròn) ---
if ($action == 'order_status') {
    $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $result = $conn->query($sql);
    
    $labels = [];
    $values = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['status'];
            $values[] = (int)$row['count'];
        }
    }
    
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    exit;
}
?>