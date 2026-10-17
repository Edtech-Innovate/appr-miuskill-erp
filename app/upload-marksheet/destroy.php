<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_GET['id']); // Secure cast to integer

    // 1. Fetch file_path from the database
    $stmt = $conn->prepare("SELECT file_path FROM marksheet_files WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['status' => 404, 'message' => 'Marksheet file not found.']);
        exit;
    }

    $row = $res->fetch_assoc();
    $filePath = '../../uploads/upload-marksheets/' . basename($row['file_path']); // Ensure safe path

    // 2. Attempt to delete file from filesystem

    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // 3. Delete from database
    $deleteStmt = $conn->prepare("DELETE FROM marksheet_files WHERE ID = ?");
    $deleteStmt->bind_param("i", $id);
    if ($deleteStmt->execute()) {
        echo json_encode(['status' => 200, 'message' => 'Marksheet file deleted successfully.']);
    } else {
        echo json_encode(['status' => 500, 'message' => 'Failed to delete from database.']);
    }

    $stmt->close();
    $deleteStmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 405, 'message' => 'Invalid request method.']);
}
