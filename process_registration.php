<?php
ob_start();
include('config.php');
ob_end_clean();

header('Content-Type: application/json');

// Validate input
if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng điền đầy đủ thông tin!'
    ]);
    exit;
}

$name = mysqli_real_escape_string($con, $_POST['name']);
$age = intval($_POST['age']);
$gender = mysqli_real_escape_string($con, $_POST['gender']);
$phone = mysqli_real_escape_string($con, $_POST['phone']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$pass = mysqli_real_escape_string($con, $_POST['password']);

// Validate email format
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email không hợp lệ!'
    ]);
    exit;
}

// Validate password length
if(strlen($pass) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'
    ]);
    exit;
}

// Check if email already exists
$check_email = mysqli_query($con, "SELECT user_id FROM tbl_registration WHERE email='$email'");
if(mysqli_num_rows($check_email) > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Email này đã được đăng ký!'
    ]);
    exit;
}

// Start transaction
mysqli_begin_transaction($con);

try {
    // 1. Insert into tbl_registration
    $sql1 = "INSERT INTO tbl_registration (name, email, phone, age, gender) 
             VALUES ('$name', '$email', '$phone', '$age', '$gender')";
    
    if(!mysqli_query($con, $sql1)) {
        throw new Exception('Không thể tạo tài khoản: ' . mysqli_error($con));
    }
    
    $user_id = mysqli_insert_id($con);
    
    // 2. Insert into tbl_login
    $sql2 = "INSERT INTO tbl_login (user_id, username, password, user_type) 
             VALUES ('$user_id', '$email', '$pass', '2')";
    
    if(!mysqli_query($con, $sql2)) {
        throw new Exception('Không thể tạo thông tin đăng nhập: ' . mysqli_error($con));
    }
    
    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...',
        'user_id' => $user_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

mysqli_close($con);
?>
