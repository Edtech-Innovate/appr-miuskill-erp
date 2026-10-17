<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = intval($_GET['id']);
  $sub_course = $conn->query("SELECT Min_Duration FROM Sub_Courses WHERE ID = $id");
  $sub_course = $sub_course->fetch_assoc();
  
  $current_duration = $_SESSION['Role'] == 'Student' ? $_SESSION['Duration'] : $sub_course['Min_Duration'];
  $minDuration = json_decode($sub_course['Min_Duration'], true);
  echo '<option value="">Choose</option>';
  for ($i = 1; $i <= (int)$minDuration; $i++) {
    $selected = ($i == $current_duration) ? 'selected' : '';
    echo '<option value="'. $i . '" ' . $selected . '>' . $i . '</option>';
  }
  
  $conn->close();
}