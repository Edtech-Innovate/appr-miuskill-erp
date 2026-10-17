<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../includes/db-config.php';

    $id = $conn->real_escape_string($_POST['id']);


    if (isset($_FILES["file"]["name"]) && $_FILES["file"]["name"] != '') {
        if ($_FILES["file"]["size"] > 800 * 1024) { // 500KB
            echo json_encode(['status' => 400, 'message' => 'File size exceeds 500KB!']);
            exit();
        }
        $temp = explode(".", $_FILES["file"]["name"]);
        $filename = round(microtime(true)) . '.' . end($temp);
        $tempname = $_FILES["file"]["tmp_name"];
        $folder = "../../uploads/syllabus/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $filename = "/uploads/syllabus/" . $filename;
        } else {
            echo json_encode(['status' => 400, 'message' => 'Unable to save file!']);
            exit();
        }
    } else {
        $filename = isset($_POST['update_file']) ? $conn->real_escape_string($_POST['update_file']) : "";
    }

    $sql = "UPDATE Syllabi SET Syllabus = '" . $filename . "' WHERE ID = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 200, 'message' => 'Syllabus Uploaded successlly!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
}