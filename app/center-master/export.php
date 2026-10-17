<?php
  // ini_set('error_reporting', E_ALL );
  // ini_set('display_errors', 1 );
  session_start();
  require '../../includes/db-config.php';
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
//   include '../../includes/helpers.php';
  if(isset($columnSortOrder)){
    $orderby = "ORDER BY $columnName $columnSortOrder";
  }else{
    $orderby = "ORDER BY Users.ID ASC";
  }
  
  $center_query = "";
  if($_SESSION['Role']!="Administrator"){
    $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = ".$_SESSION['university_id']." AND Has_Unique_Center = 1");
    if($check_has_unique_center_code->num_rows>0){
      $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
      $center_suffix = $center_suffix['Center_Suffix'];
      $center_query = " AND Code LIKE '$center_suffix%' AND Is_Unique = 1";
    }else{
      $center_query = " AND Is_Unique = 0";
    }
  }
  $center_query = "";
$counselloer_query = "";

if ($_SESSION['Role'] == 'University Head') {

    $user_id = intval($_SESSION['ID']);
    $sc_row = $center_row = '';

    //  Get User_IDs of counselors who report to the University Head
    $getcounselloer = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = $user_id");
    if ($getcounselloer && $getcounselloer->num_rows > 0) {

        $center_ids = array_column($getcounselloer->fetch_all(MYSQLI_ASSOC), 'User_ID');
        $center_ids_str = implode(',', array_map('intval', $center_ids)); 

        //  Get Center IDs that report to those counselors
        $counselloer_query = " AND Reporting IN ($center_ids_str)";
        $result = $conn->query("
            SELECT GROUP_CONCAT(University_User.User_ID) AS sc_ids 
            FROM Users 
            LEFT JOIN University_User ON Users.ID = University_User.User_ID 
            WHERE Role = 'Center' $counselloer_query
        ");

        if ($result && $result->num_rows > 0) {
            $sc_row = $result->fetch_assoc()['sc_ids'] ?? '';
        }
    }

    //  Get Center IDs that are standalone (not assigned)
    $getQuery = $conn->query("
        SELECT GROUP_CONCAT(Users.ID) AS ids 
        FROM Users 
        LEFT JOIN University_User ON Users.ID = University_User.User_ID OR Users.ID = University_User.Reporting 
        WHERE Role = 'Center' AND Reporting IS NULL AND User_ID IS NULL
    ");

    if ($getQuery && $getQuery->num_rows > 0) {
        $center_row = $getQuery->fetch_assoc()['ids'] ?? '';
    }

    //  Merge both Center ID lists and form final WHERE clause
    $merged_ids = array_filter(array_merge(
        explode(',', $sc_row ?? ''),
        explode(',', $center_row ?? '')
    ));

    if (!empty($merged_ids)) {
        $query_ids = implode(',', array_map('intval', $merged_ids));
        $center_query .= " AND Users.ID IN ($query_ids)";
    }
}
  ## Search 
  $searchQuery = " ".$center_query;
  if(isset($_GET['search'])){
    $searchValue = mysqli_real_escape_string($conn,$_GET['search']); // Search value
    if(!empty($searchValue)){
      $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
    }
  }
  
  
  ## Fetch records
  $result_record = "SELECT ID, `Name`, Short_Name, Contact_Name, `CanCreateSubCenter`, `Email`, `Mobile`, `Alternate_Mobile`, `Code`, `Address`, `City`, `District`, `State`, `Pincode`, `Status`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users WHERE Role = 'Center' $center_query $searchQuery $orderby";
  $empRecords = mysqli_query($conn, $result_record);
  $data[] = array('Code', 'Name', 'Short Name', 'Contact Name', 'Email', 'Mobile', 'Alternate Mobile', 'Address', 'City', 'District', 'State', 'Pincode', 'Password', 'Sub Center Access', 'Status', 'Universities');
  
  while ($row = mysqli_fetch_assoc($empRecords)) {
    $alloted_universities = $conn->query("SELECT GROUP_CONCAT(CONCAT(Universities.Short_Name, '(', Universities.Vertical, ')')) as Alloted_Universities FROM Alloted_Center_To_Counsellor LEFT JOIN Universities ON Alloted_Center_To_Counsellor.University_ID = Universities.ID WHERE Alloted_Center_To_Counsellor.Code = ".$row['ID']."");
    $alloted_universities = mysqli_fetch_assoc($alloted_universities);
    $row['Email'] = maskEmail($row['Email'] ?? '');
    $row['Mobile'] = maskPhone($row['Mobile'] ?? '');
    $row['Alternate_Mobile'] = maskPhone($row['Alternate_Mobile'] ?? '');
    $data[] = array(
      $row['Code'],
      $row['Name'],
      $row['Short_Name'],
      $row['Contact_Name'],
      $row['Email'],
      $row['Mobile'],
      $row['Alternate_Mobile'],
      $row['Address'],
      $row['City'],
      $row['District'],
      $row['State'],
      $row['Pincode'],
      $row['password'],
      $row['CanCreateSubCenter']==1 ? 'Yes':'No',
      $row["Status"]==1 ? 'Active' : 'Inactive',
      $alloted_universities['Alloted_Universities']
    );
  }
  

  $xlsx = SimpleXLSXGen::fromArray( $data )->downloadAs('Center Master.xlsx');
