<?php
    include_once('connect.php');
    if (isset ($_REQUEST['id']) and $_REQUEST['id'] != '') {
        $id = $_REQUEST['id'];
        $sql = "DELETE FROM member WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
           echo "Delete successfully";
        }
        else {
            echo "Error deleting record: " . $conn->error;
        }
        $conn->close();
    }
?>