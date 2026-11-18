<?php
require_once 'connect.php';

header('Content-Type: application/json');

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT File_ID, File, File_Type, Size, Status 
                            FROM files 
                            WHERE Status IN ('pending', 'rejected') 
                            ORDER BY Status DESC, File_ID DESC");
    $stmt->execute();

    // Fetch all results as an associative array
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($files);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
