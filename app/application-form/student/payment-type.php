<?php
if (isset($_POST)) {
  require '../../../includes/db-config.php';
  session_start();

  $student = $conn->query("SELECT Admission_Type_ID, Duration FROM Students WHERE ID = " . $_SESSION['Student_Table_ID']);
  $student = $student->fetch_assoc();

  $admission_type = $student['Admission_Type_ID'];
  $duration = $student['Duration'];

  echo '<option value="">Choose</option>';
  $fee_dropdowns = $conn->query("SELECT * FROM Fee_Dropdowns WHERE Admission_Type_ID = $admission_type AND Duration = $duration AND University_ID = " . $_SESSION['university_id'] . " AND Status = 1 UNION SELECT * FROM Fee_Dropdowns WHERE Admission_Type_ID = $admission_type AND Duration = 0 AND University_ID = " . $_SESSION['university_id'] . " AND Status = 1 UNION SELECT * FROM Fee_Dropdowns WHERE Admission_Type_ID IS NULL AND Duration = 0 AND University_ID = " . $_SESSION['university_id'] . " AND Status = 1 ORDER BY Admission_Type_ID ASC");
  while ($fee_dropdown = $fee_dropdowns->fetch_assoc()) {
    echo '<option value="' . $fee_dropdown['ID'] . '">' . $fee_dropdown['Name'] . '</option>';
  }
}
