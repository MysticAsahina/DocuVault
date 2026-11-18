<?php
session_start();
include '../../../phpFunctions/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    header("Location: ../../index.php");
    exit();
}

// Check role access (admin or superadmin only)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../../index.php");
    exit();
}


// Fetch analytics data 
function fetchAnalyticsData($conn) {
    try {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM files WHERE archived = 1) AS total_archives,
                (SELECT COUNT(*) FROM files WHERE status = 'pending') AS pending_approvals,
                (SELECT COUNT(*) FROM student) AS total_students";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Get analytics data
$analyticsData = fetchAnalyticsData($conn);
$totalFiles = $analyticsData['total_archives'];
$pendingApprovals = $analyticsData['pending_approvals'];
$totalStudents = $analyticsData['total_students'];

// Set the query to fetch files that are archived (Archived = 1)
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$sql = "SELECT * FROM files WHERE Archived = 1"; 

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
    <title>Archive - Admin</title>
    <link rel="stylesheet" href="../../css/Archive.css">
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
                        <h5 class="card-title">Archived Documents</h5>
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
                        <h5 class="card-title">Students</h5>
                        <p class="card-text"><?php echo $totalStudents; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <div class="files" style="width: 65%;">
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

<!-- TODO : Update Detailscon when selecting differeng list -->
