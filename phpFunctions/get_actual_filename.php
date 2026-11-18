<?php
include 'connect.php';

$file_id = $_GET['file_id'] ?? null;

if ($file_id) {
    $stmt = $conn->prepare("SELECT File FROM files WHERE File_ID = ?");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        echo json_encode(["file_name" => $file['File']]);
    } else {
        echo json_encode(["file_name" => null]);
    }
}
?>
