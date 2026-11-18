<?php
require_once 'connect.php';

if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM student WHERE Student_ID = :studentId");
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $stmt->execute();

        $student = $stmt->fetch();

        if ($student) {
            echo json_encode($student);
        } else {
            echo json_encode(["error" => "Student not found."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "No student ID provided."]);
}
?>