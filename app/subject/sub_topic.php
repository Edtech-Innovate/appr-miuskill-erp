<?php
if (isset($_GET['topicId'])) {
  require '../../includes/db-config.php';
  session_start();
    $subtopicQuery = "select * from chapter_units_sub_topics where Topic_ID=".$_GET['topicId'];
    $subtopicResult = $conn->query($subtopicQuery);
    $subtopics = [];
    while($row = $subtopicResult->fetch_assoc())
    {
        $subtopics[] = $row;
    }
    $subtopicOption = "<option>Please Select Sub-Topic</option>";
    foreach($subtopics as $subtopic)
    {
        $subtopicOption .= "<option value='".$subtopic['ID']."'>".$subtopic['Name']."</option>";
    }
    echo $subtopicOption;
}