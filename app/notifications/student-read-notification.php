<?php
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        ## Database configuration
        include '../../includes/db-config.php';
        session_start();
        // $conn->query("UPDATE `Notifications_Generated` SET `Status` = 1 WHERE ID =  $id");
    

        $ids = array();
        $add ='';
        $updated = '';

        $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE Status <> 1 AND Send_To = 'student' OR Send_To = '".'all'."'");
        while ($row = $result_record->fetch_assoc()) {
        $ids[] = $row['ID'];
        }

        $viewed_notification = $conn->query("SELECT * FROM Notifications_Viewed_By WHERE Reader_ID =  ". $_SESSION['ID'] ." ORDER BY Notifications_Viewed_By.ID DESC LIMIT 1");

        if($viewed_notification->num_rows>0){
        $viewed_records = mysqli_fetch_assoc($viewed_notification);
        $viewed_id = json_decode($viewed_records['Notification_ID']);
        foreach ($ids as $key => $value) {
            if(!in_array($value, $viewed_id)){
                $new_id[] = $value;
                $merged_id = array_merge($viewed_id, $new_id);
                $notification_ids = json_encode($merged_id);
                $updated = $conn->query("UPDATE `Notifications_Viewed_By` SET `Notification_ID` = '$notification_ids' WHERE Reader_ID =  ". $_SESSION['ID'] ." ");
            }
        }
        }else{
        $center_ID = $_SESSION['ID'];
        $record_id = json_encode($ids);
        $add = $conn->query("INSERT INTO `Notifications_Viewed_By` (`Notification_ID`, `Reader_ID`, `Status`) VALUES ('$record_id', '$center_ID', 1)");
        }

        if($add !='' || $updated != ''){ ?>
            <script type="text/javascript">
                location.reload();
            </script>
        <?php } 
    }?>