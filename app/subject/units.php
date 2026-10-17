    
<?php
if (isset($_GET['chapterId'])) {
  require '../../includes/db-config.php';
  session_start();
    $_SESSION['selectedChapter'] = $_GET['chapterId'];
    $unitQuery = "select * from Chapter_Units where Chapter_ID=".$_GET['chapterId'];
    $unitResult = $conn->query($unitQuery);
    $units = [];
    while($row = $unitResult->fetch_assoc())
    {
        $units[] = $row;
    }
    $unitOption = "<option>Please Select Unit</option>";
    foreach($units as $unit)
    {
        $unitOption .= "<option value='".$unit['ID']."'>".$unit['Name']."</option>";
    }
    echo $unitOption;
}
