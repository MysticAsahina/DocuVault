<?php
session_start();
include '../../phpFunctions/connect.php';

// Function to validate student login
function validateStudentLogin($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT Student_ID, Email, Password, First_Name, Last_Name FROM student WHERE Email = ?");
    $stmt->execute([$username]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && md5($password) === $student['Password']) {
        return [
            'role' => 'student',
            'data' => [
                'student_ID' => $student['Student_ID'],
                'email' => $student['Email'],
                'name' => $student['First_Name'] . ' ' . $student['Last_Name']
            ]
        ];
    }
    return "Invalid student username or password.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $loginResult = validateStudentLogin($username, $password, $conn);

        if (is_array($loginResult)) {
            session_regenerate_id(true);
            // CORRECTED SESSION VARIABLES
            $_SESSION['student_id'] = $loginResult['data']['student_ID'];

            $_SESSION['user_email'] = $loginResult['data']['email'];
            $_SESSION['user_name'] = $loginResult['data']['name'];
            $_SESSION['role'] = 'student';

            // Set username as either email or combined name
            $_SESSION['username'] = $loginResult['data']['email'];

            header("Location: student/Dashboard.php");
            exit();
        } else {
            $error = $loginResult;
        }
    }
}

$conn = null;
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
            <h1>Sign Up</h1>
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
