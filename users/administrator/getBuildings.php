<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Fetch distinct buildings
    $stmt = $conn->prepare("SELECT DISTINCT building FROM asset");
    $stmt->execute();

    $buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send back data in JSON format
    echo json_encode($buildings);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
