<?php
if(isset($_POST['to'])){
  include '../../../filestobeincluded/config.php';
  require '../../../filestobeincluded/db_config.php';
  require("../../../app/sendgrid/sendgrid-php.php");

  $to_email = mysqli_real_escape_string($conn, $_POST['to']);

  $details = $conn->query("SELECT Meta FROM Integrations WHERE Name = 'SendGrid'");
  if($details->num_rows==0){
    echo json_encode(['status'=>400, 'message'=>'Please add Sendgrid details!']);
    exit();
  }

  $details = $details->fetch_assoc();
  $details = json_decode($details['Meta'], true);
  $api_key = $details['key'];
  $from = $details['from'];

  $email = new \SendGrid\Mail\Mail();
  $email->setFrom($from, $app_name);
  $email->setSubject("Test Verification Email");
  $email->addTo($to_email, $app_name);
  
  $email->addContent("text/html", 'This is the test Email from '.$app_name);
  $key_id = $api_key;
  
  $sendgrid = new \SendGrid($key_id);
  try {
    $response = $sendgrid->send($email);
    $conn->query("UPDATE Integrations SET Is_Verified = now() WHERE Name = 'SendGrid'");
    echo json_encode(['status'=>$response->statusCode(), "message"=>"Sendgrid verified successfully!"]);
  } catch (Exception $e) {
    echo json_encode(['status'=>400, "message"=>"Unable to verify Sendgrid!"]);
  }
}
  
