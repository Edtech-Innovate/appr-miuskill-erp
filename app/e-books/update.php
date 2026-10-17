<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
session_start();
// error_reporting(-1);
$allowedExts = array("pdf");
$id = intval($_POST['id']);

$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$course_id = intval($_POST['course_id']);
$sub_course_id = intval($_POST['sub_course_id']);
$duration = intval($_POST['duration']);
$subject_id = intval($_POST['subject_id']);
$file_path = "";
$file_type = $extension;
$created_by = 1;
$created_at = date("Y-m-d:H:i:s");
$title = $_POST['ebook_name'];
$updated_file_path = $_POST['updated_file_path'];

if (isset($_FILES["file"]["name"]) && $_FILES["file"]["name"] != '') {
    if (in_array($extension, $allowedExts) && ($_FILES["file"]["error"] == 0)) {
        $temp = explode(".", $_FILES["file"]["name"]);
        $filename = $temp[0] . '_' . time() . '.' . end($temp);
        $path = "../../uploads/e-books/" . $filename;
        $file_path = "uploads/e-books/" . $filename;
        move_uploaded_file(
            $_FILES["file"]["tmp_name"],
            $path
        );
    } else {
        echo json_encode(['status' => 400, 'message' => 'Invalid file type!! ']);
        exit();
    }
} else {
    $file_path = $updated_file_path;
    $file_type = explode('.', $updated_file_path)[1];
}


$update = $conn->query("UPDATE  `e_books` SET `course_id`=$course_id ,
                                                     `sub_course_id`=$sub_course_id,
                                                     `semester_id`= '" . $duration . "',
                                                      `subject_id`= $subject_id ,
                                                       `file_path`= '" . $file_path . "',
                                                        `file_type`=  '" . $file_type . "',
                                                        `title`='" . $title . "',
                                                         `created_by`= '" . $created_by . "',
                                                        `created_at` = '" . $created_at . "',
                                                        `University_ID`= '" . $_SESSION['university_id'] . "'  WHERE `id`= $id ");

if ($update) {
    echo json_encode(['status' => 200, 'message' => "E-book uploaded succefully!!"]);
} else {
    echo json_encode(['status' => 400, 'message' => 'Something went to wrong!!']);
}


