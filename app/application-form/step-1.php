<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['center'])) {

  require '../../includes/db-config.php';
  session_start();

  $id = array_key_exists('inserted_id', $_POST)
    ? intval($_POST['inserted_id'])
    : 0;

  // =====================================================
  // REQUIRED
  // =====================================================

  $center = intval($_POST['center']);
  $admission_session = intval($_POST['admission_session']);
  $admission_type = intval($_POST['admission_type']);
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $duration = intval($_POST['duration']);

  if (
    empty($center) ||
    empty($admission_session) ||
    empty($admission_type) ||
    empty($course) ||
    empty($sub_course) ||
    empty($duration)
  ) {
    exit(json_encode([
      'status' => 400,
      'message' => 'All fields are required'
    ]));
  }

  // =====================================================
  // FULL NAME
  // First Name is mandatory.
  // Middle Name and Last Name are optional.
  // =====================================================

  $full_name = isset($_POST['full_name'])
    ? mysqli_real_escape_string($conn, $_POST['full_name'])
    : '';

  $full_name = trim(preg_replace('/\s+/', ' ', $full_name));

  // First name is mandatory
  if (empty($full_name)) {
    exit(json_encode([
      'status' => 400,
      'message' => 'First name is required'
    ]));
  }

  $full_name_parts = explode(' ', $full_name, 3);
  $count = count($full_name_parts);

  if ($count == 1) {

    // =============================================
    // ONLY FIRST NAME
    // =============================================

    $first_name = trim($full_name_parts[0]);
    $first_name = strtoupper(strtolower($first_name));

    $middle_name = NULL;
    $last_name = NULL;

  } elseif ($count == 2) {

    // =============================================
    // FIRST NAME + LAST NAME
    // =============================================

    $first_name = trim($full_name_parts[0]);
    $first_name = strtoupper(strtolower($first_name));

    $middle_name = NULL;

    $last_name = trim($full_name_parts[1]);
    $last_name = strtoupper(strtolower($last_name));

  } else {

    // =============================================
    // FIRST NAME + MIDDLE NAME + LAST NAME
    // =============================================

    $first_name = trim($full_name_parts[0]);
    $first_name = strtoupper(strtolower($first_name));

    $middle_name = trim($full_name_parts[1]);
    $middle_name = strtoupper(strtolower($middle_name));

    $last_name = trim($full_name_parts[2]);
    $last_name = strtoupper(strtolower($last_name));
  }

  // =====================================================
  // FATHER NAME
  // =====================================================

  $father_name = mysqli_real_escape_string(
    $conn,
    $_POST['father_name']
  );

  $father_name = strtoupper(strtolower($father_name));

  // =====================================================
  // MOTHER NAME
  // =====================================================

  $mother_name = mysqli_real_escape_string(
    $conn,
    $_POST['mother_name']
  );

  $mother_name = strtoupper(strtolower($mother_name));

  // =====================================================
  // DOB
  // =====================================================

  $dob = mysqli_real_escape_string(
    $conn,
    $_POST['dob']
  );

  $dob = date(
    'Y-m-d',
    strtotime($dob)
  );

  // =====================================================
  // OTHER DETAILS
  // =====================================================

  $gender = mysqli_real_escape_string(
    $conn,
    $_POST['gender']
  );

  $category = mysqli_real_escape_string(
    $conn,
    $_POST['category']
  );

  $employment_status = mysqli_real_escape_string(
    $conn,
    $_POST['employment_status']
  );

  $marital_status = mysqli_real_escape_string(
    $conn,
    $_POST['marital_status']
  );

  $religion = mysqli_real_escape_string(
    $conn,
    $_POST['religion']
  );

  $aadhar = mysqli_real_escape_string(
    $conn,
    $_POST['aadhar']
  );

  $nationality = mysqli_real_escape_string(
    $conn,
    $_POST['nationality']
  );

  // =====================================================
  // NULL VALUES FOR OPTIONAL MIDDLE/LAST NAME
  // =====================================================

  $middle_name_sql = (
    $middle_name !== NULL &&
    $middle_name !== ''
  )
    ? "'$middle_name'"
    : "NULL";

  $last_name_sql = (
    $last_name !== NULL &&
    $last_name !== ''
  )
    ? "'$last_name'"
    : "NULL";

  // =====================================================
  // UPDATE EXISTING STUDENT
  // =====================================================

  if (!empty($id)) {

    // 1. Fetch current step of the existing student
    // before updating

    $step_result = $conn->query("
      SELECT Step
      FROM Students
      WHERE ID = $id
    ");

    if (
      $step_result &&
      $step_result->num_rows > 0
    ) {

      $student_data = mysqli_fetch_array(
        $step_result
      );

      $step = $student_data['Step'];

    } else {

      echo json_encode([
        'status' => 400,
        'message' => 'Student not found.'
      ]);

      exit();
    }

    // =================================================
    // UPDATE STUDENT
    // =================================================

    $add_student = $conn->query("
      UPDATE Students SET

        Admission_Type_ID = $admission_type,

        Admission_Session_ID = $admission_session,

        Course_ID = $course,

        Sub_Course_ID = $sub_course,

        Duration = $duration,

        First_Name = '$first_name',

        Middle_Name = $middle_name_sql,

        Last_Name = $last_name_sql,

        Father_Name = '$father_name',

        Mother_Name = '$mother_name',

        DOB = '$dob',

        Aadhar_Number = '$aadhar',

        Category = '$category',

        Gender = '$gender',

        Nationality = '$nationality',

        Employement_Status = '$employment_status',

        Marital_Status = '$marital_status',

        Religion = '$religion',

        Step = 1

      WHERE ID = $id
    ");

    if ($add_student) {

      // =================================================
      // CONDITIONAL DELETE
      // =================================================

      if (
        $step === null ||
        $step === '' ||
        intval($step) <= 1
      ) {

        $conn->query("
          DELETE FROM Student_Academics
          WHERE Student_ID = $id
        ");

        $conn->query("
          DELETE FROM Student_Documents
          WHERE Student_ID = $id
        ");
      }

      // =================================================
      // GENERATE STUDENT LEDGER
      // =================================================

      generateStudentLedger(
        $conn,
        $id
      );

      echo json_encode([
        'status' => 200,
        'message' => 'Step 1 details saved successfully!',
        'id' => $id
      ]);

    } else {

      echo json_encode([
        'status' => 400,
        'message' => 'Something went wrong!'
      ]);
    }

  } else {

    // =====================================================
    // CHECK DUPLICATE STUDENT
    // =====================================================

    $student_check = $conn->query("
      SELECT ID
      FROM Students
      WHERE First_Name = '$first_name'
      AND Father_Name = '$father_name'
      AND Mother_Name = '$mother_name'
      AND DOB = '$dob'
      AND University_ID = " . $_SESSION['university_id'] . "
      AND Course_ID = $course
      AND Added_For = $center
    ");

    if (
      $student_check &&
      $student_check->num_rows > 0
    ) {

      echo json_encode([
        'status' => 400,
        'message' => 'Student with same details already exists!'
      ]);

      exit();
    }

    // =====================================================
    // ADDED BY
    // =====================================================

    $added_by = $_SESSION['Role'] == 'Student'
      ? $center
      : $_SESSION['ID'];

    // =====================================================
    // LEAD ID
    // =====================================================

    $lead_id = null;

    if (empty($lead_id)) {
      $lead_id = 'NULL';
    }

    // =====================================================
    // INSERT STUDENT
    // =====================================================

    $add_student = $conn->query("
      INSERT INTO Students
      (
        Added_By,
        Added_For,
        Lead_Status_ID,
        University_ID,
        Admission_Type_ID,
        Admission_Session_ID,
        Course_ID,
        Sub_Course_ID,
        Duration,
        Admission_Duration,
        First_Name,
        Middle_Name,
        Last_Name,
        Father_Name,
        Mother_Name,
        DOB,
        Aadhar_Number,
        Category,
        Gender,
        Nationality,
        Employement_Status,
        Marital_Status,
        Religion,
        Step
      )
      VALUES
      (
        " . $added_by . ",
        $center,
        $lead_id,
        " . $_SESSION['university_id'] . ",
        $admission_type,
        $admission_session,
        $course,
        $sub_course,
        $duration,
        $duration,
        '$first_name',
        $middle_name_sql,
        $last_name_sql,
        '$father_name',
        '$mother_name',
        '$dob',
        '$aadhar',
        '$category',
        '$gender',
        '$nationality',
        '$employment_status',
        '$marital_status',
        '$religion',
        1
      )
    ");

    if ($add_student) {

      // =================================================
      // GET NEW STUDENT ID
      // =================================================

      $student_id = $conn->insert_id;

      // =================================================
      // GENERATE STUDENT LEDGER
      // =================================================

      generateStudentLedger(
        $conn,
        $student_id
      );

      // =================================================
      // GENERATE UNIQUE STUDENT ID
      // =================================================

      if (
        empty($lead_id) ||
        $lead_id == 'NULL'
      ) {

        $has_unique_student_id = $conn->query("
          SELECT
            ID_Suffix,
            Max_Character
          FROM Universities
          WHERE ID = " . $_SESSION['university_id'] . "
          AND Has_Unique_StudentID = 1
        ");

        if (
          $has_unique_student_id &&
          $has_unique_student_id->num_rows > 0
        ) {

          $uni_data =
            $has_unique_student_id->fetch_assoc();

          $suffix =
            $uni_data['ID_Suffix'];

          $characters =
            $uni_data['Max_Character'];

          $unique_id = generateStudentID(
            $conn,
            $suffix,
            $characters,
            $_SESSION['university_id']
          );

          $conn->query("
            UPDATE Students
            SET Unique_ID = '$unique_id'
            WHERE ID = $student_id
          ");
        }

      } else {

        // =================================================
        // GET UNIQUE ID FROM LEAD
        // =================================================

        $unique_id = $conn->query("
          SELECT Unique_ID
          FROM Lead_Status
          WHERE ID = $lead_id
        ");

        $unique_id =
          $unique_id->fetch_assoc();

        $conn->query("
          UPDATE Students
          SET Unique_ID = '" .
          $unique_id['Unique_ID'] . "'
          WHERE ID = $student_id
        ");

        // =================================================
        // GET / CREATE FINAL STAGE
        // =================================================

        $final_stage = $conn->query("
          SELECT ID
          FROM Stages
          WHERE Name = 'Step 1 Completed'
        ");

        if (
          $final_stage &&
          $final_stage->num_rows > 0
        ) {

          $final_stage =
            $final_stage->fetch_assoc();

          $final_stage =
            $final_stage['ID'];

        } else {

          $final_stage = $conn->query("
            INSERT INTO Stages
            (
              `Name`,
              Is_Last
            )
            VALUES
            (
              'Step 1 Completed',
              1
            )
          ");

          $final_stage =
            $conn->insert_id;
        }

        // =================================================
        // UPDATE LEAD STATUS
        // =================================================

        $conn->query("
          UPDATE Lead_Status
          SET
            Admission = 1,
            Stage_ID = $final_stage,
            Reason_ID = NULL
          WHERE ID = $lead_id
        ");
      }

      // =================================================
      // SUCCESS
      // =================================================

      echo json_encode([
        'status' => 200,
        'message' => 'Step 1 details saved successfully!',
        'id' => $student_id
      ]);

    } else {

      // =================================================
      // INSERT FAILED
      // =================================================

      echo json_encode([
        'status' => 400,
        'message' => 'Something went wrong!'
      ]);
    }
  }
}
?>
