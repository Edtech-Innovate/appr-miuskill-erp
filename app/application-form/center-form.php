<?php
if (isset($_GET['university_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $role_query = str_replace("{{ table }}", "Users", $_SESSION['RoleQuery']);
  $role_query = str_replace("{{ column }}", "ID", $role_query);

  echo '<option value="">Choose</option>';
  // $centers = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM University_User LEFT JOIN Users ON University_User.`User_ID` = Users.ID WHERE University_User.University_ID = ".$_SESSION['university_id']." AND Role NOT IN ('Administrator','Accountant','Operations') $role_query ORDER BY Code ASC");
  $Usersquery = '';
  if ($_SESSION['Role'] == "Center") {
    $Usersquery = " AND (Users.Role ='Center' OR Users.Role ='Sub-Center')";
  } else if ($_SESSION['Role'] == "Administrator") {
    $Usersquery = " AND Users.Role ='Center'";
  }

  $centers = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM University_User LEFT JOIN Users ON University_User.`User_ID` = Users.ID WHERE University_User.University_ID = " . $_SESSION['university_id'] . " $Usersquery $role_query group by Users.ID ORDER BY Code  ASC");
  while ($center = $centers->fetch_assoc()) { ?>
    <option value="<?php echo $center['ID'] ?>"><?php echo $center['Name'] ?></option>
  <?php }
}
