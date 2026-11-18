<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "4585323_docuvault";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_ID = $_POST['user_ID'];
$username = $_POST['username'];
$useremail = $_POST['useremail'];
$role = $_POST['role'];

$sql = "UPDATE tbluser SET username='$username', useremail='$useremail', role='$role' WHERE user_ID='$user_ID'";

if ($conn->query($sql) === TRUE) {
    header("Location: admin.php?status=success&message=Admin updated successfully!");
} else {
    header("Location: admin.php?status=error&message=Error updating admin: " . $conn->error);
}
exit();
$conn->close();
?>