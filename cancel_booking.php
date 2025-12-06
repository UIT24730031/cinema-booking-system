<?php
session_start();
include('config.php');

if(!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$booking_id = mysqli_real_escape_string($con, $_GET['id']);
$user_id = $_SESSION['user'];

// Kiểm tra booking có thuộc user không
$check = mysqli_query($con, "SELECT * FROM tbl_bookings WHERE booking_id='$booking_id' AND user_id='$user_id' AND status='confirmed'");

if(mysqli_num_rows($check) == 0) {
    echo "<script>alert('Không tìm thấy vé hoặc vé đã bị hủy!'); window.location='booking_history.php';</script>";
    exit;
}

$booking = mysqli_fetch_array($check);

// Cập nhật trạng thái
mysqli_query($con, "UPDATE tbl_bookings SET status='cancelled' WHERE booking_id='$booking_id'");

// Hoàn lại ghế
$seat_count = count(explode(',', $booking['seats']));
mysqli_query($con, "UPDATE tbl_screenings SET available_seats = available_seats + $seat_count WHERE screening_id='{$booking['screening_id']}'");

echo "<script>alert('Hủy vé thành công!'); window.location='booking_history.php';</script>";
?>