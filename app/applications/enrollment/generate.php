<?php
if (isset($_POST['id'])) {
  require '../../../includes/db-config.php';

  $id = intval($_POST['id']);

  $student = $conn->query("SELECT Admission_Sessions.Name, Admission_Type_ID, Admission_Session_ID, Course_Type_ID, Course_ID, Sub_Course_ID FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.ID = $id");
  if ($student->num_rows > 0) {
    $student = $student->fetch_assoc();
    $enrollmentNo = $student['Name'] . $student['Admission_Type_ID'] . $student['Admission_Session_ID'] . $id . $student['Course_Type_ID'] . $student['Sub_Course_ID'];
    echo json_encode(['status' => true, 'message' => 'Generated!', 'enrollmentNo' => $enrollmentNo]);
  } else {
    echo json_encode(['status' => false, 'message' => 'Student not exists!']);
  }
}
