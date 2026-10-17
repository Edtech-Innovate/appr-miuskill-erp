<?php
if (isset($_POST['name']) && isset($_POST['structure']) && isset($_POST['admission_type']) && isset($_POST['duration']) && isset($_POST['university_id'])) {
  require '../../../includes/db-config.php';

  $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
  $admission_type = intval($_POST['admission_type']);
  $duration = intval($_POST['duration']);
  $fee_heads = $_POST['structure'];
  $semesters = isset($_POST['semesters']) ? $_POST['semesters'] : array();
  $university_id = intval($_POST['university_id']);

  if (!empty($duration) && empty($admission_type)) {
    exit(json_encode(['status' => false, 'message' => 'Please select admission type!']));
  }

  $data = array();
  $heads = $conn->query("SELECT ID FROM Fee_Structures WHERE University_ID = " . $university_id . " AND Fee_Applicable_ID IN (1,2)");
  while ($head = $heads->fetch_assoc()) {
    if (in_array($head['ID'], $fee_heads)) {
      $sems = array_filter($semesters[$head['ID']]);
      if (empty($sems)) {
        exit(json_encode(['status' => false, 'message' => 'Please select semester']));
      }
      $data[$head['ID']] = $sems;
    }
  }

  $admission_type_query = "";
  $duration_query = "";

  if (!empty($admission_type)) {
    $admission_type_query = " AND Admission_Type_ID = " . $admission_type;
  }

  if (!empty($duration)) {
    $duration_query = " AND Duration = " . $duration;
  }

  if (empty($admission_type)) {
    $admission_type = 'NULL';
  }

  $check = $conn->query("SELECT ID FROM Fee_Dropdowns WHERE Name LIKE '$name' AND University_ID = " . $university_id . $admission_type_query . $duration_query);
  if ($check->num_rows > 0) {
    exit(json_encode(['status' => false, 'message' => 'Name already exists']));
  }

  $add = $conn->query("INSERT INTO Fee_Dropdowns (`Name`, `Admission_Type_ID`, `Duration`, `Fee_Structure`, `Semester`, `University_ID`) VALUES ('$name', $admission_type, '$duration', '" . json_encode($fee_heads) . "', '" . json_encode($data) . "', '" . $university_id . "')");
  if ($add) {
    echo json_encode(['status' => true, 'message' => 'Fee Dropdown added successfully!']);
  } else {
    echo json_encode(['status' => false, 'message' => mysqli_error($conn)]);
  }
} else {
  echo json_encode(['status' => false, 'message' => 'Please choose Fee Head(s)']);
}
