<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

echo 'hehe';


if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    echo 'in';


    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    $conn = connection();

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

    if (
        !empty($firstname) && !empty($lastname) && !empty($contact)
        && !empty($email) && !empty($birthday) && !empty($password) && !empty($role)
    ) {

        //* if uploaded an image, update the image, if not, leave it
        if (!empty($_FILES['picture']['name'])) {
            $photo = file_get_contents($_FILES['picture']['tmp_name']);

            $stmt = $conn->prepare("UPDATE account SET firstName = ?, middleName = ?, lastName = ?, email = ?, password = ?, contact = ?, birthday = ?, role = ?, picture = ?, rfidNumber = ? WHERE accountId = ?");
            $stmt->bind_param('ssssssssssi', $firstname, $middlename,  $lastname, $email, $password, $contact, $birthday, $role, $photo, $rfid, $accountId);

            if (!$stmt->execute()) {
                error_log("Failed to execute SQL query: $sql");
                error_log("Error message: " . $stmt->error);
                header("location: staff.php?error=1");
                exit;
            }

            $_SESSION['updateSuccess'] = 'success';
        } else {
            $sql = "UPDATE account SET firstName = ?, middleName = ?, lastName = ?, email = ?, password = ?, contact = ?, birthday = ?, role = ?, rfidNumber = ? WHERE accountId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                'sssssssssi',
                $firstname,
                $middlename,
                $lastname,
                $email,
                $password,
                $contact,
                $birthday,
                $role,
                $rfid,
                $accountId
            );

            if (!$stmt->execute()) {
                header("location: staff.php?error=1");
            }

            $_SESSION['updateSuccess'] = 'success';
        }
    } else {
        echo 'empty';
        // header("location: staff.php?error=2");
    }
} else {
    header("location: staff.php?error=3");
}
