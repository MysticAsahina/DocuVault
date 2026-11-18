<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Bar</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Topbar styling */
        .topbar {
            background: linear-gradient(135deg, rgb(71, 82, 129),rgb(38, 71, 128));
            padding: 10px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 800;
        }

        /* User icon styling */
        .user-icon {
            font-size: 40px;
            color: white;
            transition: transform 0.3s ease, color 0.3s ease;
            cursor: pointer;
        }

        .user-icon:hover {
            transform: scale(1.1);
            color: #ffdd57;
        }

        /* Username styling */
        .username {
            font-size: 16px;
            font-weight: 500;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-left: 10px;
        }

        /* Dropdown menu styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            font-size: 14px;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            display: block;
            transition: background 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f9fafb;
        }

        .dropdown-divider {
            border-top: 1px solid #e5e7eb;
            margin: 5px 0;
        }
        
    </style>
</head>
<body>
    <!-- Top Bar -->
    <nav class="topbar">
        <!-- User Icon and Username -->
        <div class="dropdown">
            <div class="d-flex align-items-center" id="userDropdown" onclick="toggleDropdown(event)">
                <i class="fa-solid fa-circle-user user-icon"></i>
                <span class="username">
                    <?php
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'student') {
                            echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
                        } elseif ($_SESSION['role'] == 'admin') {
                            echo 'Admin';
                        } elseif ($_SESSION['role'] == 'superadmin') {
                            echo 'Super Admin';
                        }
                    } else {
                        echo 'Guest';
                    }
                    ?>
                </span>
            </div>
            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a>
                <a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> Settings</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- JavaScript for Dropdown Toggle -->
    <script>
        // Function to toggle the dropdown menu
        function toggleDropdown(event) {
            event.stopPropagation(); // Prevent the click from bubbling up
            const dropdownMenu = document.getElementById('dropdownMenu');
            dropdownMenu.classList.toggle('show');
        }

        // Close the dropdown if the user clicks outside of it
        window.addEventListener('click', function (event) {
            const dropdownMenu = document.getElementById('dropdownMenu');
            const userDropdown = document.getElementById('userDropdown');

            if (!userDropdown.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
</body>
</html>