<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include '../../includes/db-config.php';
include '../../includes/ClassHelper.php';
session_start();



$userQuery = '';
if ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Sub-Center") {
    $userQuery = " AND Added_For = " . $_SESSION['ID'];
}

$filterBySubCourse = "";
if (isset($_SESSION['filterBySubCourses'])) {
    $filterBySubCourse = $_SESSION['filterBySubCourses'];
}

$filterByDuration = "";
if (isset($_SESSION['filterByDuration'])) {
    $filterByDuration = $_SESSION['filterByDuration'];
}

$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
  $filterByVerticalType = $_SESSION['filterByVerticalType'];
}
$search_value = "";

if (isset($_GET['search'])) {
    $search_value = mysqli_real_escape_string($conn, $_GET['search']); // Search value
}

$searchQuery = " ";
if ($search_value != '') {
    if (!empty(strpos($search_value, "="))) {
        $search = explode("=", $search_value);
        $searchBy = trim($search[0]);
        $values = array_key_exists(1, $search) && !empty($search[1]) ? explode(" ", $search[1]) : array();
        $values = array_filter($values);
        if (!empty($values)) {
            $student_id_column = $_SESSION['student_id'] == 1 ? 'Students.Unique_ID' : "RIGHT(CONCAT('000000', Students.ID), 6)";
            $column = strcasecmp($searchBy, 'student id') == 0 ? $student_id_column : (strcasecmp($searchBy, 'enrollment') == 0 ? 'Students.Enrollment_No' : (strcasecmp($searchBy, 'oa number') == 0 ? 'OA_Number' : ''));
            if (!empty($column)) {
                $values = "'" . implode("','", $values) . "'";
                $searchQuery = " AND $column IN ($values)";
            }
        }
    } elseif (strcasecmp($search_value, 'completed') == 0) {
        $searchQuery = " AND Step = 4 ";
    } else {
        $searchQuery = " AND (Students.ID like '%" . $search_value . "%' OR Students.Unique_ID like '%" . $search_value . "%' OR Students.Enrollment_No like '%" . $search_value . "%' OR  Students.First_Name like '%" . $search_value . "%' OR Students.Middle_Name like '%" . $search_value . "%' OR Students.Last_Name like '%" . $search_value . "%' OR Students.Step like '%" . $search_value . "%' OR Students.Father_Name like '%" . $search_value . "%' OR Courses.Name like '%" . $search_value . "%' OR Sub_Courses.Name  like '%" . $search_value . "%' OR Sub_Courses.Short_Name like '%" . $search_value . "%')";
    }
}

// echo "<pre>"; print_r($searchQuery);die;
$examTypeSql = ($_SESSION['university_id'] == "48") ? " AND Exam_Type= 1" : "";

$filterByUniversity = " AND Students.University_ID =" . $_SESSION['university_id'] . " AND Students.Enrollment_No IS NOT NULL";
$role_query = str_replace('{{ table }}', 'Students', isset($_SESSION['RoleQuery']) ? $_SESSION['RoleQuery'] : '');
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$conditionsQr = $userQuery . $role_query . $filterByUniversity . $examTypeSql . $filterBySubCourse . $searchQuery.$filterByDuration.$filterByVerticalType;

 $result_record = "SELECT Max_Marks,Min_Marks, marksheets.obt_marks_ext,marksheets.obt_marks_int,Syllabi.Code as subject_code, Syllabi.Name as subject_name,Students.University_ID, Students.Course_Category, Students.Duration, Students.Enrollment_No, CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS full_name, Sub_Courses.Name AS sub_course_name,Courses.Short_Name as course_short_name, Users.Code, Users.Name as user_name, Users.Code as user_code FROM marksheets  LEFT JOIN Students on marksheets.enrollment_no = Students.Enrollment_No LEFT JOIN Users on Students.Added_For= Users.ID  LEFT JOIN Syllabi on marksheets.subject_id = Syllabi.ID  LEFT JOIN Courses on Syllabi.Course_ID = Courses.ID LEFT JOIN Sub_Courses on Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE 1=1  $conditionsQr ORDER BY Students.ID DESC";

$sqldata = mysqli_query($conn, $result_record);

if ($sqldata->num_rows > 0) {

    $randomNumber = rand(1, 10000000000);
    $filename = "Internal-Marks" . $randomNumber . ".csv";

    header("Content-Disposition: attachment; filename=" . $filename . "");
    header("Content-Type: text/csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $fp = fopen('php://output', 'w');
    $exam_exit_status = "1";
    $h = [];
    $flag = false;
    // $h[] = "Sr. No.\t";
    $h[] = "Enrollment No.\t";
    $h[] = "Student Name\t";
    $h[] = "Duration\t";
    $h[] = "Subject Name\t";
    $h[] = "Subject Code\t";
    $h[] = "Max Marks \t";
    $h[] = "Min Marks \t";
    $h[] = "Internal Marks \t";
    $h[] = "Sub-Course Name\t";
    $h[] = "Centre Name \t";
    fputcsv($fp, $h);
    $nums = 1;
    // Instantiate the class
    $duration = new ClassHelper();
    while ($row = $sqldata->fetch_assoc()) {

        if ($row['University_ID'] == 48) {
            $max_marks = $row['Max_Marks'];
            $min_marks = ($row['Max_Marks']) * 40 / 100;
        } else {
            $max_marks = $row['Min_Marks'];
            $min_marks = ($row['Min_Marks']) * 40 / 100;
        }

        $data = [];
        // $data[] = $nums;
        $data[] = $row['Enrollment_No'];
        $data[] = $row['full_name'];
        $data[] = $duration->getDurationFunc($row['Duration'], $row['Course_Category'], $row['University_ID']);
        $data[] = $row['subject_name'];
        $data[] = $row['subject_code'];
        $data[] = round($max_marks);
        $data[] = round($min_marks);
        $data[] = ($_SESSION['university_id'] == 48) ? $row['obt_marks_ext'] : $row['obt_marks_int'];
        $data[] = $row['sub_course_name'];
        $data[] = $row['user_name'] . "(" . $row['Code'] . ")";

        fputcsv($fp, $data);
        $nums++;
    }

    fclose($fp);
} else {
    echo "No data found";
}
