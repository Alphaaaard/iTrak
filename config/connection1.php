
<?php
//change your password
// $conn = mysqli_connect("localhost", "root", "", "upkeep");

$conn = mysqli_connect("", "u579600805_iTrak", "iTrak123", "u579600805_iTrak");

if (!$conn) {
    die('Connection failed!' . mysqli_connect_error());
}
