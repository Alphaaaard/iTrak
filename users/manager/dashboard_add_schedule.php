<?php 
session_start();
include_once("../../config/connection.php");
$conn = connection();

//Add Employee
if (isset($_SESSION['accountId'])) {
    // $sbId = $_POST['sbId'];
    $date = $_POST['date'];
    $techVoc = $_POST['techVoc'];
    $oldAcad = $_POST['oldAcad'];
    $belmonte = $_POST['belmonte'];
    $metalcasting = $_POST['metalcasting'];
    $korphil = $_POST['korphil'];
    $multipurpose = $_POST['multipurpose'];
    $chineseA = $_POST['chineseA'];
    $chineseB = $_POST['chineseB'];
    $urbanFarming = $_POST['urbanFarming'];
    $administration = $_POST['administration'];
    $bautista = $_POST['bautista'];
    $newAcad = $_POST['newAcad'];


    // $sql1 = "INSERT INTO `scheduleboard`(`sbId`, `date`, `techVoc`, `oldAcad`, `belmonte`, `metalcasting`, `korphil`, `multipurpose`, `chineseA`, `chineseB`, `urbanFarming`, `administration`, `bautista`, `newAcad`)
    // VALUES ('$sbId', '$date', '$techVoc', '$oldAcad', '$belmonte', '$metalcasting', '$korphil', '$multipurpose', '$chineseA', '$chineseB', '$urbanFarming', '$administration' , '$bautista' , '$newAcad')
    // ";

    if(!empty($techVoc) || !empty($oldAcad) || !empty($belmonte) || !empty($metalcasting) || !empty($korphil) || !empty($multipurpose) || !empty($chineseA) || !empty($chineseB || !empty($urbanFarming)) || !empty($administration) || !empty($bautista) || !empty($newAcad)) {
        $sql1 = "INSERT INTO `scheduleboard`(`date`, `techVoc`, `oldAcad`, `belmonte`, `metalcasting`, `korphil`, `multipurpose`, `chineseA`, `chineseB`, `urbanFarming`, `administration`, `bautista`, `newAcad`)
        VALUES ('$date', '$techVoc', '$oldAcad', '$belmonte', '$metalcasting', '$korphil', '$multipurpose', '$chineseA', '$chineseB', '$urbanFarming', '$administration' , '$bautista' , '$newAcad')
        ";

        if ($conn->query($sql1) === TRUE) {
            // Query executed successfully
            // header("Location: dashboard.php");
            exit;
        } else {
            // Query execution failed
            echo "Error: " . $sql1 . "<br>" . $conn->error;
        }
    }
}
?>