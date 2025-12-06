<?php
session_start();
include('config.php');

if(!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$user_id = $_SESSION['user'];
$screening_id = mysqli_real_escape_string($con, $_POST['screening_id']);
$seats = mysqli_real_escape_string($con, $_POST['seats']);
$total_amount = mysqli_real_escape_string($con, $_POST['total_amount']);

// Kiểm tra ghế còn trống không
$seat_array = explode(',', $seats);
$booked_check = mysqli_query($con, "SELECT seats FROM tbl_bookings WHERE screening_id='$screening_id' AND status!='cancelled'");
$already_booked = [];
while($row = mysqli_fetch_array($booked_check)) {
    $already_booked = array_merge($already_booked, explode(',', $row['seats']));
}

foreach($seat_array as $seat) {
    if(in_array($seat, $already_booked)) {
        echo "<script>alert('Ghế $seat đã được đặt! Vui lòng chọn ghế khác.'); window.history.back();</script>";
        exit;
    }
}

// Thêm booking
$sql = "INSERT INTO tbl_bookings (user_id, screening_id, seats, total_amount, status) 
        VALUES ('$user_id', '$screening_id', '$seats', '$total_amount', 'confirmed')";

if(mysqli_query($con, $sql)) {
    $booking_id = mysqli_insert_id($con);
    
    // Cập nhật số ghế còn lại
    $seat_count = count($seat_array);
    mysqli_query($con, "UPDATE tbl_screenings SET available_seats = available_seats - $seat_count WHERE screening_id='$screening_id'");
    
    echo "<script>alert('Đặt vé thành công! Mã đặt vé: #$booking_id'); window.location='booking_history.php';</script>";
} else {
    echo "<script>alert('Lỗi: " . mysqli_error($con) . "'); window.history.back();</script>";
}
?>