<?php
  if(isset($_GET['id']) && isset($_GET['university'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university']);
  ?>
  <option value="">Select</option>
  <?php
    $sub_counsellors = $conn->query("SELECT Users.ID, CONCAT(Users.`Name`, ' (', Users.Code, ')') as Name FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.Reporting = $id AND University_ID = $university_id AND Users.Role = 'Sub-Counsellor'");
    while($sub_counsellor = $sub_counsellors->fetch_assoc()){ ?>
      <option value="<?php echo $sub_counsellor['ID'] ?>"><?=$sub_counsellor['Name']?></option>
  <?php }
  }
