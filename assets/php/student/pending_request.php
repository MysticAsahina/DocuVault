<?php
session_start();
include '../../../phpFunctions/connect.php';

// Check if user is logged in as student
if (!isset($_SESSION['student_id']) || !isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}

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
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileSize = $_FILES['file']['size'];

    $uploadDir = "../../../uploads/approved/$studentID/";

    // Create folder if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetFile = realpath($uploadDir) . DIRECTORY_SEPARATOR . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO files (file, file_type, size, student_ID, uploaded_by, status, archived) VALUES (?, ?, ?, ?, ?, 'pending', 0)");
        $stmt->execute([$fileName, $fileType, $fileSize, $studentID, $username]);

        echo json_encode(["status" => "success", "message" => "File uploaded successfully. Waiting for approval."]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
        exit();
    }
}

// Fetch student's non-archived files
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$sql = "SELECT file, file_type, size, status FROM files WHERE student_ID = ? AND Archived = 0";
$params = [$studentID];

if (!empty($search)) {
    $sql .= " AND (file LIKE ? OR uploaded_by LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <title>HOME</title>
        <link rel="stylesheet" href="../../css/pending_request.css">
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

            <!-- Status Counter -->
            <div class="row mb-4" style="padding-top: 50px;">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Documents</h5>
                            <p class="card-text"><?php echo $totalFiles; ?></p>
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
                            <h5 class="card-title">Archived Files</h5>
                            <p class="card-text"><?php echo $studentArchive; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <div class="files" style="width: 65%;">
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary" id="plus">
                            <i class="fa-solid fa-plus"></i> Send File
                        </button>
                    </div>

                    <!-- ✅ FILE LIST TABLE -->
                    <div id="file-list">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>File Type</th>
                                    <th>Size (KB)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($files as $file) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($file['file']); ?></td>
                                        <td><?php echo htmlspecialchars($file['file_type']); ?></td>
                                        <td><?php echo round($file['size'] / 1024, 2); ?> KB</td>
                                        <td><?php echo ucfirst($file['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
            
        <!-- this form is the one for sending files to the admin -->
        <form id="upload-form" class="upload-wrapper" style="display: none;">
            <h2>Upload Files</h2>
            <p>Click or drag & drop below to upload the files</p>
            
            <div class="upload-zone">
                <div class="icon bi bi-cloud-upload"></div>
                <p class="file-name">No File Uploaded</p>
                <p class="file-size">0.00 KB</p>
                <input type="file" id="input-file" name="file" accept="image/*" required>
            </div>

            <div class="action-btns">
                <button type="reset" id="reset-btn">Cancel</button>
                <button type="submit" id="convert-btn">Upload</button>
            </div>
        </form>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        <script src="../../js/pending_request.js"></script>
    </body>

</html>

