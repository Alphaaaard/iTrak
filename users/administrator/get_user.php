<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {


    // For personnel page, check if userLevel is 3
    if($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM account WHERE accountId = ?");
    $stmt->bind_param('i', $_GET['user']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $picture = $row['picture'];

        $user = array(
            'accountId' => $row['accountId'],
            'firstName' => $row['firstName'],
            'middleName' => $row['middleName'],
            'lastName' => $row['lastName'],
            'password' => $row['password'],
            'contact' => $row['contact'],
            'email' => $row['email'],
            'birthday' => $row['birthday'],
            'role' => $row['role'],
            'expertise' => $row['expertise'],
            'picture' => base64_encode($picture),
            'rfid' => $row['rfidNumber'],
        );

        echo json_encode($user);
    } else {
        echo 'No Data';
    }
}
