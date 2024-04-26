<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }

    // Retrieve data from POST
    $accountId = $_POST['accountId'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $rfid = $_POST['rfid'];
    $expertise = $_POST['expertise']; // Add expertise field

    if (
        !empty($firstname) && !empty($lastname) && !empty($contact)
        && !empty($email) && !empty($birthday) && !empty($password) && !empty($role)
    ) {

        // Check if expertise is set in the $_POST array
        if (isset($_POST['expertise'])) {
            $expertise = $_POST['expertise'];

            // Update query to include expertise field
            $stmt = $conn->prepare("UPDATE account SET firstName = ?, middleName = ?, lastName = ?, email = ?, password = ?, contact = ?, birthday = ?, role = ?, expertise = ?, rfidNumber = ? WHERE accountId = ?");
            $stmt->bind_param('ssssssssssi', $firstname, $middlename, $lastname, $email, $password, $contact, $birthday, $role, $expertise, $rfid, $accountId);
        } else {
            // Update query without expertise field
            $stmt = $conn->prepare("UPDATE account SET firstName = ?, middleName = ?, lastName = ?, email = ?, password = ?, contact = ?, birthday = ?, role = ?, rfidNumber = ? WHERE accountId = ?");
            $stmt->bind_param('sssssssssi', $firstname, $middlename, $lastname, $email, $password, $contact, $birthday, $role, $rfid, $accountId);
        }

        // Execute the update query
        if (!$stmt->execute()) {
            error_log("Failed to execute SQL query: $sql");
            error_log("Error message: " . $stmt->error);
            header("location: staff.php?error=1");
            exit;
        }

        $_SESSION['updateSuccess'] = 'success';
    } else {
        // Redirect if required fields are empty
        header("location: staff.php?error=2");
    }
} else {
    // Redirect if session data is not set
    header("location: staff.php?error=3");
}
