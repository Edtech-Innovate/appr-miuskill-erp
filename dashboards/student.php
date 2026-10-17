<style>
  .profile_img {
    width: 100px;
    height: 100px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }

  table,
  tr,
  th,
  th {
    border: none !important;
  }

  .profile_table td {
    padding: .5rem !important;
    border: none !important;

  }

  .tile-progress .tile-footer {
    padding: 9px 20px !important;
  }

  .bg-info {
    background-color: #17a2b8 !important
  }

  .table>thead>tr>th {
    border-bottom: 2px solid #f4f4f4;
    font-weight: bold;
    color: black;
  }

  .bg-success {
    background-color: #28a745 !important;
  }

  .bg-primary {
    background-color: #007bff !important;
  }

  h5 {

    line-height: unset;
    margin-bottom: .5rem;
  }

  h4 {
    font-size: 18px !important;
  }

  .card .card-header {
    padding: 5px 16px 5px 16px !important;
  }

  .card.border.border-danger.shadow {
    height: 585px;
  }
</style>

<link rel="stylesheet" href="/assets/css/new-style.css" />
<link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
<div class="row mb-3">
  <div class="col-md-6">
    <div class="card border-primary shadow">
      <div class="card-header bg-primary text-white separator">
        <h5 class="fw-bold"><i class="ti-user mr-2"></i> Profile</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-5">
            <img class="img-fluid rounded border mb-3" width="100" src="<?= $_SESSION['Photo'] ?>" alt="">
            <h6 class="fw-semibold">Name: <?= ucwords(strtolower($_SESSION['Name'])) ?></h6>
            <h6 class="fw-semibold">Student ID: <?= $_SESSION['Unique_ID'] ?></h6>
            <h6 class="fw-semibold">Phone: <?= $_SESSION['Contact'] ?></h6>
            <h6 class="fw-semibold">Email: <?= $_SESSION['Email'] ?></h6>
          </div>
          <div class="col-md-7 border-left">
            <h6 class="fw-semibold">Academic Details:</h6>
            <div class="table-responsive">
              <table class="table mb-0 profile_table">
                <tr>
                  <td class="fw-normal" width="30%"> Adm. Session </td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= $_SESSION['Admission_Session'] ?></td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Adm. Sem</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= $_SESSION['Duration'] ?></td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Enrollment No</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal">
                    <?php echo empty($_SESSION['Enrollment_No']) ? 'Document under verification' : $_SESSION['Enrollment_No'] ?>
                  </td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Course</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= ucwords(strtolower($_SESSION['Course'])) ?></td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Specialization</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= ucwords(strtolower($_SESSION['Sub_Course'])) ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow border-success">
      <div class="card-header bg-success text-white separator">
        <h5 class="fw-bold"> <i class=" ti-ruler-pencil mr-2"></i> Academic Details</h5>
      </div>
      <div class="card-body m-t-10">
        <div class="row">
          <div class="col-md-6">
            <div class="tile-progress tile-pink">
              <div class="tile-header">
                <h4 class="mb-0">Subjects</h4>
                <?php
                $duration = $_SESSION['Duration'];
                $sub_count = $conn->query("SELECT Syllabi.Name FROM Syllabi WHERE Course_ID='" . $_SESSION['Course_ID'] . "' AND Sub_Course_ID='" . $_SESSION['Sub_Course_ID'] . "' AND Semester = '" . $duration . "' AND University_ID ='" . $_SESSION['university_id'] . "'");
                ?>

                <h4 class="mb-0"><?= $sub_count->num_rows; ?></h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"><a href="/student/lms/lms" class="text-white">View Details <i
                      class="ti-arrow-right ml-2"></i></a></h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-red">
              <div class="tile-header">

                <?php
                // $scheme_Query = $conn->query("SELECT Scheme_ID  FROM `Sub_Courses` WHERE Course_ID='" . $_SESSION['Course_ID'] . "' AND `ID` = '" . $_SESSION['Sub_Course_ID'] . "'  ORDER BY `Sub_Courses`.`ID` ASC");
                // $scheme = $scheme_Query->fetch_assoc();
                
                // echo "<pre>"; 
                // print_r($scheme);
                $assignment_count = $conn->query("SELECT *  FROM `Syllabi` WHERE `Sub_Course_ID` = '" . $_SESSION['Sub_Course_ID'] . "' AND Paper_Type='Theory' AND Semester='" . $_SESSION['Duration'] . "'  ORDER BY `Syllabi`.`ID` ASC");
                ?>

                <h4 class="mb-0">Assignments</h4>
                <h4 class="mb-0">0</h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"><a href="/student/lms/assignments" class="text-white">See Assignments <i
                      class="ti-arrow-right ml-2"></i></a></h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-cyan">
              <div class="tile-header">
                <h4 class="mb-0">Exams Session</h4>
                <h4 class="mb-0"><?= $_SESSION['Admission_Session'] ?></h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"> <a href="/student/examination/date-sheets" class="text-white">See Datesheet <i
                      class="ti-arrow-right ml-2"></i></a> </h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-aqua">
              <div class="tile-header">
                <?php
                // $result_Query = $conn->query("SELECT created_at FROM `marksheets` WHERE enrollment_no='" . $_SESSION['Enrollment_No'] . "' ORDER BY `id` DESC LIMIT 1");
                // $resultArr = array();
                // if ($result_Query->num_rows > 0) {
                
                //   $resultArr = $result_Query->fetch_assoc();
                //    $result_date = "";
                // } else {
                $result_date = "Coming Soon";
                // }
                
                ?>
                <h4 class="mb-0">Results</h4>
                <h4 class="mb-0"><?= $result_date; ?></h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"> <a href="/student/examination/results" class="text-white">See Results <i
                      class="ti-arrow-right ml-2"></i></a> </h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row mb-2">
  <div class="col-md-6">
    <div class="card border-info shadow">
      <div class="card-header bg-info text-white separator">
        <h5 class="fw-bold"><i class="ti-agenda mr-2"></i> Subject Overview</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <?php

          $getSyllabi = $conn->query("SELECT Syllabi.ID as subject_id,Syllabi.Name,Sub_Course_ID, Syllabi.Credit FROM Syllabi  LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Modes ON Sub_Courses.Mode_Id = Modes.ID  WHERE Syllabi.Sub_Course_ID ='" . $_SESSION['Sub_Course_ID'] . "' AND Syllabi.Semester='" . $_SESSION['Duration'] . "'");

          if ($getSyllabi->num_rows > 0) { ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Subject Name</th>
                  <th>Credits</th>
                  <th>Ebooks</th>
                  <th>Video</th>
                  <th>Assessments</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $rowArr = array();
                while ($rowArr = mysqli_fetch_assoc($getSyllabi)) {
                  $query = $conn->query("SELECT count(e_books.id) as total_ebook FROM e_books WHERE e_books.subject_id = '" . $rowArr['subject_id'] . "' AND e_books.status =1 AND e_books.sub_course_id='" . $rowArr['Sub_Course_ID'] . "'");
                  $e_bookArr = $query->fetch_assoc();
                  // $video_query = $conn->query("SELECT count(video_lectures.id) as total_vedio FROM video_lectures WHERE video_lectures.subject_id = '" . $rowArr['subject_id'] . "' AND video_lectures.status =1 AND video_lectures.course_id='" . $rowArr['Sub_Course_ID'] . "'");
                  // $videoArr = $video_query->fetch_assoc();
                  $assesmentArr['total_assesment'] = 0;
                  $videoArr['total_vedio'] = 0;
                  ?>
                  <tr>
                    <td><?= $rowArr['Name'] ?></td>
                    <td><?= $rowArr['Credit'] ?></td>
                    <td><a
                        href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=1"><?= $e_bookArr['total_ebook'] ?></a>
                    </td>
                    <td><a
                        href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=2"><?= $videoArr['total_vedio'] ?></a>
                    </td>
                    <td><a
                        href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=3"><?= $assesmentArr['total_assesment'] ?></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <tr>
              <h1 class="text-center" style="font-size: 20px;font-weight: 600;">No Record Found!</h1>
            </tr>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border border-danger shadow">
      <div class="card-header bg-danger text-white separator">
        <h5 class="fw-bold"><i class="ti-bell mr-2"></i> Notifications</h5>
      </div>
      <div class="card-body m-t-10">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Regarding</th>
                <th>Sent To</th>
                <th>Sent On</th>
                <th>Content</th>
                <th>Attachment</th>
              </tr>
            </thead>
            <?php
            $current_notification_id = 0;
            $session = $_SESSION['Admission_Session'];
            list($monthText, $year) = explode('-', $session);
            $monthNumber = date('m', strtotime($monthText));
            $date = $year . '-' . $monthNumber . '-01';
            $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE (Send_To = '" . 'student' . "' OR Send_To = '" . 'all' . "') AND Noticefication_Created_on >= '$date'  ORDER BY Notifications_Generated.ID DESC  ");
            $data = array();
            if ($result_record->num_rows > 0) {
              ?>
              <tbody>
                <?php
                while ($row = $result_record->fetch_assoc()) { ?>
                  <tr>
                    <td><?= ucfirst($row['Heading']) ?></td>
                    <td><?= ucfirst($row['Send_To']) ?></td>
                    <td><?= date('d-M-Y', strtotime($row['Noticefication_Created_on'])) ?></td>
                    <td class="text-center"><a type="btn btn-link" class="text-primary"
                        onclick="view_content('<?= $row['ID'] ?>');"><i class="fa fa-eye">View</i></a></td>
                    <td>
                      <?php if (!empty($row['Attachment'])) { ?>
                        <a href="<?= $row['Attachment'] ?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                      <?php } else { ?>
                        <p>No Attachment</p>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>


              </tbody>
            <?php } else { ?>
              <!-- <tbody>
                <tr>
                  <h1 class="text-center" style="font-size: 20px;font-weight: 600;">No Record Found!</h1>
                </tr>
              </tbody> -->
            <?php } ?>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <a href="/student/notifications" class="btn btn-danger float-right">See All Notifications</a>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function changeNotificationStatus(id) {
    $.ajax({
      url: '/app/notifications/current-notification?id=' + id,
      type: 'GET',
      success: function (data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal('show');
      }
    })
  }

  function view_content(id) {
    $.ajax({
      url: '/app/notifications/contents?id=' + id,
      type: 'GET',
      success: function (data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal('show');
      }
    })
  }
</script>