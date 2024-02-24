<?php 
// session_start();
// include_once("../../config/connection.php");
// $conn = connection();

function get_current_user_data() {
    global $conn;

    $sql = "SELECT * FROM account WHERE accountId = {$_SESSION['accountId']}";
    $result = $conn->query($sql) or die($conn->error);

    
    return $result->fetch_assoc();
}

?>