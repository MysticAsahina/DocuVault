<?php
session_start();
require_once 'connect.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit;
}

$studentID = $_SESSION['student_id']; 

// Check if a file was uploaded
if (!isset($_FILES['file'])) {
    echo json_encode(["status" => "error", "message" => "No file uploaded"]);
    exit;
}

$file = $_FILES['file'];
$fileName = basename($file['name']);
$fileSize = $file['size'];
$fileTmpPath = $file['tmp_name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'png', 'txt', 'mp4', 'webm'];

// Validate file type
if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(["status" => "error", "message" => "Invalid file type"]);
    exit;
}

// Define pending folder path for the student
$studentFolder = "../uploads/pending/$studentID/";

// Create student folder if it doesn't exist
if (!is_dir($studentFolder)) {
    mkdir($studentFolder, 0777, true);
}

$filePath = $studentFolder . time() . "_" . $fileName;

// Move file to the student's pending folder
if (!move_uploaded_file($fileTmpPath, $filePath)) {
    echo json_encode(["status" => "error", "message" => "File upload failed"]);
    exit;
}

// Insert file details into the database
try {
    $stmt = $conn->prepare("INSERT INTO files (file, file_type, size, student_ID, uploaded_by, status, archived) VALUES (?, ?, ?, ?, ?, 'pending', 0)");
    $stmt->execute([$fileName, $fileExt, $fileSize, $studentID, $studentID]);

    echo json_encode(["status" => "success", "message" => "File sent to admin for approval"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
