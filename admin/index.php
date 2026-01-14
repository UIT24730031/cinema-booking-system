<?php
ob_start();

session_start();
include('../config.php');

ob_end_clean();

// Kiểm tra đăng nhập admin
if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

$user_id = $_SESSION['user'];
$check_admin = mysqli_query($con, "SELECT user_type FROM tbl_login WHERE user_id='$user_id'");
$admin = mysqli_fetch_array($check_admin);

if($admin['user_type'] != 0) {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location='../index.php';</script>";
    exit;
}

// Thống kê
$total_movies = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_movie"))['total'];
$total_users = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_registration WHERE user_id != 1"))['total'];
$total_bookings = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings"))['total'];
$total_revenue = mysqli_fetch_array(mysqli_query($con, "SELECT SUM(total_amount) as total FROM tbl_bookings WHERE status='confirmed'"))['total'] ?? 0;
$today_bookings = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE DATE(booking_date) = CURDATE()"))['total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cinema Booking</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: #0a0a0a;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #1a1a1a 0%, #0a0a0a 100%);
            padding: 20px 0;
            border-right: 1px solid #333;
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            color: #e50914;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 15px 25px;
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(229, 9, 20, 0.1);
            color: #e50914;
            border-left: 3px solid #e50914;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #e50914;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 40px;
            color: #e50914;
            float: right;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #fff;
            margin: 10px 0;
        }
        .stat-label {
            color: #aaa;
            font-size: 14px;
        }
        .table-container {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .table {
            color: #e0e0e0;
        }
        .table thead {
            background: #2a2a2a;
        }
        .table tbody tr {
            border-bottom: 1px solid #333;
        }
        .table tbody tr:hover {
            background: #2a2a2a;
        }
        .btn-custom {
            background: #e50914;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: #b20710;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-film"></i> ADMIN PANEL
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_movies.php"><i class="fas fa-film"></i> Quản lý phim</a></li>
            <li><a href="manage_screenings.php"><i class="fas fa-calendar-alt"></i> Lịch chiếu</a></li>
            <li><a href="manage_bookings.php"><i class="fas fa-ticket-alt"></i> Đặt vé</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Người dùng</a></li>
            <li><a href="manage_theatres.php"><i class="fas fa-building"></i> Rạp chiếu</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Báo cáo</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </h2>

        <!-- Thống kê -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-film stat-icon"></i>
                    <div class="stat-label">Tổng phim</div>
                    <div class="stat-value"><?php echo $total_movies; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <i class="fas fa-users stat-icon" style="color: #4CAF50;"></i>
                    <div class="stat-label">Người dùng</div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <i class="fas fa-ticket-alt stat-icon" style="color: #2196F3;"></i>
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value"><?php echo $total_bookings; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #FF9800;">
                    <i class="fas fa-coins stat-icon" style="color: #FF9800;"></i>
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value" style="font-size: 24px;"><?php echo number_format($total_revenue/1000000, 1); ?>M</div>
                </div>
            </div>
        </div>

        <!-- Đặt vé hôm nay -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <h4 style="color: #fff; margin-bottom: 20px;">
                        <i class="fas fa-calendar-day"></i> Đặt vé hôm nay (<?php echo $today_bookings; ?> vé)
                    </h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã vé</th>
                                <th>Khách hàng</th>
                                <th>Phim</th>
                                <th>Rạp</th>
                                <th>Giờ chiếu</th>
                                <th>Ghế</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today_qry = mysqli_query($con, "
                                SELECT b.*, r.name, r.email, m.movie_name, t.name as theatre_name, s.show_time
                                FROM tbl_bookings b
                                JOIN tbl_registration r ON b.user_id = r.user_id
                                JOIN tbl_screenings s ON b.screening_id = s.screening_id
                                JOIN tbl_movie m ON s.movie_id = m.movie_id
                                JOIN tbl_theatre t ON s.theatre_id = t.id
                                WHERE DATE(b.booking_date) = CURDATE()
                                ORDER BY b.booking_date DESC
                                LIMIT 10
                            ");
                            
                            if(mysqli_num_rows($today_qry) > 0) {
                                while($row = mysqli_fetch_array($today_qry)) {
                                    $status_class = $row['status'] == 'confirmed' ? 'success' : 'danger';
                            ?>
                                <tr>
                                    <td>#<?php echo $row['booking_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['theatre_name']); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['show_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['seats']); ?></td>
                                    <td><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>đ</td>
                                    <td><span class="label label-<?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="8" style="text-align: center; color: #777;">Chưa có đặt vé nào hôm nay</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Phim phổ biến -->
        <div class="row" style="margin-top: 30px;">
            <div class="col-md-6">
                <div class="table-container">
                    <h4 style="color: #fff; margin-bottom: 20px;">
                        <i class="fas fa-fire"></i> Phim phổ biến nhất
                    </h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Số vé</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $popular_qry = mysqli_query($con, "
                                SELECT m.movie_name, COUNT(b.booking_id) as ticket_count, SUM(b.total_amount) as revenue
                                FROM tbl_bookings b
                                JOIN tbl_screenings s ON b.screening_id = s.screening_id
                                JOIN tbl_movie m ON s.movie_id = m.movie_id
                                WHERE b.status = 'confirmed'
                                GROUP BY m.movie_id
                                ORDER BY ticket_count DESC
                                LIMIT 5
                            ");
                            
                            while($row = mysqli_fetch_array($popular_qry)) {
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                                    <td><?php echo $row['ticket_count']; ?></td>
                                    <td><?php echo number_format($row['revenue'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="table-container">
                    <h4 style="color: #fff; margin-bottom: 20px;">
                        <i class="fas fa-crown"></i> Khách hàng thân thiết
                    </h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Số vé</th>
                                <th>Chi tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $customer_qry = mysqli_query($con, "
                                SELECT r.name, r.email, COUNT(b.booking_id) as ticket_count, SUM(b.total_amount) as total_spent
                                FROM tbl_bookings b
                                JOIN tbl_registration r ON b.user_id = r.user_id
                                WHERE b.status = 'confirmed'
                                GROUP BY b.user_id
                                ORDER BY total_spent DESC
                                LIMIT 5
                            ");
                            
                            while($row = mysqli_fetch_array($customer_qry)) {
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo $row['ticket_count']; ?></td>
                                    <td><?php echo number_format($row['total_spent'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>