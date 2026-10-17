<?php
// ini_set('display_errors', 1);
session_start();
require '../../includes/db-config.php';
require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

$search_value = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

// Handle current session filter
if (isset($_SESSION['current_session'])) {
    if ($_SESSION['current_session'] == 'All') {
        $session_query = '';
    } else {
        $session_query = "AND Admission_Sessions.Name LIKE '%" . $_SESSION['current_session'] . "%'";
    }
} else {
    $get_current_session = $conn->query("SELECT Name FROM Admission_Sessions WHERE Current_Status = 1 AND University_ID = '" . $_SESSION['university_id'] . "'");
    if ($get_current_session->num_rows > 0) {
        $gsc = $get_current_session->fetch_assoc();
        $session_query = "AND Admission_Sessions.Name LIKE '%" . $gsc['Name'] . "%'";
    } else {
        $session_query = '';
    }
}

$role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);
$step_query = "";

// Header columns: NOTE the additions right after Nationality!
$header = [
    'Student_ID', 'Enrollment_No', 'Roll_Number', 'Step', 'Added On', 'Processed By Center', 'Processed To University',
    'Student Name', 'Father Name', 'Mother Name', 'Adm Type', 'Session', 'Duration', 'Mode', 'Course', 'Sub Course',
    'Short Name', 'Email', 'Contact', 'Alternate Email', 'Alternate Contact', 'Aadhar Number', 'DOB', 'Employement Status',
    'Gender', 'Category', 'Address', 'City', 'District', 'State', 'Pincode', 'Nationality',
    // The following two must be present to match your SQL SELECT!
    'Added_For', 'subCourse',
    // Academic blocks, 5 types, each with 7 fields
    'High School', 'HS Subject', 'HS Year', 'HS Board/Institute', 'HS Marks Obtained', 'HS Maximum Marks', 'HS Total Marks',
    'Intermediate', 'Int Subject', 'Int Year', 'Int Board/Institute', 'Int Marks Obtained', 'Int Maximum Marks', 'Int Total Marks',
    'UG', 'UG Subject', 'UG Year', 'UG Board/Institute', 'UG Marks Obtained', 'UG Maximum Marks', 'UG Total Marks',
    'PG', 'PG Subject', 'PG Year', 'PG Board/Institute', 'PG Marks Obtained', 'PG Maximum Marks', 'PG Total Marks',
    'Other', 'Other Subject', 'Other Year', 'Other Board/Institute', 'Other Marks Obtained', 'Other Maximum Marks', 'Other Total Marks',
    'Center Code', 'Center Name', 'RM', 'Export Documents','Vertical'
];
if ($_SESSION['Role'] != 'Sub-Center') {
    $header = array_merge(
        $header,
        ['Student Document','Total Fee', 'Paid Fees', 'Balance Fees','University Fee','Paid University Fee','Balance University','Vertical']
    );
}

// Search filter
$searchQuery = " ";
if ($search_value != '') {
    if (strcasecmp($search_value, 'completed') == 0) {
        $searchQuery = " AND Step = 4 ";
    } else {
        $searchQuery = " AND (
            Students.Unique_ID LIKE '%$search_value%' 
            OR Students.ID LIKE '%$search_value%' 
            OR Students.First_Name LIKE '%$search_value%' 
            OR Students.Middle_Name LIKE '%$search_value%' 
            OR Students.Last_Name LIKE '%$search_value%' 
            OR Admission_Sessions.Name LIKE '%$search_value%' 
            OR Admission_Types.Name LIKE '%$search_value%' 
            OR Students.Step LIKE '%$search_value%' 
            OR Students.Father_Name LIKE '%$search_value%' 
            OR Students.Email LIKE '%$search_value%' 
            OR Students.Contact LIKE '%$search_value%' 
            OR Sub_Courses.Short_Name LIKE '%$search_value%'
        )";
    }
}

// Main student query
$result_record = "
SELECT Students.ID, Students.Unique_ID,  Students.Enrollment_No, Students.OA_Number, Students.Step, Students.Created_At, 
Students.Process_By_Center, Students.Processed_To_University, 
CONCAT(Students.First_Name, IF(Students.Middle_Name!='', CONCAT(' ', Students.Middle_Name), ''), ' ', Students.Last_Name) as Name, 
Students.Father_Name, Students.Mother_Name, Admission_Types.Name as Adm_Type, Admission_Sessions.Name as Session, 
Students.Duration, Modes.Name as Mode, Courses.Name as Course, Sub_Courses.Name AS Sub_Course, Sub_Courses.Short_Name as Short_Name, 
Students.Email, Students.Contact, Students.Alternate_Email, Students.Alternate_Contact, Students.Aadhar_Number, Students.DOB, 
Students.Employement_Status, Students.Gender, Students.Category, 
REPLACE(JSON_EXTRACT(Students.Address, '$.present_address'), '\"', '') as Address, 
REPLACE(JSON_EXTRACT(Students.Address, '$.present_city'), '\"', '') as City, 
REPLACE(JSON_EXTRACT(Students.Address, '$.present_district'), '\"', '') as District, 
REPLACE(JSON_EXTRACT(Students.Address, '$.present_state'), '\"', '') as State, 
REPLACE(JSON_EXTRACT(Students.Address, '$.present_pincode'), '\"', '') as Pincode, 
Students.Nationality, Students.Added_For ,Sub_Courses.ID AS subCourse
FROM Students 
LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID 
LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID 
LEFT JOIN Modes ON Students.Mode_ID = Modes.ID 
LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
WHERE Students.University_ID = " . $_SESSION['university_id'] . " $searchQuery $role_query $step_query $session_query ORDER BY ID DESC";

