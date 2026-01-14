<?php
ob_start();

session_start();
include('../config.php');

ob_end_clean();

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Lấy tháng/năm từ form hoặc mặc định tháng hiện tại
$month = isset($_GET['month']) ? mysqli_real_escape_string($con, $_GET['month']) : date('Y-m');
$month_arr = explode('-', $month);
$year = $month_arr[0];
$month_num = $month_arr[1];

// Thống kê doanh thu theo ngày trong tháng
$daily_revenue = [];
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);

for($i = 1; $i <= $days_in_month; $i++) {
    $date = $year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $revenue = mysqli_fetch_array(mysqli_query($con, "SELECT COALESCE(SUM(total_amount), 0) as total FROM tbl_bookings WHERE DATE(booking_date) = '$date' AND status='confirmed'"))['total'];
    $daily_revenue[] = ['date' => $i, 'revenue' => $revenue];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo & Thống kê - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-chart-bar"></i> Báo Cáo & Thống Kê
        </h2>

        <!-- Chọn tháng -->
        <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <form method="GET" class="form-inline">
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Chọn tháng:</label>
                    <input type="month" name="month" class="form-control" value="<?php echo $month; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Xem báo cáo
                </button>
                <a href="reports.php" class="btn btn-default">Tháng hiện tại</a>
            </form>
        </div>

        <!-- Tổng quan tháng -->
        <div class="row" style="margin-bottom: 30px;">
            <?php
            $month_bookings = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE MONTH(booking_date) = '$month_num' AND YEAR(booking_date) = '$year'"))['total'];
            $month_revenue = mysqli_fetch_array(mysqli_query($con, "SELECT COALESCE(SUM(total_amount), 0) as total FROM tbl_bookings WHERE MONTH(booking_date) = '$month_num' AND YEAR(booking_date) = '$year' AND status='confirmed'"))['total'];
            $month_tickets = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings b WHERE MONTH(b.booking_date) = '$month_num' AND YEAR(b.booking_date) = '$year' AND status='confirmed'"))['total'];
            $avg_ticket_price = $month_tickets > 0 ? $month_revenue / $month_tickets : 0;
            ?>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #2196F3, #1976D2); padding: 25px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-calendar-alt" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 36px;"><?php echo $month_bookings; ?></h3>
                    <p style="color: #fff; margin: 0;">Đơn đặt vé</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #4CAF50, #388E3C); padding: 25px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-coins" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 28px;"><?php echo number_format($month_revenue/1000000, 1); ?>M</h3>
                    <p style="color: #fff; margin: 0;">Doanh thu</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #FF9800, #F57C00); padding: 25px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-ticket-alt" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 36px;"><?php echo $month_tickets; ?></h3>
                    <p style="color: #fff; margin: 0;">Vé đã bán</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); padding: 25px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-money-bill-wave" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 28px;"><?php echo number_format($avg_ticket_price, 0); ?>đ</h3>
                    <p style="color: #fff; margin: 0;">Giá vé TB</p>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu theo ngày -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h4 style="color: #fff; margin-bottom: 20px;">
                <i class="fas fa-chart-line"></i> Doanh Thu Theo Ngày
            </h4>
            <canvas id="revenueChart" height="80"></canvas>
        </div>

        <div class="row">
            <!-- Top phim -->
            <div class="col-md-6">
                <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                    <h4 style="color: #fff; margin-bottom: 20px;">
                        <i class="fas fa-fire"></i> Top 10 Phim Bán Chạy
                    </h4>
                    <table class="table" style="color: #e0e0e0;">
                        <thead style="background: #2a2a2a;">
                            <tr>
                                <th>#</th>
                                <th>Phim</th>
                                <th>Vé bán</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $top_movies = mysqli_query($con, "
                                SELECT m.movie_name, COUNT(b.booking_id) as tickets, SUM(b.total_amount) as revenue
                                FROM tbl_bookings b
                                JOIN tbl_screenings s ON b.screening_id = s.screening_id
                                JOIN tbl_movie m ON s.movie_id = m.movie_id
                                WHERE MONTH(b.booking_date) = '$month_num' AND YEAR(b.booking_date) = '$year' AND b.status='confirmed'
                                GROUP BY m.movie_id
                                ORDER BY revenue DESC
                                LIMIT 10
                            ");
                            
                            $rank = 1;
                            while($row = mysqli_fetch_array($top_movies)) {
                            ?>
                            <tr>
                                <td><strong><?php echo $rank++; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                                <td><span class="label label-info"><?php echo $row['tickets']; ?></span></td>
                                <td><strong style="color: #4CAF50;"><?php echo number_format($row['revenue'], 0, ',', '.'); ?>đ</strong></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top rạp -->
            <div class="col-md-6">
                <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                    <h4 style="color: #fff; margin-bottom: 20px;">
                        <i class="fas fa-building"></i> Top Rạp Chiếu
                    </h4>
                    <table class="table" style="color: #e0e0e0;">
                        <thead style="background: #2a2a2a;">
                            <tr>
                                <th>#</th>
                                <th>Rạp</th>
                                <th>Vé bán</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $top_theatres = mysqli_query($con, "
                                SELECT t.name, COUNT(b.booking_id) as tickets, SUM(b.total_amount) as revenue
                                FROM tbl_bookings b
                                JOIN tbl_screenings s ON b.screening_id = s.screening_id
                                JOIN tbl_theatre t ON s.theatre_id = t.id
                                WHERE MONTH(b.booking_date) = '$month_num' AND YEAR(b.booking_date) = '$year' AND b.status='confirmed'
                                GROUP BY t.id
                                ORDER BY revenue DESC
                            ");
                            
                            $rank = 1;
                            while($row = mysqli_fetch_array($top_theatres)) {
                            ?>
                            <tr>
                                <td><strong><?php echo $rank++; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><span class="label label-warning"><?php echo $row['tickets']; ?></span></td>
                                <td><strong style="color: #FF9800;"><?php echo number_format($row['revenue'], 0, ',', '.'); ?>đ</strong></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- So sánh với tháng trước -->
        <?php
        $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
        $prev_month_arr = explode('-', $prev_month);
        $prev_year = $prev_month_arr[0];
        $prev_month_num = $prev_month_arr[1];
        
        $prev_revenue = mysqli_fetch_array(mysqli_query($con, "SELECT COALESCE(SUM(total_amount), 0) as total FROM tbl_bookings WHERE MONTH(booking_date) = '$prev_month_num' AND YEAR(booking_date) = '$prev_year' AND status='confirmed'"))['total'];
        $prev_tickets = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE MONTH(booking_date) = '$prev_month_num' AND YEAR(booking_date) = '$prev_year' AND status='confirmed'"))['total'];
        
        $revenue_change = $prev_revenue > 0 ? (($month_revenue - $prev_revenue) / $prev_revenue * 100) : 0;
        $tickets_change = $prev_tickets > 0 ? (($month_tickets - $prev_tickets) / $prev_tickets * 100) : 0;
        ?>
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">
                <i class="fas fa-exchange-alt"></i> So Sánh Với Tháng Trước
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <div style="background: #2a2a2a; padding: 20px; border-radius: 8px;">
                        <h5 style="color: #aaa; margin-top: 0;">Doanh thu</h5>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 24px; font-weight: bold; color: #4CAF50; margin: 0;">
                                    <?php echo number_format($month_revenue, 0, ',', '.'); ?>đ
                                </p>
                                <p style="color: #999; font-size: 14px; margin: 5px 0 0 0;">
                                    Tháng trước: <?php echo number_format($prev_revenue, 0, ',', '.'); ?>đ
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 28px; color: <?php echo $revenue_change >= 0 ? '#4CAF50' : '#f44336'; ?>">
                                    <?php echo $revenue_change >= 0 ? '↑' : '↓'; ?> <?php echo abs(round($revenue_change, 1)); ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: #2a2a2a; padding: 20px; border-radius: 8px;">
                        <h5 style="color: #aaa; margin-top: 0;">Vé bán ra</h5>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 24px; font-weight: bold; color: #2196F3; margin: 0;">
                                    <?php echo $month_tickets; ?> vé
                                </p>
                                <p style="color: #999; font-size: 14px; margin: 5px 0 0 0;">
                                    Tháng trước: <?php echo $prev_tickets; ?> vé
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 28px; color: <?php echo $tickets_change >= 0 ? '#4CAF50' : '#f44336'; ?>">
                                    <?php echo $tickets_change >= 0 ? '↑' : '↓'; ?> <?php echo abs(round($tickets_change, 1)); ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        // Biểu đồ doanh thu
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?php echo json_encode($daily_revenue); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => 'Ngày ' + item.date),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData.map(item => item.revenue),
                    borderColor: '#e50914',
                    backgroundColor: 'rgba(229, 9, 20, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#e0e0e0'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#aaa',
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        },
                        grid: {
                            color: '#333'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#aaa'
                        },
                        grid: {
                            color: '#333'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
