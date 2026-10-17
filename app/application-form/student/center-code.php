<?php
if (isset($_POST['code'])) {
  require '../../../includes/db-config.php';
  session_start();
  $code = mysqli_real_escape_string($conn, $_POST['code']);

  $user = $conn->query("SELECT ID, UPPER(Name) as Name FROM Users WHERE Code = '$code'");
  if ($user->num_rows > 0) {
    $user = $user->fetch_assoc();
    $_SESSION['Added_For'] = $user['ID'];
    echo json_encode(['status' => true, 'message' => "Your Co-Ordinator is " . $user['Name']]);
  } else {
    echo json_encode(['status' => false, 'message' => 'Code is invalid!']);
  }
}
