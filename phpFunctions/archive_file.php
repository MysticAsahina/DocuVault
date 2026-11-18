<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id']; 

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM files WHERE File_ID = :file_id");
    $stmt->bindParam(":file_id", $file_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'File not found in the database.']);
    } else {
        // If file found, update the status to 'archived'
        $stmt = $conn->prepare("UPDATE files SET Archived = 1 WHERE File_ID = :file_id");
        $stmt->bindParam(":file_id", $file_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'File archived successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to archive file.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
