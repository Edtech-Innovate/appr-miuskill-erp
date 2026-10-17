<?php
if (
  isset($_POST['amount'])
) {
  session_start();
  require '../../includes/db-config.php';

  $amount = sprintf("%.1f", $_POST['amount']);
  $transaction_id = strtoupper(strtolower(uniqid('SAU')));

  $key = $_SESSION['access_key'];
  $salt = $_SESSION['secret_key'];
  
  if (isset($_SESSION['student_row_id']) && !empty($_SESSION['student_row_id'])) {
    $std_row_ID = $_SESSION['student_row_id'];
} else {
    // Stop execution and send a 400 Bad Request response back to your AJAX handler
    exit(json_encode([
        'status' => 400, 
        'message' => 'Error: Student row reference session ID is missing or expired.'
    ]));
}
  // Safely figure out the correct ID pointing to the student record
    $target_student_id = !empty($std_row_ID) ? intval($std_row_ID) : (isset($_SESSION['Student_Table_ID']) ? intval($_SESSION['Student_Table_ID']) : 0);

    $student_info_query = $conn->query("SELECT `Duration`, `Unique_ID` FROM Students WHERE ID = $target_student_id");
        
    if ($student_info_query && $student_info_query->num_rows > 0) {
        $student_row = $student_info_query->fetch_assoc();
        $duration = intval($student_row['Duration']);
        $unique_id = mysqli_real_escape_string($conn, $student_row['Unique_ID']);
      
    } else {
        // Fallback defaults if the student record is missing
        $duration = 1; 
        $unique_id = '';
    }
  

  $product_info = 'Fee Payment';
 

  if ($_SESSION['Role'] == 'Student') {
    
  
    $mobile = $_SESSION['Contact'];
    $conn->query("INSERT INTO Payments (`Type`, `Amount`, `Transaction_ID`, `Duration`, `Added_For`, `Added_By`, `University_ID`, `Source`, `Sub_Source`) VALUES (2, '$amount', '$transaction_id', '$duration',  '" . $_SESSION['Student_Table_ID'] . "', '" . $_SESSION['Added_For'] . "', '" . $_SESSION['university_id'] . "', 'Student', 'Ledger')");
  } else {
    
   
    $mobile = $_SESSION['Mobile'];
    $conn->query("INSERT INTO Payments (`Type`, `Amount`, `Transaction_ID`, `Duration`, `Added_For`,  `Added_By`, `University_ID`, `Source`, `Sub_Source`) VALUES (2, '$amount', '$transaction_id', '$duration', '$std_row_ID', '" . $_SESSION['ID'] . "', '" . $_SESSION['university_id'] . "', 'Accounts', 'Student Ledger')");
  }
  
  $conn->query("INSERT INTO Invoices (`Invoice_No`, `Student_ID`, `Duration`, `University_ID`, `Amount`, `User_ID`) VALUES ('$transaction_id', '$std_row_ID', '$duration','" . $_SESSION['university_id'] . "','$amount' , '" . $_SESSION['ID'] . "')");

  // Split Payment into half
  
  if($amount > 200){

  $split_accounts = json_encode(array(
      "Edtech Innovate Pvt Ltd." => $amount
  ));
    } else {
    echo json_encode(['status' => 400, 'message' => 'Amount must be grater than Rs.200!']);
    exit();
  }
 

  $value = $key . '|' . $transaction_id . '|' . $amount . '|' . $product_info . '|' . trim($_SESSION['Name']) . '|' . trim($_SESSION['Email']) . '|||||||||||' . $salt;
  $hash = hash('sha512', $value);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://pay.easebuzz.in/payment/initiateLink',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
      'key' => $key,
      'txnid' => $transaction_id,
      'amount' => $amount,
      'productinfo' => $product_info,
      'firstname' => trim($_SESSION['Name']),
      'phone' => $mobile,
      'email' => trim($_SESSION['Email']),
      'surl' => 'http://localhost:3000/response.php',
      'furl' => 'http://localhost:3000/response.php',
      'udf1' => "",
      'udf2' => "",
      'udf3' => "",
      'udf4' => "",
      'udf5' => "",
      'udf6' => "",
      'udf7' => "",
      "split_payments" => $split_accounts,
      'hash' => $hash
    ),
    CURLOPT_HTTPHEADER => array(
      'Cookie: csrftoken=snWOPdXYVpBAqLuFIUxANDKiKw3slBhr'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);

  echo $response;
}