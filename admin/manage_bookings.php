<?php
session_start();
include('../config.php');

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Xử lý cập nhật trạng thái
if(isset($_POST['update_status'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    // Nếu hủy vé, hoàn lại ghế
    if($status == 'cancelled') {
        $booking_info = mysqli_fetch_array(mysqli_query($con, "SELECT screening_id, seats FROM tbl_bookings WHERE booking_id='$booking_id'"));
        $seat_count = count(explode(',', $booking_info['seats']));
        mysqli_query($con, "UPDATE tbl_screenings SET available_seats = available_seats + $seat_count WHERE screening_id='{$booking_info['screening_id']}'");
    }
    
    mysqli_query($con, "UPDATE tbl_bookings SET status='$status' WHERE booking_id='$booking_id'");
    echo "<script>alert('Cập nhật trạng thái thành công!'); window.location='manage_bookings.php';</script>";
}

// Xử lý xóa booking
if(isset($_GET['delete'])) {
    $booking_id = mysqli_real_escape_string($con, $_GET['delete']);
    
    // Hoàn lại ghế trước khi xóa
    $booking_info = mysqli_fetch_array(mysqli_query($con, "SELECT screening_id, seats FROM tbl_bookings WHERE booking_id='$booking_id'"));
    $seat_count = count(explode(',', $booking_info['seats']));
    mysqli_query($con, "UPDATE tbl_screenings SET available_seats = available_seats + $seat_count WHERE screening_id='{$booking_info['screening_id']}'");
    
    mysqli_query($con, "DELETE FROM tbl_bookings WHERE booking_id='$booking_id'");
    echo "<script>alert('Xóa đặt vé thành công!'); window.location='manage_bookings.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đặt vé - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-ticket-alt"></i> Quản Lý Đặt Vé
        </h2>

        <!-- Thống kê nhanh -->
        <div class="row" style="margin-bottom: 30px;">
            <?php
            $total_bookings = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings"))['total'];
            $confirmed = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE status='confirmed'"))['total'];
            $cancelled = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE status='cancelled'"))['total'];
            $today = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE DATE(booking_date) = CURDATE()"))['total'];
            ?>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #2196F3, #1976D2); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-ticket-alt" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $total_bookings; ?></h3>
                    <p style="color: #fff; margin: 0;">Tổng đặt vé</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #4CAF50, #388E3C); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $confirmed; ?></h3>
                    <p style="color: #fff; margin: 0;">Đã xác nhận</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #f44336, #d32f2f); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-times-circle" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $cancelled; ?></h3>
                    <p style="color: #fff; margin: 0;">Đã hủy</p>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #FF9800, #F57C00); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-calendar-day" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $today; ?></h3>
                    <p style="color: #fff; margin: 0;">Hôm nay</p>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <form method="GET" class="form-inline">
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Trạng thái:</label>
                    <select name="status" class="form-control" style="background: #2a2a2a; color: #fff; border: none;">
                        <option value="">Tất cả</option>
                        <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status']=='confirmed') ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status']=='cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status']=='pending') ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    </select>
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Từ ngày:</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $_GET['from_date'] ?? ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Đến ngày:</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $_GET['to_date'] ?? ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Tìm kiếm:</label>
                    <input type="text" name="search" class="form-control" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Mã vé, tên KH, email..." style="background: #2a2a2a; color: #fff; border: none; width: 250px;">
                </div>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Tìm
                </button>
                <a href="manage_bookings.php" class="btn btn-default">Reset</a>
            </form>
        </div>

        <!-- Danh sách đặt vé -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">Danh sách đặt vé</h4>
            <div style="overflow-x: auto;">
                <table class="table table-hover" style="color: #e0e0e0;">
                    <thead style="background: #2a2a2a;">
                        <tr>
                            <th>Mã vé</th>
                            <th>Khách hàng</th>
                            <th>Phim</th>
                            <th>Rạp</th>
                            <th>Ngày chiếu</th>
                            <th>Giờ</th>
                            <th>Ghế</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "WHERE 1=1";
                        
                        if(isset($_GET['status']) && !empty($_GET['status'])) {
                            $status = mysqli_real_escape_string($con, $_GET['status']);
                            $where .= " AND b.status = '$status'";
                        }
                        
                        if(isset($_GET['from_date']) && !empty($_GET['from_date'])) {
                            $from = mysqli_real_escape_string($con, $_GET['from_date']);
                            $where .= " AND DATE(b.booking_date) >= '$from'";
                        }
                        
                        if(isset($_GET['to_date']) && !empty($_GET['to_date'])) {
                            $to = mysqli_real_escape_string($con, $_GET['to_date']);
                            $where .= " AND DATE(b.booking_date) <= '$to'";
                        }
                        
                        if(isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($con, $_GET['search']);
                            $where .= " AND (b.booking_id LIKE '%$search%' OR r.name LIKE '%$search%' OR r.email LIKE '%$search%' OR m.movie_name LIKE '%$search%')";
                        }
                        
                        $qry = mysqli_query($con, "
                            SELECT b.*, r.name, r.email, m.movie_name, t.name as theatre_name, 
                                   s.show_date, s.show_time, s.screen_name
                            FROM tbl_bookings b
                            JOIN tbl_registration r ON b.user_id = r.user_id
                            JOIN tbl_screenings s ON b.screening_id = s.screening_id
                            JOIN tbl_movie m ON s.movie_id = m.movie_id
                            JOIN tbl_theatre t ON s.theatre_id = t.id
                            $where
                            ORDER BY b.booking_date DESC
                        ");
                        
                        if(mysqli_num_rows($qry) > 0) {
                            while($row = mysqli_fetch_array($qry)) {
                                $status_class = $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'cancelled' ? 'danger' : 'warning');
                                $status_text = $row['status'] == 'confirmed' ? 'Đã xác nhận' : ($row['status'] == 'cancelled' ? 'Đã hủy' : 'Chờ xác nhận');
                        ?>
                        <tr>
                            <td><strong>#<?php echo $row['booking_id']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($row['name']); ?><br>
                                <small style="color: #999;"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['theatre_name']); ?><br>
                                <small style="color: #999;"><?php echo htmlspecialchars($row['screen_name']); ?></small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($row['show_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($row['show_time'])); ?></td>
                            <td><span class="label label-info"><?php echo htmlspecialchars($row['seats']); ?></span></td>
                            <td><strong><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                    <select name="status" class="form-control input-sm" style="background: #2a2a2a; color: #fff; border: none; width: 120px;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $row['status']=='pending'?'selected':''; ?>>Chờ xác nhận</option>
                                        <option value="confirmed" <?php echo $row['status']=='confirmed'?'selected':''; ?>>Đã xác nhận</option>
                                        <option value="cancelled" <?php echo $row['status']=='cancelled'?'selected':''; ?>>Đã hủy</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td>
                                <a href="view_booking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?delete=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa đặt vé này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="11" style="text-align: center; color: #777;">Không tìm thấy đặt vé nào</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>