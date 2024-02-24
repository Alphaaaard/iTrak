<?php
function connection()
{
    //LOCAL
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "upkeep";

    // HOSTED
    // $host = "localhost";
    // $username = "u483250324_UpKeep";
    // $password = ":El7+4xg";
    // $database = "u483250324_UpKeep";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        return $conn;
    }
}
