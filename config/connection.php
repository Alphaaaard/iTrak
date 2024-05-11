<?php
function connection()
{
    // LOCAL
    // $host = "localhost";
    // $username = "root";
    // $password = "";
    // $database = "upkeep";

    // HOSTED
    $host = "localhost";
    $username = "u579600805_iTrak";
    $password = "iTrak123";
    $database = "u579600805_iTrak";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        return $conn;
    }
}
