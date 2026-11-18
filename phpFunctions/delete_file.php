<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');

// Debugging: Log the request data
error_log("Received POST data: " . print_r($_POST, true));

// Check if user is authenticated
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if the request method is POST and file_id is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id']; // Use File_ID

    // Debugging: Log the file_id
    error_log("File ID received: " . $file_id);

    try {
        // Get file details from database
        $stmt = $conn->prepare("SELECT File, Student_ID FROM files WHERE File_ID = ?");
        $stmt->execute([$file_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'File not found in database.']);
            exit();
        }

        $file_name = $row['File'];
        $student_ID = $row['Student_ID'];
        $file_path = "C:/xampp/htdocs/DocuVaultFinals/uploads/approved/" . $student_ID . "/" . $file_name;

        // Debugging: Log the file path
        error_log("File path: " . $file_path);

        // Delete file from database
        $stmt = $conn->prepare("DELETE FROM files WHERE File_ID = ?");
        $stmt->execute([$file_id]);

        if ($stmt->rowCount() > 0) {
            // Delete actual file from server
            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    echo json_encode(['success' => true, 'message' => 'File deleted successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete file from server.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'File not found on server.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete file from database.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Debugging: Log the reason for invalid request
    error_log("Invalid request. Method: " . $_SERVER['REQUEST_METHOD'] . ", file_id: " . ($_POST['file_id'] ?? 'not set'));
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>