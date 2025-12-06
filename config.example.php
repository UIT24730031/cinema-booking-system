<?php
// Cấu hình kết nối database
// Copy file này thành config.php và điền thông tin của bạn

$host = "localhost";
$user = "root";
$password = ""; // Điền mật khẩu MySQL của bạn
$database = "cinema_booking";

// Kết nối database
$con = mysqli_connect($host, $user, $password, $database);

// Kiểm tra kết nối
if (!$con) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Set charset UTF-8 để hiển thị tiếng Việt
mysqli_set_charset($con, "utf8mb4");
?>
