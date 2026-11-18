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
} else {
    $user_id = $_SESSION['student_id'];
    $sql = "SELECT * FROM student WHERE student_id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (md5($currentPassword) === $user['userpassword'] || md5($currentPassword) === $user['password']) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = md5($newPassword);

            if (isset($_SESSION['user_ID'])) {
                $updateSql = "UPDATE tbluser SET userpassword = ? WHERE user_ID = ?";
            } else {
                $updateSql = "UPDATE student SET password = ? WHERE student_id = ?";
            }

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $hashedPassword, $user_id);

            if ($updateStmt->execute()) {
                header("Location: change-password.php?success=true");
                exit();
            } else {
                $errorMessage = "Error updating password.";
            }

            $updateStmt->close();
        } else {
            $errorMessage = "New passwords do not match.";
        }
    } else {
        $errorMessage = "Current password is incorrect.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="bahay.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .form-section input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .form-section button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-section button:hover {
            background-color: #45a049;
        }

        .form-section .error, .form-section .success {
            color: red;
            font-size: 16px;
            margin: 10px 0;
        }

        .form-section .success {
            color: green;
        }
    </style>
</head>

<body>
    <?php  include 'includes/sidebar.php'; ?>
    <?php  include 'includes/topbar.php'; ?>
    <div class="container">
        <div class="form-section">
            <h2>Change Your Password</h2>

            <!-- Success Notification -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'true') { ?>
                <div class="success">Password changed successfully!</div>
            <?php } ?>

            <?php if (isset($errorMessage)) { ?>
                <div class="error"><?php echo $errorMessage; ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>
