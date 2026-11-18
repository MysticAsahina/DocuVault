<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

// Get the file ID and status from the request
$file_id = $_POST['file_id'] ?? null;
$status = $_POST['status'] ?? null;

if ($file_id && $status) {
    try {
        // Update the status of the file
        $stmt = $conn->prepare("UPDATE files SET status = ? WHERE File_ID = ?");
        $stmt->execute([$status, $file_id]);

        echo json_encode(["status" => "success", "message" => "File status updated to '$status'."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
