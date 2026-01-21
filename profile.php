<?php 
include('header.php'); 

if(!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user'];
$user_qry = mysqli_query($con, "SELECT * FROM tbl_registration WHERE user_id='$user_id'");
$user = mysqli_fetch_array($user_qry);

// Thống kê
$total_bookings = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM tbl_bookings WHERE user_id='$user_id'"))['total'];
$total_spent = mysqli_fetch_array(mysqli_query($con, "SELECT SUM(total_amount) as total FROM tbl_bookings WHERE user_id='$user_id' AND status='confirmed'"))['total'] ?? 0;
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; text-align: center;">
                <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #e50914, #b20710); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user" style="font-size: 50px; color: #fff;"></i>
                </div>
                <h4 style="color: #fff; margin-bottom: 5px;"><?php echo htmlspecialchars($user['name']);?></h4>
                <p style="color: #aaa; font-size: 14px; margin-bottom: 20px;"><?php echo htmlspecialchars($user['email']);?></p>
                
                <div style="border-top: 1px solid #333; padding-top: 20px; margin-top: 20px;">
                    <p style="color: #aaa; font-size: 13px; margin-bottom: 10px;">Thành viên từ</p>
                    <p style="color: #e50914; font-weight: bold;"><?php echo date('d/m/Y', strtotime($user['created_at']));?></p>
                </div>
            </div>
            
            <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <a href="profile.php" class="btn btn-block" style="background: #2a2a2a; color: #fff; margin-bottom: 10px; text-align: left;">
                    <i class="fas fa-user"></i> Thông tin cá nhân
                </a>
                <a href="booking_history.php" class="btn btn-block" style="background: #2a2a2a; color: #fff; margin-bottom: 10px; text-align: left;">
                    <i class="fas fa-ticket-alt"></i> Lịch sử đặt vé
                </a>
                <a href="logout.php" class="btn btn-block" style="background: #f44336; color: #fff; text-align: left;">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
        
        <!-- Nội dung chính -->
        <div class="col-md-9">
            <!-- Thống kê -->
            <div class="row" style="margin-bottom: 30px;">
                <div class="col-md-6">
                    <div style="background: linear-gradient(135deg, #e50914, #b20710); padding: 25px; border-radius: 10px; text-align: center;">
                        <i class="fas fa-ticket-alt" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                        <h3 style="color: #fff; margin: 0; font-size: 36px; font-weight: bold;"><?php echo $total_bookings;?></h3>
                        <p style="color: #fff; margin: 5px 0 0 0;">Vé đã đặt</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: linear-gradient(135deg, #4CAF50, #388E3C); padding: 25px; border-radius: 10px; text-align: center;">
                        <i class="fas fa-coins" style="font-size: 40px; color: #fff; margin-bottom: 10px;"></i>
                        <h3 style="color: #fff; margin: 0; font-size: 36px; font-weight: bold;"><?php echo number_format($total_spent, 0, ',', '.');?>đ</h3>
                        <p style="color: #fff; margin: 5px 0 0 0;">Tổng chi tiêu</p>
                    </div>
                </div>
            </div>
            
            <!-- Form cập nhật thông tin -->
            <div style="background: #1a1a1a; padding: 30px; border-radius: 10px;">
                <h3 style="color: #fff; margin-bottom: 25px;">
                    <i class="fas fa-user-edit"></i> Thông Tin Cá Nhân
                </h3>
                
                <?php
                if(isset($_POST['update_profile'])) {
                    $name = mysqli_real_escape_string($con, $_POST['name']);
                    $phone = mysqli_real_escape_string($con, $_POST['phone']);
                    $age = mysqli_real_escape_string($con, $_POST['age']);
                    $gender = mysqli_real_escape_string($con, $_POST['gender']);
                    
                    $update = mysqli_query($con, "UPDATE tbl_registration SET name='$name', phone='$phone', age='$age', gender='$gender' WHERE user_id='$user_id'");
                    
                    if($update) {
                        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Cập nhật thành công!</div>';
                        $user = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tbl_registration WHERE user_id='$user_id'"));
                    } else {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Lỗi: '.mysqli_error($con).'</div>';
                    }
                }
                ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #aaa;">Họ và tên</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']);?>" style="background: #2a2a2a; border: none; color: #fff; height: 45px;" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #aaa;">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']);?>" style="background: #2a2a2a; border: none; color: #777; height: 45px;" disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #aaa;">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']);?>" style="background: #2a2a2a; border: none; color: #fff; height: 45px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="color: #aaa;">Tuổi</label>
                                <input type="number" name="age" class="form-control" value="<?php echo $user['age'];?>" style="background: #2a2a2a; border: none; color: #fff; height: 45px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="color: #aaa;">Giới tính</label>
                        <select name="gender" class="form-control" style="background: #2a2a2a; border: none; color: #fff; height: 45px;">
                            <option value="Male" <?php echo $user['gender']=='Male'?'selected':'';?>>Nam</option>
                            <option value="Female" <?php echo $user['gender']=='Female'?'selected':'';?>>Nữ</option>
                            <option value="Other" <?php echo $user['gender']=='Other'?'selected':'';?>>Khác</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-danger" style="background: #e50914; height: 45px; padding: 0 40px; font-weight: bold;">
                        <i class="fas fa-save"></i> LƯU THÔNG TIN
                    </button>
                </form>
            </div>
            
            <!-- Đổi mật khẩu -->
            <div style="background: #1a1a1a; padding: 30px; border-radius: 10px; margin-top: 20px;">
                <h3 style="color: #fff; margin-bottom: 25px;">
                    <i class="fas fa-key"></i> Đổi Mật Khẩu
                </h3>
                
                <?php
                if(isset($_POST['change_password'])) {
                    $old_pass = mysqli_real_escape_string($con, $_POST['old_password']);
                    $new_pass = mysqli_real_escape_string($con, $_POST['new_password']);
                    $confirm_pass = mysqli_real_escape_string($con, $_POST['confirm_password']);
                    
                    $check_pass = mysqli_query($con, "SELECT * FROM tbl_login WHERE user_id='$user_id' AND password='$old_pass'");
                    
                    if(mysqli_num_rows($check_pass) == 0) {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Mật khẩu cũ không đúng!</div>';
                    } elseif($new_pass != $confirm_pass) {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Mật khẩu xác nhận không khớp!</div>';
                    } elseif(strlen($new_pass) < 6) {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Mật khẩu phải có ít nhất 6 ký tự!</div>';
                    } else {
                        mysqli_query($con, "UPDATE tbl_login SET password='$new_pass' WHERE user_id='$user_id'");
                        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Đổi mật khẩu thành công!</div>';
                    }
                }
                ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label style="color: #aaa;">Mật khẩu cũ</label>
                        <input type="password" name="old_password" class="form-control" style="background: #2a2a2a; border: none; color: #fff; height: 45px;" required>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" style="background: #2a2a2a; border: none; color: #fff; height: 45px;" required>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" style="background: #2a2a2a; border: none; color: #fff; height: 45px;" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-warning" style="height: 45px; padding: 0 40px; font-weight: bold;">
                        <i class="fas fa-lock"></i> ĐỔI MẬT KHẨU
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>