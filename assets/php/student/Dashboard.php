<?php
session_start();
include '../../../phpFunctions/connect.php';

// Check if user is logged in as student
if (!isset($_SESSION['student_id']) || !isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}

// Get student information directly from session
$studentID = $_SESSION['student_id'];
$username = $_SESSION['username'];


// Fetch analytics data 
function fetchAnalyticsData($conn, $studentID) {
    try {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM files WHERE archived = 0 AND student_ID = ?) AS total_files,
                (SELECT COUNT(*) FROM files WHERE status = 'pending' AND student_ID = ?) AS pending_approvals,
                (SELECT COUNT(*) FROM files WHERE archived = 1 AND student_ID = ?) AS student_archive";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$studentID, $studentID, $studentID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Get analytics data
$analyticsData = fetchAnalyticsData($conn, $studentID);
$totalFiles = $analyticsData['total_files'];
$pendingApprovals = $analyticsData['pending_approvals'];
$studentArchive = $analyticsData['student_archive'];

// File Upload Processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']['name'])) {
    $fileName = basename($_FILES['file']['name']);
    $uploadDir = "../../../uploads/approved/$studentID/";

    // Create folder if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetFile = realpath($uploadDir) . DIRECTORY_SEPARATOR . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO files (file, student_ID, uploaded_by, status, archived) VALUES (?, ?, ?, 'pending', 0)");
        $stmt->execute([$fileName, $studentID, $username]);

        echo json_encode(["status" => "success", "message" => "File uploaded successfully. Waiting for approval."]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
        exit();
    }
}

// Fetch student's non-archived files
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$sql = "SELECT * FROM files WHERE student_ID = ? AND Archived = 0 AND Uploaded_By = 'admin'";


$params = [$studentID];

$params = [$studentID];

if (!empty($search)) {
    $sql .= " AND (file LIKE ? OR uploaded_by LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $conn->prepare($sql);
$stmt->execute([$studentID]);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/logo1.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../css/Dashboard.css">
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
                        <h5 class="card-title">Total Documents</h5>
                        <p class="card-text" id="total-archived-documents"><?php echo $totalFiles; ?></p>
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
                        <h5 class="card-title">Archived File</h5>
                        <p class="card-text"><?php echo $studentArchive; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <div class="files" style="width: 65%;">
                <div class="d-flex justify-content-end mb-4">
                </div>
                <div id="archived-file-list">
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php $file_id = $row['File_ID']; ?>
                        <div class="file-item d-flex align-items-center p-2 border rounded mb-2" 
                            data-id="<?= $file_id; ?>"
                            data-name="<?= htmlspecialchars($row['File']); ?>" 
                            data-type="<?= pathinfo($row['File'], PATHINFO_EXTENSION); ?>" 
                            data-size="<?= $row['Size'] ?? 0; ?>" 
                            data-uploader="<?= htmlspecialchars($row['Uploaded_By'] ?? 'Unknown'); ?>" 
                            data-path="<?= htmlspecialchars("/DocuVaultFinals/uploads/approved/" . $row['Student_ID'] . "/" . $row['File']); ?>">
                            
                            <span class="me-3"><i class="far fa-file-alt"></i></span>
                            <span class="flex-grow-1"><?= htmlspecialchars($row['File']); ?></span>
                            <span class="badge bg-secondary"><?= strtoupper(pathinfo($row['File'], PATHINFO_EXTENSION)); ?></span>
                            <a href="<?= $file_path; ?>" class="btn btn-link download-btn" download>
                                <i class="fas fa-download"></i>
                            </a>
                            <div class="dropdown ms-auto">
                                <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item restore-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($row['File']); ?>">Restore</a>
                                    <a class="dropdown-item delete-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($row['File']); ?>">Delete</a>
                                </div>
                            </div>
                        </div>

                        <?php $file_id = $row['File_ID']; ?>
                        <div class="file-item d-flex align-items-center p-2 border rounded mb-2" 
                            data-id="<?= $file_id; ?>"
                            data-name="<?= htmlspecialchars($row['File']); ?>" 
                            data-type="<?= pathinfo($row['File'], PATHINFO_EXTENSION); ?>" 
                            data-size="<?= $row['Size'] ?? 0; ?>" 
                            data-uploader="<?= htmlspecialchars($row['Uploaded_By'] ?? 'Unknown'); ?>" 
                            data-path="<?= htmlspecialchars("/DocuVaultFinals/uploads/approved/" . $row['Student_ID'] . "/" . $row['File']); ?>">

                            <span class="me-3"><i class="far fa-file-alt"></i></span>
                            <span class="flex-grow-1"><?= htmlspecialchars($row['File']); ?></span>
                            <span class="badge bg-secondary"><?= strtoupper(pathinfo($row['File'], PATHINFO_EXTENSION)); ?></span>
                            <a href="<?= $file_path; ?>" class="btn btn-link download-btn" download>
                                <i class="fas fa-download"></i>
                            </a>
                            <div class="dropdown ms-auto">
                                <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item restore-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($row['File']); ?>">Restore</a>
                                    <a class="dropdown-item delete-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($row['File']); ?>">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="../../js/Archive.js"></script>
</body>
</html>
