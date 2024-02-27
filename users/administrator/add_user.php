<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Function to generate a random color
function generateRandomColor()
{
    return sprintf("#%06X", mt_rand(0, 0xFFFFFF));
}

if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact = $_POST['contact'];
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $userLevel = isset($_POST['userLevel']) ? $_POST['userLevel'] : '';
    $rfidNumber = $_POST['rfidNumber'];
    $photo = $_FILES['picture']['name'];

    if (
        !empty($firstName) && !empty($lastName) && !empty($contact)
        && !empty($email) && !empty($birthday) && !empty($password) && !empty($role) && !empty($photo)
    ) {
        // Generate a random color
        $color = generateRandomColor();

        // Setting the image
        $photo = file_get_contents($_FILES['picture']['tmp_name']);

        $stmt = $conn->prepare("INSERT INTO `account` (`userLevel`, `firstName`, `middleName`, `lastName`, `email`, `password`, `contact`, `birthday`, `role`, `picture`, `rfidNumber`, `color`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $userLevel, $firstName, $middleName, $lastName, $email, $password, $contact, $birthday, $role, $photo, $rfidNumber, $color);

        if ($stmt->execute()) {
            header("Location: staff.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        header('location: staff.php');
    }
}