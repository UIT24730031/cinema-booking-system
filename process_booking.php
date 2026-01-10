<?php
session_start();
include('config.php');

// Return JSON response helper function
function json_response($success, $message, $data = null, $error_code = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'error_code' => $error_code
    ]);
    exit;
}

// Check if user is logged in
if(!isset($_SESSION['user'])) {
    json_response(false, 'Vui lòng đăng nhập để đặt vé!', null, 'not_authenticated');
}

// Validate input
if(!isset($_POST['screening_id']) || !isset($_POST['seats']) || !isset($_POST['total_amount'])) {
    json_response(false, 'Dữ liệu không hợp lệ!', null, 'invalid_input');
}

$user_id = $_SESSION['user'];
$screening_id = intval($_POST['screening_id']);
$seats_string = trim($_POST['seats']);
$total_amount = floatval($_POST['total_amount']);

// Validate seats format
if(empty($seats_string)) {
    json_response(false, 'Vui lòng chọn ít nhất 1 ghế!', null, 'no_seats_selected');
}

$seat_array = array_map('trim', explode(',', $seats_string));
$seat_count = count($seat_array);

// Validate seat count
if($seat_count === 0 || $seat_count > 10) {
    json_response(false, 'Số lượng ghế không hợp lệ (tối đa 10 ghế)!', null, 'invalid_seat_count');
}

// Execute booking within a transaction
$result = execute_transaction($con, function($connection) use ($user_id, $screening_id, $seat_array, $seat_count, $total_amount, $seats_string) {
    
    // Step 1: Lock the screening row to prevent concurrent modifications
    $stmt = mysqli_prepare($connection, "SELECT screening_id, available_seats, price FROM tbl_screenings WHERE screening_id = ? FOR UPDATE");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return ['success' => false, 'message' => 'Lỗi hệ thống!', 'error_code' => 'db_error'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $screening_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $screening = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if(!$screening) {
        return ['success' => false, 'message' => 'Lịch chiếu không tồn tại!', 'error_code' => 'screening_not_found'];
    }
    
    // Step 2: Check if enough seats are available
    if($screening['available_seats'] < $seat_count) {
        return ['success' => false, 'message' => 'Không đủ ghế trống! Chỉ còn ' . $screening['available_seats'] . ' ghế.', 'error_code' => 'insufficient_seats'];
    }
    
    // Step 3: Check if specific seats are already booked (using the new table)
    $placeholders = str_repeat('?,', count($seat_array) - 1) . '?';
    $stmt = mysqli_prepare($connection, "SELECT seat_number FROM tbl_seat_bookings WHERE screening_id = ? AND seat_number IN ($placeholders)");
    
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return ['success' => false, 'message' => 'Lỗi hệ thống!', 'error_code' => 'db_error'];
    }
    
    $types = 'i' . str_repeat('s', count($seat_array));
    $params = array_merge([$screening_id], $seat_array);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $taken_seats = [];
    while($row = mysqli_fetch_assoc($result)) {
        $taken_seats[] = $row['seat_number'];
    }
    mysqli_stmt_close($stmt);
    
    if(!empty($taken_seats)) {
        $taken_list = implode(', ', $taken_seats);
        return ['success' => false, 'message' => "Ghế $taken_list đã được đặt bởi người dùng khác! Vui lòng chọn ghế khác.", 'error_code' => 'seats_taken', 'taken_seats' => $taken_seats];
    }
    
    // Step 4: Insert into tbl_bookings
    $stmt = mysqli_prepare($connection, "INSERT INTO tbl_bookings (user_id, screening_id, seats, total_amount, status) VALUES (?, ?, ?, ?, 'confirmed')");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return ['success' => false, 'message' => 'Lỗi hệ thống!', 'error_code' => 'db_error'];
    }
    
    mysqli_stmt_bind_param($stmt, "iisd", $user_id, $screening_id, $seats_string, $total_amount);
    
    if(!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Không thể tạo đặt vé!', 'error_code' => 'booking_failed'];
    }
    
    $booking_id = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);
    
    // Step 5: Insert individual seats into tbl_seat_bookings
    $stmt = mysqli_prepare($connection, "INSERT INTO tbl_seat_bookings (booking_id, screening_id, seat_number) VALUES (?, ?, ?)");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return ['success' => false, 'message' => 'Lỗi hệ thống!', 'error_code' => 'db_error'];
    }
    
    foreach($seat_array as $seat) {
        mysqli_stmt_bind_param($stmt, "iis", $booking_id, $screening_id, $seat);
        if(!mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            
            // Check for duplicate key error (1062)
            if(strpos($error, 'Duplicate entry') !== false || strpos($error, '1062') !== false) {
                return ['success' => false, 'message' => "Ghế $seat đã được đặt bởi người dùng khác! Vui lòng thử lại.", 'error_code' => 'seat_taken', 'taken_seats' => [$seat]];
            }
            
            error_log("Execute failed: " . $error);
            return ['success' => false, 'message' => 'Không thể đặt ghế!', 'error_code' => 'seat_booking_failed'];
        }
    }
    mysqli_stmt_close($stmt);
    
    // Step 6: Update available seats count
    $stmt = mysqli_prepare($connection, "UPDATE tbl_screenings SET available_seats = available_seats - ? WHERE screening_id = ?");
    if(!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return ['success' => false, 'message' => 'Lỗi hệ thống!', 'error_code' => 'db_error'];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $seat_count, $screening_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Success!
    return [
        'success' => true, 
        'message' => 'Đặt vé thành công!', 
        'booking_id' => $booking_id,
        'seats' => $seat_array,
        'total_amount' => $total_amount
    ];
});

// Handle transaction result
if($result === false) {
    json_response(false, 'Đã xảy ra lỗi trong quá trình đặt vé. Vui lòng thử lại!', null, 'transaction_failed');
}

if($result['success']) {
    json_response(true, $result['message'], [
        'booking_id' => $result['booking_id'],
        'seats' => $result['seats'],
        'total_amount' => $result['total_amount']
    ]);
} else {
    json_response(false, $result['message'], 
        isset($result['taken_seats']) ? ['taken_seats' => $result['taken_seats']] : null, 
        $result['error_code']
    );
}
?>
