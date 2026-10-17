<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
$id = $_POST['course_id'];
$option ='';
$sub_course = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE Course_ID = $id AND Status = 1");
if ($sub_course->num_rows == 0) {
    $option = "<option value =''>No Sub-Course Found!</option>";
} else {
    while ($row = $sub_course->fetch_assoc()) {
        $option .= "<option value ='". $row['ID'] . "'>" . ucwords(strtolower($row['Name'])) . "</option>";
    }
}
echo $option;


