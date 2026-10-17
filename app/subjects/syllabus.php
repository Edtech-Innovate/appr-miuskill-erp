<?php
require '../../includes/db-config.php';
require '../../includes/helpers.php';

session_start();
if ((isset($_GET['course_id']) && isset($_GET['semester']) && empty($_GET['lms'])) || (empty($_GET['lms']) && isset($_GET['duration']) && isset($_GET['course_id']) && $_GET['duration'] != 'undefined')) {
  $sub_course_id = intval($_GET['course_id']);
  $duration = isset($_GET['duration']) ? $_GET['duration'] : '';
  $course_category = isset($_GET['course_category']) ? $_GET['course_category'] : '';

  if ($duration == 'advance_diploma' && ($course_category == 'advance_diploma' || $course_category == '11/advanced')) {
    $duration = "11/advanced";
  } else if ($duration == 'certified' && ($course_category == 'certified' || $course_category == '6/certified')) {
    $duration = "6/certified";
  } else if ($duration == 'certification' && ($course_category == 'certification' || $course_category == '3')) {
    $duration = "3";
  }
  // echo $duration; die;
  if ($duration != null) {

    if ($_SESSION['university_id'] == "48") {
      $syllabus = $conn->query("SELECT Syllabi.*, Users.Name as user_name, Sub_Courses.Name as sub_course_name  FROM Syllabi LEFT JOIN Users ON Syllabi.User_ID = Users.ID LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID  WHERE Semester ='" . $duration . "' AND Users.Role = 'Center' AND Syllabi.Sub_Course_ID = $sub_course_id ORDER BY ID DESC");
    } else {
      $syllabus = $conn->query("SELECT Syllabi.*, Sub_Courses.Name as sub_course_name  FROM Syllabi  LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID  WHERE Semester ='" . $duration . "'  AND Syllabi.Sub_Course_ID = $sub_course_id ORDER BY ID DESC");
    }
  } else {

    $semester = explode("|", $_GET['semester']);
    $scheme = $semester[0];
    $semester = $semester[1];
    if ($_SESSION['university_id'] == 48) {
      // $syllabus = $conn->query("SELECT Syllabi.*, Users.Name as user_name, Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID  LEFT JOIN Users ON Syllabi.User_ID = Users.ID WHERE Sub_Course_ID = $sub_course_id AND Users.Role = 'Center' AND Syllabi.Scheme_ID = $scheme AND Syllabi.Semester = $semester ORDER BY ID DESC");

      $syllabus = $conn->query("SELECT Syllabi.*, Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID  WHERE Sub_Course_ID = $sub_course_id AND Syllabi.Scheme_ID = $scheme AND Syllabi.Semester = $semester ORDER BY ID DESC");
    } else {
      $syllabus = $conn->query("SELECT Syllabi.*, Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE Sub_Course_ID = $sub_course_id AND Syllabi.Scheme_ID = $scheme AND Syllabi.Semester = $semester ORDER BY ID DESC");
    }
  }
  // print_r($syllabus);die;
  ?>

  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <?php if ($_SESSION['university_id'] == 48) { ?>
              <!-- <th>Center Name</th> -->
            <?php } ?>
            <th>Sub-Course Name</th>
            <th>Credit</th>
            <th>Duration</th>
            <th>Paper Type</th>
            <th>Min/Max Marks</th>
            <th>Syllabus</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($syllabus->num_rows > 0) {
            while ($row = $syllabus->fetch_assoc()) { ?>
              <tr>
                <td>
                  <?= $row['Code'] ?>
                </td>
                <td>
                  <?= $row['Name'] ?>
                </td>
                <?php if ($_SESSION['university_id'] == 48) { ?>
                  <!-- <td> -->
                  <?php //ucwords(strtolower($row['user_name'])) ?>
                  <!-- </td> -->
                <?php } ?>
                <td> <?= $row['sub_course_name'] ?>
                </td>
                <td>
                  <?= $row['Credit'] ?>
                </td>
                <td><?= $row['Semester'] ?></td>
                <td>
                  <?= $row['Paper_Type'] ?>
                </td>
                <td>
                  <?= $row['Min_Marks'] ?>/
                  <?= $row['Max_Marks'] ?>
                </td>
                <td>
                  <?php if (!is_null($row['Syllabus']) && !empty($row['Syllabus'])) {
                    $files = explode("|", $row['Syllabus']);
                    foreach ($files as $file) { ?>
                      <a href="<?= $file ?>" target="_blank" download="<?= $row['Code'] ?>">Download</a>
                    <?php }
                  }
                  if (is_null($row['Syllabus']) && $_SESSION['Role'] == "Student") {
                    echo "NA";
                  }
                  ?>
                  <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])) { ?>
                    <div class="d-flex">
                      Upload (
                      <span class="text-primary cursor-pointer"
                        onclick="uploadFile('Syllabi', 'Syllabus', <?= $row['ID'] ?>)">PDF</span> /
                      <span class="text-primary cursor-pointer"
                        onclick="uploadFile('Syllabi', 'Syllabus', <?= $row['ID'] ?>)">Video</span>
                      )
                    </div>
                  <?php } ?>
                </td>
                <td>
                  <i class="bi bi-pen" id="editsubject" onclick="editsubject(<?= $row['ID']; ?>)"></i>
                </td>
              </tr>
            <?php }
          } else { ?>
            <tr>
              <td colspan='8' style="text-align:center">No data available in table</td>
            </tr>
          <?php } ?>

        </tbody>
      </table>
    </div>
  </div>
