<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
session_start();

$html = '';
if (isset($_POST['edit']) && $_POST['edit'] == "edit") {
    $sub_course_id = $_POST['sub_course_id'];
    $sqlQuery = " AND ID = $sub_course_id ";
} else {
    $subCourseId = intval($_POST['id']);
    $sqlQuery = " AND ID = $subCourseId ";
}

$sql = "SELECT Name, Min_Duration FROM Sub_Courses WHERE  Status = 1 $sqlQuery";

$result = $conn->query($sql);

$html = '<option value="">Select Duration</option>';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $duration = intval($row['Min_Duration']);
    for ($i = 1; $i <= $duration; $i++) {
        $html .= '<option value="' . $i . '" >' . $i . '</option>';
    }
}

echo $html;