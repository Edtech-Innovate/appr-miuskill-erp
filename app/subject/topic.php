<?php
if (isset($_GET['unitId'])) {
  require '../../includes/db-config.php';
  session_start();
    $topicQuery = "select * from Chapter_Units_Topics where Unit_ID=".$_GET['unitId'];
    $topicResult = $conn->query($topicQuery);
    $topics = [];
    while($row = $topicResult->fetch_assoc())
    {
        $topics[] = $row;
    }
    $topicOption = "<option>Please Select Topic</option>";
    foreach($topics as $topic)
    {
        $topicOption .= "<option value='".$topic['ID']."'>".$topic['Name']."</option>";
    }
    echo $topicOption;
}