<?php
// Cấu hình kết nối database cho Railway

// Đọc biến môi trường từ Railway (production)
if (isset($_ENV['MYSQL_HOST'])) {
    // Railway environment
    $host = $_ENV['MYSQL_HOST'];
    $user = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];
    $database = $_ENV['MYSQL_DATABASE'];
    $port = $_ENV['MYSQL_PORT'] ?? 3306;
} else {
    // Local development
    $host = "localhost";
    $user = "root";
    $password = ""; 
    $database = "cinema_booking";
    $port = 3306;
}

// Kết nối database
$con = mysqli_connect($host, $user, $password, $database, $port);

// Kiểm tra kết nối
if (!$con) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Set charset UTF-8 để hiển thị tiếng Việt
mysqli_set_charset($con, "utf8mb4");
?>
