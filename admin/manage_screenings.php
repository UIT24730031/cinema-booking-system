<?php
session_start();
include('../config.php');

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Xử lý xóa lịch chiếu
if(isset($_GET['delete'])) {
    $screening_id = mysqli_real_escape_string($con, $_GET['delete']);
    mysqli_query($con, "DELETE FROM tbl_screenings WHERE screening_id='$screening_id'");
    echo "<script>alert('Xóa lịch chiếu thành công!'); window.location='manage_screenings.php';</script>";
}

// Xử lý thêm/sửa lịch chiếu
if(isset($_POST['submit'])) {
    $screening_id = isset($_POST['screening_id']) ? mysqli_real_escape_string($con, $_POST['screening_id']) : '';
    $movie_id = mysqli_real_escape_string($con, $_POST['movie_id']);
    $theatre_id = mysqli_real_escape_string($con, $_POST['theatre_id']);
    $screen_name = mysqli_real_escape_string($con, $_POST['screen_name']);
    $show_date = mysqli_real_escape_string($con, $_POST['show_date']);
    $show_time = mysqli_real_escape_string($con, $_POST['show_time']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $available_seats = mysqli_real_escape_string($con, $_POST['available_seats']);
    
    if(empty($screening_id)) {
        $sql = "INSERT INTO tbl_screenings (movie_id, theatre_id, screen_name, show_date, show_time, price, available_seats) 
                VALUES ('$movie_id', '$theatre_id', '$screen_name', '$show_date', '$show_time', '$price', '$available_seats')";
    } else {
        $sql = "UPDATE tbl_screenings SET movie_id='$movie_id', theatre_id='$theatre_id', screen_name='$screen_name', 
                show_date='$show_date', show_time='$show_time', price='$price', available_seats='$available_seats' 
                WHERE screening_id='$screening_id'";
    }
    
    if(mysqli_query($con, $sql)) {
        echo "<script>alert('Lưu thành công!'); window.location='manage_screenings.php';</script>";
    } else {
        echo "<script>alert('Lỗi: ".mysqli_error($con)."');</script>";
    }
}

// Lấy thông tin lịch chiếu nếu đang sửa
$edit_screening = null;
if(isset($_GET['edit'])) {
    $screening_id = mysqli_real_escape_string($con, $_GET['edit']);
    $edit_qry = mysqli_query($con, "SELECT * FROM tbl_screenings WHERE screening_id='$screening_id'");
    $edit_screening = mysqli_fetch_array($edit_qry);
}

// Lấy danh sách phim và rạp
$movies = mysqli_query($con, "SELECT movie_id, movie_name FROM tbl_movie ORDER BY movie_name");
$theatres = mysqli_query($con, "SELECT id, name FROM tbl_theatre ORDER BY name");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý lịch chiếu - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-calendar-alt"></i> Quản Lý Lịch Chiếu
        </h2>

        <!-- Form thêm/sửa -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h4 style="color: #e50914; margin-bottom: 20px;">
                <?php echo $edit_screening ? 'Sửa lịch chiếu' : 'Thêm lịch chiếu mới'; ?>
            </h4>
            <form method="POST" action="">
                <input type="hidden" name="screening_id" value="<?php echo $edit_screening ? $edit_screening['screening_id'] : ''; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phim *</label>
                            <select name="movie_id" class="form-control" required style="background: #2a2a2a; color: #fff; border: none;">
                                <option value="">-- Chọn phim --</option>
                                <?php 
                                mysqli_data_seek($movies, 0);
                                while($movie = mysqli_fetch_array($movies)) { 
                                    $selected = ($edit_screening && $edit_screening['movie_id'] == $movie['movie_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $movie['movie_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($movie['movie_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Rạp chiếu *</label>
                            <select name="theatre_id" class="form-control" required style="background: #2a2a2a; color: #fff; border: none;">
                                <option value="">-- Chọn rạp --</option>
                                <?php 
                                mysqli_data_seek($theatres, 0);
                                while($theatre = mysqli_fetch_array($theatres)) { 
                                    $selected = ($edit_screening && $edit_screening['theatre_id'] == $theatre['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $theatre['id']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($theatre['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phòng chiếu *</label>
                            <input type="text" name="screen_name" class="form-control" value="<?php echo $edit_screening ? htmlspecialchars($edit_screening['screen_name']) : ''; ?>" required placeholder="VD: Screen 1" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ngày chiếu *</label>
                            <input type="date" name="show_date" class="form-control" value="<?php echo $edit_screening ? $edit_screening['show_date'] : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Giờ chiếu *</label>
                            <input type="time" name="show_time" class="form-control" value="<?php echo $edit_screening ? $edit_screening['show_time'] : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Giá vé (VNĐ) *</label>
                            <input type="number" name="price" class="form-control" value="<?php echo $edit_screening ? $edit_screening['price'] : ''; ?>" required placeholder="80000" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Số ghế *</label>
                            <input type="number" name="available_seats" class="form-control" value="<?php echo $edit_screening ? $edit_screening['available_seats'] : '150'; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn btn-danger" style="background: #e50914;">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <?php if($edit_screening) { ?>
                    <a href="manage_screenings.php" class="btn btn-default">Hủy</a>
                <?php } ?>
            </form>
        </div>

        <!-- Lọc -->
        <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <form method="GET" class="form-inline">
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Từ ngày:</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $_GET['from_date'] ?? date('Y-m-d'); ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Đến ngày:</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $_GET['to_date'] ?? date('Y-m-d', strtotime('+7 days')); ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <a href="manage_screenings.php" class="btn btn-default">Reset</a>
            </form>
        </div>

        <!-- Danh sách lịch chiếu -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">Danh sách lịch chiếu</h4>
            <div style="overflow-x: auto;">
                <table class="table table-hover" style="color: #e0e0e0;">
                    <thead style="background: #2a2a2a;">
                        <tr>
                            <th>ID</th>
                            <th>Phim</th>
                            <th>Rạp</th>
                            <th>Phòng</th>
                            <th>Ngày chiếu</th>
                            <th>Giờ chiếu</th>
                            <th>Giá</th>
                            <th>Ghế trống</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "WHERE 1=1";
                        if(isset($_GET['from_date']) && !empty($_GET['from_date'])) {
                            $from = mysqli_real_escape_string($con, $_GET['from_date']);
                            $where .= " AND s.show_date >= '$from'";
                        }
                        if(isset($_GET['to_date']) && !empty($_GET['to_date'])) {
                            $to = mysqli_real_escape_string($con, $_GET['to_date']);
                            $where .= " AND s.show_date <= '$to'";
                        }
                        
                        $qry = mysqli_query($con, "
                            SELECT s.*, m.movie_name, t.name as theatre_name 
                            FROM tbl_screenings s
                            JOIN tbl_movie m ON s.movie_id = m.movie_id
                            JOIN tbl_theatre t ON s.theatre_id = t.id
                            $where
                            ORDER BY s.show_date DESC, s.show_time DESC
                        ");
                        
                        if(mysqli_num_rows($qry) > 0) {
                            while($row = mysqli_fetch_array($qry)) {
                                $date_color = strtotime($row['show_date']) < strtotime(date('Y-m-d')) ? '#777' : '#fff';
                        ?>
                        <tr style="color: <?php echo $date_color; ?>">
                            <td><?php echo $row['screening_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['theatre_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['screen_name']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['show_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($row['show_time'])); ?></td>
                            <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <span class="label <?php echo $row['available_seats'] > 50 ? 'label-success' : ($row['available_seats'] > 20 ? 'label-warning' : 'label-danger'); ?>">
                                    <?php echo $row['available_seats']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $row['screening_id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $row['screening_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="9" style="text-align: center; color: #777;">Không có lịch chiếu nào</td></tr>';
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