<?php
if (isset($_GET)) {
  require '../../../includes/db-config.php';
  session_start();

  $student = $conn->query("SELECT Admission_Type_ID, Sub_Course_ID FROM Students WHERE ID = " . $_SESSION['Student_Table_ID']);
  $student = $student->fetch_assoc();

  $id = $student['Sub_Course_ID'];
  $admission_type_id = $student['Admission_Type_ID'];

  $eligibility = $conn->query("SELECT Eligibility FROM Sub_Courses WHERE ID = $id");
  $eligibility = $eligibility->fetch_assoc();
  $eligibility = !empty($eligibility['Eligibility']) ? json_decode($eligibility['Eligibility'], true) : [];

  $required = $eligibility[$admission_type_id]['required'];
  $optional = array_key_exists('optional', $eligibility[$admission_type_id]) ? $eligibility[$admission_type_id]['optional'] : array();

  $all = array_merge($required, $optional);

  if (count($eligibility) > 0) {
    echo json_encode(['status' => true, 'required' => $required, 'optional' => $optional, 'count' => count($all)]);
  } else {
    echo json_encode(['status' => false, 'eligibility' => $eligibility, 'count' => count($eligibility)]);
  }
}