<?php } else if (isset($_GET['course_id']) && isset($_GET['semester']) && $_GET['lms'] == "lms") {
  $sub_course_ids = intval($_GET['course_id']);
  $query = '';
  if ($_SESSION['university_id'] == 47) {
    $semesterArr = explode("|", $_GET['semester']);
    $schemes = $semesterArr[0];
    $query = 'AND Scheme_ID = ' . $schemes;
    $semesters = $semesterArr[1];
  } else {
    $semesters = $_GET['semester'];
  }

  if ($_SESSION['university_id'] == 48) {
    $center_id = getUserIdFunc($conn, $_SESSION['Added_For']);
    $getCenterID = $conn->query("select Code from Users where ID = '$center_id'");
    if ($getCenterID->num_rows > 0) {
      $codeArr = $getCenterID->fetch_assoc();
      $code = $codeArr['Code'];
    }else{
      $code ='';
    }
    
    $syllabus_query = $conn->query("SELECT Name, ID FROM Syllabi WHERE JSON_CONTAINS(User_ID, '\"" . mysqli_real_escape_string($conn, $code) . "\"') AND Sub_Course_ID = " . intval($sub_course_ids) . "  AND Semester = '" . mysqli_real_escape_string($conn, $semesters) . "' $query ORDER BY ID DESC");
  } else {
    $syllabus_query = $conn->query("SELECT Name, ID FROM Syllabi WHERE Sub_Course_ID = " . intval($sub_course_ids) . "  AND Semester = '" . mysqli_real_escape_string($conn, $semesters) . "' $query ORDER BY ID DESC");
  }

  $bg_colors = array("0" => "bg-yellow-gradient", "1" => "bg-purple-gradient", "2" => "bg-green-gradient", "3" => "bg-aqua-gradient", "4" => "bg-red-gradient", "5" => "bg-aqua-gradient", "6" => "bg-maroon-gradient", "7" => "bg-teal-gradient", "8" => "bg-blue-gradient");
  $colorIndex = 0;

  if ($syllabus_query->num_rows > 0) {
    while ($rows = $syllabus_query->fetch_assoc()) {
      $clr = $bg_colors[$colorIndex % count($bg_colors)];
      $colorIndex++;

      ?>
        <div class="col-md-3">
          <div class="card info-box p-0">
            <a href="/student/lms/subjects?id=<?= $rows['ID'] ?>&type=1&duration=<?= $semesters ?>">
              <div class="card-img-top <?= $clr ?>">
                <p class="subject-name"><?= $rows['Name']; ?> </p>
              </div>
            </a>
            <div class="card-footer">
              <div class="row justify-content-between align-items-center">
                <?php
                $ebook_query = $conn->query("SELECT id FROM e_books WHERE subject_id = '" . $rows['ID'] . "' AND Course_ID='" . $_SESSION['Sub_Course_ID'] . "' and Status = 1");
                $ebook_count = ($ebook_query->num_rows > 0) ? $ebook_query->num_rows : 0;

                $video_query = $conn->query("SELECT id FROM video_lectures WHERE subject_id = '" . $rows['ID'] . "' AND Course_ID='" . $_SESSION['Sub_Course_ID'] . "' and Status = 1");
                $video_count = ($video_query->num_rows > 0) ? $video_query->num_rows : 0;
                ?>
                <div class="col-md-4 text-center">
                  <a href="/student/lms/subjects?id=<?= $rows['ID'] ?>&type=1&duration=<?= $semesters ?>"><i
                      class="ti-book mr-2"></i><span><?= $ebook_count ?></span></a>
                </div>
                <div class="col-md-4 text-center">
                  <a href="/student/lms/subjects?id=<?= $rows['ID'] ?>&type=2&duration=<?= $semesters ?>"><i
                      class="ti- ti-video-clapper mr-2"></i><span><?= $video_count ?></span></a>
                </div>
                <div class="col-md-4 text-center">
                  <a href="/student/lms/subjects?id=<?= $rows['ID'] ?>&type=3&duration=<?= $semesters ?>"><i
                      class=" ti-clipboard mr-2"></i><span>0</span></a>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php }
  } else { ?>
      <div class="col-md-4"></div>
      <div class="col-md-4" style="font-weight:700;font-size:19px"> Subjects Not Alloted To Center!</div>
      <div class="col-md-4"></div>
  <?php } ?>
<?php } else {

  $usersFilter = '';
  if (isset($_SESSION['usersFilter'])) {
    $usersFilter = $_SESSION['usersFilter'];
  }
  $course_sql = '';
  if (isset($_GET['id'])) {
    $course_sql .= ' AND Sub_Course_ID =' . $_GET['id'];
  }

  if ($_SESSION['university_id'] == 48) {
    // $syllabuss = $conn->query("SELECT Syllabi.*, Users.Name as user_name, Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Users ON Syllabi.User_ID = Users.ID WHERE  Users.Role = 'Center' AND Syllabi.University_ID = " . $_SESSION['university_id'] . "  $course_sql $usersFilter  ORDER BY Syllabi.ID DESC ");

    $syllabuss = $conn->query("SELECT Syllabi.*,  Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID  WHERE  Syllabi.University_ID = " . $_SESSION['university_id'] . "  $course_sql $usersFilter  ORDER BY Syllabi.ID DESC ");
  } else {
    $syllabuss = $conn->query("SELECT Syllabi.*, Sub_Courses.Name as sub_course_name FROM Syllabi LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE Syllabi.University_ID = " . $_SESSION['university_id'] . "  $course_sql  ORDER BY Syllabi.ID DESC ");
  }
  ?>
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Code</th>
              <th>Name</th>
              <th>Duration</th>
            <?php if ($_SESSION['university_id'] == 48) { ?>
                <!-- <th>Center Name</th> -->
            <?php } ?>

              <th>Sub-Course Name</th>
              <th>Credit</th>
              <th>Paper Type</th>
              <th>Min/Max Marks</th>
              <th>Syllabus</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($syllabuss->num_rows > 0) {
            while ($row = $syllabuss->fetch_assoc()) {
              ?>
                <tr>
                  <td>
                  <?= $row['Code'] ?>
                  </td>
                  <td>
                  <?= $row['Name'] ?>
                  </td>
                  <td><?= $row['Semester'] ?></td>

                <?php if ($_SESSION['university_id'] == 48) { ?>
                    <!-- <td> -->
                  <?php // ucwords(strtolower($row['user_name'])) ?>
                    <!-- </td> -->
                <?php } ?>
                  <td> <?= ucwords(strtolower($row['sub_course_name'])) ?></td>
                  <td>
                  <?= $row['Credit'] ?>
                  </td>
                  <td>
                  <?= $row['Paper_Type'] ?>
                  </td>
                  <td>
                  <?= $row['Min_Marks'] ?>/
                  <?= $row['Max_Marks'] ?>
                  </td>

                  <td>
                  <?php if (!is_null($row['Syllabus']) && !empty($row['Syllabus'])) {
                    $files = explode("|", $row['Syllabus']);
                    foreach ($files as $file) { ?>
                        <a href="<?= $file ?>" target="_blank" download="<?= $row['Code'] ?>">Download</a>
                    <?php }
                  } ?>
                  <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])) { ?><span
                        class="text-primary cursor-pointer"
                        onclick="uploadFile('Syllabi', 'Syllabus', <?= $row['ID'] ?>)">Upload</span>
                  <?php } ?>
                  </td>

                  <td>
                    <i class="uil uil-edit" id="editsubject" onclick="editsubject(<?= $row['ID']; ?>)"></i>

                  </td>
                </tr>
            <?php }
          } else {
            echo '<tr><td colspan="10" class="text-center">No data found.</td></tr>';
          } ?>
          </tbody>
        </table>
      </div>
    </div>
<?php } ?>
<script>
  function editsubject(id) {
    $.ajax({
      url: '/app/subjects/update_subject',
      data: {
        id: id
      },
      method: 'POST',
      success: function (response) {
        $('#md-modal-content').html(response);
        $('#mdmodal').modal('show');
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      }
    });
  }
</script>