<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();
// Assuming session_start() and database connection $conn are already initialized
$current_date = $_POST['date']; // Use the date from the POST request
$loggedInUser = $_SESSION['firstName'] . ' ' . $_SESSION['middleName'] . ' ' . $_SESSION['lastName'];

$sqlSchedule = "SELECT * FROM scheduleboard WHERE date = ? AND CONCAT(techVoc, ' ', oldAcad, ' ', belmonte, ' ', metalcasting, ' ', korphil, ' ', multipurpose, ' ', chineseA, ' ', chineseB, ' ', urbanFarming, ' ', administration, ' ', bautista, ' ', newAcad) LIKE ?";
$stmt = $conn->prepare($sqlSchedule);
$searchTerm = "%" . $loggedInUser . "%";
$stmt->bind_param("ss", $current_date, $searchTerm);
$stmt->execute();
$resultSchedule = $stmt->get_result();

$schedules = [];
if ($resultSchedule->num_rows > 0) {
    while ($row = $resultSchedule->fetch_assoc()) {
        foreach (['techVoc', 'oldAcad', 'belmonte', 'metalcasting', 'korphil', 'multipurpose', 'chineseA', 'chineseB', 'urbanFarming', 'administration', 'bautista', 'newAcad'] as $building) {
            if (!empty($row[$building])) {
                // Add only the column name
                $schedules[] = $building;
            }
        }
    }
}

// Return the column names as JSON
echo json_encode($schedules);
?>