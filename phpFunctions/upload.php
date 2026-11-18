<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_ID']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit();
}

// Check if form was submitted properly
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file']['name']) || empty($_POST['student_ID'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request. Missing required data."]);
    exit();
}

$student_ID = $_POST['student_ID'];
$uploaded_by = $_SESSION['username'] ?? 'admin';
$role = $_SESSION['role'];
$status = ($role === 'admin') ? 'approved' : 'pending';

$targetDir = "C:/xampp/htdocs/DocuVaultFinals/uploads/approved/" . $student_ID . "/";

// Create directory if it doesn't exist
if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
    echo json_encode(["status" => "error", "message" => "Failed to create student folder."]);
    exit();
}

$fileName = basename($_FILES['file']['name']);
$targetFilePath = $targetDir . $fileName;

// Move the uploaded file
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
    // Calculate the file size in bytes
    $fileSize = filesize($targetFilePath);

    try {
        // Insert file details into the database, including the file size
        $stmt = $conn->prepare("INSERT INTO files (File, Uploaded_By, Student_ID, Status, Size) VALUES (?, ?, ?, ?, ?)"); 
        $stmt->execute([$fileName, $uploaded_by, $student_ID, $status, $fileSize]);

        echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "File upload failed."]);
}
?>
