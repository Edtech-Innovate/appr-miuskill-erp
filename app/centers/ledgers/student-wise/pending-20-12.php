<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);
  $centerID = intval($_GET['id']);
  $added_for[] = $id;
  $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
  while ($downline = $downlines->fetch_assoc()) {
    $added_for[] = $downline['User_ID'];
  }

  $added_by_sub_center = '';
  $users = implode(",", array_filter($added_for));
  $downlines = $conn->query("SELECT `Created_By`, ID FROM Users WHERE ID = " . $_SESSION['ID'] . " AND Role = 'Sub-Center' ");
  while ($downline = $downlines->fetch_assoc()) {
    $added_for[] = $downline['Created_By'];
    $added_by_sub_center = "Sub-Center";
  }
  $users = implode(",", array_filter($added_for));

  $already = array();
  $already_ids = array();
  if ($added_by_sub_center) {
    $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = " . $_SESSION['ID'] . " AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
  } else {
    $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
  }

  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }

  $query = empty($already_ids) ? " AND ID IS NULL" : " AND ID IN (" . implode(',', $already_ids) . ")";

  $sessionQuery = "";
  if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
    $admission_session_id = intval($_GET['admission_session_id']);
    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
  }

  if ($_SESSION["Role"] == "Sub-Center") {
    $users = intval($_GET['id']);
  } else {
    $subcenter_id = array();
    $subcenter = $conn->query("SELECT Sub_Center FROM `Center_SubCenter` WHERE Center=$id ");
    while ($subcenterArr = $subcenter->fetch_assoc()) {
      $subcenter_id[] = $subcenterArr['Sub_Center'];
    }

    if (!empty($subcenter_id)) {
      $users .= "," . implode(",", array_filter($subcenter_id));
    }
  }

  $students = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID, Students.Duration, Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Invoices.`User_ID` IN ($users) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Students.ID DESC");
  if ($students->num_rows == 0) {
    $students = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Unique_ID , Students.Duration, Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Payments.Added_By IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 0 $sessionQuery ORDER BY Students.ID DESC");
  }

  while ($students_offline_Arr = $students->fetch_assoc()) {
    $studentsArrData[] = $students_offline_Arr;
  }

  // echo "<pre>"; print_r($_SESSION); die;
  if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Payable</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // while ($student = $students->fetch_assoc()) {
              foreach ($studentsArrData as $student) {
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'] ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <td>
                    <?php
                    if ($_SESSION['Role'] == "Center" ||  $_SESSION['Role'] == "Administrator") {
                      $centerArr = array();
                      if ($_SESSION['university_id'] == 48) {
                        $center_fee_Query = $conn->query("SELECT Fee FROM `Center_Sub_Courses` WHERE `User_ID` = $centerID  AND Duration = '" . $student['Duration'] . "' AND `Course_ID` = " . $student['Course_ID'] . " AND `Sub_Course_ID` = " . $student['Sub_Course_ID'] . " AND University_ID=" . $_SESSION['university_id'] . "");
                      } else {
                        $center_fee_Query = $conn->query("SELECT Fee FROM `Center_Sub_Courses` WHERE `User_ID` = $centerID  AND `Course_ID` = " . $student['Course_ID'] . "  AND `Sub_Course_ID` = " . $student['Sub_Course_ID'] . " AND University_ID=" . $_SESSION['university_id'] . "");
                      }
                      $centerArr = $center_fee_Query->fetch_assoc();
                      echo number_format($centerArr['Fee'] * (-1), 2) . " &#8377; ";
                    } else {
                      $balance = 0;
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= '" . $student['Duration'] . "'");
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $balance = $ledger['Fee'];
                      }
                      echo number_format((-1) * $balance, 2) . " &#8377; ";
                    }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php }
}

?>