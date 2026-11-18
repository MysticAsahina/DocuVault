<?php
session_start();
include 'connect.php';

// Ensure database connection is established
if (!isset($conn)) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check role access (admin or superadmin only)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if the file_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id'];

    // Prepare SQL to update Archived status to 0
    try {
        $stmt = $conn->prepare("UPDATE files SET Archived = 0 WHERE File_ID = :file_id");
        $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if any rows were affected (i.e., file was updated)
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'File restored successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'File not found or already restored.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
