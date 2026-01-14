<?php
// Cấu hình kết nối database

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

// ============================================
// TRANSACTION HELPER FUNCTIONS
// ============================================

// Start a database transaction
function begin_transaction($connection) {
    return mysqli_begin_transaction($connection, MYSQLI_TRANS_START_READ_WRITE);
}

// Commit the current transaction
function commit_transaction($connection) {
    return mysqli_commit($connection);
}

// Rollback the current transaction
function rollback_transaction($connection) {
    return mysqli_rollback($connection);
}

// Execute a query with automatic transaction handling
// Returns callback result on success, false on failure
function execute_transaction($connection, $callback) {
    try {
        if (!begin_transaction($connection)) {
            return false;
        }
        
        $result = $callback($connection);
        
        if ($result === false) {
            rollback_transaction($connection);
            return false;
        }
        
        if (!commit_transaction($connection)) {
            rollback_transaction($connection);
            return false;
        }
        
        return $result;
    } catch (Exception $e) {
        rollback_transaction($connection);
        error_log("Transaction error: " . $e->getMessage());
        return false;
    }
}
?>
