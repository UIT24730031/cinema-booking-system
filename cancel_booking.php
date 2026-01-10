<?php
session_start();
include('config.php');

if(!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user'];

// Execute cancellation within a transaction
$result = execute_transaction($con, function($connection) use ($booking_id, $user_id) {
    
    // Step 1: Get and lock the booking record
    $stmt = mysqli_prepare($connection, "SELECT b.booking_id, b.screening_id, b.seats, b.status 
                                          FROM tbl_bookings b 
                                          WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'confirmed' 
                                          FOR UPDATE");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if(!$booking) {
        return false;
    }
    
    // Step 2: Update booking status to cancelled
    $stmt = mysqli_prepare($connection, "UPDATE tbl_bookings SET status = 'cancelled' WHERE booking_id = ?");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    if(!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    mysqli_stmt_close($stmt);
    
    // Step 3: Delete from tbl_seat_bookings (CASCADE will handle this, but we do it explicitly for clarity)
    $stmt = mysqli_prepare($connection, "DELETE FROM tbl_seat_bookings WHERE booking_id = ?");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    $deleted_seats = 0;
    if(mysqli_stmt_execute($stmt)) {
        $deleted_seats = mysqli_stmt_affected_rows($stmt);
    }
    mysqli_stmt_close($stmt);
    
    // Step 4: Restore available seats count
    if($deleted_seats > 0) {
        $stmt = mysqli_prepare($connection, "UPDATE tbl_screenings SET available_seats = available_seats + ? WHERE screening_id = ?");
        if(!$stmt) {
            error_log("Prepare failed: " . mysqli_error($connection));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $deleted_seats, $booking['screening_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    return true;
});

if($result === false) {
    echo "<script>alert('Không tìm thấy vé hoặc vé đã bị hủy!'); window.location='booking_history.php';</script>";
} else {
    echo "<script>alert('Hủy vé thành công!'); window.location='booking_history.php';</script>";
}
