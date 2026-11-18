<?php
session_start();
include '../../../phpFunctions/connect.php';

if (!isset($_SESSION['user_ID']) && !isset($_SESSION['student_id'])) {
    header("Location: index.php");
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
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../images/logo1.svg" type="image/x-icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <title>Reports</title>
        <link rel="stylesheet" href="../../css/reports.css">
    </head>

    <body>
        <?php 
        include '../includes/sidebar.php';
        include '../includes/topbar.php'; 
        ?>

        <div class="container-fluid custom-width main-content">
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
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        var ctxBar = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Total Documents', 'Pending Approvals', 'Total Archives'],
                datasets: [{
                    label: 'File Statistics',
                    data: [<?php echo $totalFiles; ?>, <?php echo $pendingApprovals; ?>, <?php echo $totalArchives; ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
