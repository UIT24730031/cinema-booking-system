<?php
ob_start();

include('config.php');

ob_end_clean();

$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$pass = $_POST['password'];

// 1. Lưu thông tin cá nhân
$sql1 = "INSERT INTO tbl_registration (name, email, phone, age, gender) VALUES ('$name', '$email', '$phone', '$age', '$gender')";

if(mysqli_query($con, $sql1)){
    $user_id = mysqli_insert_id($con); // Lấy ID vừa tạo
    
    // 2. Tạo tài khoản đăng nhập
    $sql2 = "INSERT INTO tbl_login (user_id, username, password, user_type) VALUES ('$user_id', '$email', '$pass', '2')";
    mysqli_query($con, $sql2);
    
    echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location='login.php';</script>";
} else {
    echo "Lỗi: " . mysqli_error($con);
}
?>