<?php
require '../../includes/db-config.php'; // DB connection

header('Content-Type: application/json');

if (!isset($_POST['field']) || !isset($_POST['id'])) {
    echo json_encode([
        'status' => 400,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$field = $_POST['field'];
$id    = intval($_POST['id']);

// Allowed columns
$allowed_columns = ['receiving_date', 'dispatch_date', 'center_doc'];
if (!in_array($field, $allowed_columns)) {
    echo json_encode([
        'status' => 400,
        'message' => 'Invalid column name.'
    ]);
    exit;
}

$response_message = "";

// ================= FILE UPLOAD ==================
if ($field === 'center_doc') {
    if (!isset($_FILES['files'])) {
        echo json_encode([
            'status' => 400,
            'message' => 'No files uploaded.'
        ]);
        exit;
    }

    $uploadDir = "../../uploads/center_docs/"; 
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploaded_files = [];

    foreach ($_FILES['files']['name'] as $key => $filename) {
        $tmp_name = $_FILES['files']['tmp_name'][$key];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // only allow images and pdf
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (!in_array(strtolower($ext), $allowed_ext)) {
            continue; // skip invalid file
        }

        $new_name = uniqid("doc_") . "." . $ext;
        $path = $uploadDir . $new_name;

        if (move_uploaded_file($tmp_name, $path)) {
            $uploaded_files[] = $new_name;
        }
    }

    if (count($uploaded_files) > 0) {
        // Save file names (JSON encoded for multiple)
        $file_data = json_encode($uploaded_files);

        $stmt = $conn->prepare("UPDATE center_authorize SET center_doc = ? WHERE id = ?");
        $stmt->bind_param("si", $file_data, $id);

        if ($stmt->execute()) {
            $response_message = "Documents uploaded successfully!";
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Database Error: ' . $stmt->error
            ]);
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'No valid files uploaded.'
        ]);
        exit;
    }
} 

// ================= DATES + STATUS ==================
else {
    if (!isset($_POST['date'])) {
        echo json_encode([
            'status' => 400,
            'message' => 'Date value missing.'
        ]);
        exit;
    }

    $date_value = $_POST['date'];

    $stmt = $conn->prepare("UPDATE center_authorize SET `$field` = ? WHERE id = ?");
    $stmt->bind_param("si", $date_value, $id);

    if ($stmt->execute()) {
        // check receiving/dispatch
        $check = $conn->prepare("SELECT receiving_date, dispatch_date FROM center_authorize WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $check->close();

        $new_status = 'pending';
        if (!empty($result['receiving_date']) && empty($result['dispatch_date'])) {
            $new_status = 'received';
        } elseif (!empty($result['receiving_date']) && !empty($result['dispatch_date'])) {
            $new_status = 'dispatch';
        }

        $update_status = $conn->prepare("UPDATE center_authorize SET status = ? WHERE id = ?");
        $update_status->bind_param("si", $new_status, $id);
        $update_status->execute();
        $update_status->close();

        $response_message = ucfirst(str_replace("_", " ", $field)) . " updated successfully! Status changed to $new_status";
    } else {
        echo json_encode([
            'status' => 500,
            'message' => 'Database Error: ' . $stmt->error
        ]);
        exit;
    }

    $stmt->close();
}

$conn->close();

echo json_encode([
    'status' => 200,
    'message' => $response_message
]);
