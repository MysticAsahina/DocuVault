<?php
session_start();

if (!isset($_SESSION['user_ID'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$useremail = $_SESSION['useremail'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" href="../images/logo1.svg" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>ADMINS</title>
    <style>
        .custom-width {
            max-width: 1300px; 
            margin-left: auto; 
            margin-right: auto; 
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
            overflow: hidden;
        }

        .dropdown-menu a {
            color: black;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .dropdown.show .dropdown-menu {
            display: block;
        }

        .list-group-item.active {
            background-color:rgb(193, 193, 194); /* Change to blue */
            color: white; /* Change text color to white */
            border-color:rgb(187, 189, 190);
        }

        .main-content {
            margin-top: 80px; 
        }
    </style>
</head>

<body>
    <?php include 'includes/topbar.php'; ?>

    <?php include 'includes/sidebar.php'; ?>

    <div class="container-fluid custom-width main-content">
        <div class="row">
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search.....">
                    </div>
                    <button class="btn btn-primary" id="addAdminButton">
                        <i class="fa-solid fa-plus"></i> Add Admin
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Admins</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Pending Approvals</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Admins</h5>
                                <h1 id="totalAdmins"></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            <p class="me-3">Username</p>
                            <p class="me-3">Email</p>
                            <p>Role</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php
                            include 'conn.php';

                            $sql = "SELECT user_ID, username, useremail, role FROM tbluser";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<div class='list-group-item list-group-item-action' data-username='" . $row['username'] . "' data-useremail='" . $row['useremail'] . "' data-role='" . $row['role'] . "' data-userid='" . $row['user_ID'] . "'>";
                                    echo "<div class='d-flex justify-content-between align-items-center'>";
                                    echo "<div class='d-flex'>";
                                    echo "<p class='me-3'>" . $row['username'] . "</p>";
                                    echo "<p class='me-3'>" . $row['useremail'] . "</p>";
                                    echo "<p>" . $row['role'] . "</p>";
                                    echo "</div>";
                                    echo "<div class='dropdown'>";
                                    echo "<button class='dropdown-button'>";
                                    echo "<i class='fa-solid fa-ellipsis-vertical'></i>";
                                    echo "</button>";
                                    echo "<div class='dropdown-menu'>";
                                    echo "<a href='#' class='edit-admin' data-userid='" . $row['user_ID'] . "'>Edit</a>";
                                    echo "<a href='#' class='delete-admin' data-userid='" . $row['user_ID'] . "'>Delete</a>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>No admins found in the database.</p>";
                            }

                            $conn->close();
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fa-solid fa-user fa-3x"></i>
                        </div>
                        <h5 class="card-title">Admin Details</h5>
                        <p class="card-text"><strong>Username:</strong> <span id="detailUsername"></span></p>
                        <p class="card-text"><strong>Email:</strong> <span id="detailUseremail"></span></p>
                        <p class="card-text"><strong>Role:</strong> <span id="detailRole"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Admin Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" action="insert_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="useremail" class="form-label">Email:</label>
                            <input type="text" class="form-control" id="useremail" name="useremail" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role:</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="Admin">Admin</option>
                                <option value="Super Admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary">CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Admin Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="edit-form" action="update_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="editUsername" name="username" placeholder="Enter Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUseremail" class="form-label">Email:</label>
                            <input type="text" class="form-control" id="editUseremail" name="useremail" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role:</label>
                            <input type="text" class="form-control" id="editRole" name="role" placeholder="Enter Role" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Enter Password">
                        </div>
                        <input type="hidden" id="editUserID" name="user_ID">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary">CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.dropdown-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation(); 
                const dropdownMenu = this.closest('.dropdown').querySelector('.dropdown-menu');
                dropdownMenu.classList.toggle('show');
            });
        });

        window.addEventListener('click', function () {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        });

        document.getElementById('searchInput').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const adminItems = document.querySelectorAll('.list-group-item');

            adminItems.forEach(item => {
                const username = item.dataset.username.toLowerCase();
                const useremail = item.dataset.useremail.toLowerCase();
                const role = item.dataset.role.toLowerCase();

                if (username.includes(filter) || useremail.includes(filter) || role.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const status = "<?php echo $_GET['status']; ?>";
            const message = "<?php echo $_GET['message']; ?>";

            if (status === 'success') {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                document.getElementById('successMessage').textContent = message;
                successModal.show();
            } else if (status === 'error') {
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                document.getElementById('errorMessage').textContent = message;
                errorModal.show();
            }

            // Remove URL parameters after displaying the notification
            const url = new URL(window.location);
            url.searchParams.delete('status');
            url.searchParams.delete('message');
            window.history.replaceState({}, document.title, url);
        });
        <?php endif; ?>

        document.getElementById('addAdminButton').addEventListener('click', function () {
            const addAdminModal = new bootstrap.Modal(document.getElementById('myModal'));
            addAdminModal.show();
            addAdminModal.addEventListener('hidden.bs.modal', function () {
                location.reload(); // Reload the page after closing the modal
            });
        });

        document.querySelectorAll('.edit-admin').forEach(editButton => {
            editButton.addEventListener('click', function (event) {
                event.preventDefault();
                const userId = this.dataset.userid;

                const adminRow = this.closest('.list-group-item');
                document.getElementById('editUsername').value = adminRow.dataset.username;
                document.getElementById('editUseremail').value = adminRow.dataset.useremail;
                document.getElementById('editRole').value = adminRow.dataset.role;
                document.getElementById('editUserID').value = adminRow.dataset.userid;
                document.getElementById('editPassword').value = ''; // Clear the password field

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
                editModal.addEventListener('hidden.bs.modal', function () {
                    location.reload(); // Reload the page after closing the modal
                });
            });
        });

        document.querySelectorAll('.delete-admin').forEach(deleteButton => {
            deleteButton.addEventListener('click', function (event) {
                event.preventDefault();
                const userId = this.dataset.userid;

                if (confirm('Are you sure you want to delete this admin?')) {
                    fetch('delete_admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_ID: userId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = 'admin.php?status=success&message=' + encodeURIComponent(data.message);
                        } else {
                            window.location.href = 'admin.php?status=error&message=' + encodeURIComponent(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = 'admin.php?status=error&message=' + encodeURIComponent('Error deleting admin.');
                    });
                }
            });
        });

        const adminRows = document.querySelectorAll('.list-group-item');
        adminRows.forEach(row => {
            row.addEventListener('click', function () {
                adminRows.forEach(r => r.classList.remove('active'));
                this.classList.add('active');

                document.getElementById('detailUsername').textContent = this.dataset.username;
                document.getElementById('detailUseremail').textContent = this.dataset.useremail;
                document.getElementById('detailRole').textContent = this.dataset.role;
            });
        });
    </script>
</body>

</html>