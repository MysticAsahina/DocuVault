<?php
session_start();

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

include 'conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_ID = $data['user_ID'];

if (empty($user_ID)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
    exit();
}

$sql = "DELETE FROM tbluser WHERE user_ID='$user_ID'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Admin deleted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting admin: " . $conn->error]);
}

$conn->close();
?>