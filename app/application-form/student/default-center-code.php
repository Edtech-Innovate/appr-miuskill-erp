<?php
if (isset($_POST)) {
  session_start();
  require '../../../includes/db-config.php';

  $user = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' AND University_User.University_ID = " . $_SESSION['university_id']);
  if ($user->num_rows > 0) {
    $user = $user->fetch_assoc();
    $_SESSION['Added_For'] = $user['ID'];
    echo json_encode(['status' => true, 'message' => "Skipping!"]);
  } else {
    echo json_encode(['status' => false, 'message' => "Something went wrong!"]);
  }
}
