<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
// require '../../includes/helpers.php';

function getSubCourses($conn, $course_id)
{
  if (isset($course_id) && !empty($course_id)) {
    $getSubCourse_sql = $conn->query("SELECT ID,Name FROM Sub_Courses WHERE Status =1 AND Course_ID = $course_id order by Name ASC");
    $options = '';
    $row = [];
    while ($row = $getSubCourse_sql->fetch_assoc()) {
      $options .= '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
    }
    return $options;
  } else {
    return "<option value=''>No Sub-Course Found.</option>";
  }
}
if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['type'] == 'Course') {
    // subcourse
    $course_id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $option = "<option value=''>Choose Sub-Course</option>";
    $option .= getSubCourses($conn, $course_id);
    echo $option; die;

} else if (isset($_GET['course_id']) && !empty($_GET['course_id']) && isset($_GET['sub_course_id']) && !empty($_GET['sub_course_id']) && $_GET['type'] == 'Semester') {
    // duration 
    $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : NULL;
    $sub_course_id = isset($_GET['sub_course_id']) ? $_GET['sub_course_id'] : NULL;
    $duration_sql = $conn->query("SELECT Min_Duration From Sub_Courses WHERE  ID = $sub_course_id AND Course_ID = $course_id AND  Status=1");
    $sem = 0;
    if ($duration_sql->num_rows > 0) {
        $row = $duration_sql->fetch_assoc();
        //$sem = $duration['Min_Duration'];
        if (is_numeric($row['Min_Duration']) == 1) {
             $sem = intval($row['Min_Duration']);
        } else {
           
            $sem = json_decode($row['Min_Duration'], true)[0];
        }
    }
    $options = '';
    if ($sem == 0) {
        $options = "<option value =''>No Record Found</option>";
    } else {
        for ($i = 1; $i <= $sem; $i++) {
            $options .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }
    echo $options;

} else {
    echo "<option value=''>No Record Found</option>";
}