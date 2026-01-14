<?php
ob_start();

session_start();
include('../config.php');

ob_end_clean();

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Xử lý xóa rạp
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete']);
    mysqli_query($con, "DELETE FROM tbl_theatre WHERE id='$id'");
    echo "<script>alert('Xóa rạp thành công!'); window.location='manage_theatres.php';</script>";
}

// Xử lý thêm/sửa rạp
if(isset($_POST['submit'])) {
    $id = isset($_POST['id']) ? mysqli_real_escape_string($con, $_POST['id']) : '';
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $total_screens = mysqli_real_escape_string($con, $_POST['total_screens']);
    
    if(empty($id)) {
        $sql = "INSERT INTO tbl_theatre (name, address, city, phone, total_screens) 
                VALUES ('$name', '$address', '$city', '$phone', '$total_screens')";
    } else {
        $sql = "UPDATE tbl_theatre SET name='$name', address='$address', city='$city', 
                phone='$phone', total_screens='$total_screens' WHERE id='$id'";
    }
    
    if(mysqli_query($con, $sql)) {
        echo "<script>alert('Lưu thành công!'); window.location='manage_theatres.php';</script>";
    } else {
        echo "<script>alert('Lỗi: ".mysqli_error($con)."');</script>";
    }
}

// Lấy thông tin rạp nếu đang sửa
$edit_theatre = null;
if(isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($con, $_GET['edit']);
    $edit_qry = mysqli_query($con, "SELECT * FROM tbl_theatre WHERE id='$id'");
    $edit_theatre = mysqli_fetch_array($edit_qry);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý rạp chiếu - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-building"></i> Quản Lý Rạp Chiếu
        </h2>

        <!-- Form thêm/sửa -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h4 style="color: #e50914; margin-bottom: 20px;">
                <?php echo $edit_theatre ? 'Sửa rạp chiếu' : 'Thêm rạp mới'; ?>
            </h4>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $edit_theatre ? $edit_theatre['id'] : ''; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên rạp *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $edit_theatre ? htmlspecialchars($edit_theatre['name']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Thành phố *</label>
                            <input type="text" name="city" class="form-control" value="<?php echo $edit_theatre ? htmlspecialchars($edit_theatre['city']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ *</label>
                    <input type="text" name="address" class="form-control" value="<?php echo $edit_theatre ? htmlspecialchars($edit_theatre['address']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $edit_theatre ? htmlspecialchars($edit_theatre['phone']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Số phòng chiếu *</label>
                            <input type="number" name="total_screens" class="form-control" value="<?php echo $edit_theatre ? $edit_theatre['total_screens'] : '1'; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn btn-danger" style="background: #e50914;">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <?php if($edit_theatre) { ?>
                    <a href="manage_theatres.php" class="btn btn-default">Hủy</a>
                <?php } ?>
            </form>
        </div>

        <!-- Danh sách rạp -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">Danh sách rạp chiếu</h4>
            <div class="row">
                <?php
                $qry = mysqli_query($con, "
                    SELECT t.*, 
                           COUNT(DISTINCT s.screening_id) as total_screenings,
                           COUNT(DISTINCT b.booking_id) as total_bookings
                    FROM tbl_theatre t
                    LEFT JOIN tbl_screenings s ON t.id = s.theatre_id
                    LEFT JOIN tbl_bookings b ON s.screening_id = b.screening_id
                    GROUP BY t.id
                    ORDER BY t.name
                ");
                
                while($row = mysqli_fetch_array($qry)) {
                ?>
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <div style="background: #2a2a2a; border-radius: 10px; padding: 20px; height: 100%;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <h4 style="color: #e50914; margin: 0;">
                                <i class="fas fa-building"></i> <?php echo htmlspecialchars($row['name']); ?>
                            </h4>
                            <div>
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        
                        <p style="color: #aaa; margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #e50914; width: 20px;"></i>
                            <?php echo htmlspecialchars($row['address']); ?>
                        </p>
                        
                        <p style="color: #aaa; margin-bottom: 8px;">
                            <i class="fas fa-city" style="color: #e50914; width: 20px;"></i>
                            <?php echo htmlspecialchars($row['city']); ?>
                        </p>
                        
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-phone" style="color: #e50914; width: 20px;"></i>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </p>
                        
                        <div style="border-top: 1px solid #444; padding-top: 15px;">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <div style="background: rgba(229, 9, 20, 0.1); padding: 10px; border-radius: 5px;">
                                        <div style="font-size: 24px; font-weight: bold; color: #e50914;"><?php echo $row['total_screens']; ?></div>
                                        <div style="font-size: 12px; color: #aaa;">Phòng chiếu</div>
                                    </div>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <div style="background: rgba(33, 150, 243, 0.1); padding: 10px; border-radius: 5px;">
                                        <div style="font-size: 24px; font-weight: bold; color: #2196F3;"><?php echo $row['total_screenings']; ?></div>
                                        <div style="font-size: 12px; color: #aaa;">Lịch chiếu</div>
                                    </div>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <div style="background: rgba(76, 175, 80, 0.1); padding: 10px; border-radius: 5px;">
                                        <div style="font-size: 24px; font-weight: bold; color: #4CAF50;"><?php echo $row['total_bookings']; ?></div>
                                        <div style="font-size: 12px; color: #aaa;">Vé đã bán</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>