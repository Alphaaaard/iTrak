<?php
require_once("./config/connection1.php");
header("Content-Type: application/json");

date_default_timezone_set('Asia/Manila');
$current_timestamp = date("Y-m-d H:i:s");
$current_date = date("Y-m-d"); // Define current date

function message($status, $message)
{
    $msg = array(
        "success" => $status,
        "message" => $message
    );
    echo json_encode($msg);
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents("php://input");

    // Decode the JSON data
    $jsonData = json_decode($data, true);
    $rfid = $jsonData['rfid'];

    // Find user from database
    $findUserStmt = $conn->prepare("SELECT * FROM `account` WHERE rfidNumber = ?");
    $findUserStmt->bind_param("s", $rfid);
    $findUserStmt->execute();

    // Get results
    $result = $findUserStmt->get_result();

    // Put results in row
    $user = $result->fetch_assoc();

    // Conditions
    if ($user) {
        // Check if it's midnight
        $currentTime = date("H:i:s");
        if ($currentTime === "00:00:00") {
            message("Autologged out due to midnight", true);
        }

        // If user has already timed out within the day
        $checkTimeoutStmt = $conn->prepare("SELECT * FROM attendancelogs WHERE accountId = ? AND date = CURRENT_DATE() AND timeOut IS NOT NULL");
        $checkTimeoutStmt->bind_param("i", $user['accountId']);
        $checkTimeoutStmt->execute();

        // Get results 
        $resultTimeout = $checkTimeoutStmt->get_result();

        // Put results in timeout log
        $timeoutLog = $resultTimeout->fetch_assoc();

        if ($timeoutLog) {
            message("You already timed out!", false);
        }

        // Check user if he/she has logs today
        $checkLogStmt = $conn->prepare("SELECT attendancelogs.*, account.rfidNumber 
        FROM attendancelogs LEFT JOIN account ON attendancelogs.accountId = account.accountId 
        WHERE account.rfidNumber = ? AND attendancelogs.date = ?");
        $checkLogStmt->bind_param("ss", $rfid, $current_date);
        $checkLogStmt->execute();

        // Get results
        $result = $checkLogStmt->get_result();

        // Put results in log
        $log = $result->fetch_assoc();

        // If user has existing logs

        // Time out
        if ($log) {
            $updateLogStmt = $conn->prepare('UPDATE attendancelogs SET timeOut = ? WHERE attendanceId = ?');
            $updateLogStmt->bind_param('si', $current_timestamp, $log['attendanceId']);
            $updateLogStmt->execute();
            $updateResult = $updateLogStmt->get_result();

            $clearLocationStmt = $conn->prepare("UPDATE `account` SET latitude = NULL, longitude = NULL WHERE accountId = ?");
            $clearLocationStmt->bind_param("i", $user['accountId']);
            $clearLocationStmt->execute();

            message("Timed out successfully!", true);
        }

        // If user is accessing for the first time
        if (!isset($log['attendanceId'])) {
            $createLogStmt = $conn->prepare('INSERT INTO `attendancelogs` (`attendanceId`, `accountId`, `date`, `timeIn`, `timeOut`) VALUES (NULL, ?, ?, ?, NULL)');
            $createLogStmt->bind_param('iss', $user['accountId'], $current_date, $current_timestamp);
            $createLogStmt->execute();

            message("Timed in successfully!", true);
        }
    } else {
        message("Unauthorized access", false);
    }
}
