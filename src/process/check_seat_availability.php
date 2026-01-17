<?php
// Prevent any output before JSON
ob_start();

session_start();
include(__DIR__ . '/../../config.php');

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Suppress HTML errors
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Validate input
if(!isset($_GET['screening_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    mysqli_close($con);
    exit;
}

$screening_id = intval($_GET['screening_id']);

// Check if table exists (for graceful degradation)
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'tbl_seat_bookings'");
if(mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist, return empty booked seats
    echo json_encode([
        'success' => true,
        'booked_seats' => [],
        'available_seats' => 100,
        'note' => 'Database table not found'
    ]);
    mysqli_close($con);
    exit;
}

// Get all booked seats for this screening using the new seat_bookings table
$stmt = mysqli_prepare($con, "SELECT seat_number FROM tbl_seat_bookings WHERE screening_id = ?");

if(!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    mysqli_close($con);
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
    mysqli_close($con);
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

// Close connection to free resources
mysqli_close($con);
