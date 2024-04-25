
<?php
// LOCAL
$conn = mysqli_connect("localhost", "root", "", "upkeep");

//HOSTED
// $conn = mysqli_connect("localhost", "u579600805_iTrak", "iTrak123", "u579600805_iTrak");

if (!$conn) {
    die('Connection failed!' . mysqli_connect_error());
}
