<?php
ob_start();
session_start();
include(__DIR__ . '/../../config.php');
ob_end_clean();

header('Content-Type: application/json');

if(!isset($_POST['Email']) || !isset($_POST['Password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng điền đầy đủ thông tin!'
    ]);
    exit;
}

$email = mysqli_real_escape_string($con, $_POST['Email']);
$pass = mysqli_real_escape_string($con, $_POST['Password']);

// Kiểm tra trong bảng login
$sql = "SELECT * FROM tbl_login WHERE username='$email' AND password='$pass'";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    
    // Lấy thông tin user từ bảng registration
    $user_qry = mysqli_query($con, "SELECT name FROM tbl_registration WHERE user_id='{$row['user_id']}'");
    $user_info = mysqli_fetch_array($user_qry);
    
    $_SESSION['user'] = $row['user_id'];
    $_SESSION['user_name'] = $user_info ? $user_info['name'] : $email;
    $_SESSION['user_type'] = $row['user_type'];
    
    // Xác định redirect
    $redirect = ($row['user_type'] == 0) ? 'admin/index.php' : 'index.php';
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công!',
        'redirect' => $redirect,
        'user_type' => $row['user_type']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Email hoặc mật khẩu không đúng!'
    ]);
}

mysqli_close($con);
?>
