<?php
session_start();
include_once("./config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

$response = ['timeout' => false];

if (isset($_SESSION['accountId'])) {
    $accountId = $_SESSION['accountId'];
    $todayDate = date("Y-m-d");
    $timeoutQuery = "SELECT timeout FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
    $timeoutResult = $conn->query($timeoutQuery);
    $timeoutRow = $timeoutResult->fetch_assoc();

    if ($timeoutRow && $timeoutRow['timeout'] !== null) {
        $response['timeout'] = true;
    }
}

echo json_encode($response);
