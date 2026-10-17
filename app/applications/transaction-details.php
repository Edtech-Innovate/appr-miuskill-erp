<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

## Database and environment setup
require '../../includes/db-config.php';
session_start();

## Fallback redirect if identification is missing
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Invalid context or missing record targeting identifiers.");
}

## Replicating your native decoding string manipulation sequence precisely
//$student_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($_GET['id'])));
$student_id = $_GET['id'];

## Fetch basic Student Profile information for header rendering
$student_stmt = $conn->prepare("SELECT Admission_Sessions.Name as Session, Admission_Types.Name as Admission_Type, Courses.Short_Name as Course, Sub_Courses.Name as Sub_Course, Student_Documents.Location FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' WHERE Students.ID = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_profile = $student_stmt->get_result()->fetch_assoc();

## Apply System Security constraints
$university_query = $_SESSION['Role'] != 'Administrator' ? " AND Payments.University_ID =" . intval($_SESSION['university_id']) : '';
$role_query       = str_replace('{{ table }}', 'Payments', $_SESSION['RoleQuery']);
$role_query       = str_replace('{{ column }}', 'Added_By', $role_query);

## Query History Records
$query_string = "SELECT 
                    Payments.ID, 
                    Payments.Type, 
                    Payments.Duration,
                    Payments.Created_At as Transaction_Date, 
                    Payments.Transaction_ID, 
                    Payments.Gateway_ID, 
                    Payments.Amount, 
                    Payments.Payment_Mode, 
                    Payments.Bank, 
                    Payments.Status, 
                    CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University 
                 FROM Payments 
                 LEFT JOIN Universities ON Payments.University_ID = Universities.ID 
                 WHERE Payments.Added_For = ? 
                 $university_query 
                 $role_query 
                 ORDER BY Payments.ID DESC";

$stmt = $conn->prepare($query_string);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$records = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        .profile_img { max-height: 100px; border-radius: 50%; border: 2px solid #ddd; }
        .page-container { margin-top: 30px; margin-bottom: 50px; width: 100% !important; max-width: 100% !important; padding: 0 30px; }
        .table { width: 100% !important; }
        .dataTables_wrapper { width: 100%; }
    </style>
</head>
<body>

<div class="page-container">
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="javascript:history.back()" class="btn btn-secondary">&larr; Go Back</a>
        </div>
    </div>

    <?php if ($student_profile): ?>
    <div class="row d-flex justify-content-center mb-4">
        <div class="col-md-6 text-center">
            <div class="card bg-light p-3">
                <?php if (!empty($student_profile['Location'])): ?>
                    <img class="profile_img mx-auto mb-2" src="<?= $student_profile['Location'] ?>" alt="Profile">
                <?php endif; ?>
                <h5><?= $student_profile['Session'] ?> (<?= $student_profile['Admission_Type'] ?>)</h5>
                <h6 class="text-muted"><?= $student_profile['Course'] ?> (<?= $student_profile['Sub_Course'] ?>)</h6>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm p-4">
                <div class="table-responsive">
                    <table id="txHistoryTable" class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Transaction Date</th>
                                <th>Transaction ID</th>
                                <th>University</th>
                                <th>Duration</th>
                                <th>Payment Mode</th>
                                <th>Bank</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($records && $records->num_rows > 0): ?>
                                <?php while ($row = $records->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= empty($row['Transaction_Date']) ? 'N/A' : date("d-m-Y h:i A", strtotime($row['Transaction_Date'])) ?></td>
                                        
                                        <td><strong><?= empty($row['Transaction_ID']) ? 'N/A' : htmlspecialchars($row['Transaction_ID']) ?></strong></td>
                                        
                                        <td><?= empty($row['University']) ? 'N/A' : htmlspecialchars($row['University']) ?></td>
                                        
                                        <td><?= empty($row['Duration']) ? 'N/A' : htmlspecialchars($row['Duration']) ?></td>
                                        
                                        <td>
                                            <?php 
                                            $payment_type = $row['Payment_Mode'] ?? $row['Type'] ?? 0;
                                            if ($payment_type == 1) {
                                                echo '<span class="badge badge-secondary">Offline</span>';
                                            } elseif ($payment_type == 2) {
                                                echo '<span class="badge badge-primary">Online</span>';
                                            } elseif ($payment_type == 3) {
                                                echo '<span class="badge badge-info">Wallet Payment</span>';
                                            } else {
                                                echo htmlspecialchars((string)($payment_type ?: 'N/A'));
                                            }
                                            ?>
                                        </td>
                                        
                                        <td><?= empty($row['Bank']) ? 'N/A' : htmlspecialchars($row['Bank']) ?></td>
                                        
                                        <td><?= empty($row['Amount']) ? '0.00' : htmlspecialchars($row['Amount']) ?></td>
                                        
                                        <td>
                                            <?php 
                                            if (intval($row['Status']) === 1) {
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } elseif (intval($row['Status']) === 2) {
                                                echo '<span class="badge badge-danger">Rejected</span>';
                                            } else {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#txHistoryTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "language": {
            "emptyTable": "No historical statements matching this tracking record."
        }
    });
});
</script>

</body>
</html>