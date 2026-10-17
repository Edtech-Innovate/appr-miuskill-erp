<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
session_start();

$html = '';

$subCourseId = isset($_POST['id']) ? intval($_POST['id']) : '';
$sql = "SELECT Name, Min_Duration FROM Sub_Courses WHERE ID = $subCourseId AND Status = 1";
$result = $conn->query($sql);

$html = '<option value="">Select Duration</option>';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (is_numeric($row['Min_Duration']) == 1) {
        $duration = intval($row['Min_Duration']);

    } else {
        $duration = json_decode($row['Min_Duration'], true)[0];

    }
    for ($i = 1; $i <= $duration; $i++) {
        $html .= '<option value="' . $i . '" >' . $i . '</option>';
    }
}

echo $html;