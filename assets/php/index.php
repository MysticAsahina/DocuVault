<?php
session_start();
include '../../phpFunctions/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in the `admins` table
    $stmt = $conn->prepare("SELECT * FROM admins WHERE Admin_Email = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && md5($password) === $admin['Admin_Pass']) {
        $_SESSION['user_ID'] = $admin['Admin_ID'];
        $_SESSION['role'] = 'admin';
        header("Location: admin/Dashboard.php");
        exit();
    }

    // If no match found
    $error = "Invalid username or password!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logo1.svg" type="image/x-icon">
    <title>DocuVault Sign In</title>
    <link rel="stylesheet" href="../css/index.css">
</head>

<body>
    <div class="left">
        <div class="contain">
            <img src="../drawables/sheesh2.svg" alt="DocuVault Logo">
            <div class="logo-container">
                <h2 class="logotxt">DocuVault</h2>
                <hr class="custom-line">
            </div>
            <h4 class="sub-logotxt">Document Management System</h4>
        </div>
    </div>
    <div class="right">
        <div class="contain2">
            <img src="../drawables/sheesh2.svg" alt="DocuVault Logo">
            <div class="logo-container2">
                <h2 class="logotxt">DocuVault</h2>
                <hr class="custom-line">
            </div>
            <h4 class="sub-logotxt">Document Management System</h4>
        </div>
        <img class="rghtimg" src="../drawables/bggg.svg" alt="Background Image">
        <div class="formcon">
            <h1>Admin</h1>
            <form method="post" action="">
                <div class="input-boxes">
                    <?php if (!empty($error)): ?>
                        <p class="error-message"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <div class="lbl">
                        <p>*</p><label class="Plabel1" for="userid">Email</label>
                    </div>
                    <div class="input-box">
                        <input id="userid" type="text" placeholder="Enter your Username" name="username" required>
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="lbl">
                        <p>*</p><label class="Plabel2" for="password">Password</label>
                    </div>
                    <div class="input-box">
                        <input id="password" type="password" placeholder="Enter your password" name="password" required>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div class="button">
                        <button type="submit">
                            Sign In <i class="fa fa-sign-in" aria-hidden="true"></i>
                        </button>
                        <a href="/assets/php-password-reset-main/forgot-password.php">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<!-- 
superadmin@example.com
admin123

admin@example.com
admin123

student1@example.com
student123

student2@example.com
student123

-->