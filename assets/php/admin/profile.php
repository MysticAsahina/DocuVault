<?php 

include 'conn.php';

session_start();

if (!isset($_SESSION['user_ID']) && !isset($_SESSION['student_id'])) {
    header("Location: index.php"); 
    exit();
}

if (isset($_SESSION['user_ID'])) {
    $user_id = $_SESSION['user_ID'];
    $sql = "SELECT * FROM tbluser WHERE user_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $user_id = $_SESSION['student_id'];
    $sql = "SELECT * FROM student WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="bahay.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
    <style>
      
       
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header h1 {
            font-size: 24px;
            font-weight: 500;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            color: white;
        }

        .profile-details {
            font-size: 16px;
            color: #555;
        }

        .profile-details p {
            margin: 5px 0;
        }

        .edit-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #45a049;
        }

        .profile-section {
            margin-top: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-section h2 {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .profile-section p {
            font-size: 16px;
            color: #777;
        }
    </style>
</head>

<body>
<?php  include 'includes/sidebar.php'; ?>
<?php  include 'includes/topbar.php'; ?>
    <div class="container">
        <div class="profile-header">
            <div class="profile-info">
                <div class="profile-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                
                <div class="profile-details">
                    <h1><?php echo htmlspecialchars($user['username'] ?? $user['first_name']); ?></h1>
                    <p>Email: <?php echo htmlspecialchars($user['useremail'] ?? $user['email']); ?></p>
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
