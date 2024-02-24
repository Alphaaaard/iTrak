<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && !empty($_POST['archiveAccId'])) {
    $accountId = $_POST['archiveAccId'];

    //* GET THE ROW FIRST 
    $stmt = $conn->prepare("SELECT * FROM account WHERE accountId = ?");
    $stmt->bind_param("i", $accountId);

    if (!$stmt->execute()) {
        header('location: staff.php');
        die("ERROR EXECUTING");
    }

    $row = $stmt->get_result()->fetch_assoc();

    //* INSERT FETCHED DATA TO archiveacc
    // $stmt = $conn->prepare("INSERT INTO archiveacc SELECT * FROM account WHERE accountId = ?");
    $stmt = $conn->prepare("
        INSERT INTO archiveacc 
            (archiveId, firstName, middleName, lastName, email, password, contact, birthday, role, picture, userLevel, rfidNumber, latitude, longitude, color)
        VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "sssssssssssisss",
        $row['accountId'],
        $row['firstName'],
        $row['middleName'],
        $row['lastName'],
        $row['email'],
        $row['password'],
        $row['contact'],
        $row['birthday'],
        $row['role'],
        $row['picture'],
        $row['userLevel'],
        $row['rfidNumber'],
        $row['latitude'],
        $row['longitude'],
        $row['color']
    );

    if (!$stmt->execute()) {
        header('location: staff.php');
        die("ERROR EXECUTING");
    }

    $stmt = $conn->prepare("DELETE FROM account WHERE accountId = ?");
    $stmt->bind_param("i", $accountId);

    if (!$stmt->execute()) {
        header('location: staff.php');
        die("ERROR EXECUTING");
    }

    header('location: staff.php');
} else {
    header('location: staff.php');
}