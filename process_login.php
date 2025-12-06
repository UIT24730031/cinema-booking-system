<?php
session_start();
include('config.php');

$email = $_POST['Email'];
$pass = $_POST['Password'];

// Kiểm tra trong bảng login
$sql = "SELECT * FROM tbl_login WHERE username='$email' AND password='$pass'";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $_SESSION['user'] = $row['user_id'];
    $_SESSION['user_name'] = $email; // Tạm thời lấy email làm tên hiển thị
    
    // Nếu là admin (user_type = 0)
    if($row['user_type'] == 0){
        header("location: admin/index.php");
    } else {
        header("location: index.php");
    }
} else {
    echo "<script>alert('Sai email hoặc mật khẩu!'); window.location='login.php';</script>";
}
?>