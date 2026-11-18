<?php
session_start();

$mysqli = require __DIR__ . "/database.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $token = $_GET["token"];
    $token_hash = hash("sha256", $token);

    $sql = "SELECT * FROM student WHERE reset_token_hash = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user === null) {
        $errors[] = "Token not found.";
    }

    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        $errors[] = "Token has expired.";
    }

    $_SESSION["reset_token"] = $token;
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_SESSION["reset_token"] ?? null;
    if (!$token) {
        $errors[] = "Invalid request.";
    }

    $token_hash = hash("sha256", $token);

    $sql = "SELECT * FROM student WHERE reset_token_hash = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user === null) {
        $errors[] = "Token not found.";
    }

    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        $errors[] = "Token has expired.";
    }

    if (strlen($_POST["password"]) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (!preg_match("/[a-z]/i", $_POST["password"])) {
        $errors[] = "Password must contain at least one letter.";
    }

    if (!preg_match("/[0-9]/", $_POST["password"])) {
        $errors[] = "Password must contain at least one number.";
    }

    if ($_POST["password"] !== $_POST["password_confirmation"]) {
        $errors[] = "Passwords must match.";
    }

    if (empty($errors)) {
        $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

        $sql = "UPDATE student
                SET password = ?,
                    reset_token_hash = NULL,
                    reset_token_expires_at = NULL
                WHERE student_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $password_hash, $user["student_id"]);
        $stmt->execute();

        unset($_SESSION["reset_token"]);

        $success = "Password updated. You can now login.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e0f7fa;
            font-family: Arial, sans-serif;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #00796b;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #00796b;
        }

        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #00796b;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #004d40;
        }

        .notification {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #c62828;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Reset Password</h1>

        <!-- Display success message -->
        <?php if (!empty($success)): ?>
            <div class="notification success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Display errors -->
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="notification error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Password reset form -->
        <?php if (empty($success)): ?>
            <form method="post" action="">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION["reset_token"] ?? '') ?>">
                <label for="password">New password</label>
                <input type="password" id="password" name="password">
                <label for="password_confirmation">Repeat password</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
                <button>Send</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>