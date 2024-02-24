<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'fetch_schedule') {
    $selectedDate = $_POST['date'];

    $stmt = $conn->prepare("SELECT * FROM scheduleboard WHERE date = ?");
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($schedules);
}
