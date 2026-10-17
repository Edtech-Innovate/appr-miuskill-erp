<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

// $data = file_get_contents('php://input');
// $data = json_decode($data, true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// DB
require '../../includes/db-config.php';

if($_REQUEST['username'] && $_REQUEST['password']){
    
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $check = $conn->query("select ID from Students where Enrollment_No='$username' and Contact='$password' and Status=1");
    if($check->num_rows>0)
    {
        $ids = $check->fetch_assoc();
        $id = $ids['ID'];
       $result_record = "SELECT Student_Pendencies.ID as Pendency, Student_Pendencies.Status as Pendency_Status, UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) as DOB, Students.*, Admission_Sessions.`Name` as Adm_Session, Admission_Types.`Name` as Adm_Type, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Short_Name, Courses.Name as Course_Name,Student_Documents.`Location`,Modes.Name as Mode_Name FROM Students  LEFT JOIN Student_Pendencies ON Students.ID = Student_Pendencies.Student_ID AND Student_Pendencies.Status != 1 LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Modes ON Sub_Courses.Mode_ID=Modes.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' WHERE Students.ID=$id";
        $empRecords = mysqli_query($conn, $result_record);
        $data = $empRecords->fetch_assoc();
        echo json_encode(array("status"=>'success','data'=>$data));
    }else
    {
        echo json_encode(array("status"=>'error','message'=>"Invalid credentials!"));
    }
}else
{
    echo json_encode(array('status'=>'error','message'=>"Usename and password mandatory"));
}

