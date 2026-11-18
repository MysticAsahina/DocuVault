<?php 
include '../../../phpFunctions/connect.php';
session_start();

if (!isset($_SESSION['user_ID']) && !isset($_SESSION['student_id'])) {
    header("Location: index.php"); 
    exit();
}

if (isset($_SESSION['user_ID'])) {
    $user_id = $_SESSION['user_ID'];
    $sql = "SELECT Admin_ID AS id, Admin_Name AS name, Admin_Email AS email FROM admins WHERE Admin_ID = ?";
} else {
    $user_id = $_SESSION['student_id'];
    $sql = "SELECT Student_ID AS id, CONCAT(First_Name, ' ', Last_Name) AS name, Email AS email FROM student WHERE Student_ID = ?";
}

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]); 

$user = $stmt->fetch(PDO::FETCH_ASSOC); 

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../css/profile.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
</head>

<body>
    <?php 
    include '../includes/sidebar.php'; 
    include '../includes/topbar.php';  
    ?>
    <div class="container">
        <div class="profile-header">
            <div class="profile-info">
                <div class="profile-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                
                <div class="profile-details">
                    <h1><?php echo htmlspecialchars($user['name'] ?? 'Unknown User'); ?></h1>
                    <p>Email: <?php echo htmlspecialchars($user['email'] ?? 'No Email'); ?></p>
                    <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p> 
                </div>
            </div>
           
            <a href="edit-profile.php" class="edit-button">Edit profile</a>
        </div>

        <div class="profile-section">
            <h2>Personal Information</h2>
            <p>Phone: </p>
            <p>Address: </p>
        </div>

        <div class="profile-section">
            <h2>Account Settings</h2>
            <p>Change your password, email, or privacy settings.</p>
            <a href="change-password.php" class="edit-button">Change Password</a>
        </div>

        <div class="profile-section">
            <h2>Recent Activity</h2>
            <p>No recent activity to show.</p>
        </div>
    </div>
</body>

</html>
