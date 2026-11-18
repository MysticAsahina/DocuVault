<?php
session_start();
include 'connect.php'; // Ensure this path is correct

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';

    if (empty($studentId)) {
        echo json_encode(['error' => 'Student ID is required.']);
        exit;
    }

    try {
        // Fetch student details for modal
        if (!isset($_POST['first_name'])) {
            $stmt = $conn->prepare("SELECT * FROM student WHERE Student_ID = ?");
            $stmt->execute([$studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                echo json_encode($student);
            } else {
                echo json_encode(['error' => 'Student not found.']);
            }
            exit;
        }

        // Handle update request
        $firstName = $_POST['first_name'] ?? '';
        $middleName = $_POST['middle_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $yearSection = $_POST['year_section'] ?? '';

        if (empty($firstName) || empty($lastName) || empty($email) || empty($yearSection)) {
            echo json_encode(['error' => 'All fields are required.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE student SET First_Name=?, Middle_Name=?, Last_Name=?, Email=?, Year_Section=? WHERE Student_ID=?");
        $stmt->execute([$firstName, $middleName, $lastName, $email, $yearSection, $studentId]);

        echo json_encode(['status' => 'success', 'message' => 'Student updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
