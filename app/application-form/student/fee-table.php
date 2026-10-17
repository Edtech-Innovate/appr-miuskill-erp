<?php
if (isset($_POST['payment_type'])) {
  require '../../../includes/db-config.php';
  session_start();

  $student = $conn->query("SELECT Admission_Session_ID, Admission_Type_ID, Sub_Course_ID, Duration FROM Students WHERE ID = " . $_SESSION['Student_Table_ID']);
  $student = $student->fetch_assoc();

  $admission_session = $student['Admission_Session_ID'];
  $sub_course = $student['Sub_Course_ID'];
  $duration = $student['Duration'];
  $payment_type = intval($_POST['payment_type']);

  // Session
  $session = $conn->query("SELECT Scheme FROM Admission_Sessions WHERE ID = " . $admission_session);
  $session = $session->fetch_assoc();
  $schemes = json_decode($session['Scheme'], true);

  foreach ($schemes['dates'] as $schemeId => $date) {
    if (date("Y-m-d") >= $date) {
      $scheme_id = $schemeId;
    }
  }

  // Has Sharing
  $universitySharing = $conn->query("SELECT Sharing FROM Universities WHERE ID = " . $_SESSION['university_id']);
  $universitySharing = $universitySharing->fetch_assoc();
  $universitySharing = $universitySharing['Sharing'];

  // Scheme - Fee Structure
  $scheme = $conn->query("SELECT Fee_Structure FROM Schemes WHERE ID = " . $scheme_id);
  $scheme = $scheme->fetch_assoc();
  $schemeStructure = json_decode($scheme['Fee_Structure'], true);

  $sub_course_detail = $conn->query("SELECT Mode_ID, Min_Duration FROM Sub_Courses WHERE ID = $sub_course");
  $sub_course_detail = $sub_course_detail->fetch_assoc();

  $maxDuration = $sub_course_detail['Min_Duration'];

  $mode = $conn->query("SELECT Name FROM Modes WHERE ID = " . $sub_course_detail['Mode_ID']);
  $mode = $mode->fetch_assoc();
  $modes = $mode['Name'];

  $fee_dropdown = $conn->query("SELECT * FROM Fee_Dropdowns WHERE ID = $payment_type AND Status = 1");
  if ($fee_dropdown->num_rows > 0) {

    $table_top = '<div class="col-md-12"><div class="responsive"><table class="table table-bordered">';
    $table_header = '';
    $table_body = '';
    $table_bottom = '</table></div></div>';

    $fee_dropdown = $fee_dropdown->fetch_assoc();

    $fee_structures = json_decode($fee_dropdown['Fee_Structure'], true);
    $fee_structures = array_intersect($fee_structures, $schemeStructure);

    $semester = !empty($fee_dropdown['Semester']) ? json_decode($fee_dropdown['Semester'], true) : [];

    $applicableOn = array();
    $heads = array();
    $feeApplicables = $conn->query("SELECT ID, Fee_Applicable_ID, Name FROM Fee_Structures WHERE ID IN (" . implode(',', $fee_structures) . ")");
    while ($feeApplicable = $feeApplicables->fetch_assoc()) {
      $applicableOn[$feeApplicable['ID']] = $feeApplicable['Fee_Applicable_ID'];
      $heads[$feeApplicable['ID']] = $feeApplicable['Name'];
    }

    $max_count = array();
    foreach ($semester as $key => $value) {
      $max_count[$key] = count($value);
    }

    $hasMaxSem = !empty($max_count) ? array_search(max($max_count), $max_count) : $fee_structures[0];
    $allSemester = !empty($max_count) ? $semester[$hasMaxSem] : [1];
    if (count($allSemester) > 1) {
      $newAllSemesters = array();
      foreach ($allSemester as $sems) {
        if ($sems >= $duration && $sems <= $maxDuration) {
          $newAllSemesters[] = $sems;
        }
      }
      $allSemester = $newAllSemesters;
    }

    if (!empty($semester) && count($allSemester) > 1) {
      $selected_heads = array($modes);
    } else {
      $selected_heads = array();
    }

    foreach ($fee_structures as $value) {
      $selected_heads[$value] = $heads[$value];
    }

    // Late Fee
    $lateFeeApplied = 0;
    if ($fee_dropdown['Late_Fee'] == 1) {
      $late_fees = $conn->query("SELECT * FROM Late_Fees WHERE University_ID = " . $_SESSION['university_id'] . " AND Status = 1 ORDER BY Start_Date DESC");
      if ($late_fees->num_rows > 0) {
        while ($late_fee = mysqli_fetch_assoc($late_fees)) {
          $sessions = !empty($late_fee['Admission_Session']) ? json_decode($late_fee['Admission_Session'], true) : array();
          if (in_array($admission_session, $sessions) && date("Y-m-d") >= $late_fee['Start_Date']) {
            $lateFeeApplied = 1;
            array_push($selected_heads, 'Late Fine');
            break;
          }
        }
      }
    }

    array_push($selected_heads, 'Total');

    $table_header = '<thead><tr><th>' . implode("</th><th>", $selected_heads) . '</th></tr></thead>';
    $table_body = "<tbody>";

    $applicability = array();
    $courseFee = array();
    if ($universitySharing) {
      $fees = $conn->query("SELECT Fee, Fee_Structure_ID, Applicable_In FROM Fee_Constant WHERE Scheme_ID = $scheme_id AND Sub_Course_ID = $sub_course AND Fee_Structure_ID IN (" . implode(',', $fee_structures) . ")");
      while ($fee = $fees->fetch_assoc()) {
        $courseFee[$fee['Fee_Structure_ID']] = $fee['Fee'];
        $applicability[$fee['Fee_Structure_ID']] = !empty($fee['Applicable_In']) ? json_decode($fee['Applicable_In'], true) : json_decode('{"4": []}');
      }
    } else {
      $fees = $conn->query("SELECT Fee FROM User_Sub_Courses WHERE Scheme_ID = $scheme_id AND Sub_Course_ID = $sub_course AND User_ID = $user_id");
      $fees = $fees->fetch_assoc();
      $fees = json_decode($fees['Fee'], true);
      foreach ($fees as $id => $fee) {
        if (in_array($id, $fee_structures)) {
          $courseFee[$id] = $fee;
          $applicability[$id] = array($applicableOn[$id] => range(1, $maxDuration));
        }
      }
    }


    $grandTotal = array();
    $oneTimeFee = array();
    $lateFeeApplied = 0;
    foreach ($allSemester as $sem) {
      $total_fee = array();
      $table_body .= "<tr>";
      foreach ($selected_heads as $key => $value) {
        if (array_key_exists($key, $courseFee)) {
          $feeSem = array_key_exists($key, $semester) ? $semester[$key] : ['OneTime'];
          $fee = in_array($sem, $feeSem) && in_array($sem, $applicability[$key][$applicableOn[$key]]) ? $courseFee[$key] : '';
          if (empty($fee)) {
            $fee = in_array('OneTime', $feeSem) && !in_array($key, $oneTimeFee) ? $courseFee[$key] : '';
            $oneTimeFee[] = $key;
          }
          $total_fee[] = $fee;
          $table_body .= "<td>" . $fee . "</td>";
        } elseif ($value == 'Late Fine') {
          if ($lateFeeApplied == 0) {
            $lateFeeApplied = 1;
            $total_fee[] = $late_fee['Fee'];
            $table_body .= "<td>" . $late_fee['Fee'] . "</td>";
          } else {
            $total_fee[] = '';
            $table_body .= "<td></td>";
          }
        } elseif ($value == $modes && count($allSemester) > 1) {
          $table_body .= '<td>' . $sem . '</td>';
        } elseif ($value == 'Total') {
          $grandTotal[] = array_sum($total_fee);
          $table_body .= '<td class="text-end">' . array_sum($total_fee) . '</td>';
        }
      }
      $table_body .= "</tr>";
    }

    $tableColumns = count($selected_heads);

    if (count($allSemester) > 1) {
      $table_body .= '<tr><td colspan="' . ($tableColumns - 1) . '"><b>Grand Total</b></td><td class="text-end">' . array_sum($grandTotal) . '</td></tr>';
    }

    $table_body .= "</tbody>";

    $finalTable = $table_top . $table_header . $table_body . $table_bottom;
    echo json_encode(['status' => true, 'table' => $finalTable, 'name' => $fee_dropdown['Name'], 'amount' => array_sum($grandTotal)]);
  }
}
