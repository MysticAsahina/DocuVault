<?php
require_once 'connect.php';

header('Content-Type: application/json');

try {
    $query = "SELECT student_ID, first_name, last_name FROM student";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($students);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?> 