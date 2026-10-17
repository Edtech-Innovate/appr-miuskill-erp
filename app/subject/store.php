<?php
if (isset($_POST['course']) && isset($_POST['sub_course'])  && isset($_POST['name']) || isset($_POST['code'])) {
  require '../../includes/db-config.php';
  session_start();
  ini_set('display_errors', 1);
  // echo "<pre>";
// print_r($_POST);die;

  $university_id = $_SESSION['university_id'];
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $code = mysqli_real_escape_string($conn, $_POST['code']);
//   $category = intval($_POST['category']);
  $semester = intval($_POST['semester']);
  $paper_type = mysqli_real_escape_string($conn, $_POST['paper_type']);
  $credit = intval($_POST['credit']);
  $min_marks = intval($_POST['min_marks']);
  $max_marks = intval($_POST['max_marks']);
//   $group = intval($_POST['group']);



  $getscheme = $conn->query('SELECT * FROM Sub_Courses WHERE Course_ID = "' . $course . '" AND ID = "' . $sub_course . '"');
  $schemeArr = $getscheme->fetch_assoc();
  $scheme = $_POST['Scheme_ID'];

  $check_code = $conn->query("SELECT ID FROM Syllabi WHERE Code like '$code'");
  if ($check_code->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Code already exists!']);
    exit();
  }
  
  $check = $conn->query("SELECT * FROM Syllabi WHERE Course_ID =  $course  AND Sub_Course_ID = $sub_course AND Code='$code' AND Name = '$name' ");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 403, 'message' => $name . 'is already Exist !']);
    exit();
  }

  if (empty($university_id) || empty($course) || empty($sub_course) || empty($name) || empty($code)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are mandatory!']);
    exit();
  }

  $add = $conn->query("INSERT INTO `Syllabi`(`Name`, `Code`, `Course_ID`, `Sub_Course_ID`, `University_ID`, `Scheme_ID`,`Semester`,`Paper_Type`,`Credit`,`Min_Marks`,`Max_Marks`) VALUES   ('$name', '$code', $course, $sub_course, $university_id, $scheme,$semester,'$paper_type',$credit,$min_marks,$max_marks)");

  if ($add) {
    echo json_encode(['status' => 200, 'message' => $name . ' added successlly!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
    
}
