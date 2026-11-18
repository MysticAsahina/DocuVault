<?php
session_start();
include 'connect.php'; // Ensure this path is correct

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $studentId = $data['student_ID'] ?? '';

    if (empty($studentId)) {
        echo json_encode(['error' => 'Student ID is required.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM student WHERE Student_ID = ?");
        $stmt->execute([$studentId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
        } else {
            echo json_encode(['error' => 'Student not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
