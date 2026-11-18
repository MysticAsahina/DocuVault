<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Custom Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            transform: translateX(-250px);
        }

        .logocon {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        .logoimg {
            width: 100%;
            max-width: 150px;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar .line {
            border-color: #34495e;
            margin: 10px 0;
        }

        /* Toggle Button Styling */
        .navbar-toggler {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background-color: #2c3e50;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            display: block;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                transform: translateX(-250px);
            }
        }
    </style>
</head>
<body>
    <!-- Toggle Button -->
    <button class="navbar-toggler" id="toggleButton">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logocon">
            <img class="logoimg" src="../../drawables/aulogo.svg" alt="Logo">
        </div>
        <div class="top">
            <?php
            // Determine the home link based on user role
            $homeLink = 'Dashboard.php';
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
                $homeLink = 'Dashboard.php';
            }
            ?>
            <a class="inactive" href="<?php echo $homeLink; ?>"><i class="fa-solid fa-house"></i>&nbsp;&nbsp;Home</a>
            
            <?php
            // Check if the user is an admin
            if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin')) {
                echo '<a class="inactive" href="Students.php"><i class="fa-solid fa-user-graduate"></i>&nbsp;&nbsp;Students</a>';
                echo '<a class="inactive" href="approval.php"><i class="fa-solid fa-box-archive"></i>&nbsp;&nbsp;Request</a>';
                echo '<a class="inactive" href="archive.php"><i class="fa-solid fa-box-archive"></i>&nbsp;&nbsp;Archive</a>';
                if ($_SESSION['role'] === 'superadmin') {
                    echo '<a class="inactive" href="admin.php"><i class="fa-solid fa-user-shield"></i>&nbsp;&nbsp;Admin</a>';
                }
            }
            // Check if the user is a student
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
                echo '<a class="inactive" href="pending_request.php"><i class="fa-solid fa-clock"></i>&nbsp;&nbsp;Pending Request</a>'; // New option for students
            }
            ?>
            
            <a class="inactive" href="about.php"><i class="fa-solid fa-circle-info"></i>&nbsp;&nbsp;About</a>
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin')): ?>
                <a class="inactive" href="reports.php"><i class="fa-regular fa-file"></i>&nbsp;&nbsp;Reports</a>
            <?php endif; ?>

          
        </div>
    </div>

    <!-- JavaScript for Toggle Button -->
    <script>
        const toggleButton = document.getElementById('toggleButton');
        const sidebar = document.getElementById('sidebar');

        // Collapse sidebar by default
        sidebar.classList.add('collapsed');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    </script>

</body>
</html>
