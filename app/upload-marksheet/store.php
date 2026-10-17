<?php
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_name']) && isset($_FILES["file_path"]["name"]) && $_FILES["file_path"]["name"] != '') {
        require '../../includes/db-config.php';
        session_start();

        $file_name = mysqli_real_escape_string($conn, $_POST['file_name']);

        // Allowed extensions & MIME types
        $allowedExts = ['pdf'];
        $allowedMime = ['application/pdf'];

        $extension = pathinfo($_FILES["file_path"]["name"], PATHINFO_EXTENSION);
        $mime = $_FILES["file_path"]["type"];
        $size = $_FILES["file_path"]["size"];
        $error = $_FILES["file_path"]["error"];

        // Validation
        if (!in_array(strtolower($extension), $allowedExts) || !in_array($mime, $allowedMime)) {
            echo json_encode(['status' => 400, 'message' => 'Only PDF files are allowed.']);
            exit();
        }

        if ($error !== 0) {
            echo json_encode(['status' => 400, 'message' => 'Upload error. Please try again.']);
            exit();
        }

        if ($size > 500 * 1024 * 1024) { // 500MB limit
            echo json_encode(['status' => 400, 'message' => 'File too large. Max size: 10MB']);
            exit();
        }

        // Prepare paths
        $temp = explode(".", $_FILES["file_path"]["name"]);
        $filename = $temp[0] . '_' . time() . '.' . end($temp);
        $uploadDir = "../../uploads/upload-marksheets/";
        $fullPath = $uploadDir . $filename;
        $dbPath = "uploads/upload-marksheets/" . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["file_path"]["tmp_name"], $fullPath)) {
            $stmt = $conn->prepare("INSERT INTO marksheet_files (file_name, file_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $file_name, $dbPath);

            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'Marksheet uploaded successfully!']);
            } else {
                echo json_encode(['status' => 500, 'message' => 'Database insert failed.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 400, 'message' => 'Failed to move uploaded file.']);
        }
    } else {
        echo json_encode(['status' => 403, 'message' => 'File name and file are mandatory.']);
    }
} else {
    echo json_encode(['status' => 405, 'message' => 'Invalid request method.']);
}
?>
