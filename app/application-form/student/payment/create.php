<?php
if (isset($_POST['amount'])) {
  require '../../../../includes/db-config.php';
  session_start();

  $amount = intval($_POST['amount']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
  $productInfo = mysqli_real_escape_string($conn, $_POST['productInfo']);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['status' => false, 'message' => 'Invalid Email!']));
  }

  if (!validateMobile($mobile)) {
    exit(json_encode(['status' => false, 'message' => 'Invalid Mobile!']));
  }

  $gateway = $conn->query("SELECT * FROM Payment_Gateways WHERE Status = 1 AND University_ID = " . $_SESSION['university_id']);
  $gateway = $gateway->fetch_assoc();

  $txnId = strtoupper(uniqid('SAU'));
  $value = $gateway['Access_Key'] . '|' . $txnId . '|' . $amount . '|' . $productInfo . '|' . trim($_SESSION['Name']) . '|' . $email . '|||||||||||' . $gateway['Secret_Key'];
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
      'key' => $gateway['Access_Key'],
      'txnid' => $txnId,
      'amount' => $amount,
      'productinfo' => $productInfo,
      'firstname' => trim($_SESSION['Name']),
      'phone' => $mobile,
      'email' => trim($email),
      'surl' => 'http://localhost:3000/response.php',
      'furl' => 'http://localhost:3000/response.php',
      'hash' => $hash
    ),
    CURLOPT_HTTPHEADER => array(
      'Cookie: csrftoken=snWOPdXYVpBAqLuFIUxANDKiKw3slBhr'
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $response = json_decode($response, true);
  if (array_key_exists('data', $response)) {
    $status = $response['status'] == 1 ? true : false;
    if ($response['status'] == 1) {
      $add = $conn->query("INSERT INTO Payments (`Type`, `Transaction_ID`, `Amount`, `Added_For`, `Added_By`, `University_ID`, `Source`, `Sub_Source`) VALUES (2, '$txnId', '$amount', " . $_SESSION['Student_Table_ID'] . ", " . $_SESSION['Added_For'] . ", " . $_SESSION['university_id'] . ", 'Student', 'Application Form')");
      if (!$add) {
        exit(json_encode(['status' => false, 'message' => mysqli_error($conn)]));
      }
      $invoice = $conn->query("INSERT INTO Invoices (`Invoice_No`, `Student_ID`, `Duration`, `University_ID`, `Amount`) VALUES ('$txnId', " . $_SESSION['Student_Table_ID'] . ", " . $_SESSION['Duration'] . ", " . $_SESSION['University_ID'] . ", $amount)");
    }
    $data = array(
      "orderId" => $response['data'],
      "accessKey" => $gateway['Access_Key'],
      "status" => $status
    );

    echo json_encode($data);
  }
}
