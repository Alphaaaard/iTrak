<?php
include_once("../../config/connection.php");
$conn = connection();

$accountId = $_GET['accountId'];

$sql = "SELECT day, date, timeIn, timeOut, totalHours FROM attendancelogs WHERE accountId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();

$data = array();

while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);

$conn->close();
