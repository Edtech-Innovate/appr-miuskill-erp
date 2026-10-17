<?php
    require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
    header('Content-Type: application/json; charset=utf-8');
	$id = $_GET['student_id'];
	$id = base64_decode($id);
	session_start();
// 	print_r($_SESSION);die;
	$id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
// 	print_r($id);die;
	if($id>0){
		ini_set('display_errors', 1);
    

    function getMappings()
    {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apprenticeship.miuskill.in/assets/json/miu-mapping.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response, true);
    }

    function sendDataToMIU($conn, $studentId)
    {
      $student = $conn->query("
        SELECT
          TRIM(Students.First_Name) as First_Name,
          TRIM(Students.Middle_Name) as Middle_Name,
          TRIM(Students.Last_Name) as Last_Name,
          Students.DOB,
          Students.Duration,
          Students.Gender,
          Students.Category,
          Students.Religion,
          Students.Contact,
          LOWER(TRIM(Students.Email)) as Email,
          Students.Alternate_Contact,
          Students.Aadhar_Number,
          Students.Nationality,
          TRIM(JSON_UNQUOTE(JSON_EXTRACT(Students.Address, '$.present_address'))) as Address,
          JSON_UNQUOTE(JSON_EXTRACT(Students.Address, '$.present_city')) as City,
          JSON_UNQUOTE(JSON_EXTRACT(Students.Address, '$.present_state')) as `State`,
          JSON_UNQUOTE(JSON_EXTRACT(Students.Address, '$.present_pincode')) as `Pincode`,
          TRIM(Students.Father_Name) as Father_Name,
          TRIM(Students.Mother_Name) as Mother_Name,
          Admission_Sessions.`Name` as Admission_Session_Name,
          Admission_Types.`Name` as Admission_Type_Name,
          Course_Types.`Name` as Course_Type_Name,
          Courses.`Name` as Course_Name,
          Sub_Courses.`Name` as Sub_Course_Name
        FROM
          Students
          LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID
          LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID
          LEFT JOIN Courses ON Students.Course_ID = Courses.ID
          LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID
          LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID
        WHERE
          Students.ID = $studentId
        ");
      if ($student->num_rows == 0) {
        return json_encode(['status' => false, 'message' => 'Student not exists!']);
      }

      $student = $student->fetch_assoc();

      $studentName = implode(" ", array_filter([$student['First_Name'], $student['Middle_Name'], $student['Last_Name']]));

      $sessionName = $student['Admission_Session_Name'];
      $explodedSessionName = explode("-", $sessionName);
      $sessionMonth = $explodedSessionName[0];
      $sessionYear = $explodedSessionName[1];

      $sessionMonthInNumber = date("m", strtotime("01-" . $sessionName));

      $sessionStartYear = $sessionYear;
      $sessionYearInTwoDigits = date("y", strtotime("01-01-" . $sessionYear));
      if ($sessionMonthInNumber < 7) {
        $sessionStartYear = $sessionStartYear - 1;
      } else {
        $sessionYearInTwoDigits += 1;
      }

      $sessionName = $sessionStartYear . '-' . $sessionYearInTwoDigits;

      $mappings = getMappings();
        
      $courses = $mappings['courses'];
      $specializations = $mappings['specializations'];
      $sessions = $mappings['sessions'];
      $states = $mappings['states'];
      $cities = $mappings['cities'];

      $courseId = $schoolId = $specializationId = $sessionId = $stateId = $cityId = 0;
        // print_r($sessions);die;
      // Sessions
      if (array_key_exists($sessionName, $sessions)) {
        $sessionId = $sessions[$sessionName];
      }

      if (empty($sessionId)) {
        return json_encode(['state' => false, 'message' => 'Session not found!']);
      }
   
      // Courses
      foreach ($courses as $course) {
          if(strtolower($student['Course_Name'])=="diploma"){
              $student['Course_Name'] =  $student['Course_Name']. " in ". $student['Sub_Course_Name'];
          }
          if(strtolower($student['Course_Name'])=="skill"){
              $student['Course_Name'] =  $student['Sub_Course_Name'];
          }
        //   print_r($student['Course_Name']);die;
        if (strtolower($course['name']) == strtolower($student['Course_Name'])) {
          $courseId = $course['id'];
          $schoolId = $course['schoolId'];
          break;
        }
      }
         
      if (empty($courseId) && empty($schoolId)) {
        return json_encode(['status' => false, 'message' => 'Course and School not found!']);
      }
       
      if (array_key_exists($courseId, $specializations)) {

        foreach ($specializations[$courseId] as $specialization) {
          if (strtolower($specialization['name']) === strtolower($student['Sub_Course_Name'])) {
            $specializationId = $specialization['id'];
            break;
          }
        }
      }
        // print_r($specializationId);die;
      // State
      $stateId = 0;
      $cityId = 0;
      if (array_key_exists($student['State'], $states)) {
        $stateId = $states[$student['State']];
        // Cities
        if (array_key_exists($stateId, $cities)) {
          foreach ($cities[$stateId] as $city) {
            if (strtlower($city['name'] === strtolower($student['City']))) {
              $cityId = $city['id'];
              break;
            }
          }
        }
      }

      // Last Qualification
      $qualification = "";
      $academics = $conn->query("SELECT `Type`, `Subject` FROM Student_Academics WHERE Student_ID = $studentId ORDER BY ID DESC LIMIT 1");
      if ($academics->num_rows > 0) {
        $academics = $academics->fetch_assoc();
        $qualification = !in_array($academics['Type'], ['Intermediate', 'High School']) ? $academics['Subject'] : $academics['Type'];
      }
      $entryType = "Fresh";
        if($_SESSION['university_id']==20){
            $entryType = "Vocational";
        }elseif( $_SESSION['university_id']==41){
            $entryType = "Skills";
        }
        $url = "https://erp.miu.edu.in/api/onboard.php";
        $token = "4721a7f5-b33f-4f75-a807-f6b95e7a78f6";
        // $url = "https://admin.miu.edu.in/student/seclogin.php";
        // $token = "zYtkrQQ-ePRZ85J0c-H1GlHf-kZVsRd";
      $data = [
        "token" => "4721a7f5-b33f-4f75-a807-f6b95e7a78f6",
        "adm_session" => 8,
        "school" => 28,
        "course" => 45,//50
        "entrytype" => $entryType, //Vocational
        //"entrytype" => 'Lateral',
        //"stream" => $specializationId, //71
        "stream" => 116,
        "courseyear" => $student['Duration'],
        "name" => $studentName,
        "dob" => $student['DOB'],
        "gender" => $student['Gender'],
        "category" => $student['Category'],
        "religion" => $student['Religion'],
        "mobile" => $student['Contact'],
        "email" => $student['Email'],
        "aadharno" => $student['Aadhar_Number'],
        "nationality" => $student['Nationality'],
        "state" => $stateId,
        "city" => $cityId,
        "address" => $student['Address'],
        "pincode" => $student['Pincode'],
        "fathername" => $student['Father_Name'],
        "mothername" => $student['Mother_Name'],
        "qualification" => $qualification,
        "adm_date" => "2026-07-01",
        "center" =>12
      ];
     die("X Ab mat kar dena!!!");
        
     // print_r($data);die;
        $jsonData = json_encode($data);
         // print_r($jsonData);die();
          $ch = curl_init($url);

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
          curl_setopt($ch, CURLOPT_TIMEOUT, 30);
          curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

          // Headers
          curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $token"
          ]);

          $response = curl_exec($ch);
       // print_r($response);die;
          if (curl_errno($ch)) {
            // echo "Curl Error: " . curl_error($ch);
            echo json_encode(array("status"=>false,"message"=>curl_error($ch)));
          } else {
            // echo "HTTP Code: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "<br>";
            // echo "Response: " . $response;
            // echo "Request: " . $jsonData;
            // print_r($response);die;
            $dataResponse = json_decode($response,true);
            if($dataResponse['status']==false){
                 echo json_encode(array("status"=>false,"message"=>$dataResponse['message']));
                 exit;
            }
            $oaNumber = $dataResponse['Reg. No.'];
            $enrollmentNo = $dataResponse['Enroll. No.'];
            $conn->query("update Students set OA_Number = $oaNumber,Enrollment_No='$enrollmentNo',Processed_To_University='2026-04-22 09:44:50' where ID = $studentId");
             echo json_encode(array("status"=>true,"message"=>json_decode($response,true)));
          }



    }

      echo sendDataToMIU($conn, $id);
      $conn->close();
	}else{
		echo json_encode(array('status'=>false,'message'=>'Student id is mandatory'));
	}
