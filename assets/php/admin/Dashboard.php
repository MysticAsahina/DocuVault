<?php
session_start();
include '../../../phpFunctions/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    header("Location: ../index.php");
    exit();
}

// Check role access (admin or superadmin only)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../index.php");
    exit();
}

// Fetch analytics data 
function fetchAnalyticsData($conn) {
    try {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM files WHERE archived=0 AND status = 'approved') AS total_documents,
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
$totalFiles = $analyticsData['total_documents'];
$pendingApprovals = $analyticsData['pending_approvals'];
$totalArchives = $analyticsData['total_archives'];

// File Upload Processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']['name']) && isset($_POST['student_ID'])) {
    $studentID = $_POST['student_ID'];
    $fileName = basename($_FILES['file']['name']);
    $uploadDir = "../../../uploads/pending/$studentID/";

    // Create folder if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Ensure folder exists before proceeding
    if (!is_dir($uploadDir)) {
        die(json_encode(["status" => "error", "message" => "Failed to create student folder."]));
    }

    $targetFile = realpath($uploadDir) . DIRECTORY_SEPARATOR . $fileName;

    // Move uploaded file
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO files (file, student_ID, uploaded_by, status, archived) VALUES (?, ?, ?, 'approved', 0)");
        $stmt->execute([$fileName, $studentID, $_SESSION['username']]);

        echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
        exit();
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$sql = "SELECT * FROM files WHERE Archived = 0 AND status = 'approved'";

$params = [];

if (!empty($search)) {
    $sql .= " AND (File LIKE ? OR Uploaded_By LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);

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
        <title>Dashboard - Admin</title>
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
                            <p class="card-text" id="total-documents"><?php echo $totalFiles; ?></p>
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
                            <i class="fa-solid fa-plus"></i> Upload File
                        </button>
                    </div>
                    <div id="file-list">
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php $file_id = $row['File_ID']; ?>
                            <div class="file-item d-flex align-items-center p-2 border rounded mb-2" 
                                data-id="<?= $file_id; ?>";
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
                                        <a class="dropdown-item archive-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($file_name); ?>">Archive</a>
                                        <a class="dropdown-item delete-btn" href="#" data-id="<?= $file_id; ?>" data-file="<?= htmlspecialchars($file_name); ?>">Delete</a>
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
        
        <form id="upload-form" class="upload-wrapper" style="display: none;">
            <h2>Upload Files</h2>
            <p>Click or drag & drop below to upload the files</p>
            
            <div class="upload-zone">
                <div class="icon bi bi-cloud-upload"></div>
                <p class="file-name">No File Uploaded</p>
                <p class="file-size">0.00 KB</p>
                <input type="file" id="input-file" name="file" accept="image/*" required>
            </div>

            <div class="student-search-container">
                <input type="text" id="student-search" class="form-control" placeholder="Search student...">
                <input type="hidden" id="selected-student" name="student_ID"> 

                <div class="table-container" style="max-height: 200px; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                            </tr>
                        </thead>
                        <tbody id="student-table-body"></tbody>
                    </table>
                </div>
            </div>

            <div class="action-btns">
                <button type="reset" id="reset-btn">Cancel</button>
                <button type="submit" id="convert-btn">Upload</button>
            </div>
        </form>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        <script src="../../js/DashboardAdmin.js?v=<?php echo time(); ?>"></script>
    </body>

</html>

<!-- Todo:  -->
