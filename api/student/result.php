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
        $data = $conn->query("select Duration from Students where Enrollment_No='$enrollment_no'");
        if($data->num_rows>0)
        {
            $data = $data->fetch_assoc();
            $sem = $data['Duration'];
            $semQuery=  "&year_sem=".$sem;
            $webQuery ="?enroll_no=".$enrollment_no.$semQuery;
            $get_result_data_url = "https://aparni.edtechinnovate.in/student/examination/api".$webQuery;
            $result = file_get_contents($get_result_data_url);
            echo $result;
        }else
        {
             echo json_encode(array("status"=>"error","message"=>"Invalid Enrollment"));
        }
        
}else{
    echo json_encode(array("status"=>"error","message"=>"Enrollment number is required"));
}