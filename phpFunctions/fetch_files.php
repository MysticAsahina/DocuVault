<?php
include 'connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$sql = "SELECT File_ID, File, Student_ID, Size, Uploaded_By 
        FROM files 
        WHERE Status = 'approved' AND Archived = 0";

$params = [];

if (!empty($search)) {
    $sql .= " AND (File LIKE ? OR Uploaded_By LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($files);
?>
