<?php

include 'conn.php';

$username = $_POST['username'];
$useremail = $_POST['useremail'];
$role = $_POST['role'];
$password = md5($_POST['password']); // Hash the password using md5

$stmt = $conn->prepare("INSERT INTO tbluser (username, useremail, role, userpassword) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $useremail, $role, $password);

if ($stmt->execute() === TRUE) {
    header("Location: admin.php?status=success&message=Admin added successfully!");
} else {
    if ($stmt->errno == 1062) { // Duplicate entry error code
        header("Location: admin.php?status=error&message=Duplicate entry. Admin already exists.");
    } else {
        header("Location: admin.php?status=error&message=Error adding admin: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>