<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_ID'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access.']));
}

// Log received POST data for debugging
error_log(print_r($_POST, true));

// Retrieve form data
$firstName = $_POST['first_name'] ?? '';
$middleName = $_POST['middle_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$yearSection = $_POST['year_section'] ?? '';
$studentId = $_POST['student_id'] ?? '';
$password = $_POST['password'] ?? '';


// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($yearSection) || empty($studentId) || empty($password)) {
    die(json_encode(['status' => 'error', 'message' => 'All fields are required.']));
}

// Hash the password
$hashedPassword = md5($password);

try {
    // Check if Student_ID already exists
    $stmt = $conn->prepare("SELECT * FROM student WHERE Student_ID = ?");
    $stmt->execute([$studentId]);
    $existingStudent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingStudent) {
        die(json_encode(['status' => 'error', 'message' => 'Student ID already exists.']));
    }

    // Insert the new student
    $stmt = $conn->prepare("INSERT INTO student (Student_ID, First_Name, Middle_Name, Last_Name, Email, Year_Section, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$studentId, $firstName, $middleName, $lastName, $email, $yearSection, $hashedPassword]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Student added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add student.']);
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage()); // Log the error
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>