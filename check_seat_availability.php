<?php
session_start();
include('config.php');

header('Content-Type: application/json');

// Validate input
if(!isset($_GET['screening_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$screening_id = intval($_GET['screening_id']);

// Check if table exists (for debugging)
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'tbl_seat_bookings'");
if(mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist yet, return empty booked seats
    echo json_encode([
        'success' => true,
        'booked_seats' => [],
        'available_seats' => 100,
        'note' => 'Migration not run yet'
    ]);
    exit;
}

// Get all booked seats for this screening using the new seat_bookings table
$stmt = mysqli_prepare($con, "SELECT seat_number FROM tbl_seat_bookings WHERE screening_id = ?");

if(!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $screening_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$booked_seats = [];
while($row = mysqli_fetch_assoc($result)) {
    $booked_seats[] = $row['seat_number'];
}

mysqli_stmt_close($stmt);

// Get available seats count
$stmt = mysqli_prepare($con, "SELECT available_seats FROM tbl_screenings WHERE screening_id = ?");
if(!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $screening_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$screening = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'booked_seats' => $booked_seats,
    'available_seats' => $screening ? $screening['available_seats'] : 0
]);
