<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

//Para madisplay sa dropdown na pagselected na yung data is magiging not clickable na
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['column'])) {
    $columnName = $_POST['column'];
    $columnName = mysqli_real_escape_string($conn, $columnName);

    // Include a date field in the select statement
    $stmt = $conn->prepare("SELECT DISTINCT $columnName, date FROM scheduleboard");
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // Handle invalid requests
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
}
?>
