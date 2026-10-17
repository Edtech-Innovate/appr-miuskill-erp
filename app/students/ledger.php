<?php
if (isset($_GET['id']) && !empty($_GET['id'])) {
 
  require '../../includes/db-config.php';
  session_start();
  
  $id = intval($_GET['id']);
  
  if ($id <= 0) {
     echo "<div class='alert alert-danger'>Invalid Student ID provided.</div>";
     exit;
  }
  
  $_SESSION['student_row_id'] = $id;

  $heads = array();
  $fee_heads = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id']);
  while ($fee_head = $fee_heads->fetch_assoc()) {
    $heads[$fee_head['ID']] = $fee_head['Name'];
  }
  
  $student_query = $conn->query("SELECT Duration, University_ID FROM Students WHERE ID = $id");
  if ($student_query->num_rows == 0) {
     echo "<div class='alert alert-danger'>Student record not found.</div>";
     exit;
  }
  $student_data = $student_query->fetch_assoc();
    
  $student_profile_query = $conn->query("SELECT Admission_Sessions.Name as Session, Admission_Types.Name as Admission_Type, Courses.Short_Name as Course, Sub_Courses.Name as Sub_Course, Students.Duration as Duration, Student_Documents.Location, Modes.Name as Mode FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' LEFT JOIN Modes ON Sub_Courses.Mode_ID = Modes.ID WHERE Students.ID = $id");
  
  $student = $student_profile_query->fetch_assoc();
  $student_duration = !empty($student['Duration']) ? intval($student['Duration']) : 0;
?>

<style>
  .ledger-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    width: 80%;
    margin: 20px auto;
  }
  .profile-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }
  .profile-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    border: 2px solid #007bff;
  }
  .action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 20px;
  }
  .custom-btn {
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background 0.2s;
  }
  .btn-primary-custom { background-color: #007bff; color: #fff; }
  .btn-primary-custom:hover { background-color: #0056b3; }
  .btn-success-custom { background-color: #28a745; color: #fff; }
  .btn-success-custom:hover { background-color: #218838; }
  .btn-outline-custom { background-color: #f8f9fa; color: #17a2b8; border: 1px solid #17a2b8; }
  .btn-outline-custom:hover { background-color: #17a2b8; color: #fff; }

  .table-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
  }
  .table-responsive {
    width: 100%;
    overflow-x: auto;
  }
  .custom-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
  }
  .custom-table th {
    background-color: #343a40;
    color: #ffffff;
    padding: 12px 15px;
    font-weight: 600;
  }
  .custom-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
  }
  .custom-table tbody tr:hover {
    background-color: #f8f9fa;
  }
  .text-right { text-align: right; }
  
  .year-header-row td {
    background-color: #e9ecef !important;
    font-weight: bold;
    color: #495057;
    padding: 10px 15px;
    border-top: 2px solid #ced4da;
    border-bottom: 2px solid #ced4da;
  }

  .highlight-credit {
    background-color: #d4edda !important;
    color: #155724;
    font-weight: 600;
  }
  .highlight-debit {
    background-color: #f8d7da !important;
    color: #721c24;
    font-weight: 600;
  }
  .badge-dr {
    color: #721c24;
    background: #f8d7da;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }
  .badge-cr {
    color: #155724;
    background: #d4edda;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }
  .alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 500;
  }
  .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
</style>

<div class="ledger-container">
  <div style="display: flex; justify-content: center;">
    <div style="width: 350px;">
      <div class="profile-card">
        <img class="profile-img" src="<?= htmlspecialchars($student['Location']) ?>" alt="Profile">
        <h4 style="margin: 5px 0 2px 0;"><?= htmlspecialchars($student['Session']) ?> <span style="font-size: 14px; color: #6c757d;">(<?= htmlspecialchars($student['Admission_Type']) ?>)</span></h4>
        <h5 style="margin: 0; color: #495057; font-weight: normal;"><?= htmlspecialchars($student['Course']) ?> (<?= htmlspecialchars($student['Sub_Course']) ?>)</h5>
      </div>
    </div>
  </div>

  <div class="action-buttons">
    <?php if (isset($_SESSION['gateway'])) { ?>
    <button type="button" class="custom-btn btn-primary-custom" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')">Pay Online</button>
    <?php } ?>
    <button class="custom-btn btn-success-custom" onclick="add('offline-payments', 'lg')">Pay Offline</button>
    <a href="/app/applications/transaction-details.php?id=<?= $_GET['id'] ?>" class="custom-btn btn-outline-custom">Transaction Details</a>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th><?= htmlspecialchars($student['Mode']) ?></th>
            <th>Date</th>
            <th>Particular</th>
            <th>Source</th>
            <th>Transaction ID</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Balance</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $temp_balance = 0;
          $initial_ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $id AND Duration <= " . $student_duration . " AND Status = 1 ORDER BY Duration, Type, Created_At");
          
          while ($ledger = $initial_ledgers->fetch_assoc()) {
              $fees = json_decode($ledger['Fee'], true);
              foreach ($fees as $value) {
                  if (empty($value)) continue;
                  $debit = $ledger['Type'] == 1 ? abs($value) : 0;
                  $credit = ($ledger['Type'] == 2 || $ledger['Type'] == 3) ? abs($value) : 0;
                $temp_balance = ($temp_balance + $credit) - $debit;
                  
              }
          }

          $display_max_duration = $student_duration;
          if ($temp_balance == 0) {
              $display_max_duration = $student_duration + 1;
          }
          
          $balance = 0;
          $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $id AND Duration <= " . $display_max_duration . " AND Status = 1 ORDER BY Duration, Type, Created_At");
          
          $current_duration = null;

          while ($ledger = $ledgers->fetch_assoc()) {
            if ($current_duration !== $ledger['Duration']) {
                $current_duration = $ledger['Duration'];
                echo '<tr class="year-header-row"><td colspan="8">📌 ' . htmlspecialchars($student['Mode']) . ': Year / Duration ' . $current_duration . '</td></tr>';
            }

            $fees = json_decode($ledger['Fee'], true);
            foreach ($fees as $key => $value) {
              if (empty($value)) {
                continue;
              }
              $debit = $ledger['Type'] == 1 ? abs($value) : 0;
              $credit = ($ledger['Type'] == 2 || $ledger['Type'] == 3) ? abs($value) : 0;
          ?>
            <tr>
              <td><?= htmlspecialchars($ledger['Duration']) ?></td>
              <td><?= date("d-m-Y", strtotime($ledger['Created_At'])) ?></td>
              <td><?php echo $key == "Paid" || $key == "Fee" ? '' : ($key == "Late Fine" ? $key : $heads[$key]) ?></td>
              <td><?= htmlspecialchars($ledger['Source']) ?></td>
              <td><u style="color: #007bff;"><?= htmlspecialchars($ledger['Transaction_ID']) ?></u></td>
              
              <td class="text-right <?= $debit > 0 ? 'highlight-debit' : '' ?>">
                <?= $debit > 0 ? number_format($debit, 2) : '0' ?>
              </td>
              
              <td class="text-right <?= $credit > 0 ? 'highlight-credit' : '' ?>">
                <?= $credit > 0 ? number_format($credit, 2) : '0' ?>
              </td>
              
              <td class="text-right">
                <?php  
                $balance = ($balance + $credit) - $debit;

                if ($balance < 0) {
                  echo "<span class='badge-dr'>" . number_format(-1 * ($balance), 2) . " Dr</span>";
                } elseif ($balance > 0) {
                  echo "<span class='badge-cr'>" . number_format($balance, 2) . " Cr</span>";
                } else {
                  echo "0.00";
                }
                ?>
              </td>
            </tr>
          <?php  
            }
          }
          $_SESSION['amount_online_payment_for_student_ledger'] = abs($balance);
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php } else {
    echo "<div class='alert alert-warning'>No student selected.</div>";
} ?>