<?php

if (!function_exists('stringToSecret')) {
    function stringToSecret(string $string = NULL)
    {
      if (!$string) {
        return NULL;
      }
      $length = strlen($string);
      $visibleCount = (int) round($length / 6);
      $hiddenCount = $length - ($visibleCount * 2);
      return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
    }
}


function uuidGenerator($table, $conn)
{
  $all_key = array();
  $get_key = $conn->query("SELECT Api_Key FROM $table");
  while ($gk = $get_key->fetch_assoc()) {
    $all_key[] = $gk['Api_Key'];
  }

  $data = $data ?? random_bytes(16);
  assert(strlen($data) == 16);
  // Set version to 0100
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  // Set bits 6-7 to 10
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  // Output the 36 character UUID.
  $generated_key = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  if (in_array($generated_key, $all_key)) {
    uuidGenerator($table, $conn);
  } else {
    return $generated_key;
  }
}

function generateStudentLedger($conn, $student_id)
{

  $check = $conn->query("SELECT ID FROM Student_Ledgers WHERE Student_ID = $student_id");
  if ($check->num_rows > 0) {
    $conn->query("DELETE FROM Student_Ledgers WHERE Student_ID = $student_id");
  }

  $student_fee = array();
  $student_fee_without_sharing = array();

  $student = $conn->query("SELECT Students.Admission_Type_ID, Students.Admission_Session_ID, Students.University_ID, Students.Duration, Students.Course_ID, Students.Sub_Course_ID, Sub_Courses.Min_Duration, Students.Added_For, Students.Created_At, Universities.Course_Allotment, Universities.Sharing FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID WHERE Students.ID = $student_id");
  $student = mysqli_fetch_assoc($student);

  // Session
  $session = $conn->query("SELECT Scheme FROM Admission_Sessions WHERE ID = " . $student['Admission_Session_ID']);
  $session = $session->fetch_assoc();
  $schemes = json_decode($session['Scheme'], true);

  foreach ($schemes['dates'] as $scheme_id => $date) {
    if (date("Y-m-d") >= $date) {
      $student['Scheme_ID'] = $scheme_id;
    }
  }

  // Scheme - Fee Structure
  $scheme = $conn->query("SELECT Fee_Structure FROM Schemes WHERE ID = " . $student['Scheme_ID']);
  $scheme = $scheme->fetch_assoc();
  $schemeStructure = json_decode($scheme['Fee_Structure'], true);

  $structures = array();
  $feeHeads = array();
  $fee_structures = $conn->query("SELECT ID, Fee_Applicable_ID FROM Fee_Structures WHERE University_ID = " . $student['University_ID'] . " AND Status = 1 ORDER BY Fee_Applicable_ID");
  while ($fee_structure = $fee_structures->fetch_assoc()) {
    $feeHeads[$fee_structure['ID']] = $fee_structure['Fee_Applicable_ID'];
  }

  foreach ($schemeStructure as $feeId) {
    $structures[$feeId] = $feeHeads[$feeId]??"";
  }

  for ($i = $student['Duration']; $i <= $student['Min_Duration']; $i++) {
    foreach ($structures as $id => $applicable) {
      $fee_structure = $conn->query("SELECT ID, Name, Sharing, Is_Constant FROM Fee_Structures WHERE ID = $id");
      $fee_structure = $fee_structure->fetch_assoc();

      // Constant Fee with Sharing
      if (!empty($fee_structure) && $fee_structure['Sharing'] == 1 && $fee_structure['Is_Constant'] == 1) {
        if (!$student['Sharing'] && $student['Course_Allotment']) {
          $fee = $conn->query("SELECT Fee, 'Applicable_In' FROM User_Sub_Courses WHERE University_ID = " . $student['University_ID'] . " AND `User_ID` = " . $student['Added_For'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND Admission_Session_ID = " . $student['Admission_Session_ID'] . " AND Scheme_ID = " . $student['Scheme_ID']);
        } else {
          $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Constant WHERE Scheme_ID = " . $student['Scheme_ID'] . " AND Fee_Structure_ID = $id AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");
        }

        if ($fee->num_rows == 0) {
          exit(json_encode(['status' => 203, 'message' => 'Fee for this course is not configured yet!']));
        }

        $fee = $fee->fetch_assoc();

        if ($fee['Applicable_In'] == 'Applicable_In') {
          $fee['Applicable_In'] = json_encode([$applicable => range(1, $student['Min_Duration'])]);
          $courseFee = json_decode($fee['Fee'], true);
          $fee['Fee'] = $courseFee[$id];
          $sharing = 100;
        } else {
          $sharing = $conn->query("SELECT Fee FROM Fee_Variables WHERE Code = " . $student['Added_For'] . " AND University_ID = " . $student['University_ID'] . " AND Admission_Session_ID = " . $student['Admission_Session_ID'] . " AND Scheme_ID = " . $student['Scheme_ID']);
          if ($sharing->num_rows > 0) {
            $sharing = mysqli_fetch_assoc($sharing);
            $sharing = json_decode($sharing['Fee'], true);
            $sharing = array_key_exists($id, $sharing) ? 100 - (int)$sharing[$id] : 100;
          } else {
            $sharing = 100;
          }
        }

        $applicability = json_decode($fee['Applicable_In'], true);
        $applicability_type = array_keys($applicability);

        $constant_fee = in_array($applicable, [1, 2]) && in_array($i, $applicability[$applicable]) ? $fee['Fee'] : (!in_array($applicable, [1, 2]) ? $fee['Fee'] : 0);

        // All
        if ($applicability_type[0] == 1) {
          $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? round(($constant_fee / 100) * $sharing) : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $constant_fee : 0;
        }

        // On Selected Duration
        if ($applicability_type[0] == 2) {
          $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? round(($constant_fee / 100) * $sharing) : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $constant_fee : 0;
        }

        // On Admission Type
        if ($applicability_type[0] == 3) {
          $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? round(($constant_fee / 100) * $sharing) : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $constant_fee : 0;
        }

        // On New Admission Punch
        if ($applicability_type[0] == 4) {
          $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? round(($constant_fee / 100) * $sharing) : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $constant_fee : 0;
        }
      }

      // Constant Fee without Sharing
      if (!empty($fee_structure) && $fee_structure['Sharing'] == 0 && $fee_structure['Is_Constant'] == 1) {
        $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Constant WHERE Scheme_ID = " . $student['Scheme_ID'] . " AND Fee_Structure_ID = $id AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");
        if ($fee->num_rows == 0) {
          exit(json_encode(['status' => 203, 'message' => 'Fee for this course is not configured yet!']));
        }
        $fee = $fee->fetch_assoc();

        $applicability = json_decode($fee['Applicable_In'], true);
        $applicability_type = array_keys($applicability);

        // All
        if ($applicability_type[0] == 1) {
          $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
        }

        // On Selected Duration
        if ($applicability_type[0] == 2) {
          $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
        }

        // On Admission Type
        if ($applicability_type[0] == 3) {
          $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
        }

        // On New Admission Punch
        if ($applicability_type[0] == 4) {
          $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
        }
      }

      // Variable Fee
      if (!empty($fee_structure) && $fee_structure['Sharing'] == 0 && $fee_structure['Is_Constant'] == 0) {
        if (!$student['Sharing'] && $student['Course_Allotment']) {
          $fee = $conn->query("SELECT Fee, 'Applicable_In' FROM User_Sub_Courses WHERE University_ID = " . $student['University_ID'] . " AND `User_ID` = " . $student['Added_For'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND Admission_Session_ID = " . $student['Admission_Session_ID'] . " AND Scheme_ID = " . $student['Scheme_ID']);
        } else {
          $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Variables WHERE Fee_Structure_ID = $id AND Code = " . $student['Added_For'] . " AND University_ID = " . $student['University_ID'] . " AND Admission_Session_ID = " . $student['Admission_Session_ID'] . " AND Scheme_ID = " . $student['Scheme_ID']);
        }

        if ($fee->num_rows == 0) {
          exit(json_encode(['status' => 203, 'message' => 'Fee for this course is not configured yet!']));
        }

        $fee = $fee->fetch_assoc();

        if ($fee['Applicable_In'] == 'Applicable_In') {
          $fee['Applicable_In'] = '{"1": [1, 2, 3, 4, 5, 6]}';
        }

        $courseFee = json_decode($fee['Fee'], true);
        $fee['Fee'] = $courseFee[$id]??0;

        $applicability = json_decode($fee['Applicable_In'], true);

        $applicability_type = array_keys($applicability);

        // All
        if ($applicability_type[0] == 1) {
          $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
        }

        // On Selected Duration
        if ($applicability_type[0] == 2) {
          $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
        }

        // On Admission Type
        if ($applicability_type[0] == 3) {
          $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
        }

        // On New Admission Punch
        if ($applicability_type[0] == 4) {
          $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
          $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
        }
      }
    }

    $date = date('Y-m-d', strtotime($student['Created_At']));
    $add = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Fee_Without_Sharing, Status) VALUES ('$date', $student_id, $i, " . $student['University_ID'] . ", 1, '" . json_encode($student_fee) . "', '" . json_encode($student_fee_without_sharing) . "', 1)");
  }

  // Late Fee
  $date = date('Y-m-d', strtotime($student['Created_At']));
  $late_fees = $conn->query("SELECT Fee, Start_Date, Admission_Session FROM Late_Fees WHERE University_ID = " . $student['University_ID'] . " AND Status = 1 ORDER BY Start_Date DESC");
  if ($late_fees->num_rows > 0) {
    while ($late_fee = $late_fees->fetch_assoc()) {
      $admission_session = !empty($late_fee['Admission_Session']) ? json_decode($late_fee['Admission_Session'], true) : [];
      if (in_array($student['Admission_Session_ID'], $admission_session) && $date >= $late_fee['Start_Date']) {
        $student_fee = array('Late Fine' => $late_fee['Fee']);
        $add = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Status) VALUES ('$date', $student_id, " . $student['Duration'] . ", " . $student['University_ID'] . ", 1, '" . json_encode($student_fee) . "', 1)");
        break;
      }
    }
  }
}

function activityLogs($conn, $message, $user_id)
{
}

function generateLeadHistory($conn, $lead_id, $user_id, $old, $new)
{
  $result = array_diff($old, $new);
  if (!empty($result)) {
    $update = $conn->query("INSERT INTO Lead_Histories (Lead_ID, `User_ID`, Data, Created_By) VALUES ($lead_id, $user_id, '" . json_encode($result) . "', " . $_SESSION['ID'] . ")");
  }
}


function generateStudentID($conn, $suffix, $length, $university_id)
{
  $student_ids = array();
  $ids = $conn->query("SELECT Unique_ID FROM Students WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $ids = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $result = '';
  for ($i = 0; $i < $length; $i++) {
    $result .= mt_rand(0, 9);
  }

  $new_id = $suffix . $result;
  if (in_array($new_id, $student_ids)) {
    generateStudentID($conn, $suffix, $length, $university_id);
  } else {
    return $new_id;
  }
}

function clean($string)
{
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9\-.|,]/', '', $string); // Removes special chars.
}

function balanceAmount($conn, $student_id, $duration)
{
  $balance = 0;
  $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $student_id AND Status = 1 AND Duration <= " . $duration);
  while ($ledger = $ledgers->fetch_assoc()) {
    $fees = json_decode($ledger['Fee'], true);
    foreach ($fees as $key => $value) {
      $debit = $ledger['Type'] == 1 ? $value : 0;
      $credit = $ledger['Type'] == 2 ? $value : 0;
      $balance = ($balance + $credit) - $debit;
    }
  }

  return (int)$balance;
}

function getFirstLetterOfEachWords($string)
{
  $words = preg_split("/[\s,_-]+/", $string);
  $acronym = "";
  foreach ($words as $w) {
    $acronym .= substr($w, 0, 1);
  }
  return $acronym;
}

function generateUsername($conn, $prefix)
{
  $digits = 5;
  $suffix = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

  $check = $conn->query("SELECT ID FROM Users WHERE Code = '$prefix.$suffix'");
  if ($check->num_rows > 0) {
    generateUsername($conn, $prefix);
  }

  return $prefix . $suffix;
}


function validateMobile($mobile)
{
  return preg_match('/^[6-9]\d{9}$/', $mobile);
}

function sendMail($sender, $receivers, $mail, $cc = array(), $bcc = array(), $replyTo = array(), $scheduleAt = NULL)
{
  $data = array();
  if (!empty($replyTo)) {
    $data['replyTo'] = $replyTo;
  }

  $attachmentContent = "";
  if (!empty($mail['Attachments'])) {
    $attachments = json_decode($mail['Attachments'], true);
    foreach ($attachments as $attachment) {
      $files[] = array("url" => "https://admission-portal.com" . $attachment['path'], "name" => $attachment['name'], "type" => $attachment['type']);
    }
    $data['attachment'] = $files;
  }

  if (!empty($receivers)) {
    $data['to'] = $receivers;
  } else {
    return json_encode(['status' => false, 'message' => 'Receiver cannot be empty!']);
  }

  if (!empty($cc)) {
    $data['cc'] = $cc;
  }

  if (!empty($bcc)) {
    $data['bcc'] = $bcc;
  }

  if (!is_null($scheduleAt)) {
    $data["scheduledAt"] = $scheduleAt;
  }

  $data['sender'] = $sender;
  $data['subject'] = $mail['subject'];
  $data['htmlContent'] = $mail['body'];

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.sendinblue.com/v3/smtp/email',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => array(
      'api-key: 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',
      'Content-Type: application/json'
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $response = json_decode($response, true);
  if (array_key_exists('messageId', $response)) {
    return json_encode(['status' => true, 'message' => 'Mail sent successfully!']);
  } else {
    return json_encode(['status' => false, 'message' => $response['message']]);
  }
}

function sendMessage()
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://softsms.in/app/smsapi/index.php?key=63c41b0de668c&type=text&contacts=&senderid=&peid=&templateid=&msg=',
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
  echo $response;
}

function getCenterIdFunc($conn, $subcenter_id = null)
{
  $subcenterQuery = $conn->query("SELECT Code, ID,Role FROM Users WHERE ID=$subcenter_id AND Role='Sub-Center'");
  $subcenterArr = $subcenterQuery->fetch_assoc();
  $subcentercode = explode('.', $subcenterArr["Code"]);
  $centerCode = $subcentercode[0];
  $centerQuery = $conn->query("SELECT  ID, Code, Role FROM Users WHERE Code='$centerCode' AND Role='Center'");
  $centerArr = $centerQuery->fetch_assoc();
  $center_id = $centerArr['ID'];
  return $center_id;
}
// ---------- AVOID FUNCTION REDECLARATION (wrap your maskEmail / maskPhone) ----------
if (!function_exists('maskEmail')) {
    function maskEmail($email) {
        $email = trim((string)$email);
        if ($email !== '' && strpos($email, '@') !== false) {
            $visible = substr($email, 0, 2);
            $masked = str_repeat('*', max(strlen($email) - 2, 0));
            return $visible . $masked;
        }
        return '';
    }
}

if (!function_exists('maskPhone')) {
    function maskPhone($phone) {
        $phone = preg_replace('/\D/', '', (string)$phone); // remove non-digits
        $len = strlen($phone);
        if ($len > 3) {
            return str_repeat('*', $len - 3) . substr($phone, -3);
        }
        return $phone; // show as-is if too short
    }
}
function getLedgerSummary($conn, $student_id)
{
  // Total Fee
  $totalFee = array();
  $remittedFee = array();
  $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  if ($debits->num_rows == 0) {
    generateStudentLedger($conn, $student_id);
    $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  }

  if ($debits->num_rows > 0) {
    while ($debit = $debits->fetch_assoc()) {
      if (empty($debit['Fee_Without_Sharing'])) {
        generateStudentLedger($conn, $student_id);
        $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
        while ($debit = $debits->fetch_assoc()) {
          $fees = json_decode($debit['Fee_Without_Sharing'], true);
          $totalFee[] = array_sum($fees);
        }
      } else {
        $fees = json_decode($debit['Fee_Without_Sharing'], true);
        $totalFee = $fees;
      }
    }
  }

  $credits = $conn->query("SELECT Fee FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 2");
  if ($credits->num_rows > 0) {
    while ($credit = $credits->fetch_assoc()) {
      $paid = $credit['Fee'];
      $remittedFee = $paid;
    }
  }
    $totalFee = array_sum($totalFee);
  return json_encode(['totalFee' => $totalFee, 'totalRemitted' => $remittedFee, 'totalBalance' => $totalFee - (int) $remittedFee]);
}


function numberTowords($number)
{
  $decimal = round($number - ($no = floor($number)), 2) * 100;
  $hundred = null;
  $digits_length = strlen($no);
  $i = 0;
  $str = array();
  $words = array(
    0 => '',
    1 => 'one',
    2 => 'two',
    3 => 'three',
    4 => 'four',
    5 => 'five',
    6 => 'six',
    7 => 'seven',
    8 => 'eight',
    9 => 'nine',
    10 => 'ten',
    11 => 'eleven',
    12 => 'twelve',
    13 => 'thirteen',
    14 => 'fourteen',
    15 => 'fifteen',
    16 => 'sixteen',
    17 => 'seventeen',
    18 => 'eighteen',
    19 => 'nineteen',
    20 => 'twenty',
    30 => 'thirty',
    40 => 'forty',
    50 => 'fifty',
    60 => 'sixty',
    70 => 'seventy',
    80 => 'eighty',
    90 => 'ninety'
  );
  $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
  while ($i < $digits_length) {
    $divider = ($i == 2) ? 10 : 100;
    $number = floor($no % $divider);
    $no = floor($no / $divider);
    $i += $divider == 10 ? 1 : 2;
    if ($number) {
      $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
      $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
      $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
    } else
      $str[] = null;
  }
  $Rupees = implode('', array_reverse($str));
  $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
  return ($Rupees ? $Rupees . 'Rs. Only ' : '') . $paise;
}

if(!function_exists('totalUloadedSubjectsFunc')){
        function totalUloadedSubjectsFunc($conn, $university_id, $student_id, $duration, $searchSqlQuery=null)
{

  $getStuData = $conn->query("SELECT Course_ID,Sub_Course_ID,Added_For,University_ID  FROM Students WHERE ID = $student_id");
  $stuData = $getStuData->fetch_assoc();
  if ($university_id == 48) {
    $center_id = getUserIdFunc($conn, $stuData['Added_For']);
    $getCenterID = $conn->query("select Code from Users where ID = '$center_id'");
    if ($getCenterID->num_rows > 0) {
      $codeArr = $getCenterID->fetch_assoc();
      $code = trim($codeArr['Code']);
      $userQuery = " AND JSON_CONTAINS(User_ID, '\"" . mysqli_real_escape_string($conn, $code) . "\"')";
    } else {
      $code = '';
      $userQuery = '';
    }
    $sub_count = $conn->query("SELECT Syllabi.Name FROM Syllabi WHERE  Course_ID='" . $stuData['Course_ID'] . "' AND Sub_Course_ID='" . $stuData['Sub_Course_ID'] . "' AND Semester = '" . $duration . "' AND University_ID ='" . $stuData['University_ID'] . "'  $userQuery " .$searchSqlQuery);
  } else {
    $sub_count = $conn->query("SELECT Syllabi.Name FROM Syllabi WHERE Course_ID='" . $stuData['Course_ID'] . "' AND Sub_Course_ID='" . $stuData['Sub_Course_ID'] . "' AND Semester = '" . $duration . "' AND University_ID ='" . $stuData['University_ID'] . "' $searchSqlQuery ");
    if( $stuData['Sub_Course_ID']==1051 && $duration==1){
       $sub_count = $conn->query("SELECT Syllabi.Name FROM Syllabi WHERE Course_ID='" . $stuData['Course_ID'] . "' AND Sub_Course_ID='" . $stuData['Sub_Course_ID'] . "' AND Semester = '" . $duration . "' AND University_ID ='" . $stuData['University_ID'] . "' AND Code NOT IN ('HSAD-101', 'HSAD-102','HSAD-103','HSAD-104', 'HSAD-105', 'HSAD-121') ");
    }
  }
   
  $subjectArr = [];
  while ($row = $sub_count->fetch_assoc()) {
    $subjectArr[] = $row;
  }
  return $subjectArr;

}
}
if(!function_exists('numberToWordFunc')){
    

function numberToWordFunc($number) {
  $words = array('','One','Two','Three','Four','Five','Six','Seven','Eight','Nine');
  $wordsTeen = array('Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen');
  $wordsTens = array('','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety');

  if ($number == 0) return 'Zero';

  $wordsArray = array();

  if ($number >= 1000000000) {
      $wordsArray[] = numberToWordFunc(floor($number / 1000000000)) . ' Billion';
      $number %= 1000000000;
  }

  if ($number >= 1000000) {
      $wordsArray[] = numberToWordFunc(floor($number / 1000000)) . ' Million';
      $number %= 1000000;
  }

  if ($number >= 1000) {
      $wordsArray[] = numberToWordFunc(floor($number / 1000)) . ' Thousand';
      $number %= 1000;
  }

  if ($number >= 100) {
      $wordsArray[] = numberToWordFunc(floor($number / 100)) . ' Hundred';
      $number %= 100;
  }

  if ($number >= 20) {
      $wordsArray[] = $wordsTens[floor($number / 10)];
      $number %= 10;
  }

  if ($number >= 10) {
      $wordsArray[] = $wordsTeen[$number - 10];
      $number = 0;
  }

  if ($number > 0) {
      $wordsArray[] = $words[$number];
  }

  return implode(' ', $wordsArray);
}
}

if (! function_exists('addLog')) {
    function addLog($conn, $university_id, $user_id, $action, $table, $record_id, $description = '', $old_data = null, $new_data = null)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $old_data = is_array($old_data) ? json_encode($old_data) : $old_data;
        $new_data = is_array($new_data) ? json_encode($new_data) : $new_data;

        $stmt = $conn->prepare("INSERT INTO activity_logs
        (university_id, user_id, action, table_name, record_id, description, old_data, new_data, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("iississss", $university_id, $user_id, $action, $table, $record_id, $description, $old_data, $new_data, $ip);
        $stmt->execute();
    }
}
