<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../includes/db-config.php';
    session_start();
    // Get and escape user inputs
    $id = $conn->real_escape_string($_POST['ID']);
    $coursetype = $conn->real_escape_string($_POST['coursetype']);
    $sub_course_type = $conn->real_escape_string($_POST['subcourse_id']);
    $semester = $conn->real_escape_string($_POST['seme']);
    $name = $conn->real_escape_string($_POST['subjectname']);
    $papertype = $conn->real_escape_string($_POST['paper_type']);
    $credit = $conn->real_escape_string($_POST['subjectcredit']);
    $minMarks = $conn->real_escape_string($_POST['minMarks']);
    $maxMarks = $conn->real_escape_string($_POST['maxMarks']);
    $code = $conn->real_escape_string($_POST['subjectcode']);
    // Handle file upload

   $sql = "UPDATE Syllabi SET University_ID = " . $_SESSION['university_id'] . ", Course_ID = '$coursetype',Sub_Course_ID = '$sub_course_type', Semester = '$semester',Name = '$name',  Paper_Type = '$papertype', Credit = '$credit', Min_Marks = '$minMarks', Max_Marks = '$maxMarks', Code = '$code' WHERE ID = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 200, 'message' => 'Subject updated successlly!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
} else {
    echo "Invalid request method.";
}
$conn->close();
