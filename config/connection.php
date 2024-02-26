<?php
function connection()
{
    //LOCAL
    // $host = "localhost";
    // $username = "root";
    // $password = "";
    // $database = "upkeep";

    // HOSTED
    $host = "localhost";
    $username = "u226014500_iTrak";
    $password = "iTrak123";
    $database = "u226014500_iTrak";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        return $conn;
    }
}
