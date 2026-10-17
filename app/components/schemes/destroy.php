<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../../includes/db-config.php';

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $check = $conn->query("SELECT ID, LMS_ID, University_ID FROM Schemes WHERE ID = $id");
  if ($check->num_rows > 0) {
    $check = $check->fetch_assoc();
    $delete = $conn->query("DELETE FROM Schemes WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Scheme deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Scheme not exists!']);
  }
}