$empRecords = mysqli_query($conn, $result_record);
$data[] = $header;

// Loop through student records
while ($row = mysqli_fetch_row($empRecords)) {
    // Find Center / Added_For
    if ($_SESSION['Role'] == 'Center') {
        $user = $conn->query("SELECT ID, Code, Name, vertical FROM Users WHERE ID = " . $row[33]);
    } else {
        $user = $conn->query("SELECT ID, Code, Name, vertical FROM Users WHERE ID = " . $row[33] . " AND Role = 'Center'");
        if ($user->num_rows == 0) {
            $user = $conn->query("SELECT Users.ID, Code, Name FROM Users 
                LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center 
                WHERE Sub_Center = " . $row[33]);
        }
    }
    if ($user->num_rows > 0) {
        $user = $user->fetch_assoc();
        // print_r($user);die;
    } else {
        $user = ['ID' => 0, 'Name' => '', 'Code' => '', 'vertical'=>1];
    }

    // RM
    $rm_name = "";
    if (!empty($user['Name'])) {
        $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name 
            FROM Alloted_Center_To_Counsellor 
            LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID 
            WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " 
            AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
        $rm_name = $rm->num_rows > 0 ? $rm->fetch_assoc()['Name'] : $user['Name'];
    }

    // Academic details
    $courses = ['High School', 'Intermediate', 'UG', 'PG', 'Other'];
    $acad_row = [];
    foreach ($courses as $course) {
        $academics = $conn->query("SELECT Type, Subject, Year, `Board/Institute`, Marks_Obtained, Max_Marks, Total_Marks 
            FROM Student_Academics WHERE Student_ID = $row[0] AND Type = '$course'");
        if ($academics->num_rows > 0) {
            $academic = mysqli_fetch_row($academics);
        } else {
            $academic = [$course, '', '', '', '', '', ''];
        }
        $acad_row = array_merge($acad_row, $academic);
    }

    // Add center info + link
    $center_code = $user['Code'];
    $center_name = $user['Name'];
    $vertical = "Edtech";
    // print_r($user['vertical']);die;
    $vertical = isset($user['vertical'])&&$user['vertical']==1?"Edtech":"IITS";
    // print_r($vertical);die;
    $rm_val = $rm_name;

    $encode = base64_encode($row[0] . "W1Ebt1IhGN3ZOLplom9I");
    $export_link = '<i><a href="https://' . $_SERVER['HTTP_HOST'] . '/app/applications/zip?id=' . $encode . '">Click Here</a></i>';

    $main_row = array_merge($row, $acad_row, [$center_code, $center_name, $rm_val, $export_link]);

    // Fees (only for non-sub-center)
    if ($_SESSION['Role'] != 'Sub-Center') {
        $courseFee = 0;
        $paidFees = 0;
        $balanceFees = 0;
        $subCourseId = $row[34];
        $student = $row[0];

        $fee_q = $conn->query("SELECT Fee, Type FROM Student_Ledgers WHERE Status='1' AND Student_ID = $student");
        if ($fee_q->num_rows > 0) {
            while ($fee_data = $fee_q->fetch_assoc()) {
                if ($fee_data['Type'] == 1) {
                    $fee_array = json_decode($fee_data['Fee'], true);
                    if (is_array($fee_array)) {
                        $courseFee += array_sum($fee_array);
                    } else {
                        $courseFee += floatval($fee_data['Fee']);
                    }
                } elseif ($fee_data['Type'] == 2) {
                    $pay_array = json_decode($fee_data['Fee'], true);
                    if (is_array($pay_array)) {
                        $paidFees += array_sum($pay_array);
                    } else {
                        $paidFees += floatval($fee_data['Fee']);
                    }
                }
            }
        }
        $balanceFees = $courseFee - $paidFees;

        // University Fee
        $universityFee = 0;
        $paidUniversityFee = 0;
        $balanceUniversityFee = 0;
        $uniFeeMainQuery = $conn->query("SELECT Sub_Courses.University_fee,COALESCE(SUM(university_payment.amount), 0) AS paid_university_fee FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN university_payment ON Students.ID= university_payment.student_id  WHERE Students.ID='$student'");
        if ($uniFeeMainQuery && $uniFeeMainQuery->num_rows > 0) {
            $universityFeeData = $uniFeeMainQuery->fetch_assoc();
            $universityFee = $universityFeeData['University_fee'];
            $paidUniversityFee = $universityFeeData['paid_university_fee'];
            $balanceUniversityFee = $universityFee - $paidUniversityFee;
        }
        // Concatenate fee columns
        $main_row = array_merge($main_row, [
            '', // 'Student Document' (empty or implement as needed)
            $courseFee, $paidFees, $balanceFees,
            $universityFee, $paidUniversityFee, $balanceUniversityFee
        ]);
    }
     array_push($main_row,$vertical);
    // Format Student ID for export
    if (!empty($main_row[1])) {
        $main_row[0] = $main_row[1];
    } else {
        $main_row[0] = sprintf("%'.06d", $main_row[0]);
    }
   
    unset($main_row[1]); // remove duplicate Unique_ID
    
    $data[] = $main_row;
    // print_r($data);die;
}
// print_r($data);die;
// Generate Excel
SimpleXLSXGen::fromArray($data)->downloadAs('Students.xlsx');
exit;

?>
