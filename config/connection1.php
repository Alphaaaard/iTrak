
<?php
//change your password
$conn = mysqli_connect("localhost", "root", "", "upkeep");

// $conn = mysqli_connect("localhost", "u226014500_iTrak", "iTrak123", "u226014500_iTrak");

if (!$conn) {
    die('Connection failed!' . mysqli_connect_error());
}
