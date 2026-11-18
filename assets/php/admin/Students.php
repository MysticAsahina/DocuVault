<?php
session_start();
include '../../../phpFunctions/connect.php';

if (!isset($_SESSION['user_ID'])) {
    header("Location: index.php");
    exit();
}

if (!$conn) {
    die("Database connection failed.");
}

// Fetch analytics data 
function fetchAnalyticsData($conn) {
    try {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM student) AS total_students,
                (SELECT COUNT(*) FROM files WHERE status = 'pending') AS pending_approvals,
                (SELECT COUNT(*) FROM files WHERE archived = 1) AS total_archives";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Get analytics data
$analyticsData = fetchAnalyticsData($conn);
$totalStudents = $analyticsData['total_students'];
$pendingApprovals = $analyticsData['pending_approvals'];
$totalArchives = $analyticsData['total_archives'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" href="../drawables/logo1.svg" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Students - Admin</title>
    <link rel="stylesheet" href="../../css/Students.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

    <body>
        <?php 
        include '../includes/sidebar.php';
        include '../includes/topbar.php'; 
        ?>

        <div class="container-fluid custom-width main-content">
            <form method="GET">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search..." id="search-input">
                </div>
            </form>

            <div class="row mb-4" style="padding-top: 50px;">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <p class="card-text" id="total-documents"><?php echo $totalStudents; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Approvals</h5>
                            <p class="card-text"><?php echo $pendingApprovals; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">File Archives</h5>
                            <p class="card-text"><?php echo $totalArchives; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <div class="files" style="width: 65%;">
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary" id="plus">
                            <i class="fa-solid fa-plus"></i> Add Student
                        </button>
                    </div>

                    <!-- Table to List Students -->
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Last Name</th>
                                <th>Middle Name</th>
                                <th>First Name</th>
                                <th>Student ID</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch students from the database
                            $stmt = $conn->prepare("SELECT * FROM student");
                            $stmt->execute();
                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($students as $student) {
                                echo "
                                <tr>
                                    <td>{$student['Last_Name']}</td>
                                    <td>{$student['Middle_Name']}</td>
                                    <td>{$student['First_Name']}</td>
                                    <td>{$student['Student_ID']}</td>
                                    <td>
                                        <button class='btn btn-sm btn-warning edit-btn' data-id='{$student['Student_ID']}'>
                                            <i class='fa-solid fa-pen-to-square'></i> Edit
                                        </button>
                                        <button class='btn btn-sm btn-danger delete-btn' data-id='{$student['Student_ID']}'>
                                            <i class='fa-solid fa-trash'></i> Delete
                                        </button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="detailscon" style="width: 20%;">
                    <div class="imgecon text-center">
                        <img id="image-preview" src="" alt="">
                    </div>
                    <div class="dtlstxtcon">
                        <p class="subt"><strong>Name</strong></p>
                        <p class="dettxt" id="name"></p>
                        <br>
                        <p class="subt"><strong>Type</strong></p>
                        <p class="dettxt" id="type"></p>
                        <br>
                        <p class="subt"><strong>Size</strong></p>
                        <p class="dettxt" id="size"></p>
                        <br>
                        <p class="subt"><strong>Uploaded By</strong></p>
                        <p class="dettxt" id="uploadedby"></p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Student Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" action="phpFunctions/add_student.php" method="POST">
                        <div class="mb-3">
                            <label for="stdntFN" class="form-label">First Name:</label>
                            <input type="text" class="form-control" id="stdntFN" name="first_name" placeholder="Enter Student First Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="stdntMN" class="form-label">Middle Name:</label>
                            <input type="text" class="form-control" id="stdntMN" name="middle_name" placeholder="Enter Student Middle Name">
                        </div>
                        <div class="mb-3">
                            <label for="stdntLN" class="form-label">Last Name:</label>
                            <input type="text" class="form-control" id="stdntLN" name="last_name" placeholder="Enter Student Last Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="stdntEML" class="form-label">PhinmaEd Mail:</label>
                            <input type="email" class="form-control" id="stdntEML" name="email" placeholder="Enter Student Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="stdntYRSEC" class="form-label">Year & Section:</label>
                            <input type="text" class="form-control" id="stdntYRSEC" name="year_section" placeholder="Enter Student Year & Section" required>
                        </div>
                        <div class="mb-3">
                            <label for="stdntID" class="form-label">Student ID:</label>
                            <input type="text" class="form-control" id="stdntID" name="student_id" placeholder="Enter Student ID" required>
                        </div>
                        <div class="mb-3">
                            <label for="stdntPWD" class="form-label">Password:</label>
                            <input type="password" name="password" id="password" required>
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

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="edit-form" action="phpFunctions/update_student.php" method="POST">
                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="editFirstName" class="form-label">First Name:</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" placeholder="Enter Student First Name" required>
                        </div>
                        <!-- Middle Name -->
                        <div class="mb-3">
                            <label for="editMiddleName" class="form-label">Middle Name:</label>
                            <input type="text" class="form-control" id="editMiddleName" name="middle_name" placeholder="Enter Student Middle Name">
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="editLastName" class="form-label">Last Name:</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" placeholder="Enter Student Last Name" required>
                        </div>
                        <!-- Student ID -->
                        <div class="mb-3">
                            <label for="editStudentId" class="form-label">Student ID:</label>
                            <input type="text" class="form-control" id="editStudentId" name="student_id" placeholder="Enter Student ID" required readonly>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">PhinmaEd Mail:</label>
                            <input type="email" class="form-control" id="editEmail" name="email" placeholder="Enter Student Email" required>
                        </div>
                        <!-- Year & Section -->
                        <div class="mb-3">
                            <label for="editYearSection" class="form-label">Year & Section:</label>
                            <input type="text" class="form-control" id="editYearSection" name="year_section" placeholder="Enter Student Year & Section" required>
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary">CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="/DocuVaultFinals/assets/js/StudentsAdmin.js?v=<?php echo time(); ?>"></script>

    

</body>
</html>
