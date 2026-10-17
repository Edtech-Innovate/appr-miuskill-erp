<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';

  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $students = $conn->query("SELECT ID FROM Students WHERE Course_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Students exists!']);
    exit();
  }

  $check = $conn->query("SELECT ID, University_ID, LMS_ID FROM Courses WHERE ID = $id");
  if ($check->num_rows > 0) {
    $check = $check->fetch_assoc();
    $delete = $conn->query("DELETE FROM Courses WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Program deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Program not exists!']);
  }
}
