<?php
require '../../includes/db-config.php';
session_start();
if (!empty($_POST)) {
    $id = strval($_POST['id']);
    $by = strval($_POST['by']);
    if ($by == 'users') {
        $user_id = $conn->query("SELECT Code FROM Users WHERE ID = '$id'");
        if ($user_id->num_rows > 0) {
            $codeArr = $user_id->fetch_assoc();
            $center_id = json_encode(trim($codeArr['Code']), JSON_UNESCAPED_SLASHES);
            $_SESSION['usersFilter'] = " AND JSON_CONTAINS(Syllabi.User_ID, '$center_id') ";
        }
    } else if ($by == 'sub_course') {
        $_SESSION['subCourseFilter'] = " AND Syllabi.Sub_Course_ID = " . $id;
    } else if ($by == 'duration') {
        $_SESSION['durationFilter'] = " AND Syllabi.Semester = '" . $id . "'";
    } else if ($by == 'subjects') {
        $_SESSION['durationFilter'] = " AND Syllabi.Exam_Type = '" . $id . "'";
    }
    echo json_encode(['status' => true]);

} else {
    echo json_encode(['status' => false]);
}
// echo "<pre>"; 
// print_r($_SESSION);die;