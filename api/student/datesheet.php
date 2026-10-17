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

$enrollment_no = $_REQUEST['enrollment_no']??"";
if($enrollment_no!=''){
        $data = $conn->query("select Course_ID,Sub_Course_ID,Duration from Students where Enrollment_No='$enrollment_no'");
        $studentData = $data->fetch_assoc();
        $syllabus_ids = array();
            $codes = $conn->query("SELECT ID FROM Syllabi WHERE Course_ID = ".$studentData['Course_ID']." AND Sub_Course_ID = ".$studentData['Sub_Course_ID']." AND Semester = ".$studentData['Duration']."");
              while($row = $codes->fetch_assoc()) {
                $syllabus_ids[] = $row['ID'];
              }
              if(!empty($syllabus_ids))
              {
                  $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session, Syllabi.Name, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Syllabus_ID IN (".implode(",", $syllabus_ids).") ORDER BY Exam_Date ASC");
                    $datesheet = [];
                    if($date_sheets->num_rows>0)
                    {
                        while($row = $date_sheets->fetch_assoc())
                      {
                          $datesheet[] = $row;
                      }   
                    }
                  if($date_sheets->num_rows==0){
                    echo json_encode(array("status"=>"error","message"=>"Datesheet Not Available"));
                  }else{
                      echo json_encode(array("status"=>"error",'message'=>"datesheet","data"=>$datesheet));
                  }
              }else{
                   echo json_encode(array("status"=>"error","message"=>"Datesheet Not Available"));
              }
}else{
    echo json_encode(array("status"=>"error","message"=>"Enrollment number is required"));
}