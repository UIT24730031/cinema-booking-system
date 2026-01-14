<?php
ob_start();

session_start();
include('../config.php');

ob_end_clean();

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Xử lý xóa người dùng
if(isset($_GET['delete'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['delete']);
    if($user_id != 1) { // Không cho xóa admin
        mysqli_query($con, "DELETE FROM tbl_registration WHERE user_id='$user_id'");
        echo "<script>alert('Xóa người dùng thành công!'); window.location='manage_users.php';</script>";
    } else {
        echo "<script>alert('Không thể xóa tài khoản Admin!'); window.location='manage_users.php';</script>";
    }
}

// Xử lý thêm/sửa người dùng
if(isset($_POST['submit'])) {
    $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($con, $_POST['user_id']) : '';
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $age = mysqli_real_escape_string($con, $_POST['age']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $user_type = mysqli_real_escape_string($con, $_POST['user_type']);
    
    if(empty($user_id)) {
        // Thêm mới
        $sql1 = "INSERT INTO tbl_registration (name, email, phone, age, gender) VALUES ('$name', '$email', '$phone', '$age', '$gender')";
        if(mysqli_query($con, $sql1)) {
            $new_user_id = mysqli_insert_id($con);
            $sql2 = "INSERT INTO tbl_login (user_id, username, password, user_type) VALUES ('$new_user_id', '$email', '$password', '$user_type')";
            mysqli_query($con, $sql2);
            echo "<script>alert('Thêm người dùng thành công!'); window.location='manage_users.php';</script>";
        }
    } else {
        // Cập nhật
        $sql1 = "UPDATE tbl_registration SET name='$name', email='$email', phone='$phone', age='$age', gender='$gender' WHERE user_id='$user_id'";
        mysqli_query($con, $sql1);
        
        $sql2 = "UPDATE tbl_login SET username='$email', user_type='$user_type'";
        if(!empty($password)) {
            $sql2 .= ", password='$password'";
        }
        $sql2 .= " WHERE user_id='$user_id'";
        mysqli_query($con, $sql2);
        
        echo "<script>alert('Cập nhật thành công!'); window.location='manage_users.php';</script>";
    }
}

// Lấy thông tin người dùng nếu đang sửa
$edit_user = null;
if(isset($_GET['edit'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['edit']);
    $edit_qry = mysqli_query($con, "
        SELECT r.*, l.user_type 
        FROM tbl_registration r 
        LEFT JOIN tbl_login l ON r.user_id = l.user_id 
        WHERE r.user_id='$user_id'
    ");
    $edit_user = mysqli_fetch_array($edit_qry);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-users"></i> Quản Lý Người Dùng
        </h2>

        <!-- Thống kê -->
        <div class="row" style="margin-bottom: 30px;">
            <?php
            $total_users = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_registration WHERE user_id != 1"))['total'];
            $new_this_month = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_registration WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"))['total'];
            $active_users = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(DISTINCT user_id) as total FROM tbl_bookings WHERE status='confirmed'"))['total'];
            ?>
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #2196F3, #1976D2); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-users" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $total_users; ?></h3>
                    <p style="color: #fff; margin: 0;">Tổng người dùng</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #4CAF50, #388E3C); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-user-plus" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $new_this_month; ?></h3>
                    <p style="color: #fff; margin: 0;">Đăng ký tháng này</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #FF9800, #F57C00); padding: 20px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-user-check" style="font-size: 35px; color: #fff; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; margin: 5px 0; font-size: 32px;"><?php echo $active_users; ?></h3>
                    <p style="color: #fff; margin: 0;">Đã đặt vé</p>
                </div>
            </div>
        </div>

        <!-- Form thêm/sửa -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h4 style="color: #e50914; margin-bottom: 20px;">
                <?php echo $edit_user ? 'Sửa người dùng' : 'Thêm người dùng mới'; ?>
            </h4>
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?php echo $edit_user ? $edit_user['user_id'] : ''; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Họ tên *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $edit_user ? htmlspecialchars($edit_user['name']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $edit_user ? htmlspecialchars($edit_user['phone']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tuổi</label>
                            <input type="number" name="age" class="form-control" value="<?php echo $edit_user ? $edit_user['age'] : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="gender" class="form-control" style="background: #2a2a2a; color: #fff; border: none;">
                                <option value="Male" <?php echo ($edit_user && $edit_user['gender']=='Male') ? 'selected' : ''; ?>>Nam</option>
                                <option value="Female" <?php echo ($edit_user && $edit_user['gender']=='Female') ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Other" <?php echo ($edit_user && $edit_user['gender']=='Other') ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mật khẩu <?php echo $edit_user ? '(Để trống nếu không đổi)' : '*'; ?></label>
                            <input type="password" name="password" class="form-control" <?php echo !$edit_user ? 'required' : ''; ?> style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loại tài khoản</label>
                            <select name="user_type" class="form-control" style="background: #2a2a2a; color: #fff; border: none;">
                                <option value="2" <?php echo ($edit_user && $edit_user['user_type']==2) ? 'selected' : ''; ?>>Khách hàng</option>
                                <option value="1" <?php echo ($edit_user && $edit_user['user_type']==1) ? 'selected' : ''; ?>>Nhân viên</option>
                                <option value="0" <?php echo ($edit_user && $edit_user['user_type']==0) ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn btn-danger" style="background: #e50914;">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <?php if($edit_user) { ?>
                    <a href="manage_users.php" class="btn btn-default">Hủy</a>
                <?php } ?>
            </form>
        </div>

        <!-- Bộ lọc -->
        <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <form method="GET" class="form-inline">
                <div class="form-group" style="margin-right: 15px;">
                    <label style="color: #aaa; margin-right: 10px;">Tìm kiếm:</label>
                    <input type="text" name="search" class="form-control" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Tên, email, SĐT..." style="background: #2a2a2a; color: #fff; border: none; width: 300px;">
                </div>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Tìm
                </button>
                <a href="manage_users.php" class="btn btn-default">Reset</a>
            </form>
        </div>

        <!-- Danh sách người dùng -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">Danh sách người dùng</h4>
            <div style="overflow-x: auto;">
                <table class="table table-hover" style="color: #e0e0e0;">
                    <thead style="background: #2a2a2a;">
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Tuổi</th>
                            <th>Giới tính</th>
                            <th>Loại TK</th>
                            <th>Số vé đã đặt</th>
                            <th>Tổng chi tiêu</th>
                            <th>Ngày đăng ký</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "WHERE r.user_id != 1"; // Không hiển thị admin chính
                        
                        if(isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($con, $_GET['search']);
                            $where .= " AND (r.name LIKE '%$search%' OR r.email LIKE '%$search%' OR r.phone LIKE '%$search%')";
                        }
                        
                        $qry = mysqli_query($con, "
                            SELECT r.*, l.user_type,
                                   COUNT(b.booking_id) as total_bookings,
                                   COALESCE(SUM(CASE WHEN b.status='confirmed' THEN b.total_amount ELSE 0 END), 0) as total_spent
                            FROM tbl_registration r
                            LEFT JOIN tbl_login l ON r.user_id = l.user_id
                            LEFT JOIN tbl_bookings b ON r.user_id = b.user_id
                            $where
                            GROUP BY r.user_id
                            ORDER BY r.user_id DESC
                        ");
                        
                        if(mysqli_num_rows($qry) > 0) {
                            while($row = mysqli_fetch_array($qry)) {
                                $type_text = $row['user_type'] == 0 ? 'Admin' : ($row['user_type'] == 1 ? 'Nhân viên' : 'Khách hàng');
                                $type_class = $row['user_type'] == 0 ? 'danger' : ($row['user_type'] == 1 ? 'warning' : 'info');
                        ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><span class="label label-<?php echo $type_class; ?>"><?php echo $type_text; ?></span></td>
                            <td><span class="badge" style="background: #2196F3;"><?php echo $row['total_bookings']; ?></span></td>
                            <td><strong><?php echo number_format($row['total_spent'], 0, ',', '.'); ?>đ</strong></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-info" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-success" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?delete=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa người dùng này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="11" style="text-align: center; color: #777;">Không tìm thấy người dùng nào</td></tr>';
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