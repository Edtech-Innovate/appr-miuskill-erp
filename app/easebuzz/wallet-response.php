<?php
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_POST)) {
  require '../../includes/db-config.php';
//   require '../../includes/helpers.php';
  
  if (isset($_POST['response'])) {
    $response = is_array($_POST['response']) ? $_POST['response'] : [];

    if (empty($response)) {
      echo json_encode(['status' => false, 'message' => 'Payment Failed!']);
      exit();
    }

    if (strcasecmp($response['status'], 'success') == 0) {
      $gateway_id = $_POST['response']['easepayid'];
      $transaction_id = $_POST['response']['txnid'];
      $mode = $_POST['response']['mode'];
      $meta = json_encode(["msg" => $response]);

      $update = $conn->query("UPDATE Wallets SET Gateway_ID = '$gateway_id', Payment_Mode = '$mode', Meta = '$meta', Status = 1 WHERE Transaction_ID = '$transaction_id' AND `Type` = 2");
      if($update) {
        //$sutdents = $conn->query("SELECT Student_ID, Amount, Duration, University_ID FROM Wallet_Invoices WHERE Invoice_No = '$transaction_id'");
        //while ($sutdent = $sutdents->fetch_assoc()) {
         // $conn->query("UPDATE Student_Ledgers SET Updated_At = now(), Type = 2, Source = 'Online', Transaction_ID = '$transaction_id' WHERE Student_ID = " . $sutdent['Student_ID']);
          //$conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = " . $sutdent['Student_ID']);
        //}
        
         // send mail
        $userdata = $conn->query("SELECT Name, Code, Added_By as user_id,Vertical_type, Amount FROM Users left join Wallets on Added_By = Users.ID WHERE Transaction_ID = '$transaction_id' AND `Type` = 2");
        $userdata = $userdata->fetch_assoc();
        $center_name = $userdata['Name'] . '(' . $userdata['Code'] . ')';
        $vartical_type = $userdata['Vertical_type'];
        $amount = $userdata['Amount'];
        
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date("d-m-Y h:i:sa");
        $subject = "Addition Confirmation of {$amount} Rupees to {$center_name} Wallet through Online Mode";

        $message = "
            <p>Dear Reporting Manager,</p>
            <p>I hope this email finds you well.</p>
            <p>An amount of <strong>₹{$amount}</strong> has been added to the wallet of <b>{$center_name}</b> through Online Mode on {$currentTime}.</p>
            <p><em><strong>This is a system-generated email. Please do not reply.</strong></em></p>
            <p>Thanks & Regards,<br>Edtech Innovate Pvt. Ltd.</p>
        ";

        // if ($vartical_type == 0) { // 1- edtech  and 0-iits
        //   $accountent_email = "groupaccounts@iitseducation.org";
        //   if ($_SESSION['university_id'] == 48) {
        //     $operation_email = "syam@iitseducation.org";
        //   } else {
        //     $operation_email = "akhil@iitseducation.org";
        //   }
        // } else {
        //   $accountent_email = "Finance@edtechinnovate.com";
        //   $operation_email = "arya@edtechinnovate.com";
        // }

        // $to = $accountent_email . "," . $operation_email;
        // sendMail($to, $subject, $message);

        echo json_encode(['status' => true, 'message' => 'Payment updated!']);
      } else {
        echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
      }
    }
  }
}
