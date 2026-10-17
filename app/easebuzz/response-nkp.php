<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_POST)) {
  require '../../includes/db-config.php';

  if (empty($_POST)) {
    echo json_encode(['status' => false, 'message' => 'Invalid Request!']);
    exit();
  }

  $conn->query("INSERT INTO API_Requests (Data, Response) VALUES ('" . json_encode($_POST) . "', '" . json_encode(['Easebuzz Request']) . "')");

  // Check already success
  $check = $conn->query("SELECT ID FROM Payments WHERE Transaction_ID = '$transaction_id' AND `Type` = 2 AND Status = 1");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => true, 'message' => 'Payment Updated!']);
    exit();
  }

  // if (strcasecmp($_POST['status'], 'success') == 0) {
  //$gateway_id = $_POST['easepayid'];
  //$transaction_id = $_POST['txnid'];
  //$mode = $_POST['mode'];
  //$meta = json_encode(["msg" => $_POST]);
  
   $gateway_id = $_POST['response']['easepayid'];
   $transaction_id = $_POST['response']['txnid'];
   $mode = $_POST['response']['mode'];
   $meta = json_encode(["msg" => $response]);
 

  $update = $conn->query("UPDATE Payments SET Gateway_ID = '$gateway_id', Payment_Mode = '$mode', Meta = '$meta', Status = 1 WHERE Transaction_ID = '$transaction_id' AND `Type` = 2");
  if ($update) {
    $students = $conn->query("SELECT Invoice_No, Student_ID, Duration, University_ID, Amount FROM Invoices WHERE Invoice_No = '$transaction_id'");
    if ($students->num_rows > 0) {
      while ($student = $students->fetch_assoc()) {
        $conn->query("UPDATE Invoices SET Status = 1 WHERE Invoice_No = '$transaction_id'");

        $leadStatusId = 0;
        $leadId = $conn->query("SELECT Lead_Status_ID FROM Students WHERE Lead_Status_ID IS NOT NULL AND ID = " . $student['Student_ID']);
        if ($leadId->num_rows > 0) {
          $leadId = $leadId->fetch_assoc();
          $leadStatusId = $leadId['Lead_Status_ID'];

          // Send Fee Receipt
          $mail = $conn->query("SELECT Subject as subject, Template as body, Attachments FROM Email_Templates WHERE University_ID = " . $student['University_ID'] . " AND Name = 'Fee Receipt'");
          if ($mail->num_rows > 0) {
            $mail = $mail->fetch_assoc();

            // Sender
            $university = $conn->query("SELECT Name as name, Email as email FROM Universities WHERE ID = " . $student['University_ID']);
            $sender = $university->fetch_assoc();

            $studentDetails = $conn->query("SELECT DATE_FORMAT(CURDATE(),'%d-%m-%Y') as `{{ current_date }}`, Lead_Status.Unique_ID AS`{{ student_id }}`,UPPER(Leads.`Name`)AS`{{ student_name }}`,Leads.Mobile as`{{ student_mobile }}`,Leads.Email as`{{ student_email }}`,UPPER(DATE_FORMAT(Students.DOB,'%d%b%Y'))as`{{ student_password }}`,Courses.`Name` as`{{ program }}`,Sub_Courses.`Name` as`{{ specialization }}`,Universities.`Name` as`{{ university_name }}`,Universities.Vertical as`{{ university_vertical }}` FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID=Leads.ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Students ON Lead_Status.ID=Students.Lead_Status_ID WHERE Lead_Status.ID=$leadStatusId");
            if ($studentDetails->num_rows > 0) {
              $studentDetails = $studentDetails->fetch_assoc();
              $receivers[] = array('email' => $studentDetails['{{ student_email }}'], 'name' => $studentDetails['{{ student_name }}']);
              foreach ($studentDetails as $key => $value) {
                $mail['body'] = str_replace($key, $value, $mail['body']);
                $mail['subject'] = str_replace($key, $value, $mail['subject']);
              }
            }
          }

          sendMail($sender, $receivers, $mail);
        }

        $isProcessed = $conn->query("SELECT ID FROM Students WHERE Process_By_Center IS NULL AND ID = " . $student['Student_ID']);
        if ($isProcessed->num_rows > 0) {
          $conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = " . $student['Student_ID']);

          // Send Provisional
          if (!empty($leadStatusId)) {
            $mail = $conn->query("SELECT Subject as subject, Template as body, Attachments FROM Email_Templates WHERE University_ID = " . $student['University_ID'] . " AND Name = 'Provisional Letter'");
            if ($mail->num_rows > 0) {
              $mail = $mail->fetch_assoc();

              // Sender
              $university = $conn->query("SELECT Name as name, Email as email FROM Universities WHERE ID = " . $student['University_ID']);
              $sender = $university->fetch_assoc();

              $studentDetails = $conn->query("SELECT DATE_FORMAT(CURDATE(),'%d-%m-%Y') as `{{ current_date }}`, Lead_Status.Unique_ID AS`{{ student_id }}`,UPPER(Leads.`Name`)AS`{{ student_name }}`,Leads.Mobile as`{{ student_mobile }}`,Leads.Email as`{{ student_email }}`,UPPER(DATE_FORMAT(Students.DOB,'%d%b%Y'))as`{{ student_password }}`,Courses.`Name` as`{{ program }}`,Sub_Courses.`Name` as`{{ specialization }}`,Universities.`Name` as`{{ university_name }}`,Universities.Vertical as`{{ university_vertical }}` FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID=Leads.ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Students ON Lead_Status.ID=Students.Lead_Status_ID WHERE Lead_Status.ID=$leadStatusId");
              if ($studentDetails->num_rows > 0) {
                $studentDetails = $studentDetails->fetch_assoc();
                $receivers[] = array('email' => $studentDetails['{{ student_email }}'], 'name' => $studentDetails['{{ student_name }}']);
                foreach ($studentDetails as $key => $value) {
                  $mail['body'] = str_replace($key, $value, $mail['body']);
                  $mail['subject'] = str_replace($key, $value, $mail['subject']);
                }
              }
            }

            sendMail($sender, $receivers, $mail);
          }
        }


        // Add in Ledger
        
        $checkAlreadyExists = $conn->query("SELECT ID FROM Student_Ledgers WHERE Student_ID = " . $student['Student_ID'] . " AND University_ID = " . $student['University_ID'] . " AND Transaction_ID = '" . $student['Invoice_No'] . "'");
        
        if ($checkAlreadyExists->num_rows == 0) {
          $conn->query("INSERT INTO Student_Ledgers (`Date`, `Student_ID`, `Duration`, `University_ID`, `Type`, `Source`, `Transaction_ID`, `Fee`) VALUES ('" . date("Y-m-d") . "', " . $student['Student_ID'] . ", " . $student['Duration'] . ", " . $student['University_ID'] . ", 2, 'Online', '" . $student['Invoice_No'] . "', '" . json_encode(['Fee' => $student['Amount']]) . "')");
        }
      }
    }
    echo json_encode(['status' => true, 'message' => 'Payment updated!']);
  } else {
    echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
  }
  // }else{
  // echo json_encode(['status' => false, 'message' => 'Payment Failed!']);
  // }
}
