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
    $updatedUsername = $_POST['username'];
    $updatedEmail = $_POST['email'];

    if (isset($_SESSION['user_ID'])) {
        $updateSql = "UPDATE tbluser SET username = ?, useremail = ? WHERE user_ID = ?";
    } else {
        $updateSql = "UPDATE student SET first_name = ?, email = ? WHERE student_id = ?";
    }

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssi", $updatedUsername, $updatedEmail, $user_id);
    
    if ($updateStmt->execute()) {
        $_SESSION['username'] = $updatedUsername;
        $_SESSION['useremail'] = $updatedEmail;

        echo "<script>window.location.href = 'profile.php';</script>"; 
        exit();
    } else {
        $error = "Error updating profile.";
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body style="margin-top: 100px;" class="bg-gray-100 font-sans">
<?php  include 'includes/sidebar.php'; ?>
<?php  include 'includes/topbar.php'; ?>
    <!-- Main Content -->
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Profile</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <!-- Username Field -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? $user['first_name']); ?>" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['useremail'] ?? $user['email']); ?>" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Save Changes Button -->
            <div class="mt-6">
                <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</body>
</html>
