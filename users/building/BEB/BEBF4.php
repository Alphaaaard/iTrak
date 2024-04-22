<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once ("../../../config/connection.php");
$conn = connection();

// transform: rotate(180deg);

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    function logActivity($conn, $accountId, $actionDescription, $tabValue)
    {
        $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
        if (!$stmt->execute()) {
            echo "Error logging activity: " . $stmt->error;
        }
        $stmt->close();
    }

    // for notif below
    // Update the SQL to join with the account and asset tables to get the admin's name and asset information
    $loggedInUserFirstName = $_SESSION['firstName'];
    $loggedInUserMiddleName = $_SESSION['middleName']; // Get the middle name from the session
    $loggedInUserLastName = $_SESSION['lastName'];

    // Assuming $loggedInUserFirstName, $loggedInUserMiddleName, $loggedInUserLastName are set

    $loggedInFullName = $loggedInUserFirstName . ' ' . $loggedInUserMiddleName . ' ' . $loggedInUserLastName;
    $loggedInAccountId = $_SESSION['accountId'];
    // SQL query to fetch notifications related to report activities
    $sqlLatestLogs = "SELECT al.*, acc.firstName AS adminFirstName, acc.middleName AS adminMiddleName, acc.lastName AS adminLastName, acc.role AS adminRole
                FROM activitylogs AS al
               JOIN account AS acc ON al.accountID = acc.accountID
               WHERE  al.seen = '0' AND al.accountID != ?
               ORDER BY al.date DESC 
               LIMIT 5"; // Set limit to 5


    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("i", $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();


    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE seen = '0' AND accountID != ?";
    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("i", $loggedInAccountId);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();
    $todayDate = date("Y-m-d"); // Today's date


    // Your PHPMailer settings and email credentials
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Enable verbose debug output
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host = 'smtp.gmail.com';               // Set the SMTP server to send through
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = 'qcu.upkeep@gmail.com';         // SMTP username
        $mail->Password = 'qvpx bbcm bgmy hcvf';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 587;                              // TCP port to connect to

        //Recipients
        $mail->setFrom('qcu.upkeep@gmail.com', 'iTrak');
        $mail->addAddress('qcu.upkeep@gmail.com', 'Admin');     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Asset Status Changed';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'edit') === 0) {
                    $assetId = str_replace('edit', '', $key);
                    $status = $_POST['status']; // Ensure you have a field with name 'status' in your form
                    // Add your mail body content
                    $mail->Body = "The status of asset with ID $assetId has been changed to $status.";

                    $mail->send();
                    echo 'Message has been sent';
                    break; // Stop the loop after sending the email
                }
            }
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

   

        $assetIds = [15199, 15200, 15201, 15202, 15203, 15204, 15205, 15206, 16328, 16234, 16235, 16236, 16237, 16478, 16238, 16654, 16239, 16593, 16275, 16240, 16241, 16242, 16243, 16244, 16245, 16246, 16247, 16248, 16249, 16250, 16251, 16252, 16253, 16254, 16255, 15229, 15230, 15231, 15232, 15233, 16228, 16229, 16230, 16231, 16232, 16268, 16269, 16270, 16271, 16272, 16273, 16329, 16276, 16274, 16278, 16279, 16280, 16281, 16282, 16283, 16284, 16285, 16286, 16287, 16288, 16289, 16290, 16291, 16292, 16293, 16294, 16295, 16296, 16297, 16298, 16299, 16300, 16301, 16302, 16303, 16304, 16305, 16306, 16307, 16308, 16309, 16310, 16311, 16312, 16313, 16314, 16315, 16316, 16317, 16318, 16319, 16320, 16321, 16322, 16323, 16324, 15294, 16325, 16326, 16327, 15298, 15299, 16331, 15301, 15302, 15303, 15304, 15305, 15306, 15307, 15308, 15309, 15310, 15311, 15312, 15313, 15314, 15315, 15316, 15317, 15318, 15319, 15320, 15321, 15322, 15323, 15324, 15325, 15326, 15327, 15328, 15329, 15330, 15331, 15332, 15333, 15334, 15335, 15336, 15337, 15338, 15339, 15340, 15341, 15342, 15343, 15344, 15345, 15346, 15347, 15348, 15349, 16274, 16329, 16330, 16331, 16332, 16335, 16336, 16337, 16333, 16334, 16356, 16357, 16358, 16359, 16360, 16361, 16362, 16363, 16364, 16365, 16366, 16367, 16368, 16369, 16370, 16371, 16372, 16373, 16374, 16375, 16376, 16377, 16378, 16379, 16380, 16381, 16382, 16383, 16384, 16385, 16386, 16387, 16388, 16389, 16390, 16391, 16392, 16393, 16394, 16395, 16396, 16397, 16398, 16399, 16400, 16401, 16402, 16403, 16404, 16405, 15410, 15411, 15412, 15413, 15414, 15415, 15416, 15417, 15418, 15419, 15420, 15421, 15422, 15423, 15424, 15425, 15426, 15427, 15428, 15429, 16406, 15431, 15432, 15433, 15434, 15435, 15436, 15437, 15438, 15439, 15440, 15441, 15442, 15443, 15444, 15445, 15446, 15447, 15448, 15449, 15450, 15451, 15452, 15453, 15454, 15455, 15456, 15457, 15458, 15459, 15460, 15461, 15565, 16407, 16408, 16409, 16410, 16411, 16412, 16413, 16414, 16415, 16416, 16417, 16418, 16419, 16420, 16421, 16422, 16423, 16424, 16425, 16426, 16427, 16428, 16429, 16430, 16431, 16432, 16433, 16434, 16435, 16436, 16437, 16438, 16439, 16440, 16441, 16442, 16443, 16444, 16445, 16446, 16447, 16448, 16449, 16450, 16451, 16452, 16453, 16454, 16455, 16456, 16457, 16458, 16459, 16460, 16461, 16462, 16463, 16464, 16465, 16466, 15626, 15627, 16469, 16470, 16471, 16472, 16473, 16474, 16479, 16476, 16477, 15637, 16478, 16477, 16533, 16480, 16532, 16531, 16530, 16529, 16524, 16525, 16526, 16527, 16528, 16519, 16520, 16521, 16522, 16523, 16514, 16515, 16516, 16517, 16518, 16483, 16484, 16485, 16486, 16487, 16488, 16489, 16490, 16491, 16492, 16493, 16494, 16495, 16496, 16497, 16498, 16499, 16500, 16501, 16502, 16503, 16504, 16505, 16506, 16507, 16508, 16509, 16510, 16511, 16512, 15691, 16533, 16534, 16535, 16536, 16542, 16539, 16538, 16537, 16541, 16540, 16573, 16574, 16575, 16576, 16577, 16578, 16579, 16580, 16581, 16582, 16583, 16584, 16585, 16586, 16587, 16588, 16589, 16590, 16591, 16592, 16543, 16544, 16545, 16546, 16547, 16548, 16549, 16550, 16551, 16552, 16553, 16554, 16555, 16556, 16557, 16558, 16559, 16560, 16561, 16562, 16563, 16564, 16565, 16566, 16567, 16568, 16569, 16570, 16571, 16572, 15752, 16594, 16595, 16596, 16597, 16598, 16599, 16600, 16603, 16602, 16601, 16604, 16605, 16606, 16607, 16608, 16609, 16610, 16611, 16612, 16613, 16614, 16615, 16616, 16617, 16618, 16619, 16620, 16621, 16622, 16623, 16624, 16625, 16626, 16627, 16628, 16629, 16630, 16631, 16632, 16633, 16634, 16635, 16636, 16637, 16638, 16639, 16640, 16641, 16642, 16643, 16644, 16645, 16646, 16647, 16648, 16649, 16650, 16651, 16652, 16653, 15813, 16655, 16233, 16277, 16482, 16480];


    // Loop through each asset ID
    foreach ($assetIds as $id) {
        // Prepare and execute the SQL query
        $sql = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the data
        $row = $result->fetch_assoc();
        // Create variables dynamically using variable variables
        foreach ($row as $key => $value) {
            ${$key . $id} = $value;
        }
        $stmt->close();
    }

    // Function to update asset information based on asset ID
    // Function to handle update for a given asset ID
    function updateAsset($conn, $assetId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit' . $assetId])) {
            // Get form data
            $status = $_POST['status'];
            $description = $_POST['description'];
            $room = $_POST['room'];
            $assignedBy = $_POST['assignedBy'];
            // Assuming assignedName is fetched from somewhere
            $assignedName = ''; // Change this according to your logic

            // Check if status is "Need Repair" and set "Assigned Name" to none
            $assignedName = $status === 'Need Repair' ? '' : $assignedName;

            // Prepare SQL query to update the asset
            $sql = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssi', $status, $assignedName, $assignedBy, $description, $room, $assetId);

            if ($stmt->execute()) {
                // Update success
                logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
                echo "<script>alert('Asset updated successfully!');</script>";
                header("Location: BEBF4.php");
            } else {
                // Update failed
                echo "<script>alert('Failed to update asset.');</script>";
            }
            $stmt->close();
        }
    }

    // Handle form submission for any asset ID
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_img']) && isset($_POST['assetId'])) {
        // Check for upload errors
        if ($_FILES['upload_img']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['upload_img']['tmp_name'])) {
            $image = $_FILES['upload_img']['tmp_name'];
            $imgContent = file_get_contents($image); // Get the content of the file

            // Get the asset ID from the form
            $assetId = $_POST['assetId'];

            // Prepare SQL query to update the asset with the image based on asset ID
            $sql = "UPDATE asset SET upload_img = ? WHERE assetId = ?";
            $stmt = $conn->prepare($sql);

            // Null for blob data
            $null = NULL;
            $stmt->bind_param('bi', $null, $assetId);
            // Send blob data in packets
            $stmt->send_long_data(0, $imgContent);

            if ($stmt->execute()) {
                echo "<script>alert('Asset and image updated successfully!');</script>";
                header("Location: BABF1.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
        }
    }

    // Call updateAsset function for each asset ID you want to handle
    $assetIds = [15199, 15200, 15201, 15202, 15203, 15204, 15205, 15206, 16328, 16234, 16235, 16236, 16237, 16238, 16478, 16239, 16654, 16275, 16593, 16240, 16241, 16242, 16243, 16244, 16245, 16246, 16247, 16248, 16249, 16250, 16251, 16252, 16253, 16254, 16255, 15229, 15230, 15231, 15232, 15233, 16228, 16229, 16230, 16231, 16232, 16268, 16269, 16270, 16271, 16272, 16273, 16329, 16276, 16274, 16278, 16279, 16280, 16281, 16282, 16283, 16284, 16285, 16286, 16287, 16288, 16289, 16290, 16291, 16292, 16293, 16294, 16295, 16296, 16297, 16298, 16299, 16300, 16301, 16302, 16303, 16304, 16305, 16306, 16307, 16308, 16309, 16310, 16311, 16312, 16313, 16314, 16315, 16316, 16317, 16318, 16319, 16320, 16321, 16322, 16323, 16324, 16325, 16326, 16327, 15298, 15299, 16277, 15301, 15302, 15303, 15304, 15305, 15306, 15307, 15308, 15309, 15310, 15311, 15312, 15313, 15314, 15315, 15316, 15317, 15318, 15319, 15320, 15321, 15322, 15323, 15324, 15325, 15326, 15327, 15328, 15329, 15330, 15331, 15332, 15333, 15334, 15335, 15336, 15337, 15338, 15339, 15340, 15341, 15342, 15343, 15344, 15345, 15346, 15347, 15348, 15349, 16274, 16329, 16330, 16277, 16332, 16335, 16336, 16337, 16333, 16334, 16356, 16357, 16358, 16359, 16360, 16361, 16362, 16363, 16364, 16365, 16366, 16367, 16368, 16369, 16370, 16371, 16372, 16373, 16374, 16375, 16376, 16377, 16378, 16379, 16380, 16381, 16382, 16383, 16384, 16385, 16386, 16387, 16388, 16389, 16390, 16391, 16392, 16393, 16394, 16395, 16396, 16397, 16398, 16399, 16400, 16401, 16402, 16403, 16404, 16405, 15410, 15411, 15412, 15413, 15414, 15415, 15416, 15417, 15418, 15419, 15420, 15421, 15422, 15423, 15424, 15425, 15426, 15427, 15428, 15429, 16406, 15431, 15432, 15433, 15434, 15435, 15436, 15437, 15438, 15439, 15440, 15441, 15442, 15443, 15444, 15445, 15446, 15447, 15448, 15449, 15450, 15451, 15452, 15453, 15454, 15455, 15456, 15457, 15458, 15459, 15460, 15461, 15565, 16407, 16408, 16409, 16410, 16411, 16412, 16413, 16414, 16415, 16416, 16417, 16418, 16419, 16420, 16421, 16422, 16423, 16424, 16425, 16426, 16427, 16428, 16429, 16430, 16431, 16432, 16433, 16434, 16435, 16436, 16437, 16438, 16439, 16440, 16441, 16442, 16443, 16444, 16445, 16446, 16447, 16448, 16449, 16450, 16451, 16452, 16453, 16454, 16455, 16456, 16457, 16458, 16459, 16460, 16461, 16462, 16463, 16464, 16465, 16466, 15626, 15627, 16469, 16470, 16471, 16472, 16473, 16474, 16479, 16476, 16477, 15637, 16478, 16477, 16533, 16532, 16531, 16530, 16529, 16524, 16525, 16526, 16527, 16528, 16519, 16520, 16521, 16522, 16523, 16514, 16515, 16516, 16517, 16518, 16482, 16484, 16485, 16486, 16487, 16488, 16489, 16490, 16491, 16492, 16493, 16494, 16495, 16496, 16497, 16498, 16499, 16500, 16501, 16502, 16503, 16504, 16505, 16506, 16507, 16508, 16509, 16510, 16511, 16512, 16533, 16534, 16535, 16536, 16542, 16539, 16538, 16537, 16541, 16540, 16573, 16574, 16575, 16576, 16577, 16578, 16579, 16580, 16581, 16582, 16583, 16584, 16585, 16586, 16587, 16588, 16589, 16590, 16591, 16592, 16543, 16544, 16545, 16546, 16547, 16548, 16549, 16550, 16551, 16552, 16553, 16554, 16555, 16556, 16557, 16558, 16559, 16560, 16561, 16562, 16563, 16564, 16565, 16566, 16567, 16568, 16569, 16570, 16571, 16572, 15752, 16594, 16595, 16596, 16597, 16598, 16599, 16600, 16603, 16602, 16601, 16604, 16605, 16606, 16607, 16608, 16609, 16610, 16611, 16612, 16613, 16614, 16615, 16616, 16617, 16618, 16619, 16620, 16621, 16622, 16623, 16624, 16625, 16626, 16627, 16628, 16629, 16630, 16631, 16632, 16633, 16634, 16635, 16636, 16637, 16638, 16639, 16640, 16641, 16642, 16643, 16644, 16645, 16646, 16647, 16648, 16649, 16650, 16651, 16652, 16653, 15813, 16655, 16233, 16277, 16482, 16479];
    foreach ($assetIds as $id) {
        updateAsset($conn, $id);
    }

    function getStatusColor($status)
    {
        switch ($status) {
            case 'Working':
                return 'green';
            case 'Under Maintenance':
                return 'yellow';
            case 'Need Repair':
                return 'red';
            case 'For Replacement':
                return 'blue';
            default:
                return 'grey'; // Default color
        }
    }

    //FOR IMAGE UPLOAD BASED ON ASSET ID
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_img']) && isset($_POST['assetId'])) {
        // Check for upload errors
        if ($_FILES['upload_img']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['upload_img']['tmp_name'])) {
            $image = $_FILES['upload_img']['tmp_name'];
            $imgContent = file_get_contents($image); // Get the content of the file

            // Get the asset ID from the form
            $assetId = $_POST['assetId'];

            // Prepare SQL query to update the asset with the image based on asset ID
            $sql = "UPDATE asset SET upload_img = ? WHERE assetId = ?";
            $stmt = $conn->prepare($sql);

            // Null for blob data
            $null = NULL;
            $stmt->bind_param('bi', $null, $assetId);
            // Send blob data in packets
            $stmt->send_long_data(0, $imgContent);

            if ($stmt->execute()) {
                echo "<script>alert('Asset and image updated successfully!');</script>";
                header("Location: BEBF4.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload image. Error: " . $_FILES['upload_img']['error'] . "');</script>";
        }
    }
    ?>
    

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Map</title>
            <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
                integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
            <link rel="stylesheet" href="../../../src/css/main.css" />
            <link rel="stylesheet" href="../../buildingCSS/KOB/KOBF1.css" />
            <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
            <link rel="stylesheet" href="../../../src/css/map.css" />
            <link rel="stylesheet" href="../../../src/css/map-container.css" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        </head>

        <style>
            .notification-indicator {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: red;
                position: absolute;
                top: 10px;
                right: 10px;
            }
        </style>

        <body>
            <div id="navbar" class="">
                <nav>
                    <div class="hamburger">
                        <i class="bi bi-list"></i>
                        <a href="#" class="brand" title="logo">
                        </a>
                    </div>
                    <div class="content-nav">
                        <div class="notification-dropdown">
                            <a href="#" class="notification" id="notification-button">
                                <i class="fa fa-bell" aria-hidden="true"></i>
                                <!-- Notification Indicator Dot -->
                                <?php if ($unseenCount > 0): ?>
                                        <span class="notification-indicator"></span>
                                <?php endif; ?>
                            </a>

                            <div class="dropdown-content" id="notification-dropdown-content">
                                <h6 class="dropdown-header">Alerts Center</h6>
                                <!-- PHP code to display notifications will go here -->
                                <?php
                                if ($resultLatestLogs && $resultLatestLogs->num_rows > 0) {
                                    while ($row = $resultLatestLogs->fetch_assoc()) {
                                        $adminName = $row["adminFirstName"] . ' ' . $row["adminLastName"];
                                        $adminRole = $row["adminRole"]; // This should be the role such as 'Manager' or 'Personnel'
                                        $actionText = $row["action"];

                                        // Initialize the notification text as empty
                                        $notificationText = "";
                                        if (strpos($actionText, $adminRole) === false) {
                                            // Role is not in the action text, so prepend it to the admin name
                                            $adminName = "$adminRole $adminName";
                                        }
                                        // Check for 'Assigned maintenance personnel' action
                                        if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
                                            $assignedName = $matches[1];
                                            $assetId = $matches[2];
                                            $notificationText = "assigned $assignedName to asset ID $assetId";
                                        }
                                        // Check for 'Changed status of asset ID' action
                                        elseif (preg_match('/Changed status of asset ID (\d+) to (.+)/', $actionText, $matches)) {
                                            $assetId = $matches[1];
                                            $newStatus = $matches[2];
                                            $notificationText = "changed status of asset ID $assetId to $newStatus";
                                        }

                                        // If notification text is set, echo the notification
                                        if (!empty($notificationText)) {
                                            // HTML for notification item
                                            echo '<a href="#" class="notification-item" data-activity-id="' . $row["activityId"] . '">' . htmlspecialchars("$adminName $notificationText") . '</a>';
                                        }
                                    }
                                } else {
                                    // No notifications found
                                    echo '<a href="#">No new notifications</a>';
                                }
                                ?>
                                <a href="activity-logs.php" class="view-all">View All</a>

                            </div>
                        </div>

                        <a href="#" class="settings profile">
                            <div class="profile-container" title="settings">
                                <div class="profile-img">
                                    <?php
                                    if ($conn->connect_error) {
                                        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
                                    }

                                    $userId = $_SESSION['accountId'];
                                    $query = "SELECT picture FROM account WHERE accountId = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param('i', $userId);
                                    $stmt->execute();
                                    $stmt->store_result();

                                    if ($stmt->num_rows > 0) {
                                        $stmt->bind_result($userPicture);
                                        $stmt->fetch();

                                        echo "<img src='data:image/jpeg;base64," . base64_encode($userPicture) . "' title='profile-picture' />";
                                    } else {
                                        echo $_SESSION['firstName'];
                                    }

                                    $stmt->close();
                                    ?>
                                </div>
                                <div class="profile-name-container " id="desktop">
                                    <div><a class="profile-name">
                                            <?php echo $_SESSION['firstName']; ?>
                                        </a></div>
                                    <div><a class="profile-role">
                                            <?php echo $_SESSION['role']; ?>
                                        </a></div>
                                </div>
                            </div>
                        </a>

                        <div id="settings-dropdown" class="dropdown-content1">
                            <div class="profile-name-container" id="mobile">
                                <div><a class="profile-name">
                                        <?php echo $_SESSION['firstName']; ?>
                                    </a></div>
                                <div><a class="profile-role">
                                        <?php echo $_SESSION['role']; ?>
                                    </a></div>
                                <hr>
                            </div>
                            <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><i
                                    class="bi bi-person profile-icons"></i>Profile</a>
                            <a class="profile-hover" href="#" id="logoutBtn"><i class="bi bi-box-arrow-left "></i>Logout</a>
                        </div>
                        <?php
} else {
    header("Location:../../index.php");
    exit();
}
?>
            </div>
        </nav>
    </div>
    <section id="sidebar">
        <div href="#" class="brand" title="logo">
            <i><img src="../../../src/img/UpKeep.png" alt="" class="logo" /></i>
            <div class="mobile-sidebar-close">
                <i class="bi bi-arrow-left-circle"></i>
            </div>
        </div>
        <ul class="side-menu top">
            <li>
                <a href="../../administrator/dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/attendance-logs.php">
                    <i class="bi bi-calendar-week"></i>
                    <span class="text">Attendance Logs</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/staff.php">
                    <i class="bi bi-person"></i>
                    <span class="text">Staff</span>
                </a>
            </li>
                <div class="GPS-cont" onclick="toggleGPS()">
                    <li class="GPS-dropdown">
                        <div class="GPS-drondown-content">
                            <div class="GPS-side-cont">
                                <i class="bi bi-geo-alt"></i>
                                <span class="text">GPS</span>
                            </div>
                            <div class="GPS-ind">
                                <i id="chevron-icon" class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                    </li>
                </div>
                <div class="GPS-container">
                    <li class="GPS-Tracker">
                        <a href="../../administrator/gps.php">
                            <i class="bi bi-crosshair"></i>
                            <span class="text">GPS Tracker</span>
                        </a>
                    </li>
                    <li class="GPS-History">
                        <a href="../../administrator/gps-history.php">
                            <i class="bi bi-radar"></i>
                            <span class="text">GPS History</span>
                        </a>
                    </li>
                </div>
            <li class="active">
                <a href="../../administrator/map.php">
                    <i class="bi bi-map"></i>
                    <span class="text">Map</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/reports.php">
                    <i class="bi bi-clipboard"></i>
                    <span class="text">Reports</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/archive.php">
                    <i class="bi bi-archive"></i>
                    <span class="text">Archive</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/activity-logs.php">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span class="text">Activity Logs</span>
                </a>
            </li>
        </ul>
    </section>
        <div id="map-top-nav">
        <a href="../../administrator/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>

        <div class="legend-button" id="legendButton">
            <i class="bi bi-info-circle"></i>
        </div>
    </div>
    <section id="content">
        <main>
            <div class="content-container" id="content-container">
                <div id="belmonte-F1" class="content">

                    <!-- FLOOR PLAN -->
                    <img class="Floor-container-1 .NEWBF1" src="../../../src/floors/belmonteB/BB4F.png" alt="">


                    <div class="legend-body" id="legendBody">
                        <!-- Your legend body content goes here -->
                        <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                            <p>BULB</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/I-CHAIR.jpg" alt="" class="legend-img">
                            <p>CHAIR</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                            <p>CHAIR</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/I-TABLE.jpg" alt="" class="legend-img">
                            <p>TABLE</p>
                        </div>
                    </div>

                    <div class="map-nav">
                        <div class="map-legend">
                            <div class="legend-item" data-status="Working">
                                <div class="legend-color-green"></div>
                                <button class="legend-toggle">Working</button>
                            </div>
                            <div class="legend-item" data-status="Under Maintenance">
                                <div class="legend-color-under-maintenance"></div>
                                <button class="legend-toggle">Under maintenance</button>
                            </div>
                            <div class="legend-item" data-status="Need Repair">
                                <div class="legend-color-need-repair"></div>
                                <button class="legend-toggle">Need repair</button>
                            </div>
                            <div class="legend-item" data-status="For Replacement">
                                <div class="legend-color-for-replacement"></div>
                                <button class="legend-toggle">For replacement</button>
                            </div>
                        </div>
                    </div>
                    <!-- ASSETS -->

                    <!-- Start of Hallway -->

                    <!-- ASSET 16228 -->
                    <img src='../image.php?id=16228'
                        style='width:20px; cursor:pointer; position:absolute; top:170px; left:120px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16228' onclick='fetchAssetData(16228);'
                        class="asset-image" data-id="<?php echo $assetId16228; ?>"
                        data-room="<?php echo htmlspecialchars($room16228); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16228); ?>"
                        data-image="<?php echo base64_encode($upload_img16228); ?>"
                        data-status="<?php echo htmlspecialchars($status16228); ?>"
                        data-category="<?php echo htmlspecialchars($category16228); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16228); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16228); ?>; 
                        position:absolute; top:170px; left:135px;'>
                    </div>

                    <!-- ASSET 16229 -->
                    <img src='../image.php?id=16229'
                        style='width:20px; cursor:pointer; position:absolute; top:190px; left:120px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16229' onclick='fetchAssetData(16229);'
                        class="asset-image" data-id="<?php echo $assetId16229; ?>"
                        data-room="<?php echo htmlspecialchars($room16229); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16229); ?>"
                        data-image="<?php echo base64_encode($upload_img16229); ?>"
                        data-status="<?php echo htmlspecialchars($status16229); ?>"
                        data-category="<?php echo htmlspecialchars($category16229); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16229); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16229); ?>; 
                        position:absolute; top:190px; left:135px;'>
                    </div>

                    <!-- ASSET 16230 -->
                    <img src='../image.php?id=16230'
                        style='width:20px; cursor:pointer; position:absolute; top:210px; left:120px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16230' onclick='fetchAssetData(16230);'
                        class="asset-image" data-id="<?php echo $assetId16230; ?>"
                        data-room="<?php echo htmlspecialchars($room16230); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16230); ?>"
                        data-image="<?php echo base64_encode($upload_img16230); ?>"
                        data-status="<?php echo htmlspecialchars($status16230); ?>"
                        data-category="<?php echo htmlspecialchars($category16230); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16230); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16230); ?>; 
                        position:absolute; top:210px; left:135px;'>
                    </div>

                    <!-- ASSET 16231 -->
                    <img src='../image.php?id=16231'
                        style='width:20px; cursor:pointer; position:absolute; top:170px; left:175px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16231' onclick='fetchAssetData(16231);'
                        class="asset-image" data-id="<?php echo $assetId16231; ?>"
                        data-room="<?php echo htmlspecialchars($room16231); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16231); ?>"
                        data-image="<?php echo base64_encode($upload_img16231); ?>"
                        data-category="<?php echo htmlspecialchars($category16231); ?>"
                        data-status="<?php echo htmlspecialchars($status16231); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16231); ?>">

                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16231); ?>; 
                        position:absolute; top:170px; left:190px;'>
                    </div>

                    <!-- ASSET 16232 -->
                    <img src='../image.php?id=16232'
                        style='width:20px; cursor:pointer; position:absolute; top:207px; left:175px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16232' onclick='fetchAssetData(16232);'
                        class="asset-image" data-id="<?php echo $assetId16232; ?>"
                        data-room="<?php echo htmlspecialchars($room16232); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16232); ?>"
                        data-image="<?php echo base64_encode($upload_img16232); ?>"
                        data-status="<?php echo htmlspecialchars($status16232); ?>"
                        data-category="<?php echo htmlspecialchars($category16232); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16232); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16232); ?>; 
                        position:absolute; top:207px; left:190px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:390px; left:175px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:390px; left:185px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:390px; left:95px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:390px; left:105px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:490px; left:175px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:490px; left:185px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:490px; left:95px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:490px; left:105px;'>
                    </div>

                    <!-- ////// -->

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:390px; left:1040px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:390px; left:1050px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:390px; left:1120px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:390px; left:1130px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:490px; left:1040px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:490px; left:1050px;'>
                    </div>

                    <!-- ASSET 16233 -->
                    <img src='../image.php?id=16233'
                        style='width:20px; cursor:pointer; position:absolute; top:490px; left:1120px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16233' onclick='fetchAssetData(16233);'
                        class="asset-image" data-id="<?php echo $assetId16233; ?>"
                        data-room="<?php echo htmlspecialchars($room16233); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16233); ?>"
                        data-image="<?php echo base64_encode($upload_img16233); ?>"
                        data-status="<?php echo htmlspecialchars($status16233); ?>"
                        data-category="<?php echo htmlspecialchars($category16233); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16233); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16233); ?>; 
                        position:absolute; top:490px; left:1130px;'>
                    </div>

                    <!-- ASSET 16234 -->
                    <img src='../image.php?id=16234'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16234' onclick='fetchAssetData(16234);'
                        class="asset-image" data-id="<?php echo $assetId16234; ?>"
                        data-room="<?php echo htmlspecialchars($room16234); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16234); ?>"
                        data-image="<?php echo base64_encode($upload_img16234); ?>"
                        data-status="<?php echo htmlspecialchars($status16234); ?>"
                        data-category="<?php echo htmlspecialchars($category16234); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16234); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16234); ?>; 
                        position:absolute; top:252px; left:235px;'>
                    </div>

                    <!-- ASSET 16235 -->
                    <img src='../image.php?id=16235'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16235' onclick='fetchAssetData(16235);'
                        class="asset-image" data-id="<?php echo $assetId16235; ?>"
                        data-room="<?php echo htmlspecialchars($room16235); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16235); ?>"
                        data-status="<?php echo htmlspecialchars($status16235); ?>"
                        data-image="<?php echo base64_encode($upload_img16235); ?>"
                        data-category="<?php echo htmlspecialchars($category16235); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16235); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16235); ?>; 
                        position:absolute; top:320px; left:235px;'>
                    </div>

                    <!-- ASSET 16236 -->
                    <img src='../image.php?id=16236'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:305px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16236' onclick='fetchAssetData(16236);'
                        class="asset-image" data-id="<?php echo $assetId16236; ?>"
                        data-room="<?php echo htmlspecialchars($room16236); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16236); ?>"
                        data-image="<?php echo base64_encode($upload_img16236); ?>"
                        data-category="<?php echo htmlspecialchars($category16236); ?>"
                        data-status="<?php echo htmlspecialchars($status16236); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16236); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16236); ?>; 
                        position:absolute; top:320px; left:320px;'>
                    </div>

                    <!-- ASSET 16237 -->
                    <img src='../image.php?id=16237'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:305px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16237' onclick='fetchAssetData(16237);'
                        class="asset-image" data-id="<?php echo $assetId16237; ?>"
                        data-room="<?php echo htmlspecialchars($room16237); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16237); ?>"
                        data-image="<?php echo base64_encode($upload_img16237); ?>"
                        data-status="<?php echo htmlspecialchars($status16237); ?>"
                        data-category="<?php echo htmlspecialchars($category16237); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16237); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16237); ?>; 
                        position:absolute; top:252px; left:320px;'>
                    </div>



                    <!-- ASSET 16238 -->
                    <img src='../image.php?id=16238'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:380px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16238' onclick='fetchAssetData(16238);'
                        class="asset-image" data-id="<?php echo $assetId16238; ?>"
                        data-room="<?php echo htmlspecialchars($room16238); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16238); ?>"
                        data-image="<?php echo base64_encode($upload_img16238); ?>"
                        data-status="<?php echo htmlspecialchars($status16238); ?>"
                        data-category="<?php echo htmlspecialchars($category16238); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16238); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16238); ?>; 
                        position:absolute; top:252px; left:395px;'>
                    </div>

                    <!-- ASSET 16239 -->
                    <img src='../image.php?id=16239'
                    style='width:20px; cursor:pointer; position:absolute; top:320px; left:380px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16239' onclick='fetchAssetData(16239);'
                        class="asset-image" data-id="<?php echo $assetId16239; ?>"
                        data-room="<?php echo htmlspecialchars($room16239); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16239); ?>"
                        data-image="<?php echo base64_encode($upload_img16239); ?>"
                        data-category="<?php echo htmlspecialchars($category16239); ?>"
                        data-status="<?php echo htmlspecialchars($status16239); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16239); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16239); ?>; 
                        position:absolute; top:320px; left:395px;'>
                    </div>

                    <!-- ASSET 16240 -->
                    <img src='../image.php?id=16240'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:480px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16240' onclick='fetchAssetData(16240);'
                        class="asset-image" data-id="<?php echo $assetId16240; ?>"
                        data-room="<?php echo htmlspecialchars($room16240); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16240); ?>"
                        data-image="<?php echo base64_encode($upload_img16240); ?>"
                        data-category="<?php echo htmlspecialchars($category16240); ?>"
                        data-status="<?php echo htmlspecialchars($status16240); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16240); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16240); ?>; 
                        position:absolute; top:320px; left:495px;'>
                    </div>

                    <!-- ASSET 16241 -->
                    <img src='../image.php?id=16241'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:480px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16241' onclick='fetchAssetData(16241);'
                        class="asset-image" data-id="<?php echo $assetId16241; ?>"
                        data-room="<?php echo htmlspecialchars($room16241); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16241); ?>"
                        data-image="<?php echo base64_encode($upload_img16241); ?>"
                        data-category="<?php echo htmlspecialchars($category16241); ?>"
                        data-status="<?php echo htmlspecialchars($status16241); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16241); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16241); ?>; 
                        position:absolute; top:252px; left:495px;'>
                    </div>

                    <!-- ASSET 16242 -->
                    <img src='../image.php?id=16242'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:560px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16242' onclick='fetchAssetData(16242);'
                        class="asset-image" data-id="<?php echo $assetId16242; ?>"
                        data-room="<?php echo htmlspecialchars($room16242); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16242); ?>"
                        data-image="<?php echo base64_encode($upload_img16242); ?>"
                        data-category="<?php echo htmlspecialchars($category16242); ?>"
                        data-status="<?php echo htmlspecialchars($status16242); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16242); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16242); ?>; 
                        position:absolute; top:252px; left:575px;'>
                    </div>

                    <!-- ASSET 16243 -->
                    <img src='../image.php?id=16243'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:560px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16243' onclick='fetchAssetData(16243);'
                        class="asset-image" data-id="<?php echo $assetId16243; ?>"
                        data-room="<?php echo htmlspecialchars($room16243); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16243); ?>"
                        data-image="<?php echo base64_encode($upload_img16243); ?>"
                        data-category="<?php echo htmlspecialchars($category16243); ?>"
                        data-status="<?php echo htmlspecialchars($status16243); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16243); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16243); ?>; 
                        position:absolute; top:320px; left:575px;'>
                    </div>

                    <!-- ASSET 16244 -->
                    <img src='../image.php?id=16244'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:640px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16244' onclick='fetchAssetData(16244);'
                        class="asset-image" data-id="<?php echo $assetId16244; ?>"
                        data-room="<?php echo htmlspecialchars($room16244); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16244); ?>"
                        data-image="<?php echo base64_encode($upload_img16244); ?>"
                        data-status="<?php echo htmlspecialchars($status16244); ?>"
                        data-category="<?php echo htmlspecialchars($category16244); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16244); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16244); ?>; 
                        position:absolute; top:320px; left:655px;'>
                    </div>

                    <!-- ASSET 16245 -->
                    <img src='../image.php?id=16245'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:640px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16245' onclick='fetchAssetData(16245);'
                        class="asset-image" data-id="<?php echo $assetId16245; ?>"
                        data-room="<?php echo htmlspecialchars($room16245); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16245); ?>"
                        data-image="<?php echo base64_encode($upload_img16245); ?>"
                        data-category="<?php echo htmlspecialchars($category16245); ?>"
                        data-status="<?php echo htmlspecialchars($status16245); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16245); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16245); ?>; 
                        position:absolute; top:252px; left:655px;'>
                    </div>

                    <!-- ASSET 16246 -->
                    <img src='../image.php?id=16246'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:725px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16246' onclick='fetchAssetData(16246);'
                        class="asset-image" data-id="<?php echo $assetId16246; ?>"
                        data-room="<?php echo htmlspecialchars($room16246); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16246); ?>"
                        data-image="<?php echo base64_encode($upload_img16246); ?>"
                        data-status="<?php echo htmlspecialchars($status16246); ?>"
                        data-category="<?php echo htmlspecialchars($category16246); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16246); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16246); ?>; 
                        position:absolute; top:252px; left:740px;'>
                    </div>

                    <!-- ASSET 16247 -->
                    <img src='../image.php?id=16247'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:725px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16247' onclick='fetchAssetData(16247);'
                        class="asset-image" data-id="<?php echo $assetId16247; ?>"
                        data-room="<?php echo htmlspecialchars($room16247); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16247); ?>"
                        data-image="<?php echo base64_encode($upload_img16247); ?>"
                        data-status="<?php echo htmlspecialchars($status16247); ?>"
                        data-category="<?php echo htmlspecialchars($category16247); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16247); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16247); ?>; 
                        position:absolute; top:320px; left:740px;'>
                    </div>



                    <!-- ASSET 16248 -->
                    <img src='../image.php?id=16248'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:815px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16248' onclick='fetchAssetData(16248);'
                        class="asset-image" data-id="<?php echo $assetId16248; ?>"
                        data-room="<?php echo htmlspecialchars($room16248); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16248); ?>"
                        data-image="<?php echo base64_encode($upload_img16248); ?>"
                        data-status="<?php echo htmlspecialchars($status16248); ?>"
                        data-category="<?php echo htmlspecialchars($category16248); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16248); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16248); ?>; 
                        position:absolute; top:320px; left:830px;'>
                    </div>

                    <!-- ASSET 16249 -->
                    <img src='../image.php?id=16249'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:815px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16249' onclick='fetchAssetData(16249);'
                        class="asset-image" data-id="<?php echo $assetId16249; ?>"
                        data-room="<?php echo htmlspecialchars($room16249); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16249); ?>"
                        data-image="<?php echo base64_encode($upload_img16249); ?>"
                        data-category="<?php echo htmlspecialchars($category16249); ?>"
                        data-status="<?php echo htmlspecialchars($status16249); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16249); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16249); ?>; 
                        position:absolute; top:252px; left:830px;'>
                    </div>

                    <!-- ASSET 16250 -->
                    <img src='../image.php?id=16250'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:890px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16250' onclick='fetchAssetData(16250);'
                        class="asset-image" data-id="<?php echo $assetId16250; ?>"
                        data-room="<?php echo htmlspecialchars($room16250); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16250); ?>"
                        data-image="<?php echo base64_encode($upload_img16250); ?>"
                        data-category="<?php echo htmlspecialchars($category16250); ?>"
                        data-status="<?php echo htmlspecialchars($status16250); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16250); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16250); ?>; 
                        position:absolute; top:252px; left:905px;'>
                    </div>

                    <!-- ASSET 16251 -->
                    <img src='../image.php?id=16251'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:890px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16251' onclick='fetchAssetData(16251);'
                        class="asset-image" data-id="<?php echo $assetId16251; ?>"
                        data-room="<?php echo htmlspecialchars($room16251); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16251); ?>"
                        data-image="<?php echo base64_encode($upload_img16251); ?>"
                        data-category="<?php echo htmlspecialchars($category16251); ?>"
                        data-status="<?php echo htmlspecialchars($status16251); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16251); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16251); ?>; 
                        position:absolute; top:320px; left:905px;'>
                    </div>

                    <!-- ASSET 16252 -->
                    <img src='../image.php?id=16252'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:970px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16252' onclick='fetchAssetData(16252);'
                        class="asset-image" data-id="<?php echo $assetId16252; ?>"
                        data-room="<?php echo htmlspecialchars($room16252); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16252); ?>"
                        data-image="<?php echo base64_encode($upload_img16252); ?>"
                        data-category="<?php echo htmlspecialchars($category16252); ?>"
                        data-status="<?php echo htmlspecialchars($status16252); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16252); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16252); ?>; 
                        position:absolute; top:320px; left:985px;'>
                    </div>


                    <!-- ASSET 16253 -->
                    <img src='../image.php?id=16253'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:970px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16253' onclick='fetchAssetData(16253);'
                        class="asset-image" data-id="<?php echo $assetId16253; ?>"
                        data-room="<?php echo htmlspecialchars($room16253); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16253); ?>"
                        data-image="<?php echo base64_encode($upload_img16253); ?>"
                        data-category="<?php echo htmlspecialchars($category16253); ?>"
                        data-status="<?php echo htmlspecialchars($status16253); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16253); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16253); ?>; 
                        position:absolute; top:252px; left:985px;'>
                    </div>

                    <!-- ASSET 16254 -->
                    <img src='../image.php?id=16254'
                        style='width:20px; cursor:pointer; position:absolute; top:252px; left:1050px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16254' onclick='fetchAssetData(16254);'
                        class="asset-image" data-id="<?php echo $assetId16254; ?>"
                        data-room="<?php echo htmlspecialchars($room16254); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16254); ?>"
                        data-image="<?php echo base64_encode($upload_img16254); ?>"
                        data-category="<?php echo htmlspecialchars($category16254); ?>"
                        data-status="<?php echo htmlspecialchars($status16254); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16254); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16254); ?>; 
                        position:absolute; top:252px; left:1065px;'>
                    </div>

                    <!-- ASSET 16255 -->
                    <img src='../image.php?id=16255'
                        style='width:20px; cursor:pointer; position:absolute; top:320px; left:1050px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16255' onclick='fetchAssetData(16255);'
                        class="asset-image" data-id="<?php echo $assetId16255; ?>"
                        data-room="<?php echo htmlspecialchars($room16255); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16255); ?>"
                        data-image="<?php echo base64_encode($upload_img16255); ?>"
                        data-category="<?php echo htmlspecialchars($category16255); ?>"
                        data-status="<?php echo htmlspecialchars($status16255); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16255); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16255); ?>; 
                        position:absolute; top:320px; left:1065px;'>
                    </div>



                    <!-- End of Hallway -->

                    <!-- Start IC101a -->

                    <!-- ASSET 16268 -->
                    <img src='../image.php?id=16268'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16268' onclick='fetchAssetData(16268);'
                        class="asset-image" data-id="<?php echo $assetId16268; ?>"
                        data-room="<?php echo htmlspecialchars($room16268); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16268); ?>"
                        data-image="<?php echo base64_encode($upload_img16268); ?>"
                        data-category="<?php echo htmlspecialchars($category16268); ?>"
                        data-status="<?php echo htmlspecialchars($status16268); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16268); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16268); ?>; 
                        position:absolute; top:70px; left:230px;'>
                    </div>

                    <!-- ASSET 16269 -->
                    <img src='../image.php?id=16269'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16269' onclick='fetchAssetData(16269);'
                        class="asset-image" data-id="<?php echo $assetId16269; ?>"
                        data-room="<?php echo htmlspecialchars($room16269); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16269); ?>"
                        data-image="<?php echo base64_encode($upload_img16269); ?>"
                        data-status="<?php echo htmlspecialchars($status16269); ?>"
                        data-category="<?php echo htmlspecialchars($category16269); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16269); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16269); ?>; 
                        position:absolute; top:145px; left:230px;'>
                    </div>

                    <!-- ASSET 16270 -->
                    <img src='../image.php?id=16270'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:347px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16270' onclick='fetchAssetData(16270);'
                        class="asset-image" data-id="<?php echo $assetId16270; ?>"
                        data-room="<?php echo htmlspecialchars($room16270); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16270); ?>"
                        data-image="<?php echo base64_encode($upload_img16270); ?>"
                        data-status="<?php echo htmlspecialchars($status16270); ?>"
                        data-category="<?php echo htmlspecialchars($category16270); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16270); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16270); ?>; 
                        position:absolute; top:70px; left:357px;'>
                    </div>

                    <!-- ASSET 16271 -->
                    <img src='../image.php?id=16271'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:347px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16271' onclick='fetchAssetData(16271);'
                        class="asset-image" data-id="<?php echo $assetId16271; ?>"
                        data-room="<?php echo htmlspecialchars($room16271); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16271); ?>"
                        data-image="<?php echo base64_encode($upload_img16271); ?>"
                        data-category="<?php echo htmlspecialchars($category16271); ?>"
                        data-status="<?php echo htmlspecialchars($status16271); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16271); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16271); ?>; 
                        position:absolute; top:145px; left:357px;'>
                    </div>


                    <!-- ASSET 16272 -->
                    <img src='../image.php?id=16272'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:460px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16272' onclick='fetchAssetData(16272);'
                        class="asset-image" data-id="<?php echo $assetId16272; ?>"
                        data-room="<?php echo htmlspecialchars($room16272); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16272); ?>"
                        data-image="<?php echo base64_encode($upload_img16272); ?>"
                        data-status="<?php echo htmlspecialchars($status16272); ?>"
                        data-category="<?php echo htmlspecialchars($category16272); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16272); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16272); ?>; 
                        position:absolute; top:145px; left:470px;'>
                    </div>

                    <!-- ASSET 16273 -->
                    <img src='../image.php?id=16273'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:460px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16273' onclick='fetchAssetData(16273);'
                        class="asset-image" data-id="<?php echo $assetId16273; ?>"
                        data-room="<?php echo htmlspecialchars($room16273); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16273); ?>"
                        data-image="<?php echo base64_encode($upload_img16273); ?>"
                        data-status="<?php echo htmlspecialchars($status16273); ?>"
                        data-category="<?php echo htmlspecialchars($category16273); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16273); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16273); ?>; 
                        position:absolute; top:70px; left:470px;'>
                    </div>

                    <!-- ASSET 16328 asd -->
                    <img src='../image.php?id=16328'
                        style='width:15px; cursor:pointer; position:absolute; top:143px; left:430px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16328' onclick='fetchAssetData(16328);'
                        class="asset-image" data-id="<?php echo $assetId16328; ?>"
                        data-room="<?php echo htmlspecialchars($room16328); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16328); ?>"
                        data-image="<?php echo base64_encode($upload_img16328); ?>"
                        data-status="<?php echo htmlspecialchars($status16328); ?>"
                        data-category="<?php echo htmlspecialchars($category16328); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16328); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16328); ?>; 
                        position:absolute; top:135px; left:433.5px;'>
                    </div>

                    <!-- ASSET 16277 -->
                    <img src='../image.php?id=16277'
                        style='width:18px; cursor:pointer; position:absolute; top:144px; left:446px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16277'
                        onclick='fetchAssetData(16277);' class="asset-image" data-id="<?php echo $assetId16277; ?>"
                        data-room="<?php echo htmlspecialchars($room16277); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16277); ?>"
                        data-image="<?php echo base64_encode($upload_img16277); ?>"
                        data-category="<?php echo htmlspecialchars($category16277); ?>"
                        data-status="<?php echo htmlspecialchars($status16277); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16277); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16277); ?>; 
                        position:absolute; top:142px; left:452px;'>
                    </div>


                    <!-- ASSET 16275 -->
                    <img src='../image.php?id=16275'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16275' onclick='fetchAssetData(16275);'
                        class="asset-image" data-id="<?php echo $assetId16275; ?>"
                        data-room="<?php echo htmlspecialchars($room16275); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16275); ?>"
                        data-image="<?php echo base64_encode($upload_img16275); ?>"
                        data-category="<?php echo htmlspecialchars($category16275); ?>"
                        data-status="<?php echo htmlspecialchars($status16275); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16275); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16275); ?>; 
                        position:absolute; top:225px; left:230px;'>
                    </div>

                    <!-- ASSET 16276 -->
                    <img src='../image.php?id=16276'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:347px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16276' onclick='fetchAssetData(16276);'
                        class="asset-image" data-id="<?php echo $assetId16276; ?>"
                        data-room="<?php echo htmlspecialchars($room16276); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16276); ?>"
                        data-image="<?php echo base64_encode($upload_img16276); ?>"
                        data-status="<?php echo htmlspecialchars($status16276); ?>"
                        data-category="<?php echo htmlspecialchars($category16276); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16276); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16276); ?>; 
                        position:absolute; top:225px; left:357px;'>
                    </div>

                    <!-- ASSET 16274 -->
                    <img src='../image.php?id=16274'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:460px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16274' onclick='fetchAssetData(16274);'
                        class="asset-image" data-id="<?php echo $assetId16274; ?>"
                        data-room="<?php echo htmlspecialchars($room16274); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16274); ?>"
                        data-image="<?php echo base64_encode($upload_img16274); ?>"
                        data-status="<?php echo htmlspecialchars($status16274); ?>"
                        data-category="<?php echo htmlspecialchars($category16274); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16274); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16274); ?>; 
                        position:absolute; top:225px; left:470px;'>
                    </div>

                    <!-- ASSET 16278 -->
                    <img src='../image.php?id=16278'
                        style='width:18px; cursor:pointer; position:absolute; top:76px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16278' onclick='fetchAssetData(16278);'
                        class="asset-image" data-id="<?php echo $assetId16278; ?>"
                        data-room="<?php echo htmlspecialchars($room16278); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16278); ?>"
                        data-status="<?php echo htmlspecialchars($status16278); ?>"
                        data-image="<?php echo base64_encode($upload_img16278); ?>"
                        data-category="<?php echo htmlspecialchars($category16278); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16278); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16278); ?>; 
                        position:absolute; top:79px; left:246px;'>
                    </div>

                    <!-- ASSET 16279 -->
                    <img src='../image.php?id=16279'
                        style='width:18px; cursor:pointer; position:absolute; top:89px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16279' onclick='fetchAssetData(16279);'
                        class="asset-image" data-id="<?php echo $assetId16279; ?>"
                        data-room="<?php echo htmlspecialchars($room16279); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16279); ?>"
                        data-image="<?php echo base64_encode($upload_img16279); ?>"
                        data-status="<?php echo htmlspecialchars($status16279); ?>"
                        data-category="<?php echo htmlspecialchars($category16279); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16279); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16279); ?>; 
                        position:absolute; top:91px; left:246px;'>
                    </div>

                    <!-- ASSET 16280 -->
                    <img src='../image.php?id=16280'
                        style='width:18px; cursor:pointer; position:absolute; top:102px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16280' onclick='fetchAssetData(16280);'
                        class="asset-image" data-id="<?php echo $assetId16280; ?>"
                        data-room="<?php echo htmlspecialchars($room16280); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16280); ?>"
                        data-image="<?php echo base64_encode($upload_img16280); ?>"
                        data-status="<?php echo htmlspecialchars($status16280); ?>"
                        data-category="<?php echo htmlspecialchars($category16280); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16280); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16280); ?>; 
                        position:absolute; top:105px; left:246px;'>
                    </div>

                    <!-- ASSET 16281 -->
                    <img src='../image.php?id=16281'
                        style='width:18px; cursor:pointer; position:absolute; top:115px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16281' onclick='fetchAssetData(16281);'
                        class="asset-image" data-id="<?php echo $assetId16281; ?>"
                        data-room="<?php echo htmlspecialchars($room16281); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16281); ?>"
                        data-image="<?php echo base64_encode($upload_img16281); ?>"
                        data-status="<?php echo htmlspecialchars($status16281); ?>"
                        data-category="<?php echo htmlspecialchars($category16281); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16281); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16281); ?>; 
                        position:absolute; top:118px; left:246px;'>
                    </div>

                    <!-- ASSET 16282 -->
                    <img src='../image.php?id=16282'
                        style='width:18px; cursor:pointer; position:absolute; top:129px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16282' onclick='fetchAssetData(16282);'
                        class="asset-image" data-id="<?php echo $assetId16282; ?>"
                        data-room="<?php echo htmlspecialchars($room16282); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16282); ?>"
                        data-image="<?php echo base64_encode($upload_img16282); ?>"
                        data-status="<?php echo htmlspecialchars($status16282); ?>"
                        data-category="<?php echo htmlspecialchars($category16282); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16282); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16282); ?>; 
                        position:absolute; top:132px; left:246px;'>
                    </div>


                    <!-- ASSET 16283 -->
                    <img src='../image.php?id=16283'
                        style='width:18px; cursor:pointer; position:absolute; top:75px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16283' onclick='fetchAssetData(16283);'
                        class="asset-image" data-id="<?php echo $assetId16283; ?>"
                        data-room="<?php echo htmlspecialchars($room16283); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16283); ?>"
                        data-image="<?php echo base64_encode($upload_img16283); ?>"
                        data-status="<?php echo htmlspecialchars($status16283); ?>"
                        data-category="<?php echo htmlspecialchars($category16283); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16283); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16283); ?>; 
                        position:absolute; top:78px; left:270px;'>
                    </div>

                    <!-- ASSET 16284 -->
                    <img src='../image.php?id=16284'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16284' onclick='fetchAssetData(16284);'
                        class="asset-image" data-id="<?php echo $assetId16284; ?>"
                        data-room="<?php echo htmlspecialchars($room16284); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16284); ?>"
                        data-image="<?php echo base64_encode($upload_img16284); ?>"
                        data-status="<?php echo htmlspecialchars($status16284); ?>"
                        data-category="<?php echo htmlspecialchars($category16284); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16284); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16284); ?>; 
                        position:absolute; top:91px; left:270px;'>
                    </div>

                    <!-- ASSET 16285 -->
                    <img src='../image.php?id=16285'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16285' onclick='fetchAssetData(16285);'
                        class="asset-image" data-id="<?php echo $assetId16285; ?>"
                        data-room="<?php echo htmlspecialchars($room16285); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16285); ?>"
                        data-image="<?php echo base64_encode($upload_img16285); ?>"
                        data-status="<?php echo htmlspecialchars($status16285); ?>"
                        data-category="<?php echo htmlspecialchars($category16285); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16285); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16285); ?>; 
                        position:absolute; top:104px; left:270px;'>
                    </div>

                    <!-- ASSET 16286 -->
                    <img src='../image.php?id=16286'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16286' onclick='fetchAssetData(16286);'
                        class="asset-image" data-id="<?php echo $assetId16286; ?>"
                        data-room="<?php echo htmlspecialchars($room16286); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16286); ?>"
                        data-status="<?php echo htmlspecialchars($status16286); ?>"
                        data-image="<?php echo base64_encode($upload_img16286); ?>"
                        data-category="<?php echo htmlspecialchars($category16286); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16286); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16286); ?>; 
                        position:absolute; top:117px; left:270px;'>
                    </div>


                    <!-- ASSET 16287 -->
                    <img src='../image.php?id=16287'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16287' onclick='fetchAssetData(16287);'
                        class="asset-image" data-id="<?php echo $assetId16287; ?>"
                        data-room="<?php echo htmlspecialchars($room16287); ?>"
                        data-status="<?php echo htmlspecialchars($status16287); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16287); ?>"
                        data-image="<?php echo base64_encode($upload_img16287); ?>"
                        data-category="<?php echo htmlspecialchars($category16287); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16287); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16287); ?>; 
                        position:absolute; top:130px; left:270px;'>
                    </div>

                    <!-- ASSET 16288 -->
                    <img src='../image.php?id=16288'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16288' onclick='fetchAssetData(16288);'
                        class="asset-image" data-id="<?php echo $assetId16288; ?>"
                        data-room="<?php echo htmlspecialchars($room16288); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16288); ?>"
                        data-image="<?php echo base64_encode($upload_img16288); ?>"
                        data-status="<?php echo htmlspecialchars($status16288); ?>"
                        data-category="<?php echo htmlspecialchars($category16288); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16288); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16288); ?>; 
                        position:absolute; top:79px; left:293px;'>
                    </div>

                    <!-- ASSET 16289 -->
                    <img src='../image.php?id=16289'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16289' onclick='fetchAssetData(16289);'
                        class="asset-image" data-id="<?php echo $assetId16289; ?>"
                        data-room="<?php echo htmlspecialchars($room16289); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16289); ?>"
                        data-image="<?php echo base64_encode($upload_img16289); ?>"
                        data-status="<?php echo htmlspecialchars($status16289); ?>"
                        data-category="<?php echo htmlspecialchars($category16289); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16289); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16289); ?>; 
                        position:absolute; top:91px; left:293px;'>
                    </div>

                    <!-- ASSET 16290 -->
                    <img src='../image.php?id=16290'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16290' onclick='fetchAssetData(16290);'
                        class="asset-image" data-id="<?php echo $assetId16290; ?>"
                        data-room="<?php echo htmlspecialchars($room16290); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16290); ?>"
                        data-status="<?php echo htmlspecialchars($status16290); ?>"
                        data-image="<?php echo base64_encode($upload_img16290); ?>"
                        data-category="<?php echo htmlspecialchars($category16290); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16290); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16290); ?>; 
                        position:absolute; top:104px; left:293px;'>
                    </div>


                    <!-- ASSET 16291 -->
                    <img src='../image.php?id=16291'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16291' onclick='fetchAssetData(16291);'
                        class="asset-image" data-id="<?php echo $assetId16291; ?>"
                        data-room="<?php echo htmlspecialchars($room16291); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16291); ?>"
                        data-image="<?php echo base64_encode($upload_img16291); ?>"
                        data-status="<?php echo htmlspecialchars($status16291); ?>"
                        data-category="<?php echo htmlspecialchars($category16291); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16291); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16291); ?>; 
                        position:absolute; top:117px; left:293px;'>
                    </div>

                    <!-- ASSET 16292 -->
                    <img src='../image.php?id=16292'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16292' onclick='fetchAssetData(16292);'
                        class="asset-image" data-id="<?php echo $assetId16292; ?>"
                        data-room="<?php echo htmlspecialchars($room16292); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16292); ?>"
                        data-image="<?php echo base64_encode($upload_img16292); ?>"
                        data-status="<?php echo htmlspecialchars($status16292); ?>"
                        data-category="<?php echo htmlspecialchars($category16292); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16292); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16292); ?>; 
                        position:absolute; top:130px; left:293px;'>
                    </div>

                    <!-- ASSET 16293 -->
                    <img src='../image.php?id=16293'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16293' onclick='fetchAssetData(16293);'
                        class="asset-image" data-id="<?php echo $assetId16293; ?>"
                        data-room="<?php echo htmlspecialchars($room16293); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16293); ?>"
                        data-status="<?php echo htmlspecialchars($status16293); ?>"
                        data-image="<?php echo base64_encode($upload_img16293); ?>"
                        data-category="<?php echo htmlspecialchars($category16293); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16293); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16293); ?>; 
                        position:absolute; top:79px; left:315px;'>
                    </div>

                    <!-- ASSET 16294 -->
                    <img src='../image.php?id=16294'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16294' onclick='fetchAssetData(16294);'
                        class="asset-image" data-id="<?php echo $assetId16294; ?>"
                        data-room="<?php echo htmlspecialchars($room16294); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16294); ?>"
                        data-image="<?php echo base64_encode($upload_img16294); ?>"
                        data-status="<?php echo htmlspecialchars($status16294); ?>"
                        data-category="<?php echo htmlspecialchars($category16294); ?>">
                        data-assignedname="<?php echo htmlspecialchars($assignedName16294); ?>"
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16294); ?>; 
                        position:absolute; top:91px; left:315px;'>
                    </div>

                    <!-- ASSET 16295 -->
                    <img src='../image.php?id=16295'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16295' onclick='fetchAssetData(16295);'
                        class="asset-image" data-id="<?php echo $assetId16295; ?>"
                        data-room="<?php echo htmlspecialchars($room16295); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16295); ?>"
                        data-status="<?php echo htmlspecialchars($status16295); ?>"
                        data-image="<?php echo base64_encode($upload_img16295); ?>"
                        data-category="<?php echo htmlspecialchars($category16295); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16295); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16295); ?>; 
                        position:absolute; top:104px; left:315px;'>
                    </div>

                    <!-- ASSET 16296 -->
                    <img src='../image.php?id=16296'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16296' onclick='fetchAssetData(16296);'
                        class="asset-image" data-id="<?php echo $assetId16296; ?>"
                        data-room="<?php echo htmlspecialchars($room16296); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16296); ?>"
                        data-image="<?php echo base64_encode($upload_img16296); ?>"
                        data-status="<?php echo htmlspecialchars($status16296); ?>"
                        data-category="<?php echo htmlspecialchars($category16296); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16296); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16296); ?>; 
                        position:absolute; top:117px; left:315px;'>
                    </div>

                    <!-- ASSET 16297 -->
                    <img src='../image.php?id=16297'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16297' onclick='fetchAssetData(16297);'
                        class="asset-image" data-id="<?php echo $assetId16297; ?>"
                        data-room="<?php echo htmlspecialchars($room16297); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16297); ?>"
                        data-status="<?php echo htmlspecialchars($status16297); ?>"
                        data-image="<?php echo base64_encode($upload_img16297); ?>"
                        data-category="<?php echo htmlspecialchars($category16297); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16297); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16297); ?>; 
                        position:absolute; top:130px; left:315px;'>
                    </div>

                    <!-- ASSET 16298 -->
                    <img src='../image.php?id=16298'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16298' onclick='fetchAssetData(16298);'
                        class="asset-image" data-id="<?php echo $assetId16298; ?>"
                        data-room="<?php echo htmlspecialchars($room16298); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16298); ?>"
                        data-image="<?php echo base64_encode($upload_img16298); ?>"
                        data-status="<?php echo htmlspecialchars($status16298); ?>"
                        data-category="<?php echo htmlspecialchars($category16298); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16298); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16298); ?>; 
                        position:absolute; top:79px; left:338px;'>
                    </div>


                    <!-- ASSET 16299 -->
                    <img src='../image.php?id=16299'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16299' onclick='fetchAssetData(16299);'
                        class="asset-image" data-id="<?php echo $assetId16299; ?>"
                        data-room="<?php echo htmlspecialchars($room16299); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16299); ?>"
                        data-image="<?php echo base64_encode($upload_img16299); ?>"
                        data-status="<?php echo htmlspecialchars($status16299); ?>"
                        data-category="<?php echo htmlspecialchars($category16299); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16299); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16299); ?>; 
                        position:absolute; top:91px; left:338px;'>
                    </div>

                    <!-- ASSET 16300 -->
                    <img src='../image.php?id=16300'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16300' onclick='fetchAssetData(16300);'
                        class="asset-image" data-id="<?php echo $assetId16300; ?>"
                        data-room="<?php echo htmlspecialchars($room16300); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16300); ?>"
                        data-image="<?php echo base64_encode($upload_img16300); ?>"
                        data-status="<?php echo htmlspecialchars($status16300); ?>"
                        data-category="<?php echo htmlspecialchars($category16300); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16300); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16300); ?>; 
                        position:absolute; top:104px; left:338px;'>
                    </div>

                    <!-- ASSET 16301 -->
                    <img src='../image.php?id=16301'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16301' onclick='fetchAssetData(16301);'
                        class="asset-image" data-id="<?php echo $assetId16301; ?>"
                        data-room="<?php echo htmlspecialchars($room16301); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16301); ?>"
                        data-image="<?php echo base64_encode($upload_img16301); ?>"
                        data-status="<?php echo htmlspecialchars($status16301); ?>"
                        data-category="<?php echo htmlspecialchars($category16301); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16301); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16301); ?>; 
                        position:absolute; top:117px; left:338px;'>
                    </div>

                    <!-- ASSET 16302 -->
                    <img src='../image.php?id=16302'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16302' onclick='fetchAssetData(16302);'
                        class="asset-image" data-id="<?php echo $assetId16302; ?>"
                        data-room="<?php echo htmlspecialchars($room16302); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16302); ?>"
                        data-image="<?php echo base64_encode($upload_img16302); ?>"
                        data-status="<?php echo htmlspecialchars($status16302); ?>"
                        data-category="<?php echo htmlspecialchars($category16302); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16302); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16302); ?>; 
                        position:absolute; top:130px; left:338px;'>
                    </div>


                    <!-- ASSET 16303 -->
                    <img src='../image.php?id=16303'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16303' onclick='fetchAssetData(16303);'
                        class="asset-image" data-id="<?php echo $assetId16303; ?>"
                        data-room="<?php echo htmlspecialchars($room16303); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16303); ?>"
                        data-image="<?php echo base64_encode($upload_img16303); ?>"
                        data-status="<?php echo htmlspecialchars($status16303); ?>"
                        data-category="<?php echo htmlspecialchars($category16303); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16303); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16303); ?>; 
                        position:absolute; top:159px; left:246px;'>
                    </div>

                    <!-- ASSET 16304 -->
                    <img src='../image.php?id=16304'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16304' onclick='fetchAssetData(16304);'
                        class="asset-image" data-id="<?php echo $assetId16304; ?>"
                        data-room="<?php echo htmlspecialchars($room16304); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16304); ?>"
                        data-image="<?php echo base64_encode($upload_img16304); ?>"
                        data-status="<?php echo htmlspecialchars($status16304); ?>"
                        data-category="<?php echo htmlspecialchars($category16304); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16304); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16304); ?>; 
                        position:absolute; top:171px; left:246px;'>
                    </div>

                    <!-- ASSET 16305 -->
                    <img src='../image.php?id=16305'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16305' onclick='fetchAssetData(16305);'
                        class="asset-image" data-id="<?php echo $assetId16305; ?>"
                        data-room="<?php echo htmlspecialchars($room16305); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16305); ?>"
                        data-image="<?php echo base64_encode($upload_img16305); ?>"
                        data-status="<?php echo htmlspecialchars($status16305); ?>"
                        data-category="<?php echo htmlspecialchars($category16305); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16305); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16305); ?>; 
                        position:absolute; top:183px; left:246px;'>
                    </div>

                    <!-- ASSET 16306 -->
                    <img src='../image.php?id=16306'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16306' onclick='fetchAssetData(16306);'
                        class="asset-image" data-id="<?php echo $assetId16306; ?>"
                        data-room="<?php echo htmlspecialchars($room16306); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16306); ?>"
                        data-image="<?php echo base64_encode($upload_img16306); ?>"
                        data-category="<?php echo htmlspecialchars($category16306); ?>"
                        data-status="<?php echo htmlspecialchars($status16306); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16306); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16306); ?>; 
                        position:absolute; top:194px; left:246px;'>
                    </div>


                    <!-- ASSET 16307 -->
                    <img src='../image.php?id=16307'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:233px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16307' onclick='fetchAssetData(16307);'
                        class="asset-image" data-id="<?php echo $assetId16307; ?>"
                        data-room="<?php echo htmlspecialchars($room16307); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16307); ?>"
                        data-image="<?php echo base64_encode($upload_img16307); ?>"
                        data-status="<?php echo htmlspecialchars($status16307); ?>"
                        data-category="<?php echo htmlspecialchars($category16307); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16307); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16307); ?>; 
                        position:absolute; top:207px; left:246px;'>
                    </div>

                    <!-- ASSET 16308 -->
                    <img src='../image.php?id=16308'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16308' onclick='fetchAssetData(16308);'
                        class="asset-image" data-id="<?php echo $assetId16308; ?>"
                        data-room="<?php echo htmlspecialchars($room16308); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16308); ?>"
                        data-status="<?php echo htmlspecialchars($status16308); ?>"
                        data-image="<?php echo base64_encode($upload_img16308); ?>"
                        data-category="<?php echo htmlspecialchars($category16308); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16308); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16308); ?>; 
                        position:absolute; top:159px; left:269px;'>
                    </div>

                    <!-- ASSET 16309 -->
                    <img src='../image.php?id=16309'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16309' onclick='fetchAssetData(16309);'
                        class="asset-image" data-id="<?php echo $assetId16309; ?>"
                        data-room="<?php echo htmlspecialchars($room16309); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16309); ?>"
                        data-image="<?php echo base64_encode($upload_img16309); ?>"
                        data-status="<?php echo htmlspecialchars($status16309); ?>"
                        data-category="<?php echo htmlspecialchars($category16309); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16309); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16309); ?>; 
                        position:absolute; top:171px; left:269px;'>
                    </div>

                    <!-- ASSET 16310 -->
                    <img src='../image.php?id=16310'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16310' onclick='fetchAssetData(16310);'
                        class="asset-image" data-id="<?php echo $assetId16310; ?>"
                        data-room="<?php echo htmlspecialchars($room16310); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16310); ?>"
                        data-image="<?php echo base64_encode($upload_img16310); ?>"
                        data-status="<?php echo htmlspecialchars($status16310); ?>"
                        data-category="<?php echo htmlspecialchars($category16310); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16310); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16310); ?>; 
                        position:absolute; top:183px; left:269px;'>
                    </div>


                    <!-- ASSET 16311 -->
                    <img src='../image.php?id=16311'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16311' onclick='fetchAssetData(16311);'
                        class="asset-image" data-id="<?php echo $assetId16311; ?>"
                        data-room="<?php echo htmlspecialchars($room16311); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16311); ?>"
                        data-status="<?php echo htmlspecialchars($status16311); ?>"
                        data-image="<?php echo base64_encode($upload_img16311); ?>"
                        data-category="<?php echo htmlspecialchars($category16311); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16311); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16311); ?>; 
                        position:absolute; top:194px; left:269px;'>
                    </div>

                    <!-- ASSET 16312 -->
                    <img src='../image.php?id=16312'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:256px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16312' onclick='fetchAssetData(16312);'
                        class="asset-image" data-id="<?php echo $assetId16312; ?>"
                        data-room="<?php echo htmlspecialchars($room16312); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16312); ?>"
                        data-image="<?php echo base64_encode($upload_img16312); ?>"
                        data-category="<?php echo htmlspecialchars($category16312); ?>"
                        data-status="<?php echo htmlspecialchars($status16312); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16312); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16312); ?>; 
                        position:absolute; top:207px; left:269px;'>
                    </div>

                    <!-- ASSET 16313 -->
                    <img src='../image.php?id=16313'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16313' onclick='fetchAssetData(16313);'
                        class="asset-image" data-id="<?php echo $assetId16313; ?>"
                        data-room="<?php echo htmlspecialchars($room16313); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16313); ?>"
                        data-image="<?php echo base64_encode($upload_img16313); ?>"
                        data-category="<?php echo htmlspecialchars($category16313); ?>"
                        data-status="<?php echo htmlspecialchars($status16313); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16313); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16313); ?>; 
                        position:absolute; top:159px; left:292px;'>
                    </div>

                    <!-- ASSET 16314 -->
                    <img src='../image.php?id=16314'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16314' onclick='fetchAssetData(16314);'
                        class="asset-image" data-id="<?php echo $assetId16314; ?>"
                        data-room="<?php echo htmlspecialchars($room16314); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16314); ?>"
                        data-image="<?php echo base64_encode($upload_img16314); ?>"
                        data-status="<?php echo htmlspecialchars($status16314); ?>"
                        data-category="<?php echo htmlspecialchars($category16314); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16314); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16314); ?>; 
                        position:absolute; top:171px; left:292px;'>
                    </div>


                    <!-- ASSET 16315 -->
                    <img src='../image.php?id=16315'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16315' onclick='fetchAssetData(16315);'
                        class="asset-image" data-id="<?php echo $assetId16315; ?>"
                        data-room="<?php echo htmlspecialchars($room16315); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16315); ?>"
                        data-image="<?php echo base64_encode($upload_img16315); ?>"
                        data-category="<?php echo htmlspecialchars($category16315); ?>"
                        data-status="<?php echo htmlspecialchars($status16315); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16315); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16315); ?>; 
                        position:absolute; top:183px; left:292px;'>
                    </div>

                    <!-- ASSET 16316 -->
                    <img src='../image.php?id=16316'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16316' onclick='fetchAssetData(16316);'
                        class="asset-image" data-id="<?php echo $assetId16316; ?>"
                        data-room="<?php echo htmlspecialchars($room16316); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16316); ?>"
                        data-status="<?php echo htmlspecialchars($status16316); ?>"
                        data-image="<?php echo base64_encode($upload_img16316); ?>"
                        data-category="<?php echo htmlspecialchars($category16316); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16316); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16316); ?>; 
                        position:absolute; top:194px; left:292px;'>
                    </div>

                    <!-- ASSET 16317 -->
                    <img src='../image.php?id=16317'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:279px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16317' onclick='fetchAssetData(16317);'
                        class="asset-image" data-id="<?php echo $assetId16317; ?>"
                        data-room="<?php echo htmlspecialchars($room16317); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16317); ?>"
                        data-image="<?php echo base64_encode($upload_img16317); ?>"
                        data-status="<?php echo htmlspecialchars($status16317); ?>"
                        data-category="<?php echo htmlspecialchars($category16317); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16317); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16317); ?>; 
                        position:absolute; top:207px; left:292px;'>
                    </div>

                    <!-- ASSET 16318 -->
                    <img src='../image.php?id=16318'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16318' onclick='fetchAssetData(16318);'
                        class="asset-image" data-id="<?php echo $assetId16318; ?>"
                        data-room="<?php echo htmlspecialchars($room16318); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16318); ?>"
                        data-image="<?php echo base64_encode($upload_img16318); ?>"
                        data-status="<?php echo htmlspecialchars($status16318); ?>"
                        data-category="<?php echo htmlspecialchars($category16318); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16318); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16318); ?>; 
                        position:absolute; top:159px; left:315px;'>
                    </div>


                    <!-- ASSET 16319 -->
                    <img src='../image.php?id=16319'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16319' onclick='fetchAssetData(16319);'
                        class="asset-image" data-id="<?php echo $assetId16319; ?>"
                        data-room="<?php echo htmlspecialchars($room16319); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16319); ?>"
                        data-image="<?php echo base64_encode($upload_img16319); ?>"
                        data-status="<?php echo htmlspecialchars($status16319); ?>"
                        data-category="<?php echo htmlspecialchars($category16319); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16319); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16319); ?>; 
                        position:absolute; top:171px; left:315px;'>
                    </div>

                    <!-- ASSET 16320 -->
                    <img src='../image.php?id=16320'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16320' onclick='fetchAssetData(16320);'
                        class="asset-image" data-id="<?php echo $assetId16320; ?>"
                        data-room="<?php echo htmlspecialchars($room16320); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16320); ?>"
                        data-image="<?php echo base64_encode($upload_img16320); ?>"
                        data-category="<?php echo htmlspecialchars($category16320); ?>"
                        data-status="<?php echo htmlspecialchars($status16320); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16320); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16320); ?>; 
                        position:absolute; top:183px; left:315px;'>
                    </div>

                    <!-- ASSET 16321 -->
                    <img src='../image.php?id=16321'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16321' onclick='fetchAssetData(16321);'
                        class="asset-image" data-id="<?php echo $assetId16321; ?>"
                        data-room="<?php echo htmlspecialchars($room16321); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16321); ?>"
                        data-image="<?php echo base64_encode($upload_img16321); ?>"
                        data-status="<?php echo htmlspecialchars($status16321); ?>"
                        data-category="<?php echo htmlspecialchars($category16321); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16321); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16321); ?>; 
                        position:absolute; top:194px; left:315px;'>
                    </div>

                    <!-- ASSET 16322 -->
                    <img src='../image.php?id=16322'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:302px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16322' onclick='fetchAssetData(16322);'
                        class="asset-image" data-id="<?php echo $assetId16322; ?>"
                        data-room="<?php echo htmlspecialchars($room16322); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16322); ?>"
                        data-image="<?php echo base64_encode($upload_img16322); ?>"
                        data-status="<?php echo htmlspecialchars($status16322); ?>"
                        data-category="<?php echo htmlspecialchars($category16322); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16322); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16322); ?>; 
                        position:absolute; top:207px; left:315px;'>
                    </div>


                    <!-- ASSET 16323 -->
                    <img src='../image.php?id=16323'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16323' onclick='fetchAssetData(16323);'
                        class="asset-image" data-id="<?php echo $assetId16323; ?>"
                        data-room="<?php echo htmlspecialchars($room16323); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16323); ?>"
                        data-image="<?php echo base64_encode($upload_img16323); ?>"
                        data-category="<?php echo htmlspecialchars($category16323); ?>"
                        data-status="<?php echo htmlspecialchars($status16323); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16323); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16323); ?>; 
                        position:absolute; top:159px; left:338px;'>
                    </div>

                    <!-- ASSET 16324 -->
                    <img src='../image.php?id=16324'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16324' onclick='fetchAssetData(16324);'
                        class="asset-image" data-id="<?php echo $assetId16324; ?>"
                        data-room="<?php echo htmlspecialchars($room16324); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16324); ?>"
                        data-image="<?php echo base64_encode($upload_img16324); ?>"
                        data-status="<?php echo htmlspecialchars($status16324); ?>"
                        data-category="<?php echo htmlspecialchars($category16324); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16324); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16324); ?>; 
                        position:absolute; top:171px; left:338px;'>
                    </div>

                    <!-- ASSET 16325 -->
                    <img src='../image.php?id=16325'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16325' onclick='fetchAssetData(16325);'
                        class="asset-image" data-id="<?php echo $assetId16325; ?>"
                        data-room="<?php echo htmlspecialchars($room16325); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16325); ?>"
                        data-image="<?php echo base64_encode($upload_img16325); ?>"
                        data-category="<?php echo htmlspecialchars($category16325); ?>"
                        data-status="<?php echo htmlspecialchars($status16325); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16325); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16325); ?>; 
                        position:absolute; top:183px; left:338px;'>
                    </div>

                    <!-- ASSET 16326 -->
                    <img src='../image.php?id=16326'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16326' onclick='fetchAssetData(16326);'
                        class="asset-image" data-id="<?php echo $assetId16326; ?>"
                        data-room="<?php echo htmlspecialchars($room16326); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16326); ?>"
                        data-image="<?php echo base64_encode($upload_img16326); ?>"
                        data-status="<?php echo htmlspecialchars($status16326); ?>"
                        data-category="<?php echo htmlspecialchars($category16326); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16326); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16326); ?>; 
                        position:absolute; top:194px; left:338px;'>
                    </div>

                    <!-- ASSET 16327 -->
                    <img src='../image.php?id=16327'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:325px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16327' onclick='fetchAssetData(16327);'
                        class="asset-image" data-id="<?php echo $assetId16327; ?>"
                        data-room="<?php echo htmlspecialchars($room16327); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16327); ?>"
                        data-status="<?php echo htmlspecialchars($status16327); ?>"
                        data-image="<?php echo base64_encode($upload_img16327); ?>"
                        data-category="<?php echo htmlspecialchars($category16327); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16327); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16327); ?>; 
                        position:absolute; top:207px; left:338px;'>
                    </div>

                    <!-- ASSET 16329 -->
                    <img src='../image.php?id=16329'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:490px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16329' onclick='fetchAssetData(16329);'
                        class="asset-image" data-id="<?php echo $assetId16329; ?>"
                        data-room="<?php echo htmlspecialchars($room16329); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16329); ?>"
                        data-image="<?php echo base64_encode($upload_img16329); ?>"
                        data-category="<?php echo htmlspecialchars($category16329); ?>"
                        data-status="<?php echo htmlspecialchars($status16329); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16329); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16329); ?>; 
                        position:absolute; top:70px; left:500px;'>
                    </div>

                    <!-- ASSET 16330 -->
                    <img src='../image.php?id=16330'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:490px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16330' onclick='fetchAssetData(16330);'
                        class="asset-image" data-id="<?php echo $assetId16330; ?>"
                        data-room="<?php echo htmlspecialchars($room16330); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16330); ?>"
                        data-image="<?php echo base64_encode($upload_img16330); ?>"
                        data-category="<?php echo htmlspecialchars($category16330); ?>"
                        data-status="<?php echo htmlspecialchars($status16330); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16330); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16330); ?>; 
                        position:absolute; top:145px; left:500px;'>
                    </div>

                    <!-- ASSET 16331 -->
                    <img src='../image.php?id=16331'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:615px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16331' onclick='fetchAssetData(16331);'
                        class="asset-image" data-id="<?php echo $assetId16331; ?>"
                        data-room="<?php echo htmlspecialchars($room16331); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16331); ?>"
                        data-image="<?php echo base64_encode($upload_img16331); ?>"
                        data-category="<?php echo htmlspecialchars($category16331); ?>"
                        data-status="<?php echo htmlspecialchars($status16331); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16331); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16331); ?>; 
                        position:absolute; top:70px; left:625px;'>
                    </div>

                    <!-- ASSET 16332 -->
                    <img src='../image.php?id=16332'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:615px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16332' onclick='fetchAssetData(16332);'
                        class="asset-image" data-id="<?php echo $assetId16332; ?>"
                        data-room="<?php echo htmlspecialchars($room16332); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16332); ?>"
                        data-image="<?php echo base64_encode($upload_img16332); ?>"
                        data-category="<?php echo htmlspecialchars($category16332); ?>"
                        data-status="<?php echo htmlspecialchars($status16332); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16332); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16332); ?>; 
                        position:absolute; top:145px; left:625px;'>
                    </div>

                    <!-- ASSET 16333 -->
                    <img src='../image.php?id=16333'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:729px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16333' onclick='fetchAssetData(16333);'
                        class="asset-image" data-id="<?php echo $assetId16333; ?>"
                        data-room="<?php echo htmlspecialchars($room16333); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16333); ?>"
                        data-image="<?php echo base64_encode($upload_img16333); ?>"
                        data-category="<?php echo htmlspecialchars($category16333); ?>"
                        data-status="<?php echo htmlspecialchars($status16333); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16333); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16333); ?>; 
                        position:absolute; top:145px; left:739px;'>
                    </div>

                    <!-- ASSET 16334 -->
                    <img src='../image.php?id=16334'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:729px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16334' onclick='fetchAssetData(16334);'
                        class="asset-image" data-id="<?php echo $assetId16334; ?>"
                        data-room="<?php echo htmlspecialchars($room16334); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16334); ?>"
                        data-image="<?php echo base64_encode($upload_img16334); ?>"
                        data-category="<?php echo htmlspecialchars($category16334); ?>"
                        data-status="<?php echo htmlspecialchars($status16334); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16334); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16334); ?>; 
                        position:absolute; top:70px; left:739px;'>
                    </div>

                    <!-- ASSET 16335 -->
                    <img src='../image.php?id=16335'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:490px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16335' onclick='fetchAssetData(16335);'
                        class="asset-image" data-id="<?php echo $assetId16335; ?>"
                        data-room="<?php echo htmlspecialchars($room16335); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16335); ?>"
                        data-image="<?php echo base64_encode($upload_img16335); ?>"
                        data-status="<?php echo htmlspecialchars($status16335); ?>"
                        data-category="<?php echo htmlspecialchars($category16335); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16335); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16335); ?>; 
                        position:absolute; top:225px; left:500px;'>
                    </div>


                    <!-- ASSET 16336 -->
                    <img src='../image.php?id=16336'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:615px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16336' onclick='fetchAssetData(16336);'
                        class="asset-image" data-id="<?php echo $assetId16336; ?>"
                        data-room="<?php echo htmlspecialchars($room16336); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16336); ?>"
                        data-image="<?php echo base64_encode($upload_img16336); ?>"
                        data-status="<?php echo htmlspecialchars($status16336); ?>"
                        data-category="<?php echo htmlspecialchars($category16336); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16336); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16336); ?>; 
                        position:absolute; top:225px; left:625px;'>
                    </div>

                    <!-- ASSET 16337 -->
                    <img src='../image.php?id=16337'
                        style='width:15px; cursor:pointer; position:absolute; top:225px; left:729px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16337' onclick='fetchAssetData(16337);'
                        class="asset-image" data-id="<?php echo $assetId16337; ?>"
                        data-room="<?php echo htmlspecialchars($room16337); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16337); ?>"
                        data-image="<?php echo base64_encode($upload_img16337); ?>"
                        data-status="<?php echo htmlspecialchars($status16337); ?>"
                        data-category="<?php echo htmlspecialchars($category16337); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16337); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16337); ?>; 
                        position:absolute; top:225px; left:739px;'>
                    </div>

                    <!-- ASSET 16356 -->
                    <img src='../image.php?id=16356'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16356' onclick='fetchAssetData(16356);'
                        class="asset-image" data-id="<?php echo $assetId16356; ?>"
                        data-room="<?php echo htmlspecialchars($room16356); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16356); ?>"
                        data-image="<?php echo base64_encode($upload_img16356); ?>"
                        data-status="<?php echo htmlspecialchars($status16356); ?>"
                        data-category="<?php echo htmlspecialchars($category16356); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16356); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16356); ?>; 
                        position:absolute; top:79px; left:518px;'>
                    </div>

                    <!-- ASSET 16357 -->
                    <img src='../image.php?id=16357'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16357' onclick='fetchAssetData(16357);'
                        class="asset-image" data-id="<?php echo $assetId16357; ?>"
                        data-room="<?php echo htmlspecialchars($room16357); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16357); ?>"
                        data-image="<?php echo base64_encode($upload_img16357); ?>"
                        data-status="<?php echo htmlspecialchars($status16357); ?>"
                        data-category="<?php echo htmlspecialchars($category16357); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16357); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16357); ?>; 
                        position:absolute; top:92px; left:518px;'>
                    </div>


                    <!-- ASSET 16358 -->
                    <img src='../image.php?id=16358'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16358' onclick='fetchAssetData(16358);'
                        class="asset-image" data-id="<?php echo $assetId16358; ?>"
                        data-room="<?php echo htmlspecialchars($room16358); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16358); ?>"
                        data-image="<?php echo base64_encode($upload_img16358); ?>"
                        data-status="<?php echo htmlspecialchars($status16358); ?>"
                        data-category="<?php echo htmlspecialchars($category16358); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16358); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16358); ?>; 
                        position:absolute; top:104px; left:518px;'>
                    </div>

                    <!-- ASSET 16359 -->
                    <img src='../image.php?id=16359'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16359' onclick='fetchAssetData(16359);'
                        class="asset-image" data-id="<?php echo $assetId16359; ?>"
                        data-room="<?php echo htmlspecialchars($room16359); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16359); ?>"
                        data-image="<?php echo base64_encode($upload_img16359); ?>"
                        data-status="<?php echo htmlspecialchars($status16359); ?>"
                        data-category="<?php echo htmlspecialchars($category16359); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16359); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16359); ?>; 
                        position:absolute; top:118px; left:518px;'>
                    </div>

                    <!-- ASSET 16360 -->
                    <img src='../image.php?id=16360'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16360' onclick='fetchAssetData(16360);'
                        class="asset-image" data-id="<?php echo $assetId16360; ?>"
                        data-room="<?php echo htmlspecialchars($room16360); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16360); ?>"
                        data-image="<?php echo base64_encode($upload_img16360); ?>"
                        data-status="<?php echo htmlspecialchars($status16360); ?>"
                        data-category="<?php echo htmlspecialchars($category16360); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16360); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16360); ?>; 
                        position:absolute; top:131px; left:518px;'>
                    </div>

                    <!-- ASSET 16361 -->
                    <img src='../image.php?id=16361'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16361' onclick='fetchAssetData(16361);'
                        class="asset-image" data-id="<?php echo $assetId16361; ?>"
                        data-room="<?php echo htmlspecialchars($room16361); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16361); ?>"
                        data-image="<?php echo base64_encode($upload_img16361); ?>"
                        data-status="<?php echo htmlspecialchars($status16361); ?>"
                        data-category="<?php echo htmlspecialchars($category16361); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16361); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16361); ?>; 
                        position:absolute; top:79px; left:541px;'>
                    </div>

                    <!-- ASSET 16362 -->
                    <img src='../image.php?id=16362'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16362' onclick='fetchAssetData(16362);'
                        class="asset-image" data-id="<?php echo $assetId16362; ?>"
                        data-room="<?php echo htmlspecialchars($room16362); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16362); ?>"
                        data-image="<?php echo base64_encode($upload_img16362); ?>"
                        data-category="<?php echo htmlspecialchars($category16362); ?>"
                        data-status="<?php echo htmlspecialchars($status16362); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16362); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16362); ?>; 
                        position:absolute; top:91px; left:541px;'>
                    </div>

                    <!-- ASSET 16363 -->
                    <img src='../image.php?id=16363'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16363' onclick='fetchAssetData(16363);'
                        class="asset-image" data-id="<?php echo $assetId16363; ?>"
                        data-room="<?php echo htmlspecialchars($room16363); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16363); ?>"
                        data-image="<?php echo base64_encode($upload_img16363); ?>"
                        data-status="<?php echo htmlspecialchars($status16363); ?>"
                        data-category="<?php echo htmlspecialchars($category16363); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16363); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16363); ?>; 
                        position:absolute; top:104px; left:541px;'>
                    </div>

                    <!-- ASSET 16364 -->
                    <img src='../image.php?id=16364'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16364' onclick='fetchAssetData(16364);'
                        class="asset-image" data-id="<?php echo $assetId16364; ?>"
                        data-room="<?php echo htmlspecialchars($room16364); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16364); ?>"
                        data-image="<?php echo base64_encode($upload_img16364); ?>"
                        data-status="<?php echo htmlspecialchars($status16364); ?>"
                        data-category="<?php echo htmlspecialchars($category16364); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16364); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16364); ?>; 
                        position:absolute; top:117px; left:541px;'>
                    </div>

                    <!-- ASSET 16365 -->
                    <img src='../image.php?id=16365'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16365' onclick='fetchAssetData(16365);'
                        class="asset-image" data-id="<?php echo $assetId16365; ?>"
                        data-room="<?php echo htmlspecialchars($room16365); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16365); ?>"
                        data-image="<?php echo base64_encode($upload_img16365); ?>"
                        data-status="<?php echo htmlspecialchars($status16365); ?>"
                        data-category="<?php echo htmlspecialchars($category16365); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16365); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16365); ?>; 
                        position:absolute; top:130px; left:541px;'>
                    </div>

                    <!-- ASSET 16366 -->
                    <img src='../image.php?id=16366'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16366' onclick='fetchAssetData(16366);'
                        class="asset-image" data-id="<?php echo $assetId16366; ?>"
                        data-room="<?php echo htmlspecialchars($room16366); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16366); ?>"
                        data-image="<?php echo base64_encode($upload_img16366); ?>"
                        data-status="<?php echo htmlspecialchars($status16366); ?>"
                        data-category="<?php echo htmlspecialchars($category16366); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16366); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16366); ?>; 
                        position:absolute; top:79px; left:564px;'>
                    </div>

                    <!-- ASSET 16367 -->
                    <img src='../image.php?id=16367'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16367' onclick='fetchAssetData(16367);'
                        class="asset-image" data-id="<?php echo $assetId16367; ?>"
                        data-room="<?php echo htmlspecialchars($room16367); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16367); ?>"
                        data-image="<?php echo base64_encode($upload_img16367); ?>"
                        data-status="<?php echo htmlspecialchars($status16367); ?>"
                        data-category="<?php echo htmlspecialchars($category16367); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16367); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16367); ?>; 
                        position:absolute; top:91px; left:564px;'>
                    </div>

                    <!-- ASSET 16368 -->
                    <img src='../image.php?id=16368'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16368' onclick='fetchAssetData(16368);'
                        class="asset-image" data-id="<?php echo $assetId16368; ?>"
                        data-room="<?php echo htmlspecialchars($room16368); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16368); ?>"
                        data-image="<?php echo base64_encode($upload_img16368); ?>"
                        data-status="<?php echo htmlspecialchars($status16368); ?>"
                        data-category="<?php echo htmlspecialchars($category16368); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16368); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16368); ?>; 
                        position:absolute; top:104px; left:564px;'>
                    </div>

                    <!-- ASSET 16369 -->
                    <img src='../image.php?id=16369'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16369' onclick='fetchAssetData(16369);'
                        class="asset-image" data-id="<?php echo $assetId16369; ?>"
                        data-room="<?php echo htmlspecialchars($room16369); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16369); ?>"
                        data-image="<?php echo base64_encode($upload_img16369); ?>"
                        data-status="<?php echo htmlspecialchars($status16369); ?>"
                        data-category="<?php echo htmlspecialchars($category16369); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16369); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16369); ?>; 
                        position:absolute; top:117px; left:564px;'>
                    </div>


                    <!-- ASSET 16370 -->
                    <img src='../image.php?id=16370'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16370' onclick='fetchAssetData(16370);'
                        class="asset-image" data-id="<?php echo $assetId16370; ?>"
                        data-room="<?php echo htmlspecialchars($room16370); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16370); ?>"
                        data-image="<?php echo base64_encode($upload_img16370); ?>"
                        data-status="<?php echo htmlspecialchars($status16370); ?>"
                        data-category="<?php echo htmlspecialchars($category16370); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16370); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16370); ?>; 
                        position:absolute; top:130px; left:564px;'>
                    </div>

                    <!-- ASSET 16371 -->
                    <img src='../image.php?id=16371'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16371' onclick='fetchAssetData(16371);'
                        class="asset-image" data-id="<?php echo $assetId16371; ?>"
                        data-room="<?php echo htmlspecialchars($room16371); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16371); ?>"
                        data-image="<?php echo base64_encode($upload_img16371); ?>"
                        data-status="<?php echo htmlspecialchars($status16371); ?>"
                        data-category="<?php echo htmlspecialchars($category16371); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16371); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16371); ?>; 
                        position:absolute; top:79px; left:587px;'>
                    </div>

                    <!-- ASSET 16372 -->
                    <img src='../image.php?id=16372'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16372' onclick='fetchAssetData(16372);'
                        class="asset-image" data-id="<?php echo $assetId16372; ?>"
                        data-room="<?php echo htmlspecialchars($room16372); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16372); ?>"
                        data-status="<?php echo htmlspecialchars($status16372); ?>"
                        data-image="<?php echo base64_encode($upload_img16372); ?>"
                        data-category="<?php echo htmlspecialchars($category16372); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16372); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16372); ?>; 
                        position:absolute; top:91px; left:587px;'>
                    </div>

                    <!-- ASSET 16373 -->
                    <img src='../image.php?id=16373'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16373' onclick='fetchAssetData(16373);'
                        class="asset-image" data-id="<?php echo $assetId16373; ?>"
                        data-room="<?php echo htmlspecialchars($room16373); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16373); ?>"
                        data-image="<?php echo base64_encode($upload_img16373); ?>"
                        data-status="<?php echo htmlspecialchars($status16373); ?>"
                        data-category="<?php echo htmlspecialchars($category16373); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16373); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16373); ?>; 
                        position:absolute; top:104px; left:587px;'>
                    </div>

                    <!-- ASSET 16374 -->
                    <img src='../image.php?id=16374'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16374' onclick='fetchAssetData(16374);'
                        class="asset-image" data-id="<?php echo $assetId16374; ?>"
                        data-room="<?php echo htmlspecialchars($room16374); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16374); ?>"
                        data-image="<?php echo base64_encode($upload_img16374); ?>"
                        data-status="<?php echo htmlspecialchars($status16374); ?>"
                        data-category="<?php echo htmlspecialchars($category16374); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16374); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16374); ?>; 
                        position:absolute; top:117px; left:587px;'>
                    </div>

                    <!-- ASSET 16375 -->
                    <img src='../image.php?id=16375'
                        style='width:18px; cursor:pointer; position:absolute; top:127px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16375' onclick='fetchAssetData(16375);'
                        class="asset-image" data-id="<?php echo $assetId16375; ?>"
                        data-room="<?php echo htmlspecialchars($room16375); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16375); ?>"
                        data-image="<?php echo base64_encode($upload_img16375); ?>"
                        data-status="<?php echo htmlspecialchars($status16375); ?>"
                        data-category="<?php echo htmlspecialchars($category16375); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16375); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16375); ?>; 
                        position:absolute; top:130px; left:587px;'>
                    </div>

                    <!-- ASSET 16376 -->
                    <img src='../image.php?id=16376'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16376' onclick='fetchAssetData(16376);'
                        class="asset-image" data-id="<?php echo $assetId16376; ?>"
                        data-room="<?php echo htmlspecialchars($room16376); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16376); ?>"
                        data-image="<?php echo base64_encode($upload_img16376); ?>"
                        data-status="<?php echo htmlspecialchars($status16376); ?>"
                        data-category="<?php echo htmlspecialchars($category16376); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16376); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16376); ?>; 
                        position:absolute; top:79px; left:610px;'>
                    </div>

                    <!-- ASSET 16377 -->
                    <img src='../image.php?id=16377'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16377' onclick='fetchAssetData(16377);'
                        class="asset-image" data-id="<?php echo $assetId16377; ?>"
                        data-room="<?php echo htmlspecialchars($room16377); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16377); ?>"
                        data-image="<?php echo base64_encode($upload_img16377); ?>"
                        data-status="<?php echo htmlspecialchars($status16377); ?>"
                        data-category="<?php echo htmlspecialchars($category16377); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16377); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16377); ?>; 
                        position:absolute; top:91px; left:610px;'>
                    </div>

                    <!-- ASSET 16378 -->
                    <img src='../image.php?id=16378'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16378' onclick='fetchAssetData(16378);'
                        class="asset-image" data-id="<?php echo $assetId16378; ?>"
                        data-room="<?php echo htmlspecialchars($room16378); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16378); ?>"
                        data-image="<?php echo base64_encode($upload_img16378); ?>"
                        data-status="<?php echo htmlspecialchars($status16378); ?>"
                        data-category="<?php echo htmlspecialchars($category16378); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16378); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16378); ?>; 
                        position:absolute; top:104px; left:610px;'>
                    </div>

                    <!-- ASSET 16379 -->
                    <img src='../image.php?id=16379'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16379' onclick='fetchAssetData(16379);'
                        class="asset-image" data-id="<?php echo $assetId16379; ?>"
                        data-room="<?php echo htmlspecialchars($room16379); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16379); ?>"
                        data-image="<?php echo base64_encode($upload_img16379); ?>"
                        data-status="<?php echo htmlspecialchars($status16379); ?>"
                        data-category="<?php echo htmlspecialchars($category16379); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16379); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16379); ?>; 
                        position:absolute; top:117px; left:610px;'>
                    </div>

                    <!-- ASSET 16380 -->
                    <img src='../image.php?id=16380'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16380' onclick='fetchAssetData(16380);'
                        class="asset-image" data-id="<?php echo $assetId16380; ?>"
                        data-room="<?php echo htmlspecialchars($room16380); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16380); ?>"
                        data-image="<?php echo base64_encode($upload_img16380); ?>"
                        data-status="<?php echo htmlspecialchars($status16380); ?>"
                        data-category="<?php echo htmlspecialchars($category16380); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16380); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16380); ?>; 
                        position:absolute; top:130px; left:610px;'>
                    </div>

                    <!-- ASSET 16381 -->
                    <img src='../image.php?id=16381'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16381' onclick='fetchAssetData(16381);'
                        class="asset-image" data-id="<?php echo $assetId16381; ?>"
                        data-room="<?php echo htmlspecialchars($room16381); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16381); ?>"
                        data-image="<?php echo base64_encode($upload_img16381); ?>"
                        data-status="<?php echo htmlspecialchars($status16381); ?>"
                        data-category="<?php echo htmlspecialchars($category16381); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16381); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16381); ?>; 
                        position:absolute; top:159px; left:518px;'>
                    </div>

                    <!-- ASSET 16382 -->
                    <img src='../image.php?id=16382'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16382' onclick='fetchAssetData(16382);'
                        class="asset-image" data-id="<?php echo $assetId16382; ?>"
                        data-room="<?php echo htmlspecialchars($room16382); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16382); ?>"
                        data-image="<?php echo base64_encode($upload_img16382); ?>"
                        data-status="<?php echo htmlspecialchars($status16382); ?>"
                        data-category="<?php echo htmlspecialchars($category16382); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16382); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16382); ?>; 
                        position:absolute; top:171px; left:518px;'>
                    </div>

                    <!-- ASSET 16383 -->
                    <img src='../image.php?id=16383'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16383' onclick='fetchAssetData(16383);'
                        class="asset-image" data-id="<?php echo $assetId16383; ?>"
                        data-room="<?php echo htmlspecialchars($room16383); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16383); ?>"
                        data-image="<?php echo base64_encode($upload_img16383); ?>"
                        data-status="<?php echo htmlspecialchars($status16383); ?>"
                        data-category="<?php echo htmlspecialchars($category16383); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16383); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16383); ?>; 
                        position:absolute; top:183px; left:518px;'>
                    </div>

                    <!-- ASSET 16384 -->
                    <img src='../image.php?id=16384'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16384' onclick='fetchAssetData(16384);'
                        class="asset-image" data-id="<?php echo $assetId16384; ?>"
                        data-room="<?php echo htmlspecialchars($room16384); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16384); ?>"
                        data-image="<?php echo base64_encode($upload_img16384); ?>"
                        data-category="<?php echo htmlspecialchars($category16384); ?>"
                        data-status="<?php echo htmlspecialchars($status16384); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16384); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16384); ?>; 
                        position:absolute; top:194px; left:518px;'>
                    </div>

                    <!-- ASSET 16385 -->
                    <img src='../image.php?id=16385'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16385' onclick='fetchAssetData(16385);'
                        class="asset-image" data-id="<?php echo $assetId16385; ?>"
                        data-room="<?php echo htmlspecialchars($room16385); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16385); ?>"
                        data-image="<?php echo base64_encode($upload_img16385); ?>"
                        data-status="<?php echo htmlspecialchars($status16385); ?>"
                        data-category="<?php echo htmlspecialchars($category16385); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16385); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16385); ?>; 
                        position:absolute; top:207px; left:518px;'>
                    </div>

                    <!-- ASSET 16386 -->
                    <img src='../image.php?id=16386'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16386' onclick='fetchAssetData(16386);'
                        class="asset-image" data-id="<?php echo $assetId16386; ?>"
                        data-room="<?php echo htmlspecialchars($room16386); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16386); ?>"
                        data-image="<?php echo base64_encode($upload_img16386); ?>"
                        data-status="<?php echo htmlspecialchars($status16386); ?>"
                        data-category="<?php echo htmlspecialchars($category16386); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16386); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16386); ?>; 
                        position:absolute; top:159px; left:541px;'>
                    </div>

                    <!-- ASSET 16387 -->
                    <img src='../image.php?id=16387'
                    style='width:18px; cursor:pointer; position:absolute; top:167px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16387' onclick='fetchAssetData(16387);'
                        class="asset-image" data-id="<?php echo $assetId16387; ?>"
                        data-room="<?php echo htmlspecialchars($room16387); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16387); ?>"
                        data-image="<?php echo base64_encode($upload_img16387); ?>"
                        data-category="<?php echo htmlspecialchars($category16387); ?>"
                        data-status="<?php echo htmlspecialchars($status16387); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16387); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16387); ?>; 
                        position:absolute; top:171px; left:541px;'>
                    </div>

                    <!-- ASSET 16388 -->
                    <img src='../image.php?id=16388'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16388' onclick='fetchAssetData(16388);'
                        class="asset-image" data-id="<?php echo $assetId16388; ?>"
                        data-room="<?php echo htmlspecialchars($room16388); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16388); ?>"
                        data-image="<?php echo base64_encode($upload_img16388); ?>"
                        data-status="<?php echo htmlspecialchars($status16388); ?>"
                        data-category="<?php echo htmlspecialchars($category16388); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16388); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16388); ?>; 
                        position:absolute; top:183px; left:541px;'>
                    </div>

                    <!-- ASSET 16389 -->
                    <img src='../image.php?id=16389'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16389' onclick='fetchAssetData(16389);'
                        class="asset-image" data-id="<?php echo $assetId16389; ?>"
                        data-room="<?php echo htmlspecialchars($room16389); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16389); ?>"
                        data-image="<?php echo base64_encode($upload_img16389); ?>"
                        data-category="<?php echo htmlspecialchars($category16389); ?>"
                        data-status="<?php echo htmlspecialchars($status16389); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16389); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16389); ?>; 
                        position:absolute; top:195px; left:541px;'>
                    </div>

                    <!-- ASSET 16390 -->
                    <img src='../image.php?id=16390'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:528px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16390' onclick='fetchAssetData(16390);'
                        class="asset-image" data-id="<?php echo $assetId16390; ?>"
                        data-room="<?php echo htmlspecialchars($room16390); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16390); ?>"
                        data-image="<?php echo base64_encode($upload_img16390); ?>"
                        data-category="<?php echo htmlspecialchars($category16390); ?>"
                        data-status="<?php echo htmlspecialchars($status16390); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16390); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16390); ?>; 
                        position:absolute; top:207px; left:541px;'>
                    </div>

                    <!-- ASSET 16391 -->
                    <img src='../image.php?id=16391'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16391' onclick='fetchAssetData(16391);'
                        class="asset-image" data-id="<?php echo $assetId16391; ?>"
                        data-room="<?php echo htmlspecialchars($room16391); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16391); ?>"
                        data-image="<?php echo base64_encode($upload_img16391); ?>"
                        data-category="<?php echo htmlspecialchars($category16391); ?>"
                        data-status="<?php echo htmlspecialchars($status16391); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16391); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16391); ?>; 
                        position:absolute; top:159px; left:564px;'>
                    </div>

                    <!-- ASSET 16392 -->
                    <img src='../image.php?id=16392'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16392' onclick='fetchAssetData(16392);'
                        class="asset-image" data-id="<?php echo $assetId16392; ?>"
                        data-room="<?php echo htmlspecialchars($room16392); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16392); ?>"
                        data-image="<?php echo base64_encode($upload_img16392); ?>"
                        data-category="<?php echo htmlspecialchars($category16392); ?>"
                        data-status="<?php echo htmlspecialchars($status16392); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16392); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16392); ?>; 
                        position:absolute; top:171px; left:564px;'>
                    </div>


                    <!-- ASSET 16393 -->
                    <img src='../image.php?id=16393'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16393' onclick='fetchAssetData(16393);'
                        class="asset-image" data-id="<?php echo $assetId16393; ?>"
                        data-room="<?php echo htmlspecialchars($room16393); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16393); ?>"
                        data-image="<?php echo base64_encode($upload_img16393); ?>"
                        data-category="<?php echo htmlspecialchars($category16393); ?>"
                        data-status="<?php echo htmlspecialchars($status16393); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16393); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16393); ?>; 
                        position:absolute; top:183px; left:564px;'>
                    </div>

                    <!-- ASSET 16394 -->
                    <img src='../image.php?id=16394'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16394' onclick='fetchAssetData(16394);'
                        class="asset-image" data-id="<?php echo $assetId16394; ?>"
                        data-room="<?php echo htmlspecialchars($room16394); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16394); ?>"
                        data-image="<?php echo base64_encode($upload_img16394); ?>"
                        data-category="<?php echo htmlspecialchars($category16394); ?>"
                        data-status="<?php echo htmlspecialchars($status16394); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16394); ?>">

                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16394); ?>; 
                        position:absolute; top:194px; left:564px;'>
                    </div>

                    <!-- ASSET 16395 -->
                    <img src='../image.php?id=16395'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:551px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16395' onclick='fetchAssetData(16395);'
                        class="asset-image" data-id="<?php echo $assetId16395; ?>"
                        data-room="<?php echo htmlspecialchars($room16395); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16395); ?>"
                        data-image="<?php echo base64_encode($upload_img16395); ?>"
                        data-status="<?php echo htmlspecialchars($status16395); ?>"
                        data-category="<?php echo htmlspecialchars($category16395); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16395); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16395); ?>; 
                        position:absolute; top:207px; left: 564px;'>
                    </div>

                    <!-- ASSET 16396 -->
                    <img src='../image.php?id=16396'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16396' onclick='fetchAssetData(16396);'
                        class="asset-image" data-id="<?php echo $assetId16396; ?>"
                        data-room="<?php echo htmlspecialchars($room16396); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16396); ?>"
                        data-image="<?php echo base64_encode($upload_img16396); ?>"
                        data-status="<?php echo htmlspecialchars($status16396); ?>"
                        data-category="<?php echo htmlspecialchars($category16396); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16396); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16396); ?>; 
                        position:absolute; top:159px; left: 587px;'>
                    </div>

                    <!-- ASSET 16397 -->
                    <img src='../image.php?id=16397'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16397' onclick='fetchAssetData(16397);'
                        class="asset-image" data-id="<?php echo $assetId16397; ?>"
                        data-room="<?php echo htmlspecialchars($room16397); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16397); ?>"
                        data-image="<?php echo base64_encode($upload_img16397); ?>"
                        data-category="<?php echo htmlspecialchars($category16397); ?>"
                        data-status="<?php echo htmlspecialchars($status16397); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16397); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16397); ?>; 
                        position:absolute; top:171px; left:587px;'>
                    </div>

                    <!-- ASSET 16398 -->
                    <img src='../image.php?id=16398'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16398' onclick='fetchAssetData(16398);'
                        class="asset-image" data-id="<?php echo $assetId16398; ?>"
                        data-room="<?php echo htmlspecialchars($room16398); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16398); ?>"
                        data-image="<?php echo base64_encode($upload_img16398); ?>"
                        data-status="<?php echo htmlspecialchars($status16398); ?>"
                        data-category="<?php echo htmlspecialchars($category16398); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16398); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16398); ?>; 
                        position:absolute; top:183px; left:587px;'>
                    </div>

                    <!-- ASSET 16399 -->
                    <img src='../image.php?id=16399'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16399' onclick='fetchAssetData(16399);'
                        class="asset-image" data-id="<?php echo $assetId16399; ?>"
                        data-room="<?php echo htmlspecialchars($room16399); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16399); ?>"
                        data-image="<?php echo base64_encode($upload_img16399); ?>"
                        data-status="<?php echo htmlspecialchars($status16399); ?>"
                        data-category="<?php echo htmlspecialchars($category16399); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16399); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16399); ?>; 
                        position:absolute; top:194px; left: 587px;'>
                    </div>

                    <!-- ASSET 16400 -->
                    <img src='../image.php?id=16400'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:574px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16400' onclick='fetchAssetData(16400);'
                        class="asset-image" data-id="<?php echo $assetId16400; ?>"
                        data-room="<?php echo htmlspecialchars($room16400); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16400); ?>"
                        data-status="<?php echo htmlspecialchars($status16400); ?>"
                        data-image="<?php echo base64_encode($upload_img16400); ?>"
                        data-category="<?php echo htmlspecialchars($category16400); ?>">
                        data-assignedname="<?php echo htmlspecialchars($assignedName16400); ?>"
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16400); ?>; 
                        position:absolute; top:207px; left:587px;'>
                    </div>


                    <!-- ASSET 16401 -->
                    <img src='../image.php?id=16401'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16401' onclick='fetchAssetData(16401);'
                        class="asset-image" data-id="<?php echo $assetId16401; ?>"
                        data-room="<?php echo htmlspecialchars($room16401); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16401); ?>"
                        data-image="<?php echo base64_encode($upload_img16401); ?>"
                        data-status="<?php echo htmlspecialchars($status16401); ?>"
                        data-category="<?php echo htmlspecialchars($category16401); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16401); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16401); ?>; 
                    position:absolute; top:159px; left:610px;'>
                    </div>

                    <!-- ASSET 16402 -->
                    <img src='../image.php?id=16402'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16402' onclick='fetchAssetData(16402);'
                        class="asset-image" data-id="<?php echo $assetId16402; ?>"
                        data-room="<?php echo htmlspecialchars($room16402); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16402); ?>"
                        data-image="<?php echo base64_encode($upload_img16402); ?>"
                        data-status="<?php echo htmlspecialchars($status16402); ?>"
                        data-category="<?php echo htmlspecialchars($category16402); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16402); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16402); ?>; 
                        position:absolute; top:171px; left:610px;'>
                    </div>

                    <!-- ASSET 16403 -->
                    <img src='../image.php?id=16403'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16403' onclick='fetchAssetData(16403);'
                        class="asset-image" data-id="<?php echo $assetId16403; ?>"
                        data-room="<?php echo htmlspecialchars($room16403); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16403); ?>"
                        data-status="<?php echo htmlspecialchars($status16403); ?>"
                        data-image="<?php echo base64_encode($upload_img16403); ?>"
                        data-category="<?php echo htmlspecialchars($category16403); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16403); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16403); ?>; 
                        position:absolute; top:183px; left:610px;'>
                    </div>

                    <!-- ASSET 16404 -->
                    <img src='../image.php?id=16404'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16404' onclick='fetchAssetData(16404);'
                        class="asset-image" data-id="<?php echo $assetId16404; ?>"
                        data-room="<?php echo htmlspecialchars($room16404); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16404); ?>"
                        data-image="<?php echo base64_encode($upload_img16404); ?>"
                        data-category="<?php echo htmlspecialchars($category16404); ?>"
                        data-status="<?php echo htmlspecialchars($status16404); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16404); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16404); ?>; 
                        position:absolute; top:194px; left:610px;'>
                    </div>


                    <!-- ASSET 16405 -->
                    <img src='../image.php?id=16405'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left: 597px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16405' onclick='fetchAssetData(16405);'
                        class="asset-image" data-id="<?php echo $assetId16405; ?>"
                        data-room="<?php echo htmlspecialchars($room16405); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16405); ?>"
                        data-image="<?php echo base64_encode($upload_img16405); ?>"
                        data-category="<?php echo htmlspecialchars($category16405); ?>"
                        data-status="<?php echo htmlspecialchars($status16405); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16405); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16405); ?>; 
                        position:absolute; top:207px; left:610px;'>
                    </div>

                    <!-- ASSET 6261
                    <img src='../image.php?id=6261'
                        style='width:18px; cursor:pointer; position:absolute; top:144px; left:695px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6261'
                        onclick='fetchAssetData(6261);' class="asset-image" data-id="<?php echo $assetId6261; ?>"
                        data-room="<?php echo htmlspecialchars($room6261); ?>"
                        data-floor="<?php echo htmlspecialchars($floor6261); ?>"
                        data-image="<?php echo base64_encode($upload_img6261); ?>"
                        data-category="<?php echo htmlspecialchars($category6261); ?>"
                        data-status="<?php echo htmlspecialchars($status6261); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName6261); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status6261); ?>; 
position:absolute; top:142px; left:700px;'>
                    </div> -->

                    <!-- ASSET 16407 -->
                    <img src='../image.php?id=16407'
                        style='width:15px; cursor:pointer; position:absolute; top:143px; left: 698px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16407' onclick='fetchAssetData(16407);'
                        class="asset-image" data-id="<?php echo $assetId16407; ?>"
                        data-room="<?php echo htmlspecialchars($room16407); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16407); ?>"
                        data-image="<?php echo base64_encode($upload_img16407); ?>"
                        data-category="<?php echo htmlspecialchars($category16407); ?>"
                        data-status="<?php echo htmlspecialchars($status16407); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16407); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16407); ?>; 
                        position:absolute; top:135px; left:702px;'>
                    </div>

                    <!-- ASSET 16408 -->
                    <img src='../image.php?id=16408'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16408' onclick='fetchAssetData(16408);'
                        class="asset-image" data-id="<?php echo $assetId16408; ?>"
                        data-room="<?php echo htmlspecialchars($room16408); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16408); ?>"
                        data-image="<?php echo base64_encode($upload_img16408); ?>"
                        data-category="<?php echo htmlspecialchars($category16408); ?>"
                        data-status="<?php echo htmlspecialchars($status16408); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16408); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16408); ?>; 
                        position:absolute; top:70px; left:765px;'>
                    </div>


                    <!-- ASSET 16409 -->
                    <img src='../image.php?id=16409'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16409' onclick='fetchAssetData(16409);'
                        class="asset-image" data-id="<?php echo $assetId16409; ?>"
                        data-room="<?php echo htmlspecialchars($room16409); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16409); ?>"
                        data-image="<?php echo base64_encode($upload_img16409); ?>"
                        data-category="<?php echo htmlspecialchars($category16409); ?>"
                        data-status="<?php echo htmlspecialchars($status16409); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16409); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16409); ?>; 
                        position:absolute; top:145px; left:765px;'>
                    </div>

                    <!-- ASSET 16410 -->
                    <img src='../image.php?id=16410'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:885px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16410' onclick='fetchAssetData(16410);'
                        class="asset-image" data-id="<?php echo $assetId16410; ?>"
                        data-room="<?php echo htmlspecialchars($room16410); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16410); ?>"
                        data-image="<?php echo base64_encode($upload_img16410); ?>"
                        data-category="<?php echo htmlspecialchars($category16410); ?>"
                        data-status="<?php echo htmlspecialchars($status16410); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16410); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16410); ?>; 
                        position:absolute; top:70px; left:895px;'>
                    </div>

                    <!-- ASSET 16411 -->
                    <img src='../image.php?id=16411'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:885px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16411' onclick='fetchAssetData(16411);'
                        class="asset-image" data-id="<?php echo $assetId16411; ?>"
                        data-room="<?php echo htmlspecialchars($room16411); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16411); ?>"
                        data-image="<?php echo base64_encode($upload_img16411); ?>"
                        data-status="<?php echo htmlspecialchars($status16411); ?>"
                        data-category="<?php echo htmlspecialchars($category16411); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16411); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16411); ?>; 
                        position:absolute; top:145px; left:895px;'>
                    </div>

                    <!-- ASSET 16412 -->
                    <img src='../image.php?id=16412'
                        style='width:15px; cursor:pointer; position:absolute; top:145px; left:995px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16412' onclick='fetchAssetData(16412);'
                        class="asset-image" data-id="<?php echo $assetId16412; ?>"
                        data-room="<?php echo htmlspecialchars($room16412); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16412); ?>"
                        data-status="<?php echo htmlspecialchars($status16412); ?>"
                        data-image="<?php echo base64_encode($upload_img16412); ?>"
                        data-category="<?php echo htmlspecialchars($category16412); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16412); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16412); ?>; 
                        position:absolute; top:145px; left:1005px;'>
                    </div>


                    <!-- ASSET 16413 -->
                    <img src='../image.php?id=16413'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:995px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16413' onclick='fetchAssetData(16413);'
                        class="asset-image" data-id="<?php echo $assetId16413; ?>"
                        data-room="<?php echo htmlspecialchars($room16413); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16413); ?>"
                        data-image="<?php echo base64_encode($upload_img16413); ?>"
                        data-category="<?php echo htmlspecialchars($category16413); ?>"
                        data-status="<?php echo htmlspecialchars($status16413); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16413); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16413); ?>; 
                        position:absolute; top:70px; left:1005px;'>
                    </div>

                    <!-- ASSET 16414 -->
                    <img src='../image.php?id=16414'
                        style='width:15px; cursor:pointer; position:absolute; top:214px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16414' onclick='fetchAssetData(16414);'
                        class="asset-image" data-id="<?php echo $assetId16414; ?>"
                        data-room="<?php echo htmlspecialchars($room16414); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16414); ?>"
                        data-image="<?php echo base64_encode($upload_img16414); ?>"
                        data-category="<?php echo htmlspecialchars($category16414); ?>"
                        data-status="<?php echo htmlspecialchars($status16414); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16414); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16414); ?>; 
                        position:absolute; top:214px; left:765px;'>
                    </div>

                    <!-- ASSET 16415 -->
                    <img src='../image.php?id=16415'
                        style='width:15px; cursor:pointer; position:absolute; top:214px; left:885px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16415' onclick='fetchAssetData(16415);'
                        class="asset-image" data-id="<?php echo $assetId16415; ?>"
                        data-room="<?php echo htmlspecialchars($room16415); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16415); ?>"
                        data-image="<?php echo base64_encode($upload_img16415); ?>"
                        data-status="<?php echo htmlspecialchars($status16415); ?>"
                        data-category="<?php echo htmlspecialchars($category16415); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16415); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16415); ?>; 
                        position:absolute; top:214px; left:895px;'>
                    </div>

                    <!-- ASSET 16416 -->
                    <img src='../image.php?id=16416'
                        style='width:15px; cursor:pointer; position:absolute; top:214px; left:995px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16416' onclick='fetchAssetData(16416);'
                        class="asset-image" data-id="<?php echo $assetId16416; ?>"
                        data-room="<?php echo htmlspecialchars($room16416); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16416); ?>"
                        data-image="<?php echo base64_encode($upload_img16416); ?>"
                        data-status="<?php echo htmlspecialchars($status16416); ?>"
                        data-category="<?php echo htmlspecialchars($category16416); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16416); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16416); ?>; 
                        position:absolute; top:214px; left:1005px;'>
                    </div>


                    <!-- /// -->

                    <!-- ASSET 16417 -->
                    <img src='../image.php?id=16417'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16417' onclick='fetchAssetData(16417);'
                        class="asset-image" data-id="<?php echo $assetId16417; ?>"
                        data-room="<?php echo htmlspecialchars($room16417); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16417); ?>"
                        data-image="<?php echo base64_encode($upload_img16417); ?>"
                        data-category="<?php echo htmlspecialchars($category16417); ?>"
                        data-status="<?php echo htmlspecialchars($status16417); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16417); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16417); ?>; 
                        position:absolute; top:79px; left:783px;'>
                    </div>

                    <!-- ASSET 16418 -->
                    <img src='../image.php?id=16418'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16418' onclick='fetchAssetData(16418);'
                        class="asset-image" data-id="<?php echo $assetId16418; ?>"
                        data-room="<?php echo htmlspecialchars($room16418); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16418); ?>"
                        data-image="<?php echo base64_encode($upload_img16418); ?>"
                        data-category="<?php echo htmlspecialchars($category16418); ?>"
                        data-status="<?php echo htmlspecialchars($status16418); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16418); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16418); ?>; 
                        position:absolute; top:92px; left:783px;'>
                    </div>

                    <!-- ASSET 16419 -->
                    <img src='../image.php?id=16419'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16419' onclick='fetchAssetData(16419);'
                        class="asset-image" data-id="<?php echo $assetId16419; ?>"
                        data-room="<?php echo htmlspecialchars($room16419); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16419); ?>"
                        data-image="<?php echo base64_encode($upload_img16419); ?>"
                        data-status="<?php echo htmlspecialchars($status16419); ?>"
                        data-category="<?php echo htmlspecialchars($category16419); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16419); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16419); ?>; 
                        position:absolute; top:104px; left:783px;'>
                    </div>

                    <!-- ASSET 16420 -->
                    <img src='../image.php?id=16420'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16420' onclick='fetchAssetData(16420);'
                        class="asset-image" data-id="<?php echo $assetId16420; ?>"
                        data-room="<?php echo htmlspecialchars($room16420); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16420); ?>"
                        data-image="<?php echo base64_encode($upload_img16420); ?>"
                        data-category="<?php echo htmlspecialchars($category16420); ?>"
                        data-status="<?php echo htmlspecialchars($status16420); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16420); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16420); ?>; 
                        position:absolute; top:118px; left:783px;'>
                    </div>

                    <!-- ASSET 16421 -->
                    <img src='../image.php?id=16421'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16421' onclick='fetchAssetData(16421);'
                        class="asset-image" data-id="<?php echo $assetId16421; ?>"
                        data-room="<?php echo htmlspecialchars($room16421); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16421); ?>"
                        data-image="<?php echo base64_encode($upload_img16421); ?>"
                        data-category="<?php echo htmlspecialchars($category16421); ?>"
                        data-status="<?php echo htmlspecialchars($status16421); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16421); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16421); ?>; 
                        position:absolute; top:131px; left:783px;'>
                    </div>

                    <!-- ASSET 16422 -->
                    <img src='../image.php?id=16422'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16422' onclick='fetchAssetData(16422);'
                        class="asset-image" data-id="<?php echo $assetId16422; ?>"
                        data-room="<?php echo htmlspecialchars($room16422); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16422); ?>"
                        data-image="<?php echo base64_encode($upload_img16422); ?>"
                        data-category="<?php echo htmlspecialchars($category16422); ?>"
                        data-status="<?php echo htmlspecialchars($status16422); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16422); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16422); ?>; 
                        position:absolute; top:79px; left:806px;'>
                    </div>

                    <!-- ASSET 16423 -->
                    <img src='../image.php?id=16423'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16423' onclick='fetchAssetData(16423);'
                        class="asset-image" data-id="<?php echo $assetId16423; ?>"
                        data-room="<?php echo htmlspecialchars($room16423); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16423); ?>"
                        data-image="<?php echo base64_encode($upload_img16423); ?>"
                        data-status="<?php echo htmlspecialchars($status16423); ?>"
                        data-category="<?php echo htmlspecialchars($category16423); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16423); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16423); ?>; 
                        position:absolute; top:91px; left:806px;'>
                    </div>

                    <!-- ASSET 16424 -->
                    <img src='../image.php?id=16424'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16424' onclick='fetchAssetData(16424);'
                        class="asset-image" data-id="<?php echo $assetId16424; ?>"
                        data-room="<?php echo htmlspecialchars($room16424); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16424); ?>"
                        data-image="<?php echo base64_encode($upload_img16424); ?>"
                        data-status="<?php echo htmlspecialchars($status16424); ?>"
                        data-category="<?php echo htmlspecialchars($category16424); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16424); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16424); ?>; 
                        position:absolute; top:104px; left:806px;'>
                    </div>


                    <!-- ASSET 16425 -->
                    <img src='../image.php?id=16425'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16425' onclick='fetchAssetData(16425);'
                        class="asset-image" data-id="<?php echo $assetId16425; ?>"
                        data-room="<?php echo htmlspecialchars($room16425); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16425); ?>"
                        data-image="<?php echo base64_encode($upload_img16425); ?>"
                        data-status="<?php echo htmlspecialchars($status16425); ?>"
                        data-category="<?php echo htmlspecialchars($category16425); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16425); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16425); ?>; 
                        position:absolute; top:117px; left:806px;'>
                    </div>

                    <!-- ASSET 16426 -->
                    <img src='../image.php?id=16426'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16426' onclick='fetchAssetData(16426);'
                        class="asset-image" data-id="<?php echo $assetId16426; ?>"
                        data-room="<?php echo htmlspecialchars($room16426); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16426); ?>"
                        data-image="<?php echo base64_encode($upload_img16426); ?>"
                        data-status="<?php echo htmlspecialchars($status16426); ?>"
                        data-category="<?php echo htmlspecialchars($category16426); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16426); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16426); ?>; 
                        position:absolute; top:130px; left:806px;'>
                    </div>

                    <!-- ASSET 16427 -->
                    <img src='../image.php?id=16427'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16427' onclick='fetchAssetData(16427);'
                        class="asset-image" data-id="<?php echo $assetId16427; ?>"
                        data-room="<?php echo htmlspecialchars($room16427); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16427); ?>"
                        data-status="<?php echo htmlspecialchars($status16427); ?>"
                        data-image="<?php echo base64_encode($upload_img16427); ?>"
                        data-category="<?php echo htmlspecialchars($category16427); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16427); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16427); ?>; 
                        position:absolute; top:79px; left:829px;'>
                    </div>

                    <!-- ASSET 16428 -->
                    <img src='../image.php?id=16428'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16428' onclick='fetchAssetData(16428);'
                        class="asset-image" data-id="<?php echo $assetId16428; ?>"
                        data-room="<?php echo htmlspecialchars($room16428); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16428); ?>"
                        data-image="<?php echo base64_encode($upload_img16428); ?>"
                        data-status="<?php echo htmlspecialchars($status16428); ?>"
                        data-category="<?php echo htmlspecialchars($category16428); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16428); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16428); ?>; 
                        position:absolute; top:91px; left:829px;'>
                    </div>


                    <!-- ASSET 16429 -->
                    <img src='../image.php?id=16429'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16429' onclick='fetchAssetData(16429);'
                        class="asset-image" data-id="<?php echo $assetId16429; ?>"
                        data-room="<?php echo htmlspecialchars($room16429); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16429); ?>"
                        data-image="<?php echo base64_encode($upload_img16429); ?>"
                        data-category="<?php echo htmlspecialchars($category16429); ?>"
                        data-status="<?php echo htmlspecialchars($status16429); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16429); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16429); ?>; 
                        position:absolute; top:104px; left:829px;'>
                    </div>

                    <!-- ASSET 16430 -->
                    <img src='../image.php?id=16430'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16430' onclick='fetchAssetData(16430);'
                        class="asset-image" data-id="<?php echo $assetId16430; ?>"
                        data-room="<?php echo htmlspecialchars($room16430); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16430); ?>"
                        data-image="<?php echo base64_encode($upload_img16430); ?>"
                        data-category="<?php echo htmlspecialchars($category16430); ?>"
                        data-status="<?php echo htmlspecialchars($status16430); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16430); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16430); ?>; 
                        position:absolute; top:117px; left:829px;'>
                    </div>

                    <!-- ASSET 16431 -->
                    <img src='../image.php?id=16431'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16431' onclick='fetchAssetData(16431);'
                        class="asset-image" data-id="<?php echo $assetId16431; ?>"
                        data-room="<?php echo htmlspecialchars($room16431); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16431); ?>"
                        data-image="<?php echo base64_encode($upload_img16431); ?>"
                        data-status="<?php echo htmlspecialchars($status16431); ?>"
                        data-category="<?php echo htmlspecialchars($category16431); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16431); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16431); ?>; 
                        position:absolute; top:130px; left:829px;'>
                    </div>

                    <!-- ASSET 16432 -->
                    <img src='../image.php?id=16432'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16432' onclick='fetchAssetData(16432);'
                        class="asset-image" data-id="<?php echo $assetId16432; ?>"
                        data-room="<?php echo htmlspecialchars($room16432); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16432); ?>"
                        data-status="<?php echo htmlspecialchars($status16432); ?>"
                        data-image="<?php echo base64_encode($upload_img16432); ?>"
                        data-category="<?php echo htmlspecialchars($category16432); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16432); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16432); ?>; 
                        position:absolute; top:79px; left:852px;'>
                    </div>

                    <!-- ASSET 16433 -->
                    <img src='../image.php?id=16433'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16433' onclick='fetchAssetData(16433);'
                        class="asset-image" data-id="<?php echo $assetId16433; ?>"
                        data-room="<?php echo htmlspecialchars($room16433); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16433); ?>"
                        data-image="<?php echo base64_encode($upload_img16433); ?>"
                        data-status="<?php echo htmlspecialchars($status16433); ?>"
                        data-category="<?php echo htmlspecialchars($category16433); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16433); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16433); ?>; 
                        position:absolute; top:91px; left:852px;'>
                    </div>

                    <!-- ASSET 16434-->
                    <img src='../image.php?id=16434'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16434' onclick='fetchAssetData(16434);'
                        class="asset-image" data-id="<?php echo $assetId16434; ?>"
                        data-room="<?php echo htmlspecialchars($room16434); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16434); ?>"
                        data-image="<?php echo base64_encode($upload_img16434); ?>"
                        data-status="<?php echo htmlspecialchars($status16434); ?>"
                        data-category="<?php echo htmlspecialchars($category16434); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16434); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16434); ?>; 
                        position:absolute; top:104px; left:852px;'>
                    </div>

                    <!-- ASSET 16435 -->
                    <img src='../image.php?id=16435'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16435' onclick='fetchAssetData(16435);'
                        class="asset-image" data-id="<?php echo $assetId16435; ?>"
                        data-room="<?php echo htmlspecialchars($room16435); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16435); ?>"
                        data-image="<?php echo base64_encode($upload_img16435); ?>"
                        data-status="<?php echo htmlspecialchars($status16435); ?>"
                        data-category="<?php echo htmlspecialchars($category16435); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16435); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16435); ?>; 
                        position:absolute; top:117px; left:852px;'>
                    </div>

                    <!-- ASSET 16436 -->
                    <img src='../image.php?id=16436'
                        style='width:18px; cursor:pointer; position:absolute; top:127px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16436' onclick='fetchAssetData(16436);'
                        class="asset-image" data-id="<?php echo $assetId16436; ?>"
                        data-room="<?php echo htmlspecialchars($room16436); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16436); ?>"
                        data-image="<?php echo base64_encode($upload_img16436); ?>"
                        data-status="<?php echo htmlspecialchars($status16436); ?>"
                        data-category="<?php echo htmlspecialchars($category16436); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16436); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16436); ?>; 
                        position:absolute; top:130px; left:852px;'>
                    </div>

                    <!-- ASSET 16437 -->
                    <img src='../image.php?id=16437'
                        style='width:18px; cursor:pointer; position:absolute; top:74px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16437' onclick='fetchAssetData(16437);'
                        class="asset-image" data-id="<?php echo $assetId16437; ?>"
                        data-room="<?php echo htmlspecialchars($room16437); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16437); ?>"
                        data-status="<?php echo htmlspecialchars($status16437); ?>"
                        data-image="<?php echo base64_encode($upload_img16437); ?>"
                        data-category="<?php echo htmlspecialchars($category16437); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16437); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16437); ?>; 
                        position:absolute; top:79px; left:875px;'>
                    </div>

                    <!-- ASSET 16438 -->
                    <img src='../image.php?id=16438'
                        style='width:18px; cursor:pointer; position:absolute; top:87px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16438' onclick='fetchAssetData(16438);'
                        class="asset-image" data-id="<?php echo $assetId16438; ?>"
                        data-room="<?php echo htmlspecialchars($room16438); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16438); ?>"
                        data-image="<?php echo base64_encode($upload_img16438); ?>"
                        data-category="<?php echo htmlspecialchars($category16438); ?>"
                        data-status="<?php echo htmlspecialchars($status16438); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16438); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16438); ?>; 
                        position:absolute; top:91px; left:875px;'>
                    </div>

                    <!-- ASSET 16439 -->
                    <img src='../image.php?id=16439'
                        style='width:18px; cursor:pointer; position:absolute; top:100px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16439' onclick='fetchAssetData(16439);'
                        class="asset-image" data-id="<?php echo $assetId16439; ?>"
                        data-room="<?php echo htmlspecialchars($room16439); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16439); ?>"
                        data-image="<?php echo base64_encode($upload_img16439); ?>"
                        data-category="<?php echo htmlspecialchars($category16439); ?>"
                        data-status="<?php echo htmlspecialchars($status16439); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16439); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16439); ?>; 
                        position:absolute; top:104px; left:875px;'>
                    </div>

                    <!-- ASSET 16440 -->
                    <img src='../image.php?id=16440'
                        style='width:18px; cursor:pointer; position:absolute; top:113px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16440' onclick='fetchAssetData(16440);'
                        class="asset-image" data-id="<?php echo $assetId16440; ?>"
                        data-room="<?php echo htmlspecialchars($room16440); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16440); ?>"
                        data-status="<?php echo htmlspecialchars($status16440); ?>"
                        data-image="<?php echo base64_encode($upload_img16440); ?>"
                        data-category="<?php echo htmlspecialchars($category16440); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16440); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16440); ?>; 
                        position:absolute; top:117px; left:875px;'>
                    </div>


                    <!-- ASSET 16441 -->
                    <img src='../image.php?id=16441'
                        style='width:18px; cursor:pointer; position:absolute; top:126px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16441' onclick='fetchAssetData(16441);'
                        class="asset-image" data-id="<?php echo $assetId16441; ?>"
                        data-room="<?php echo htmlspecialchars($room16441); ?>"
                        data-status="<?php echo htmlspecialchars($status16441); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16441); ?>"
                        data-image="<?php echo base64_encode($upload_img16441); ?>"
                        data-category="<?php echo htmlspecialchars($category16441); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16441); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16441); ?>; 
                        position:absolute; top:130px; left:875px;'>
                    </div>

                    <!-- ASSET 16442 -->
                    <img src='../image.php?id=16442'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16442' onclick='fetchAssetData(16442);'
                        class="asset-image" data-id="<?php echo $assetId16442; ?>"
                        data-room="<?php echo htmlspecialchars($room16442); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16442); ?>"
                        data-status="<?php echo htmlspecialchars($status16442); ?>"
                        data-image="<?php echo base64_encode($upload_img16442); ?>"
                        data-category="<?php echo htmlspecialchars($category16442); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16442); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16442); ?>; 
                        position:absolute; top:159px; left:783px;'>
                    </div>

                    <!-- ASSET 16443 -->
                    <img src='../image.php?id=16443'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16443' onclick='fetchAssetData(16443);'
                        class="asset-image" data-id="<?php echo $assetId16443; ?>"
                        data-room="<?php echo htmlspecialchars($room16443); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16443); ?>"
                        data-status="<?php echo htmlspecialchars($status16443); ?>"
                        data-image="<?php echo base64_encode($upload_img16443); ?>"
                        data-category="<?php echo htmlspecialchars($category16443); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16443); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16443); ?>; 
                        position:absolute; top:171px; left:783px;'>
                    </div>

                    <!-- ASSET 16444 -->
                    <img src='../image.php?id=16444'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16444' onclick='fetchAssetData(16444);'
                        class="asset-image" data-id="<?php echo $assetId16444; ?>"
                        data-room="<?php echo htmlspecialchars($room16444); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16444); ?>"
                        data-image="<?php echo base64_encode($upload_img16444); ?>"
                        data-status="<?php echo htmlspecialchars($status16444); ?>"
                        data-category="<?php echo htmlspecialchars($category16444); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16444); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16444); ?>; 
                        position:absolute; top:183px; left:783px;'>
                    </div>


                    <!-- ASSET 16445 -->
                    <img src='../image.php?id=16445'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16445' onclick='fetchAssetData(16445);'
                        class="asset-image" data-id="<?php echo $assetId16445; ?>"
                        data-room="<?php echo htmlspecialchars($room16445); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16445); ?>"
                        data-image="<?php echo base64_encode($upload_img16445); ?>"
                        data-status="<?php echo htmlspecialchars($status16445); ?>"
                        data-category="<?php echo htmlspecialchars($category16445); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16445); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16445); ?>; 
                        position:absolute; top:194px; left:783px;'>
                    </div>

                    <!-- ASSET 16446 -->
                    <img src='../image.php?id=16446'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16446' onclick='fetchAssetData(16446);'
                        class="asset-image" data-id="<?php echo $assetId16446; ?>"
                        data-room="<?php echo htmlspecialchars($room16446); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16446); ?>"
                        data-image="<?php echo base64_encode($upload_img16446); ?>"
                        data-category="<?php echo htmlspecialchars($category16446); ?>"
                        data-status="<?php echo htmlspecialchars($status16446); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16446); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16446); ?>; 
                        position:absolute; top:207px; left:783px;'>
                    </div>

                    <!-- ASSET 16447 -->
                    <img src='../image.php?id=16447'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16447' onclick='fetchAssetData(16447);'
                        class="asset-image" data-id="<?php echo $assetId16447; ?>"
                        data-room="<?php echo htmlspecialchars($room16447); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16447); ?>"
                        data-image="<?php echo base64_encode($upload_img16447); ?>"
                        data-category="<?php echo htmlspecialchars($category16447); ?>"
                        data-status="<?php echo htmlspecialchars($status16447); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16447); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16447); ?>; 
                        position:absolute; top:159px; left:806px;'>
                    </div>

                    <!-- ASSET 16448 -->
                    <img src='../image.php?id=16448'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16448' onclick='fetchAssetData(16448);'
                        class="asset-image" data-id="<?php echo $assetId16448; ?>"
                        data-room="<?php echo htmlspecialchars($room16448); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16448); ?>"
                        data-image="<?php echo base64_encode($upload_img16448); ?>"
                        data-category="<?php echo htmlspecialchars($category16448); ?>"
                        data-status="<?php echo htmlspecialchars($status16448); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16448); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16448); ?>; 
                        position:absolute; top:171px; left:806px;'>
                    </div>

                    <!-- ASSET 16449 -->
                    <img src='../image.php?id=16449'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16449' onclick='fetchAssetData(16449);'
                        class="asset-image" data-id="<?php echo $assetId16449; ?>"
                        data-room="<?php echo htmlspecialchars($room16449); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16449); ?>"
                        data-image="<?php echo base64_encode($upload_img16449); ?>"
                        data-category="<?php echo htmlspecialchars($category16449); ?>"
                        data-status="<?php echo htmlspecialchars($status16449); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16449); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16449); ?>; 
                        position:absolute; top:183px; left:806px;'>
                    </div>

                    <!-- ASSET 16450 -->
                    <img src='../image.php?id=16450'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16450' onclick='fetchAssetData(16450);'
                        class="asset-image" data-id="<?php echo $assetId16450; ?>"
                        data-room="<?php echo htmlspecialchars($room16450); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16450); ?>"
                        data-image="<?php echo base64_encode($upload_img16450); ?>"
                        data-status="<?php echo htmlspecialchars($status16450); ?>"
                        data-category="<?php echo htmlspecialchars($category16450); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16450); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16450); ?>; 
                        position:absolute; top:195px; left:806px;'>
                    </div>

                    <!-- ASSET 16451 -->
                    <img src='../image.php?id=16451'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:793px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16451' onclick='fetchAssetData(16451);'
                        class="asset-image" data-id="<?php echo $assetId16451; ?>"
                        data-room="<?php echo htmlspecialchars($room16451); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16451); ?>"
                        data-image="<?php echo base64_encode($upload_img16451); ?>"
                        data-category="<?php echo htmlspecialchars($category16451); ?>"
                        data-status="<?php echo htmlspecialchars($status16451); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16451); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16451); ?>; 
                        position:absolute; top:207px; left:806px;'>
                    </div>

                    <!-- ASSET 16452 -->
                    <img src='../image.php?id=16452'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16452' onclick='fetchAssetData(16452);'
                        class="asset-image" data-id="<?php echo $assetId16452; ?>"
                        data-room="<?php echo htmlspecialchars($room16452); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16452); ?>"
                        data-image="<?php echo base64_encode($upload_img16452); ?>"
                        data-category="<?php echo htmlspecialchars($category16452); ?>"
                        data-status="<?php echo htmlspecialchars($status16452); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16452); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16452); ?>; 
                        position:absolute; top:159px; left:829px;'>
                    </div>


                    <!-- ASSET 16453 -->
                    <img src='../image.php?id=16453'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16453' onclick='fetchAssetData(16453);'
                        class="asset-image" data-id="<?php echo $assetId16453; ?>"
                        data-room="<?php echo htmlspecialchars($room16453); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16453); ?>"
                        data-image="<?php echo base64_encode($upload_img16453); ?>"
                        data-category="<?php echo htmlspecialchars($category16453); ?>"
                        data-status="<?php echo htmlspecialchars($status16453); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16453); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16453); ?>; 
                        position:absolute; top:171px; left:829px;'>
                    </div>

                    <!-- ASSET 16454 -->
                    <img src='../image.php?id=16454'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16454' onclick='fetchAssetData(16454);'
                        class="asset-image" data-id="<?php echo $assetId16454; ?>"
                        data-room="<?php echo htmlspecialchars($room16454); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16454); ?>"
                        data-image="<?php echo base64_encode($upload_img16454); ?>"
                        data-category="<?php echo htmlspecialchars($category16454); ?>"
                        data-status="<?php echo htmlspecialchars($status16454); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16454); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16454); ?>; 
                        position:absolute; top:183px; left:829px;'>
                    </div>

                    <!-- ASSET 16455 -->
                    <img src='../image.php?id=16455'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16455' onclick='fetchAssetData(16455);'
                        class="asset-image" data-id="<?php echo $assetId16455; ?>"
                        data-room="<?php echo htmlspecialchars($room16455); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16455); ?>"
                        data-image="<?php echo base64_encode($upload_img16455); ?>"
                        data-status="<?php echo htmlspecialchars($status16455); ?>"
                        data-category="<?php echo htmlspecialchars($category16455); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16455); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16455); ?>; 
                        position:absolute; top:194px; left:829px;'>
                    </div>

                    <!-- ASSET 16456 -->
                    <img src='../image.php?id=16456'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:816px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16456' onclick='fetchAssetData(16456);'
                        class="asset-image" data-id="<?php echo $assetId16456; ?>"
                        data-room="<?php echo htmlspecialchars($room16456); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16456); ?>"
                        data-image="<?php echo base64_encode($upload_img16456); ?>"
                        data-status="<?php echo htmlspecialchars($status16456); ?>"
                        data-category="<?php echo htmlspecialchars($category16456); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16456); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16456); ?>; 
                        position:absolute; top:207px; left:829px;'>
                    </div>

                    <!-- ASSET 16457 -->
                    <img src='../image.php?id=16457'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16457' onclick='fetchAssetData(16457);'
                        class="asset-image" data-id="<?php echo $assetId16457; ?>"
                        data-room="<?php echo htmlspecialchars($room16457); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16457); ?>"
                        data-status="<?php echo htmlspecialchars($status16457); ?>"
                        data-image="<?php echo base64_encode($upload_img16457); ?>"
                        data-category="<?php echo htmlspecialchars($category16457); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16457); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16457); ?>; 
                        position:absolute; top:159px; left:852px;'>
                    </div>

                    <!-- ASSET 16458 -->
                    <img src='../image.php?id=16458'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16458' onclick='fetchAssetData(16458);'
                        class="asset-image" data-id="<?php echo $assetId16458; ?>"
                        data-room="<?php echo htmlspecialchars($room16458); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16458); ?>"
                        data-status="<?php echo htmlspecialchars($status16458); ?>"
                        data-image="<?php echo base64_encode($upload_img16458); ?>"
                        data-category="<?php echo htmlspecialchars($category16458); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16458); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16458); ?>; 
                        position:absolute; top:171px; left:852px;'>
                    </div>

                    <!-- ASSET 16459 -->
                    <img src='../image.php?id=16459'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16459' onclick='fetchAssetData(16459);'
                        class="asset-image" data-id="<?php echo $assetId16459; ?>"
                        data-room="<?php echo htmlspecialchars($room16459); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16459); ?>"
                        data-image="<?php echo base64_encode($upload_img16459); ?>"
                        data-status="<?php echo htmlspecialchars($status16459); ?>"
                        data-category="<?php echo htmlspecialchars($category16459); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16459); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16459); ?>; 
                        position:absolute; top:183px; left:852px;'>
                    </div>

                    <!-- ASSET 16460 -->
                    <img src='../image.php?id=16460'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16460' onclick='fetchAssetData(16460);'
                        class="asset-image" data-id="<?php echo $assetId16460; ?>"
                        data-room="<?php echo htmlspecialchars($room16460); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16460); ?>"
                        data-image="<?php echo base64_encode($upload_img16460); ?>"
                        data-category="<?php echo htmlspecialchars($category16460); ?>"
                        data-status="<?php echo htmlspecialchars($status16460); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16460); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16460); ?>; 
                        position:absolute; top:194px; left:852px;'>
                    </div>


                    <!-- ASSET 16461 -->
                    <img src='../image.php?id=16461'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:839px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16461' onclick='fetchAssetData(16461);'
                        class="asset-image" data-id="<?php echo $assetId16461; ?>"
                        data-room="<?php echo htmlspecialchars($room16461); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16461); ?>"
                        data-image="<?php echo base64_encode($upload_img16461); ?>"
                        data-category="<?php echo htmlspecialchars($category16461); ?>"
                        data-status="<?php echo htmlspecialchars($status16461); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16461); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16461); ?>; 
                        position:absolute; top:207px; left:852px;'>
                    </div>

                    <!-- ASSET 16462 -->
                    <img src='../image.php?id=16462'
                        style='width:18px; cursor:pointer; position:absolute; top:155px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16462' onclick='fetchAssetData(16462);'
                        class="asset-image" data-id="<?php echo $assetId16462; ?>"
                        data-room="<?php echo htmlspecialchars($room16462); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16462); ?>"
                        data-image="<?php echo base64_encode($upload_img16462); ?>"
                        data-category="<?php echo htmlspecialchars($category16462); ?>"
                        data-status="<?php echo htmlspecialchars($status16462); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16462); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16462); ?>; 
                        position:absolute; top:159px; left:875px;'>
                    </div>

                    <!-- ASSET 16463 -->
                    <img src='../image.php?id=16463'
                        style='width:18px; cursor:pointer; position:absolute; top:167px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16463' onclick='fetchAssetData(16463);'
                        class="asset-image" data-id="<?php echo $assetId16463; ?>"
                        data-room="<?php echo htmlspecialchars($room16463); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16463); ?>"
                        data-image="<?php echo base64_encode($upload_img16463); ?>"
                        data-status="<?php echo htmlspecialchars($status16463); ?>"
                        data-category="<?php echo htmlspecialchars($category16463); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16463); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16463); ?>; 
                        position:absolute; top:171px; left:875px;'>
                    </div>

                    <!-- ASSET 16464 -->
                    <img src='../image.php?id=16464'
                        style='width:18px; cursor:pointer; position:absolute; top:179px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16464' onclick='fetchAssetData(16464);'
                        class="asset-image" data-id="<?php echo $assetId16464; ?>"
                        data-room="<?php echo htmlspecialchars($room16464); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16464); ?>"
                        data-image="<?php echo base64_encode($upload_img16464); ?>"
                        data-status="<?php echo htmlspecialchars($status16464); ?>"
                        data-category="<?php echo htmlspecialchars($category16464); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16464); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16464); ?>; 
                        position:absolute; top:183px; left:875px;'>
                    </div>

                    <!-- ASSET 16465 -->
                    <img src='../image.php?id=16465'
                        style='width:18px; cursor:pointer; position:absolute; top:191px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16465' onclick='fetchAssetData(16465);'
                        class="asset-image" data-id="<?php echo $assetId16465; ?>"
                        data-room="<?php echo htmlspecialchars($room16465); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16465); ?>"
                        data-image="<?php echo base64_encode($upload_img16465); ?>"
                        data-status="<?php echo htmlspecialchars($status16465); ?>"
                        data-category="<?php echo htmlspecialchars($category16465); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16465); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16465); ?>; 
                        position:absolute; top:194px; left:875px;'>
                    </div>

                    <!-- ASSET 16466 -->
                    <img src='../image.php?id=16466'
                        style='width:18px; cursor:pointer; position:absolute; top:203px; left:862px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16466' onclick='fetchAssetData(16466);'
                        class="asset-image" data-id="<?php echo $assetId16466; ?>"
                        data-room="<?php echo htmlspecialchars($room16466); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16466); ?>"
                        data-image="<?php echo base64_encode($upload_img16466); ?>"
                        data-status="<?php echo htmlspecialchars($status16466); ?>"
                        data-category="<?php echo htmlspecialchars($category16466); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16466); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16466); ?>; 
                        position:absolute; top:207px; left:875px;'>
                    </div>

                    <!-- ASSET 16406 -->
                    <img src='../image.php?id=16406'
                        style='width:18px; cursor:pointer; position:absolute; top:144px; left:715px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16406'
                        onclick='fetchAssetData(16406);' class="asset-image" data-id="<?php echo $assetId16406; ?>"
                        data-room="<?php echo htmlspecialchars($room16406); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16406); ?>"
                        data-image="<?php echo base64_encode($upload_img16406); ?>"
                        data-status="<?php echo htmlspecialchars($status16406); ?>"
                        data-category="<?php echo htmlspecialchars($category16406); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16406); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16406); ?>; 
                        position:absolute; top:142px; left:719px;'>
                    </div>

                    <!-- ASSET 16469 -->
                    <img src='../image.php?id=16469'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:1025px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16469' onclick='fetchAssetData(16469);'
                        class="asset-image" data-id="<?php echo $assetId16469; ?>"
                        data-room="<?php echo htmlspecialchars($room16469); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16469); ?>"
                        data-image="<?php echo base64_encode($upload_img16469); ?>"
                        data-status="<?php echo htmlspecialchars($status16469); ?>"
                        data-category="<?php echo htmlspecialchars($category16469); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16469); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16469); ?>; 
                        position:absolute; top:70px; left:1035px;'>
                    </div>


                    <!-- ASSET 16470 -->
                    <img src='../image.php?id=16470'
                        style='width:15px; cursor:pointer; position:absolute; top:195px; left:1025px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16470' onclick='fetchAssetData(16470);'
                        class="asset-image" data-id="<?php echo $assetId16470; ?>"
                        data-room="<?php echo htmlspecialchars($room16470); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16470); ?>"
                        data-image="<?php echo base64_encode($upload_img16470); ?>"
                        data-status="<?php echo htmlspecialchars($status16470); ?>"
                        data-category="<?php echo htmlspecialchars($category16470); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16470); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16470); ?>; 
                        position:absolute; top:195px; left:1035px;'>
                    </div>

                    <!-- ASSET 16471 -->
                    <img src='../image.php?id=16471'
                        style='width:15px; cursor:pointer; position:absolute; top:70px; left:1130px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16471' onclick='fetchAssetData(16471);'
                        class="asset-image" data-id="<?php echo $assetId16471; ?>"
                        data-room="<?php echo htmlspecialchars($room16471); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16471); ?>"
                        data-image="<?php echo base64_encode($upload_img16471); ?>"
                        data-status="<?php echo htmlspecialchars($status16471); ?>"
                        data-category="<?php echo htmlspecialchars($category16471); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16471); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16471); ?>; 
                        position:absolute; top:70px; left:1140px;'>
                    </div>

                    <!-- ASSET 16472 -->
                    <img src='../image.php?id=16472'
                        style='width:15px; cursor:pointer; position:absolute; top:195px; left:1130px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16472' onclick='fetchAssetData(16472);'
                        class="asset-image" data-id="<?php echo $assetId16472; ?>"
                        data-room="<?php echo htmlspecialchars($room16472); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16472); ?>"
                        data-image="<?php echo base64_encode($upload_img16472); ?>"
                        data-category="<?php echo htmlspecialchars($category16472); ?>"
                        data-status="<?php echo htmlspecialchars($status16472); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16472); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16472); ?>; 
                        position:absolute; top:195px; left:1140px;'>
                    </div>

                    <!-- ASSET 16473 -->
                    <img src='../image.php?id=16473'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16473' onclick='fetchAssetData(16473);'
                        class="asset-image" data-id="<?php echo $assetId16473; ?>"
                        data-room="<?php echo htmlspecialchars($room16473); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16473); ?>"
                        data-image="<?php echo base64_encode($upload_img16473); ?>"
                        data-category="<?php echo htmlspecialchars($category16473); ?>"
                        data-status="<?php echo htmlspecialchars($status16473); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16473); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16473); ?>; 
                        position:absolute; top:355px; left:230px;'>
                    </div>


                    <!-- ASSET 16474 -->
                    <img src='../image.php?id=16474'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16474' onclick='fetchAssetData(16474);'
                        class="asset-image" data-id="<?php echo $assetId16474; ?>"
                        data-room="<?php echo htmlspecialchars($room16474); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16474); ?>"
                        data-image="<?php echo base64_encode($upload_img16474); ?>"
                        data-category="<?php echo htmlspecialchars($category16474); ?>"
                        data-status="<?php echo htmlspecialchars($status16474); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16474); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16474); ?>; 
                        position:absolute; top:430px; left:230px;'>
                    </div>

                    <!-- ASSET 16480 -->
                    <img src='../image.php?id=16480'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:317px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal1516480' onclick='fetchAssetData(1516480);'
                        class="asset-image" data-id="<?php echo $assetId1516480; ?>"
                        data-room="<?php echo htmlspecialchars($room1516480); ?>"
                        data-floor="<?php echo htmlspecialchars($floor1516480); ?>"
                        data-image="<?php echo base64_encode($upload_img1516480); ?>"
                        data-status="<?php echo htmlspecialchars($status1516480); ?>"
                        data-category="<?php echo htmlspecialchars($category1516480); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName1516480); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status1516480); ?>; 
                        position:absolute; top:355px; left:327px;'>
                    </div>

                    <!-- ASSET 16476 -->
                    <img src='../image.php?id=16476'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:317px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16476' onclick='fetchAssetData(16476);'
                        class="asset-image" data-id="<?php echo $assetId16476; ?>"
                        data-room="<?php echo htmlspecialchars($room16476); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16476); ?>"
                        data-image="<?php echo base64_encode($upload_img16476); ?>"
                        data-status="<?php echo htmlspecialchars($status16476); ?>"
                        data-category="<?php echo htmlspecialchars($category16476); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16476); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16476); ?>; 
                        position:absolute; top:430px; left:327px;'>
                    </div>

                    <!-- ASSET 16477 -->
                    <img src='../image.php?id=16477'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:455px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16477' onclick='fetchAssetData(16477);'
                        class="asset-image" data-id="<?php echo $assetId16477; ?>"
                        data-room="<?php echo htmlspecialchars($room16477); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16477); ?>"
                        data-image="<?php echo base64_encode($upload_img16477); ?>"
                        data-status="<?php echo htmlspecialchars($status16477); ?>"
                        data-category="<?php echo htmlspecialchars($category16477); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16477); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16477); ?>; 
                        position:absolute; top:430px; left:465px;'>
                    </div>


                    <!-- ASSET 16477 -->
                    <img src='../image.php?id=16477'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:455px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16477' onclick='fetchAssetData(16477);'
                        class="asset-image" data-id="<?php echo $assetId16477; ?>"
                        data-room="<?php echo htmlspecialchars($room16477); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16477); ?>"
                        data-image="<?php echo base64_encode($upload_img16477); ?>"
                        data-status="<?php echo htmlspecialchars($status16477); ?>"
                        data-category="<?php echo htmlspecialchars($category16477); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16477); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16477); ?>; 
                        position:absolute; top:355px; left:465px;'>
                    </div>

                    <!-- ASSET 16482 -->
                    <img src='../image.php?id=16482'
                        style='width:18px; cursor:pointer; position:absolute; top:426px; left:230px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16482' onclick='fetchAssetData(16482);'
                        class="asset-image" data-id="<?php echo $assetId16482; ?>"
                        data-room="<?php echo htmlspecialchars($room16482); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16482); ?>"
                        data-image="<?php echo base64_encode($upload_img16482); ?>"
                        data-status="<?php echo htmlspecialchars($status16482); ?>"
                        data-category="<?php echo htmlspecialchars($category16482); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16482); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16482); ?>; 
                        position:absolute; top:426px; left:240px;'>
                    </div>

                    <!-- ASSET 16533 -->
                    <img src='../image.php?id=16533'
                        style='width:15px; cursor:pointer; position:absolute; top:426px; left:250px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16533' onclick='fetchAssetData(16533);'
                        class="asset-image" data-id="<?php echo $assetId16533; ?>"
                        data-room="<?php echo htmlspecialchars($room16533); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16533); ?>"
                        data-image="<?php echo base64_encode($upload_img16533); ?>"
                        data-category="<?php echo htmlspecialchars($category16533); ?>"
                        data-status="<?php echo htmlspecialchars($status16533); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16533); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16533); ?>; 
                        position:absolute; top:415px; left:254px;'>
                    </div>

                    <!-- ASSET 16479 -->
                    <img src='../image.php?id=16479'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16479' onclick='fetchAssetData(16479);'
                        class="asset-image" data-id="<?php echo $assetId16479; ?>"
                        data-room="<?php echo htmlspecialchars($room16479); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16479); ?>"
                        data-image="<?php echo base64_encode($upload_img16479); ?>"
                        data-status="<?php echo htmlspecialchars($status16479); ?>"
                        data-category="<?php echo htmlspecialchars($category16479); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16479); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16479); ?>; 
                        position:absolute; top:515px; left:230px;'>
                    </div>


                    <!-- ASSET 16478 -->
                    <img src='../image.php?id=16478'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:317px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16478' onclick='fetchAssetData(16478);'
                        class="asset-image" data-id="<?php echo $assetId16478; ?>"
                        data-room="<?php echo htmlspecialchars($room16478); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16478); ?>"
                        data-image="<?php echo base64_encode($upload_img16478); ?>"
                        data-status="<?php echo htmlspecialchars($status16478); ?>"
                        data-category="<?php echo htmlspecialchars($category16478); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16478); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16478); ?>; 
                        position:absolute; top:515px; left:327px;'>
                    </div>

                    <!-- ASSET 16478 -->
                    <img src='../image.php?id=16478'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:455px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16478' onclick='fetchAssetData(16478);'
                        class="asset-image" data-id="<?php echo $assetId16478; ?>"
                        data-room="<?php echo htmlspecialchars($room16478); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16478); ?>"
                        data-status="<?php echo htmlspecialchars($status16478); ?>"
                        data-image="<?php echo base64_encode($upload_img16478); ?>"
                        data-category="<?php echo htmlspecialchars($category16478); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16478); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16478); ?>; 
                        position:absolute; top:515px; left:465px;'>
                    </div>

                    <!-- ASSET 16533 -->
                    <img src='../image.php?id=16482'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16482'
                        onclick='fetchAssetData(16482);' class="asset-image" data-id="<?php echo $assetId16482; ?>"
                        data-room="<?php echo htmlspecialchars($room16482); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16482); ?>"
                        data-image="<?php echo base64_encode($upload_img16482); ?>"
                        data-status="<?php echo htmlspecialchars($status16482); ?>"
                        data-category="<?php echo htmlspecialchars($category16482); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16482); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16482); ?>; 
                        position:absolute; top:366px; left:331px;'>
                    </div>

                    <!-- ASSET 16532 -->
                    <img src='../image.php?id=16532'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16532'
                        onclick='fetchAssetData(16532);' class="asset-image" data-id="<?php echo $assetId16532; ?>"
                        data-room="<?php echo htmlspecialchars($room16532); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16532); ?>"
                        data-image="<?php echo base64_encode($upload_img16532); ?>"
                        data-status="<?php echo htmlspecialchars($status16532); ?>"
                        data-category="<?php echo htmlspecialchars($category16532); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16532); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16532); ?>; 
                        position:absolute; top:383px; left:331px;'>
                    </div>


                    <!-- ASSET 16531 -->
                    <img src='../image.php?id=16531'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16531'
                        onclick='fetchAssetData(16531);' class="asset-image" data-id="<?php echo $assetId16531; ?>"
                        data-room="<?php echo htmlspecialchars($room16531); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16531); ?>"
                        data-image="<?php echo base64_encode($upload_img16531); ?>"
                        data-category="<?php echo htmlspecialchars($category16531); ?>"
                        data-status="<?php echo htmlspecialchars($status16531); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16531); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16531); ?>; 
                        position:absolute; top:396px; left:331px;'>
                    </div>

                    <!-- ASSET 16530 -->
                    <img src='../image.php?id=16530'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16530'
                        onclick='fetchAssetData(16530);' class="asset-image" data-id="<?php echo $assetId16530; ?>"
                        data-room="<?php echo htmlspecialchars($room16530); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16530); ?>"
                        data-image="<?php echo base64_encode($upload_img16530); ?>"
                        data-category="<?php echo htmlspecialchars($category16530); ?>"
                        data-status="<?php echo htmlspecialchars($status16530); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16530); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16530); ?>; 
                        position:absolute; top:409px; left:331px;'>
                    </div>

                    <!-- ASSET 16529 -->
                    <img src='../image.php?id=16529'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16529'
                        onclick='fetchAssetData(16529);' class="asset-image" data-id="<?php echo $assetId16529; ?>"
                        data-room="<?php echo htmlspecialchars($room16529); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16529); ?>"
                        data-image="<?php echo base64_encode($upload_img16529); ?>"
                        data-category="<?php echo htmlspecialchars($category16529); ?>"
                        data-status="<?php echo htmlspecialchars($status16529); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16529); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16529); ?>; 
                        position:absolute; top:422px; left:331px;'>
                    </div>

                    <!-- ASSET 16524 -->
                    <img src='../image.php?id=16524'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16524'
                        onclick='fetchAssetData(16524);' class="asset-image" data-id="<?php echo $assetId16524; ?>"
                        data-room="<?php echo htmlspecialchars($room16524); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16524); ?>"
                        data-image="<?php echo base64_encode($upload_img16524); ?>"
                        data-category="<?php echo htmlspecialchars($category16524); ?>"
                        data-status="<?php echo htmlspecialchars($status16524); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16524); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16524); ?>; 
                        position:absolute; top:370px; left:354px;'>
                    </div>


                    <!-- ASSET 16525 -->
                    <img src='../image.php?id=16525'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16525'
                        onclick='fetchAssetData(16525);' class="asset-image" data-id="<?php echo $assetId16525; ?>"
                        data-room="<?php echo htmlspecialchars($room16525); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16525); ?>"
                        data-image="<?php echo base64_encode($upload_img16525); ?>"
                        data-category="<?php echo htmlspecialchars($category16525); ?>"
                        data-status="<?php echo htmlspecialchars($status16525); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16525); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16525); ?>; 
                        position:absolute; top:383px; left:354px;'>
                    </div>

                    <!-- ASSET 16526 -->
                    <img src='../image.php?id=16526'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16526'
                        onclick='fetchAssetData(16526);' class="asset-image" data-id="<?php echo $assetId16526; ?>"
                        data-room="<?php echo htmlspecialchars($room16526); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16526); ?>"
                        data-image="<?php echo base64_encode($upload_img16526); ?>"
                        data-category="<?php echo htmlspecialchars($category16526); ?>"
                        data-status="<?php echo htmlspecialchars($status16526); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16526); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16526); ?>; 
                        position:absolute; top:396px; left:354px;'>
                    </div>

                    <!-- ASSET 16527 -->
                    <img src='../image.php?id=16527'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16527'
                        onclick='fetchAssetData(16527);' class="asset-image" data-id="<?php echo $assetId16527; ?>"
                        data-room="<?php echo htmlspecialchars($room16527); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16527); ?>"
                        data-image="<?php echo base64_encode($upload_img16527); ?>"
                        data-category="<?php echo htmlspecialchars($category16527); ?>"
                        data-status="<?php echo htmlspecialchars($status16527); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16527); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16527); ?>; 
                        position:absolute; top:409px; left:354px;'>
                    </div>

                    <!-- ASSET 16528 -->
                    <img src='../image.php?id=16528'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16528'
                        onclick='fetchAssetData(16528);' class="asset-image" data-id="<?php echo $assetId16528; ?>"
                        data-room="<?php echo htmlspecialchars($room16528); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16528); ?>"
                        data-image="<?php echo base64_encode($upload_img16528); ?>"
                        data-category="<?php echo htmlspecialchars($category16528); ?>"
                        data-status="<?php echo htmlspecialchars($status16528); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16528); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16528); ?>; 
                        position:absolute; top:422px; left:354px;'>
                    </div>

                    <!-- ASSET 16519 -->
                    <img src='../image.php?id=16519'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16519'
                        onclick='fetchAssetData(16519);' class="asset-image" data-id="<?php echo $assetId16519; ?>"
                        data-room="<?php echo htmlspecialchars($room16519); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16519); ?>"
                        data-image="<?php echo base64_encode($upload_img16519); ?>"
                        data-status="<?php echo htmlspecialchars($status16519); ?>"
                        data-category="<?php echo htmlspecialchars($category16519); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16519); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16519); ?>; 
                        position:absolute; top:370px; left:377px;'>
                    </div>

                    <!-- ASSET 16520 -->
                    <img src='../image.php?id=16520'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16520'
                        onclick='fetchAssetData(16520);' class="asset-image" data-id="<?php echo $assetId16520; ?>"
                        data-room="<?php echo htmlspecialchars($room16520); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16520); ?>"
                        data-image="<?php echo base64_encode($upload_img16520); ?>"
                        data-category="<?php echo htmlspecialchars($category16520); ?>"
                        data-status="<?php echo htmlspecialchars($status16520); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16520); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16520); ?>; 
                        position:absolute; top:383px; left:377px;'>
                    </div>

                    <!-- ASSET 16521 -->
                    <img src='../image.php?id=16521'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16521'
                        onclick='fetchAssetData(16521);' class="asset-image" data-id="<?php echo $assetId16521; ?>"
                        data-room="<?php echo htmlspecialchars($room16521); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16521); ?>"
                        data-image="<?php echo base64_encode($upload_img16521); ?>"
                        data-category="<?php echo htmlspecialchars($category16521); ?>"
                        data-status="<?php echo htmlspecialchars($status16521); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16521); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16521); ?>; 
                        position:absolute; top:396px; left:377px;'>
                    </div>

                    <!-- ASSET 16522 -->
                    <img src='../image.php?id=16522'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16522'
                        onclick='fetchAssetData(16522);' class="asset-image" data-id="<?php echo $assetId16522; ?>"
                        data-room="<?php echo htmlspecialchars($room16522); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16522); ?>"
                        data-image="<?php echo base64_encode($upload_img16522); ?>"
                        data-status="<?php echo htmlspecialchars($status16522); ?>"
                        data-category="<?php echo htmlspecialchars($category16522); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName6450); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16522); ?>; 
                        position:absolute; top:409px; left:377px;'>
                    </div>


                    <!-- ASSET 16523 -->
                    <img src='../image.php?id=16523'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16523'
                        onclick='fetchAssetData(16523);' class="asset-image" data-id="<?php echo $assetId16523; ?>"
                        data-room="<?php echo htmlspecialchars($room16523); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16523); ?>"
                        data-image="<?php echo base64_encode($upload_img16523); ?>"
                        data-status="<?php echo htmlspecialchars($status16523); ?>"
                        data-category="<?php echo htmlspecialchars($category16523); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16523); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16523); ?>; 
                        position:absolute; top:422px; left:377px;'>
                    </div>

                    <!-- ASSET 16514 -->
                    <img src='../image.php?id=16514'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16514'
                        onclick='fetchAssetData(16514);' class="asset-image" data-id="<?php echo $assetId16514; ?>"
                        data-room="<?php echo htmlspecialchars($room16514); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16514); ?>"
                        data-image="<?php echo base64_encode($upload_img16514); ?>"
                        data-status="<?php echo htmlspecialchars($status16514); ?>"
                        data-category="<?php echo htmlspecialchars($category16514); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16514); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16514); ?>; 
                        position:absolute; top:370px; left:400px;'>
                    </div>

                    <!-- ASSET 16515 -->
                    <img src='../image.php?id=16515'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16515'
                        onclick='fetchAssetData(16515);' class="asset-image" data-id="<?php echo $assetId16515; ?>"
                        data-room="<?php echo htmlspecialchars($room16515); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16515); ?>"
                        data-status="<?php echo htmlspecialchars($status16515); ?>"
                        data-image="<?php echo base64_encode($upload_img16515); ?>"
                        data-category="<?php echo htmlspecialchars($category16515); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16515); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16515); ?>; 
                        position:absolute; top:383px; left:400px;'>
                    </div>

                    <!-- ASSET 16516 -->
                    <img src='../image.php?id=16516'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16516'
                        onclick='fetchAssetData(16516);' class="asset-image" data-id="<?php echo $assetId16516; ?>"
                        data-room="<?php echo htmlspecialchars($room16516); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16516); ?>"
                        data-status="<?php echo htmlspecialchars($status16516); ?>"
                        data-image="<?php echo base64_encode($upload_img16516); ?>"
                        data-category="<?php echo htmlspecialchars($category16516); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16516); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16516); ?>; 
                        position:absolute; top:396px; left:400px;'>
                    </div>

                    <!-- ASSET 16517 -->
                    <img src='../image.php?id=16517'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16517'
                        onclick='fetchAssetData(16517);' class="asset-image" data-id="<?php echo $assetId16517; ?>"
                        data-room="<?php echo htmlspecialchars($room16517); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16517); ?>"
                        data-image="<?php echo base64_encode($upload_img16517); ?>"
                        data-status="<?php echo htmlspecialchars($status16517); ?>"
                        data-category="<?php echo htmlspecialchars($category16517); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16517); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16517); ?>; 
                        position:absolute; top:409px; left:400px;'>
                    </div>

                    <!-- ASSET 16518 -->
                    <img src='../image.php?id=16518'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16518'
                        onclick='fetchAssetData(16518);' class="asset-image" data-id="<?php echo $assetId16518; ?>"
                        data-room="<?php echo htmlspecialchars($room16518); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16518); ?>"
                        data-image="<?php echo base64_encode($upload_img16518); ?>"
                        data-status="<?php echo htmlspecialchars($status16518); ?>"
                        data-category="<?php echo htmlspecialchars($category16518); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16518); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16518); ?>; 
                        position:absolute; top:422px; left:400px;'>
                    </div>

                    <!-- ASSET 16483 -->
                    <img src='../image.php?id=16483'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16483'
                        onclick='fetchAssetData(16483);' class="asset-image" data-id="<?php echo $assetId16483; ?>"
                        data-room="<?php echo htmlspecialchars($room16483); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16483); ?>"
                        data-image="<?php echo base64_encode($upload_img16483); ?>"
                        data-category="<?php echo htmlspecialchars($category16483); ?>"
                        data-status="<?php echo htmlspecialchars($status16483); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16483); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16483); ?>; 
                        position:absolute; top:370px; left:423px;'>
                    </div>

                    <!-- ASSET 16484 -->
                    <img src='../image.php?id=16484'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16484'
                        onclick='fetchAssetData(16484);' class="asset-image" data-id="<?php echo $assetId16484; ?>"
                        data-room="<?php echo htmlspecialchars($room16484); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16484); ?>"
                        data-image="<?php echo base64_encode($upload_img16484); ?>"
                        data-category="<?php echo htmlspecialchars($category16484); ?>"
                        data-status="<?php echo htmlspecialchars($status16484); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16484); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16484); ?>; 
                        position:absolute; top:383px; left:423px;'>
                    </div>

                    <!-- ASSET 16485 -->
                    <img src='../image.php?id=16485'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16485'
                        onclick='fetchAssetData(16485);' class="asset-image" data-id="<?php echo $assetId16485; ?>"
                        data-room="<?php echo htmlspecialchars($room16485); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16485); ?>"
                        data-image="<?php echo base64_encode($upload_img16485); ?>"
                        data-category="<?php echo htmlspecialchars($category16485); ?>"
                        data-status="<?php echo htmlspecialchars($status16485); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16485); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16485); ?>; 
                        position:absolute; top:396px; left:423px;'>
                    </div>

                    <!-- ASSET 16486 -->
                    <img src='../image.php?id=16486'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left: 427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16486'
                        onclick='fetchAssetData(16486);' class="asset-image" data-id="<?php echo $assetId16486; ?>"
                        data-room="<?php echo htmlspecialchars($room16486); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16486); ?>"
                        data-image="<?php echo base64_encode($upload_img16486); ?>"
                        data-category="<?php echo htmlspecialchars($category16486); ?>"
                        data-status="<?php echo htmlspecialchars($status16486); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16486); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16486); ?>; 
                        position:absolute; top:409px; left:423px;'>
                    </div>

                    <!-- ASSET 16487 -->
                    <img src='../image.php?id=16487'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16487'
                        onclick='fetchAssetData(16487);' class="asset-image" data-id="<?php echo $assetId16487; ?>"
                        data-room="<?php echo htmlspecialchars($room16487); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16487); ?>"
                        data-image="<?php echo base64_encode($upload_img16487); ?>"
                        data-category="<?php echo htmlspecialchars($category16487); ?>"
                        data-status="<?php echo htmlspecialchars($status16487); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16487); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16487); ?>; 
                        position:absolute; top:422px; left:423px;'>
                    </div>

                    <!-- ASSET 16488 -->
                    <img src='../image.php?id=16488'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16488'
                        onclick='fetchAssetData(16488);' class="asset-image" data-id="<?php echo $assetId16488; ?>"
                        data-room="<?php echo htmlspecialchars($room16488); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16488); ?>"
                        data-image="<?php echo base64_encode($upload_img16488); ?>"
                        data-category="<?php echo htmlspecialchars($category16488); ?>"
                        data-status="<?php echo htmlspecialchars($status16488); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16488); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16488); ?>; 
                        position:absolute; top:455px; left:331px;'>
                    </div>

                    <!-- ASSET 16489 -->
                    <img src='../image.php?id=16489'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16489'
                        onclick='fetchAssetData(16489);' class="asset-image" data-id="<?php echo $assetId16489; ?>"
                        data-room="<?php echo htmlspecialchars($room16489); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16489); ?>"
                        data-image="<?php echo base64_encode($upload_img16489); ?>"
                        data-category="<?php echo htmlspecialchars($category16489); ?>"
                        data-status="<?php echo htmlspecialchars($status16489); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16489); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16489); ?>; 
                        position:absolute; top:468px; left:331px;'>
                    </div>

                    <!-- ASSET 16490 -->
                    <img src='../image.php?id=16490'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16490'
                        onclick='fetchAssetData(16490);' class="asset-image" data-id="<?php echo $assetId16490; ?>"
                        data-room="<?php echo htmlspecialchars($room16490); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16490); ?>"
                        data-image="<?php echo base64_encode($upload_img16490); ?>"
                        data-category="<?php echo htmlspecialchars($category16490); ?>"
                        data-status="<?php echo htmlspecialchars($status16490); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16490); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16490); ?>; 
                        position:absolute; top:481px; left:331px;'>
                    </div>

                    <!-- ASSET 16491 -->
                    <img src='../image.php?id=16491'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16491'
                        onclick='fetchAssetData(16491);' class="asset-image" data-id="<?php echo $assetId16491; ?>"
                        data-room="<?php echo htmlspecialchars($room16491); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16491); ?>"
                        data-image="<?php echo base64_encode($upload_img16491); ?>"
                        data-status="<?php echo htmlspecialchars($status16491); ?>"
                        data-category="<?php echo htmlspecialchars($category16491); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16491); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16491); ?>; 
                        position:absolute; top:494px; left:331px;'>
                    </div>

                    <!-- ASSET 16492 -->
                    <img src='../image.php?id=16492'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:335px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16492'
                        onclick='fetchAssetData(16492);' class="asset-image" data-id="<?php echo $assetId16492; ?>"
                        data-room="<?php echo htmlspecialchars($room16492); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16492); ?>"
                        data-image="<?php echo base64_encode($upload_img16492); ?>"
                        data-status="<?php echo htmlspecialchars($status16492); ?>"
                        data-category="<?php echo htmlspecialchars($category16492); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16492); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16492); ?>; 
                        position:absolute; top:507px; left:331px;'>
                    </div>

                    <!-- ASSET 16493 -->
                    <img src='../image.php?id=16493'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16493'
                        onclick='fetchAssetData(16493);' class="asset-image" data-id="<?php echo $assetId16493; ?>"
                        data-room="<?php echo htmlspecialchars($room16493); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16493); ?>"
                        data-image="<?php echo base64_encode($upload_img16493); ?>"
                        data-status="<?php echo htmlspecialchars($status16493); ?>"
                        data-category="<?php echo htmlspecialchars($category16493); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16493); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16493); ?>; 
                        position:absolute; top:455px; left:354px;'>
                    </div>

                    <!-- ASSET 16494 -->
                    <img src='../image.php?id=16494'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16494'
                        onclick='fetchAssetData(16494);' class="asset-image" data-id="<?php echo $assetId16494; ?>"
                        data-room="<?php echo htmlspecialchars($room16494); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16494); ?>"
                        data-image="<?php echo base64_encode($upload_img16494); ?>"
                        data-category="<?php echo htmlspecialchars($category16494); ?>"
                        data-status="<?php echo htmlspecialchars($status16494); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16494); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16494); ?>; 
                        position:absolute; top:468px; left:354px;'>
                    </div>

                    <!-- ASSET 16495 -->
                    <img src='../image.php?id=16495'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16495'
                        onclick='fetchAssetData(16495);' class="asset-image" data-id="<?php echo $assetId16495; ?>"
                        data-room="<?php echo htmlspecialchars($room16495); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16495); ?>"
                        data-image="<?php echo base64_encode($upload_img16495); ?>"
                        data-category="<?php echo htmlspecialchars($category16495); ?>"
                        data-status="<?php echo htmlspecialchars($status16495); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName6469); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16495); ?>; 
                        position:absolute; top:481px; left:354px;'>
                    </div>

                    <!-- ASSET 16496 -->
                    <img src='../image.php?id=16496'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16496'
                        onclick='fetchAssetData(16496);' class="asset-image" data-id="<?php echo $assetId16496; ?>"
                        data-room="<?php echo htmlspecialchars($room16496); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16496); ?>"
                        data-image="<?php echo base64_encode($upload_img16496); ?>"
                        data-category="<?php echo htmlspecialchars($category16496); ?>"
                        data-status="<?php echo htmlspecialchars($status16496); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16496); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16496); ?>; 
                        position:absolute; top:494px; left:354px;'>
                    </div>


                    <!-- ASSET 16497 -->
                    <img src='../image.php?id=16497'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:358px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16497'
                        onclick='fetchAssetData(16497);' class="asset-image" data-id="<?php echo $assetId16497; ?>"
                        data-room="<?php echo htmlspecialchars($room16497); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16497); ?>"
                        data-image="<?php echo base64_encode($upload_img16497); ?>"
                        data-category="<?php echo htmlspecialchars($category16497); ?>"
                        data-status="<?php echo htmlspecialchars($status16497); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16497); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16497); ?>; 
                        position:absolute; top:507px; left:354px;'>
                    </div>

                    <!-- ASSET 16498 -->
                    <img src='../image.php?id=16498'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16498'
                        onclick='fetchAssetData(16498);' class="asset-image" data-id="<?php echo $assetId16498; ?>"
                        data-room="<?php echo htmlspecialchars($room16498); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16498); ?>"
                        data-image="<?php echo base64_encode($upload_img16498); ?>"
                        data-category="<?php echo htmlspecialchars($category16498); ?>"
                        data-status="<?php echo htmlspecialchars($status16498); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16498); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16498); ?>; 
                        position:absolute; top:455px; left:377px;'>
                    </div>

                    <!-- ASSET 16499 -->
                    <img src='../image.php?id=16499'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16499'
                        onclick='fetchAssetData(16499);' class="asset-image" data-id="<?php echo $assetId16499; ?>"
                        data-room="<?php echo htmlspecialchars($room16499); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16499); ?>"
                        data-image="<?php echo base64_encode($upload_img16499); ?>"
                        data-category="<?php echo htmlspecialchars($category16499); ?>"
                        data-status="<?php echo htmlspecialchars($status16499); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16499); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16499); ?>; 
                        position:absolute; top:468px; left:377px;'>
                    </div>

                    <!-- ASSET 16500 -->
                    <img src='../image.php?id=16500'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16500'
                        onclick='fetchAssetData(16500);' class="asset-image" data-id="<?php echo $assetId16500; ?>"
                        data-room="<?php echo htmlspecialchars($room16500); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16500); ?>"
                        data-image="<?php echo base64_encode($upload_img16500); ?>"
                        data-category="<?php echo htmlspecialchars($category16500); ?>"
                        data-status="<?php echo htmlspecialchars($status16500); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16500); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16500); ?>; 
                        :absolute; top:481px; left:377px;'>
                    </div>

                    <!-- ASSET 16501 -->
                    <img src='../image.php?id=16501'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16501'
                        onclick='fetchAssetData(16501);' class="asset-image" data-id="<?php echo $assetId16501; ?>"
                        data-room="<?php echo htmlspecialchars($room16501); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16501); ?>"
                        data-image="<?php echo base64_encode($upload_img16501); ?>"
                        data-category="<?php echo htmlspecialchars($category16501); ?>"
                        data-status="<?php echo htmlspecialchars($status16501); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16501); ?>; 
                        position:absolute; top:494px; left:377px;'>
                    </div>

                    <!-- ASSET 16502 -->
                    <img src='../image.php?id=16502'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:381px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16502'
                        onclick='fetchAssetData(16502);' class="asset-image" data-id="<?php echo $assetId16502; ?>"
                        data-room="<?php echo htmlspecialchars($room16502); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16502); ?>"
                        data-image="<?php echo base64_encode($upload_img16502); ?>"
                        data-category="<?php echo htmlspecialchars($category16502); ?>"
                        data-status="<?php echo htmlspecialchars($status16502); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16502); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16502); ?>; 
                        position:absolute; top:507px; left:377px;'>
                    </div>

                    <!-- ASSET 16503 -->
                    <img src='../image.php?id=16503'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16503'
                        onclick='fetchAssetData(16503);' class="asset-image" data-id="<?php echo $assetId16503; ?>"
                        data-room="<?php echo htmlspecialchars($room16503); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16503); ?>"
                        data-image="<?php echo base64_encode($upload_img16503); ?>"
                        data-status="<?php echo htmlspecialchars($status16503); ?>"
                        data-category="<?php echo htmlspecialchars($category16503); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16503); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16503); ?>; 
                        position:absolute; top:455px; left:400px;'>
                    </div>

                    <!-- ASSET 16504 -->
                    <img src='../image.php?id=16504'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16504'
                        onclick='fetchAssetData(16504);' class="asset-image" data-id="<?php echo $assetId16504; ?>"
                        data-room="<?php echo htmlspecialchars($room16504); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16504); ?>"
                        data-image="<?php echo base64_encode($upload_img16504); ?>"
                        data-category="<?php echo htmlspecialchars($category16504); ?>"
                        data-status="<?php echo htmlspecialchars($status16504); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16504); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16504); ?>; 
                        position:absolute; top:468px; left:400px;'>
                    </div>


                    <!-- ASSET 16505 -->
                    <img src='../image.php?id=16505'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16505'
                        onclick='fetchAssetData(16505);' class="asset-image" data-id="<?php echo $assetId16505; ?>"
                        data-room="<?php echo htmlspecialchars($room16505); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16505); ?>"
                        data-image="<?php echo base64_encode($upload_img16505); ?>"
                        data-status="<?php echo htmlspecialchars($status16505); ?>"
                        data-category="<?php echo htmlspecialchars($category16505); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16505); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16505); ?>; 
                        position:absolute; top:481px; left:400px;'>
                    </div>

                    <!-- ASSET 16506 -->
                    <img src='../image.php?id=16506'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16506'
                        onclick='fetchAssetData(16506);' class="asset-image" data-id="<?php echo $assetId16506; ?>"
                        data-room="<?php echo htmlspecialchars($room16506); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16506); ?>"
                        data-image="<?php echo base64_encode($upload_img16506); ?>"
                        data-category="<?php echo htmlspecialchars($category16506); ?>"
                        data-status="<?php echo htmlspecialchars($status16506); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16506); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16506); ?>; 
                        position:absolute; top:494px; left:400px;'>
                    </div>

                    <!-- ASSET 16507 -->
                    <img src='../image.php?id=16507'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:404px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16507'
                        onclick='fetchAssetData(16507);' class="asset-image" data-id="<?php echo $assetId16507; ?>"
                        data-room="<?php echo htmlspecialchars($room16507); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16507); ?>"
                        data-image="<?php echo base64_encode($upload_img16507); ?>"
                        data-category="<?php echo htmlspecialchars($category16507); ?>"
                        data-status="<?php echo htmlspecialchars($status16507); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16507); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16507); ?>; 
                        position:absolute; top:507px; left:400px;'>
                    </div>

                    <!-- ASSET 16508 -->
                    <img src='../image.php?id=16508'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16508'
                        onclick='fetchAssetData(16508);' class="asset-image" data-id="<?php echo $assetId16508; ?>"
                        data-room="<?php echo htmlspecialchars($room16508); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16508); ?>"
                        data-image="<?php echo base64_encode($upload_img16508); ?>"
                        data-category="<?php echo htmlspecialchars($category16508); ?>"
                        data-status="<?php echo htmlspecialchars($status16508); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16508); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16508); ?>; 
                        position:absolute; top:455px; left:423px;'>
                    </div>

                    <!-- ASSET 16509 -->
                    <img src='../image.php?id=16509'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16509'
                        onclick='fetchAssetData(16509);' class="asset-image" data-id="<?php echo $assetId16509; ?>"
                        data-room="<?php echo htmlspecialchars($room16509); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16509); ?>"
                        data-image="<?php echo base64_encode($upload_img16509); ?>"
                        data-category="<?php echo htmlspecialchars($category16509); ?>"
                        data-status="<?php echo htmlspecialchars($status16509); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16509); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16509); ?>; 
                        position:absolute; top:468px; left:423px;'>
                    </div>

                    <!-- ASSET 16510 -->
                    <img src='../image.php?id=16510'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16510'
                        onclick='fetchAssetData(16510);' class="asset-image" data-id="<?php echo $assetId16510; ?>"
                        data-room="<?php echo htmlspecialchars($room16510); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16510); ?>"
                        data-image="<?php echo base64_encode($upload_img16510); ?>"
                        data-category="<?php echo htmlspecialchars($category16510); ?>"
                        data-status="<?php echo htmlspecialchars($status16510); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16510); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16510); ?>; 
                        position:absolute; top:481px; left:423px;'>
                    </div>

                    <!-- ASSET 16511 -->
                    <img src='../image.php?id=16511'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16511'
                        onclick='fetchAssetData(16511);' class="asset-image" data-id="<?php echo $assetId16511; ?>"
                        data-room="<?php echo htmlspecialchars($room16511); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16511); ?>"
                        data-image="<?php echo base64_encode($upload_img16511); ?>"
                        data-status="<?php echo htmlspecialchars($status16511); ?>"
                        data-category="<?php echo htmlspecialchars($category16511); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16511); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16511); ?>; 
                        position:absolute; top:494px; left:423px;'>
                    </div>

                    <!-- ASSET 16512 -->
                    <img src='../image.php?id=16512'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:427px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16512'
                        onclick='fetchAssetData(16512);' class="asset-image" data-id="<?php echo $assetId16512; ?>"
                        data-room="<?php echo htmlspecialchars($room16512); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16512); ?>"
                        data-image="<?php echo base64_encode($upload_img16512); ?>"
                        data-category="<?php echo htmlspecialchars($category16512); ?>"
                        data-status="<?php echo htmlspecialchars($status16512); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16512); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16512); ?>; 
                        position:absolute; top:507px; left:423px;'>
                    </div>

                    <!-- Start of IC207 -->

                    <!-- ASSET 16595 -->
                    <img src='../image.php?id=16595'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16595' onclick='fetchAssetData(16595);'
                        class="asset-image" data-id="<?php echo $assetId16595; ?>"
                        data-room="<?php echo htmlspecialchars($room16595); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16595); ?>"
                        data-image="<?php echo base64_encode($upload_img16595); ?>"
                        data-category="<?php echo htmlspecialchars($category16595); ?>"
                        data-status="<?php echo htmlspecialchars($status16595); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16595); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16595); ?>; 
                        position:absolute; top:355px; left:765px;'>
                    </div>

                    <!-- ASSET 16596 -->
                    <img src='../image.php?id=16596'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16596' onclick='fetchAssetData(16596);'
                        class="asset-image" data-id="<?php echo $assetId16596; ?>"
                        data-room="<?php echo htmlspecialchars($room16596); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16596); ?>"
                        data-image="<?php echo base64_encode($upload_img16596); ?>"
                        data-category="<?php echo htmlspecialchars($category16596); ?>"
                        data-status="<?php echo htmlspecialchars($status16596); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16596); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16596); ?>; 
                        position:absolute; top:430px; left:765px;'>
                    </div>

                    <!-- ASSET 16597 -->
                    <img src='../image.php?id=16597'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:850px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16597' onclick='fetchAssetData(16597);'
                        class="asset-image" data-id="<?php echo $assetId16597; ?>"
                        data-room="<?php echo htmlspecialchars($room16597); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16597); ?>"
                        data-image="<?php echo base64_encode($upload_img16597); ?>"
                        data-category="<?php echo htmlspecialchars($category16597); ?>"
                        data-status="<?php echo htmlspecialchars($status16597); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16597); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16597); ?>; 
                        position:absolute; top:355px; left:860px;'>
                    </div>

                    <!-- ASSET 16598 -->
                    <img src='../image.php?id=16598'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:850px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16598' onclick='fetchAssetData(16598);'
                        class="asset-image" data-id="<?php echo $assetId16598; ?>"
                        data-room="<?php echo htmlspecialchars($room16598); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16598); ?>"
                        data-image="<?php echo base64_encode($upload_img16598); ?>"
                        data-category="<?php echo htmlspecialchars($category16598); ?>"
                        data-status="<?php echo htmlspecialchars($status16598); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16598); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16598); ?>; 
                        position:absolute; top:430px; left:860px;'>
                    </div>

                    <!-- ASSET 16599 -->
                    <img src='../image.php?id=16599'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:990px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16599' onclick='fetchAssetData(16599);'
                        class="asset-image" data-id="<?php echo $assetId16599; ?>"
                        data-room="<?php echo htmlspecialchars($room16599); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16599); ?>"
                        data-image="<?php echo base64_encode($upload_img16599); ?>"
                        data-category="<?php echo htmlspecialchars($category16599); ?>"
                        data-status="<?php echo htmlspecialchars($status16599); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16599); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16599); ?>; 
                        position:absolute; top:430px; left:1000px;'>
                    </div>

                    <!-- ASSET 16600 -->
                    <img src='../image.php?id=16600'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:990px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16600' onclick='fetchAssetData(16600);'
                        class="asset-image" data-id="<?php echo $assetId16600; ?>"
                        data-room="<?php echo htmlspecialchars($room16600); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16600); ?>"
                        data-image="<?php echo base64_encode($upload_img16600); ?>"
                        data-category="<?php echo htmlspecialchars($category16600); ?>"
                        data-status="<?php echo htmlspecialchars($status16600); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16600); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16600); ?>; 
                        position:absolute; top:355px; left:1000px;'>
                    </div>

                    <!-- ASSET 16655 -->
                    <img src='../image.php?id=16655'
                        style='width:15px; cursor:pointer; position:absolute; top:426px; left:788px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16655' onclick='fetchAssetData(16655);'
                        class="asset-image" data-id="<?php echo $assetId16655; ?>"
                        data-room="<?php echo htmlspecialchars($room16655); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16655); ?>"
                        data-image="<?php echo base64_encode($upload_img16655); ?>"
                        data-category="<?php echo htmlspecialchars($category16655); ?>"
                        data-status="<?php echo htmlspecialchars($status16655); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16655); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16655); ?>; 
                        position:absolute; top:415px; left:792px;'>
                    </div>

                    <!-- ASSET 16654 ibalik to-->
                    <img src='../image.php?id=16654'
                        style='width:18px; cursor:pointer; position:absolute; top:426px; left:770px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16654' onclick='fetchAssetData(16654);'
                        class="asset-image" data-id="<?php echo $assetId16654; ?>"
                        data-room="<?php echo htmlspecialchars($room16654); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16654); ?>"
                        data-image="<?php echo base64_encode($upload_img16654); ?>"
                        data-category="<?php echo htmlspecialchars($category16654); ?>"
                        data-status="<?php echo htmlspecialchars($status16654); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16654); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16654); ?>; 
                        position:absolute; top:405px; left:764px;'>
                    </div>

                    <!-- ASSET 16603 -->
                    <img src='../image.php?id=16603'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:755px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16603' onclick='fetchAssetData(16603);'
                        class="asset-image" data-id="<?php echo $assetId16603; ?>"
                        data-room="<?php echo htmlspecialchars($room16603); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16603); ?>"
                        data-image="<?php echo base64_encode($upload_img16603); ?>"
                        data-category="<?php echo htmlspecialchars($category16603); ?>"
                        data-status="<?php echo htmlspecialchars($status16603); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16603); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16603); ?>; 
                        position:absolute; top:515px; left:765px;'>
                    </div>

                    <!-- ASSET 16602 -->
                    <img src='../image.php?id=16602'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:850px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16602' onclick='fetchAssetData(16602);'
                        class="asset-image" data-id="<?php echo $assetId16602; ?>"
                        data-room="<?php echo htmlspecialchars($room16602); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16602); ?>"
                        data-image="<?php echo base64_encode($upload_img16602); ?>"
                        data-category="<?php echo htmlspecialchars($category16602); ?>"
                        data-status="<?php echo htmlspecialchars($status16602); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16602); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16602); ?>; 
                        position:absolute; top:515px; left:860px;'>
                    </div>

                    <!-- ASSET 16601 -->
                    <img src='../image.php?id=16601'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:990px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16601' onclick='fetchAssetData(16601);'
                        class="asset-image" data-id="<?php echo $assetId16601; ?>"
                        data-room="<?php echo htmlspecialchars($room16601); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16601); ?>"
                        data-image="<?php echo base64_encode($upload_img16601); ?>"
                        data-category="<?php echo htmlspecialchars($category16601); ?>"
                        data-status="<?php echo htmlspecialchars($status16601); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16601); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16601); ?>; 
                        position:absolute; top:515px; left:1000px;'>
                    </div>

                    <!-- ASSET 16604 -->
                    <img src='../image.php?id=16604'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16604'
                        onclick='fetchAssetData(16604);' class="asset-image" data-id="<?php echo $assetId16604; ?>"
                        data-room="<?php echo htmlspecialchars($room16604); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16604); ?>"
                        data-image="<?php echo base64_encode($upload_img16604); ?>"
                        data-status="<?php echo htmlspecialchars($status16604); ?>"
                        data-category="<?php echo htmlspecialchars($category16604); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16604); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16604); ?>; 
                        position:absolute; top:370px; left:869px;'>
                    </div>

                    <!-- ASSET 16605 -->
                    <img src='../image.php?id=16605'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16605'
                        onclick='fetchAssetData(16605);' class="asset-image" data-id="<?php echo $assetId16605; ?>"
                        data-room="<?php echo htmlspecialchars($room16605); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16605); ?>"
                        data-image="<?php echo base64_encode($upload_img16605); ?>"
                        data-status="<?php echo htmlspecialchars($status16605); ?>"
                        data-category="<?php echo htmlspecialchars($category16605); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16605); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16605); ?>; 
                        position:absolute; top:383px; left:869px;'>
                    </div>

                    <!-- ASSET 16606 -->
                    <img src='../image.php?id=16606'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16606'
                        onclick='fetchAssetData(16606);' class="asset-image" data-id="<?php echo $assetId16606; ?>"
                        data-room="<?php echo htmlspecialchars($room16606); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16606); ?>"
                        data-image="<?php echo base64_encode($upload_img16606); ?>"
                        data-status="<?php echo htmlspecialchars($status16606); ?>"
                        data-category="<?php echo htmlspecialchars($category16606); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16606); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16606); ?>; 
                        position:absolute; top:396px; left:869px;'>
                    </div>

                    <!-- ASSET 16607 -->
                    <img src='../image.php?id=16607'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16607'
                        onclick='fetchAssetData(16607);' class="asset-image" data-id="<?php echo $assetId16607; ?>"
                        data-room="<?php echo htmlspecialchars($room16607); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16607); ?>"
                        data-image="<?php echo base64_encode($upload_img16607); ?>"
                        data-category="<?php echo htmlspecialchars($category16607); ?>"
                        data-status="<?php echo htmlspecialchars($status16607); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16607); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16607); ?>; 
                        position:absolute; top:409px; left:869px;'>
                    </div>

                    <!-- ASSET 16608 -->
                    <img src='../image.php?id=16608'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16608'
                        onclick='fetchAssetData(16608);' class="asset-image" data-id="<?php echo $assetId16608; ?>"
                        data-room="<?php echo htmlspecialchars($room16608); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16608); ?>"
                        data-image="<?php echo base64_encode($upload_img16608); ?>"
                        data-category="<?php echo htmlspecialchars($category16608); ?>"
                        data-status="<?php echo htmlspecialchars($status16608); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16608); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16608); ?>; 
                        position:absolute; top:422px; left:869px;'>
                    </div>

                    <!-- ASSET 16609 -->
                    <img src='../image.php?id=16609'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16609'
                        onclick='fetchAssetData(16609);' class="asset-image" data-id="<?php echo $assetId16609; ?>"
                        data-room="<?php echo htmlspecialchars($room16609); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16609); ?>"
                        data-image="<?php echo base64_encode($upload_img16609); ?>"
                        data-status="<?php echo htmlspecialchars($status16609); ?>"
                        data-category="<?php echo htmlspecialchars($category16609); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16609); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16609); ?>; 
                        position:absolute; top:370px; left:892px;'>
                    </div>

                    <!-- ASSET 16610 -->
                    <img src='../image.php?id=16610'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16610'
                        onclick='fetchAssetData(16610);' class="asset-image" data-id="<?php echo $assetId16610; ?>"
                        data-room="<?php echo htmlspecialchars($room16610); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16610); ?>"
                        data-image="<?php echo base64_encode($upload_img16610); ?>"
                        data-category="<?php echo htmlspecialchars($category16610); ?>"
                        data-status="<?php echo htmlspecialchars($status16610); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16610); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16610); ?>; 
                        position:absolute; top:383px; left:892px;'>
                    </div>

                    <!-- ASSET 16611 -->
                    <img src='../image.php?id=16611'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16611'
                        onclick='fetchAssetData(16611);' class="asset-image" data-id="<?php echo $assetId16611; ?>"
                        data-room="<?php echo htmlspecialchars($room16611); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16611); ?>"
                        data-image="<?php echo base64_encode($upload_img16611); ?>"
                        data-category="<?php echo htmlspecialchars($category16611); ?>"
                        data-status="<?php echo htmlspecialchars($status16611); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16611); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16611); ?>; 
                        position:absolute; top:396px; left:892px;'>
                    </div>

                    <!-- ASSET 16612 -->
                    <img src='../image.php?id=16612'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16612'
                        onclick='fetchAssetData(16612);' class="asset-image" data-id="<?php echo $assetId16612; ?>"
                        data-room="<?php echo htmlspecialchars($room16612); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16612); ?>"
                        data-image="<?php echo base64_encode($upload_img16612); ?>"
                        data-category="<?php echo htmlspecialchars($category16612); ?>"
                        data-status="<?php echo htmlspecialchars($status16612); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16612); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16612); ?>; 
                        position:absolute; top:409px; left:892px;'>
                    </div>

                    <!-- ASSET 16613 -->
                    <img src='../image.php?id=16613'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16613'
                        onclick='fetchAssetData(16613);' class="asset-image" data-id="<?php echo $assetId16613; ?>"
                        data-room="<?php echo htmlspecialchars($room16613); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16613); ?>"
                        data-image="<?php echo base64_encode($upload_img16613); ?>"
                        data-status="<?php echo htmlspecialchars($status16613); ?>"
                        data-category="<?php echo htmlspecialchars($category16613); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16613); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16613); ?>; 
                        position:absolute; top:422px; left:892px;'>
                    </div>

                    <!-- ASSET 16614 -->
                    <img src='../image.php?id=16614'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16614'
                        onclick='fetchAssetData(16614);' class="asset-image" data-id="<?php echo $assetId16614; ?>"
                        data-room="<?php echo htmlspecialchars($room16614); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16614); ?>"
                        data-image="<?php echo base64_encode($upload_img16614); ?>"
                        data-status="<?php echo htmlspecialchars($status16614); ?>"
                        data-category="<?php echo htmlspecialchars($category16614); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16614); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16614); ?>; 
                        position:absolute; top:370px; left:915px;'>
                    </div>

                    <!-- ASSET 16615 -->
                    <img src='../image.php?id=16615'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16615'
                        onclick='fetchAssetData(16615);' class="asset-image" data-id="<?php echo $assetId16615; ?>"
                        data-room="<?php echo htmlspecialchars($room16615); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16615); ?>"
                        data-image="<?php echo base64_encode($upload_img16615); ?>"
                        data-status="<?php echo htmlspecialchars($status16615); ?>"
                        data-category="<?php echo htmlspecialchars($category16615); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16615); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16615); ?>; 
                        position:absolute; top:383px; left:915px;'>
                    </div>

                    <!-- ASSET 16616 -->
                    <img src='../image.php?id=16616'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16616'
                        onclick='fetchAssetData(16616);' class="asset-image" data-id="<?php echo $assetId16616; ?>"
                        data-room="<?php echo htmlspecialchars($room16616); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16616); ?>"
                        data-image="<?php echo base64_encode($upload_img16616); ?>"
                        data-category="<?php echo htmlspecialchars($category16616); ?>"
                        data-status="<?php echo htmlspecialchars($status16616); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16616); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16616); ?>; 
                        position:absolute; top:396px; left:915px;'>
                    </div>

                    <!-- ASSET 16617 -->
                    <img src='../image.php?id=16617'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16617'
                        onclick='fetchAssetData(16617);' class="asset-image" data-id="<?php echo $assetId16617; ?>"
                        data-room="<?php echo htmlspecialchars($room16617); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16617); ?>"
                        data-image="<?php echo base64_encode($upload_img16617); ?>"
                        data-category="<?php echo htmlspecialchars($category16617); ?>"
                        data-status="<?php echo htmlspecialchars($status16617); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16617); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16617); ?>; 
                        position:absolute; top:409px; left:915px;'>
                    </div>

                    <!-- ASSET 16618 -->
                    <img src='../image.php?id=16618'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16618'
                        onclick='fetchAssetData(16618);' class="asset-image" data-id="<?php echo $assetId16618; ?>"
                        data-room="<?php echo htmlspecialchars($room16618); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16618); ?>"
                        data-image="<?php echo base64_encode($upload_img16618); ?>"
                        data-status="<?php echo htmlspecialchars($status16618); ?>"
                        data-category="<?php echo htmlspecialchars($category16618); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16618); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16618); ?>; 
                        position:absolute; top:422px; left:915px;'>
                    </div>

                    <!-- ASSET 16619 -->
                    <img src='../image.php?id=16619'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16619'
                        onclick='fetchAssetData(16619);' class="asset-image" data-id="<?php echo $assetId16619; ?>"
                        data-room="<?php echo htmlspecialchars($room16619); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16619); ?>"
                        data-image="<?php echo base64_encode($upload_img16619); ?>"
                        data-status="<?php echo htmlspecialchars($status16619); ?>"
                        data-category="<?php echo htmlspecialchars($category16619); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16619); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16619); ?>; 
                        position:absolute; top:370px; left:938px;'>
                    </div>

                    <!-- ASSET 16620 -->
                    <img src='../image.php?id=16620'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16620'
                        onclick='fetchAssetData(16620);' class="asset-image" data-id="<?php echo $assetId16620; ?>"
                        data-room="<?php echo htmlspecialchars($room16620); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16620); ?>"
                        data-image="<?php echo base64_encode($upload_img16620); ?>"
                        data-status="<?php echo htmlspecialchars($status16620); ?>"
                        data-category="<?php echo htmlspecialchars($category16620); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16620); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16620); ?>; 
                        position:absolute; top:383px; left:938px;'>
                    </div>

                    <!-- ASSET 16621 -->
                    <img src='../image.php?id=16621'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16621'
                        onclick='fetchAssetData(16621);' class="asset-image" data-id="<?php echo $assetId16621; ?>"
                        data-room="<?php echo htmlspecialchars($room16621); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16621); ?>"
                        data-image="<?php echo base64_encode($upload_img16621); ?>"
                        data-category="<?php echo htmlspecialchars($category16621); ?>"
                        data-status="<?php echo htmlspecialchars($status16621); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16621); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16621); ?>; 
                        position:absolute; top:396px; left:938px;'>
                    </div>

                    <!-- ASSET 16622 -->
                    <img src='../image.php?id=16622'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16622'
                        onclick='fetchAssetData(16622);' class="asset-image" data-id="<?php echo $assetId16622; ?>"
                        data-room="<?php echo htmlspecialchars($room16622); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16622); ?>"
                        data-image="<?php echo base64_encode($upload_img16622); ?>"
                        data-status="<?php echo htmlspecialchars($status16622); ?>"
                        data-category="<?php echo htmlspecialchars($category16622); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16622); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16622); ?>; 
                        position:absolute; top:409px; left:938px;'>
                    </div>

                    <!-- ASSET 16623 -->
                    <img src='../image.php?id=16623'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16623'
                        onclick='fetchAssetData(16623);' class="asset-image" data-id="<?php echo $assetId16623; ?>"
                        data-room="<?php echo htmlspecialchars($room16623); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16623); ?>"
                        data-image="<?php echo base64_encode($upload_img16623); ?>"
                        data-status="<?php echo htmlspecialchars($status16623); ?>"
                        data-category="<?php echo htmlspecialchars($category16623); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16623); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16623); ?>; 
                        position:absolute; top:422px; left:938px;'>
                    </div>

                    <!-- ASSET 16624 -->
                    <img src='../image.php?id=16624'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16624'
                        onclick='fetchAssetData(16624);' class="asset-image" data-id="<?php echo $assetId16624; ?>"
                        data-room="<?php echo htmlspecialchars($room16624); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16624); ?>"
                        data-image="<?php echo base64_encode($upload_img16624); ?>"
                        data-status="<?php echo htmlspecialchars($status16624); ?>"
                        data-category="<?php echo htmlspecialchars($category16624); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16624); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16624); ?>; 
                        position:absolute; top:370px; left:961px;'>
                    </div>

                    <!-- ASSET 16625 -->
                    <img src='../image.php?id=16625'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16625'
                        onclick='fetchAssetData(16625);' class="asset-image" data-id="<?php echo $assetId16625; ?>"
                        data-room="<?php echo htmlspecialchars($room16625); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16625); ?>"
                        data-status="<?php echo htmlspecialchars($status16625); ?>"
                        data-image="<?php echo base64_encode($upload_img16625); ?>"
                        data-category="<?php echo htmlspecialchars($category16625); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16625); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16625); ?>; 
                        position:absolute; top:383px; left:961px;'>
                    </div>

                    <!-- ASSET 16626 -->
                    <img src='../image.php?id=16626'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16626'
                        onclick='fetchAssetData(16626);' class="asset-image" data-id="<?php echo $assetId16626; ?>"
                        data-room="<?php echo htmlspecialchars($room16626); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16626); ?>"
                        data-image="<?php echo base64_encode($upload_img16626); ?>"
                        data-status="<?php echo htmlspecialchars($status16626); ?>"
                        data-category="<?php echo htmlspecialchars($category16626); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16626); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16626); ?>; 
                        position:absolute; top:396px; left:961px;'>
                    </div>

                    <!-- ASSET 16627 -->
                    <img src='../image.php?id=16627'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16627'
                        onclick='fetchAssetData(16627);' class="asset-image" data-id="<?php echo $assetId16627; ?>"
                        data-room="<?php echo htmlspecialchars($room16627); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16627); ?>"
                        data-image="<?php echo base64_encode($upload_img16627); ?>"
                        data-category="<?php echo htmlspecialchars($category16627); ?>"
                        data-status="<?php echo htmlspecialchars($status16627); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16627); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16627); ?>; 
                        position:absolute; top:409px; left:961px;'>
                    </div>

                    <!-- ASSET 16628 -->
                    <img src='../image.php?id=16628'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16628'
                        onclick='fetchAssetData(16628);' class="asset-image" data-id="<?php echo $assetId16628; ?>"
                        data-room="<?php echo htmlspecialchars($room16628); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16628); ?>"
                        data-image="<?php echo base64_encode($upload_img16628); ?>"
                        data-category="<?php echo htmlspecialchars($category16628); ?>"
                        data-status="<?php echo htmlspecialchars($status16628); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16628); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16628); ?>; 
                        position:absolute; top:422px; left:961px;'>
                    </div>


                    <!-- ASSET 16629 -->
                    <img src='../image.php?id=16629'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16629'
                        onclick='fetchAssetData(16629);' class="asset-image" data-id="<?php echo $assetId16629; ?>"
                        data-room="<?php echo htmlspecialchars($room16629); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16629); ?>"
                        data-image="<?php echo base64_encode($upload_img16629); ?>"
                        data-status="<?php echo htmlspecialchars($status16629); ?>"
                        data-category="<?php echo htmlspecialchars($category16629); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16629); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16629); ?>; 
                        position:absolute; top:455px; left:869px;'>
                    </div>

                    <!-- ASSET 16630 -->
                    <img src='../image.php?id=16630'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16630'
                        onclick='fetchAssetData(16630);' class="asset-image" data-id="<?php echo $assetId16630; ?>"
                        data-room="<?php echo htmlspecialchars($room16630); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16630); ?>"
                        data-image="<?php echo base64_encode($upload_img16630); ?>"
                        data-category="<?php echo htmlspecialchars($category16630); ?>"
                        data-status="<?php echo htmlspecialchars($status16630); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16630); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16630); ?>; 
                        position:absolute; top:468px; left:869px;'>
                    </div>

                    <!-- ASSET 16631 -->
                    <img src='../image.php?id=16631'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16631'
                        onclick='fetchAssetData(16631);' class="asset-image" data-id="<?php echo $assetId16631; ?>"
                        data-room="<?php echo htmlspecialchars($room16631); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16631); ?>"
                        data-image="<?php echo base64_encode($upload_img16631); ?>"
                        data-status="<?php echo htmlspecialchars($status16631); ?>"
                        data-category="<?php echo htmlspecialchars($category16631); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16631); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16631); ?>; 
                        position:absolute; top:481px; left:869px;'>
                    </div>

                    <!-- ASSET 16632 -->
                    <img src='../image.php?id=16632'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16632'
                        onclick='fetchAssetData(16632);' class="asset-image" data-id="<?php echo $assetId16632; ?>"
                        data-room="<?php echo htmlspecialchars($room16632); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16632); ?>"
                        data-status="<?php echo htmlspecialchars($status16632); ?>"
                        data-image="<?php echo base64_encode($upload_img16632); ?>"
                        data-category="<?php echo htmlspecialchars($category16632); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16632); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16632); ?>; 
                        position:absolute; top:494px; left:869px;'>
                    </div>

                    <!-- ASSET 16633 -->
                    <img src='../image.php?id=16633'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:873px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16633'
                        onclick='fetchAssetData(16633);' class="asset-image" data-id="<?php echo $assetId16633; ?>"
                        data-room="<?php echo htmlspecialchars($room16633); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16633); ?>"
                        data-image="<?php echo base64_encode($upload_img16633); ?>"
                        data-status="<?php echo htmlspecialchars($status16633); ?>"
                        data-category="<?php echo htmlspecialchars($category16633); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16633); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16633); ?>; 
                        position:absolute; top:507px; left:869px;'>
                    </div>

                    <!-- ASSET 16634 -->
                    <img src='../image.php?id=16634'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16634'
                        onclick='fetchAssetData(16634);' class="asset-image" data-id="<?php echo $assetId16634; ?>"
                        data-room="<?php echo htmlspecialchars($room16634); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16634); ?>"
                        data-image="<?php echo base64_encode($upload_img16634); ?>"
                        data-status="<?php echo htmlspecialchars($status16634); ?>"
                        data-category="<?php echo htmlspecialchars($category16634); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16634); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16634); ?>; 
                        position:absolute; top:455px; left:892px;'>
                    </div>

                    <!-- ASSET 16635 -->
                    <img src='../image.php?id=16635'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16635'
                        onclick='fetchAssetData(16635);' class="asset-image" data-id="<?php echo $assetId16635; ?>"
                        data-room="<?php echo htmlspecialchars($room16635); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16635); ?>"
                        data-image="<?php echo base64_encode($upload_img16635); ?>"
                        data-status="<?php echo htmlspecialchars($status16635); ?>"
                        data-category="<?php echo htmlspecialchars($category16635); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16635); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16635); ?>; 
                        position:absolute; top:468px; left:891px;'>
                    </div>

                    <!-- ASSET 16636 -->
                    <img src='../image.php?id=16636'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16636'
                        onclick='fetchAssetData(16636);' class="asset-image" data-id="<?php echo $assetId16636; ?>"
                        data-room="<?php echo htmlspecialchars($room16636); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16636); ?>"
                        data-image="<?php echo base64_encode($upload_img16636); ?>"
                        data-status="<?php echo htmlspecialchars($status16636); ?>"
                        data-category="<?php echo htmlspecialchars($category16636); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16636); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16636); ?>; 
                        position:absolute; top:481px; left:891px;'>
                    </div>

                    <!-- ASSET 16637 -->
                    <img src='../image.php?id=16637'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16637'
                        onclick='fetchAssetData(16637);' class="asset-image" data-id="<?php echo $assetId16637; ?>"
                        data-room="<?php echo htmlspecialchars($room16637); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16637); ?>"
                        data-image="<?php echo base64_encode($upload_img16637); ?>"
                        data-category="<?php echo htmlspecialchars($category16637); ?>"
                        data-status="<?php echo htmlspecialchars($status16637); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16637); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16637); ?>; 
                        position:absolute; top:494px; left:891px;'>     
                    </div>

                    <!-- ASSET 16638 -->
                    <img src='../image.php?id=16638'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:896px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16638'
                        onclick='fetchAssetData(16638);' class="asset-image" data-id="<?php echo $assetId16638; ?>"
                        data-room="<?php echo htmlspecialchars($room16638); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16638); ?>"
                        data-image="<?php echo base64_encode($upload_img16638); ?>"
                        data-status="<?php echo htmlspecialchars($status16638); ?>"
                        data-category="<?php echo htmlspecialchars($category16638); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16638); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16638); ?>; 
                        position:absolute; top:507px; left:891px;'>
                    </div>

                    <!-- ASSET 16639 -->
                    <img src='../image.php?id=16639'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16639'
                        onclick='fetchAssetData(16639);' class="asset-image" data-id="<?php echo $assetId16639; ?>"
                        data-room="<?php echo htmlspecialchars($room16639); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16639); ?>"
                        data-image="<?php echo base64_encode($upload_img16639); ?>"
                        data-status="<?php echo htmlspecialchars($status16639); ?>"
                        data-category="<?php echo htmlspecialchars($category16639); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16639); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16639); ?>; 
                        position:absolute; top:455px; left:915px;'>
                    </div>

                    <!-- ASSET 16640 -->
                    <img src='../image.php?id=16640'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16640'
                        onclick='fetchAssetData(16640);' class="asset-image" data-id="<?php echo $assetId16640; ?>"
                        data-room="<?php echo htmlspecialchars($room16640); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16640); ?>"
                        data-status="<?php echo htmlspecialchars($status16640); ?>"
                        data-image="<?php echo base64_encode($upload_img16640); ?>"
                        data-category="<?php echo htmlspecialchars($category16640); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16640); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16640); ?>; 
                        position:absolute; top:468px; left:915px;'>
                    </div>

                    <!-- ASSET 16641 -->
                    <img src='../image.php?id=16641'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16641'
                        onclick='fetchAssetData(16641);' class="asset-image" data-id="<?php echo $assetId16641; ?>"
                        data-room="<?php echo htmlspecialchars($room16641); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16641); ?>"
                        data-image="<?php echo base64_encode($upload_img16641); ?>"
                        data-status="<?php echo htmlspecialchars($status16641); ?>"
                        data-category="<?php echo htmlspecialchars($category16641); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16641); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16641); ?>; 
                        position:absolute; top:481px; left:915px;'>     
                    </div>

                    <!-- ASSET 16642 -->
                    <img src='../image.php?id=16642'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16642'
                        onclick='fetchAssetData(16642);' class="asset-image" data-id="<?php echo $assetId16642; ?>"
                        data-room="<?php echo htmlspecialchars($room16642); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16642); ?>"
                        data-status="<?php echo htmlspecialchars($status16642); ?>"
                        data-image="<?php echo base64_encode($upload_img16642); ?>"
                        data-category="<?php echo htmlspecialchars($category16642); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16642); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16642); ?>; 
                        position:absolute; top:494px; left:915px;'>     
                    </div>

                    <!-- ASSET 16643 -->
                    <img src='../image.php?id=16643'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:919px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16643'
                        onclick='fetchAssetData(16643);' class="asset-image" data-id="<?php echo $assetId16643; ?>"
                        data-room="<?php echo htmlspecialchars($room16643); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16643); ?>"
                        data-image="<?php echo base64_encode($upload_img16643); ?>"
                        data-status="<?php echo htmlspecialchars($status16643); ?>"
                        data-category="<?php echo htmlspecialchars($category16643); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16643); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16643); ?>; 
                        position:absolute; top:507px; left:915px;'>
                    </div>

                    <!-- ASSET 16644 -->
                    <img src='../image.php?id=16644'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16644'
                        onclick='fetchAssetData(16644);' class="asset-image" data-id="<?php echo $assetId16644; ?>"
                        data-room="<?php echo htmlspecialchars($room16644); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16644); ?>"
                        data-image="<?php echo base64_encode($upload_img16644); ?>"
                        data-status="<?php echo htmlspecialchars($status16644); ?>"
                        data-category="<?php echo htmlspecialchars($category16644); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16644); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16644); ?>; 
                        position:absolute; top:455px; left:938px;'>
                    </div>

                    <!-- ASSET 16645 -->
                    <img src='../image.php?id=16645'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16645'
                        onclick='fetchAssetData(16645);' class="asset-image" data-id="<?php echo $assetId16645; ?>"
                        data-room="<?php echo htmlspecialchars($room16645); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16645); ?>"
                        data-image="<?php echo base64_encode($upload_img16645); ?>"
                        data-status="<?php echo htmlspecialchars($status16645); ?>"
                        data-category="<?php echo htmlspecialchars($category16645); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16645); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16645); ?>; 
                        position:absolute; top:468px; left:938px;'>
                    </div>

                    <!-- ASSET 16646 -->
                    <img src='../image.php?id=16646'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16646'
                        onclick='fetchAssetData(16646);' class="asset-image" data-id="<?php echo $assetId16646; ?>"
                        data-room="<?php echo htmlspecialchars($room16646); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16646); ?>"
                        data-image="<?php echo base64_encode($upload_img16646); ?>"
                        data-status="<?php echo htmlspecialchars($status16646); ?>"
                        data-category="<?php echo htmlspecialchars($category16646); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16646); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16646); ?>; 
                        position:absolute; top:481px; left:938px;'>
                    </div>

                    <!-- ASSET 16647 -->
                    <img src='../image.php?id=16647'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16647'
                        onclick='fetchAssetData(16647);' class="asset-image" data-id="<?php echo $assetId16647; ?>"
                        data-room="<?php echo htmlspecialchars($room16647); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16647); ?>"
                        data-image="<?php echo base64_encode($upload_img16647); ?>"
                        data-status="<?php echo htmlspecialchars($status16647); ?>"
                        data-category="<?php echo htmlspecialchars($category16647); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16647); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16647); ?>; 
                        position:absolute; top:494px; left:938px;'>
                    </div>

                    <!-- ASSET 16648 -->
                    <img src='../image.php?id=16648'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:942px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16648'
                        onclick='fetchAssetData(16648);' class="asset-image" data-id="<?php echo $assetId16648; ?>"
                        data-room="<?php echo htmlspecialchars($room16648); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16648); ?>"
                        data-image="<?php echo base64_encode($upload_img16648); ?>"
                        data-status="<?php echo htmlspecialchars($status16648); ?>"
                        data-category="<?php echo htmlspecialchars($category16648); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16648); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16648); ?>; 
                        position:absolute; top:507px; left:938px;'>
                    </div>

                    <!-- ASSET 16649 -->
                    <img src='../image.php?id=16649'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16649'
                        onclick='fetchAssetData(16649);' class="asset-image" data-id="<?php echo $assetId16649; ?>"
                        data-room="<?php echo htmlspecialchars($room16649); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16649); ?>"
                        data-image="<?php echo base64_encode($upload_img16649); ?>"
                        data-status="<?php echo htmlspecialchars($status16649); ?>"
                        data-category="<?php echo htmlspecialchars($category16649); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16649); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16649); ?>; 
                        position:absolute; top:455px; left:961px;'>
                    </div>

                    <!-- ASSET 16650 -->
                    <img src='../image.php?id=16650'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16650'
                        onclick='fetchAssetData(16650);' class="asset-image" data-id="<?php echo $assetId16650; ?>"
                        data-room="<?php echo htmlspecialchars($room16650); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16650); ?>"
                        data-image="<?php echo base64_encode($upload_img16650); ?>"
                        data-status="<?php echo htmlspecialchars($status16650); ?>"
                        data-category="<?php echo htmlspecialchars($category16650); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16650); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16650); ?>; 
                        position:absolute; top:468px; left:961px;'>
                    </div>

                    <!-- ASSET 16651 -->
                    <img src='../image.php?id=16651'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16651'
                        onclick='fetchAssetData(16651);' class="asset-image" data-id="<?php echo $assetId16651; ?>"
                        data-room="<?php echo htmlspecialchars($room16651); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16651); ?>"
                        data-image="<?php echo base64_encode($upload_img16651); ?>"
                        data-status="<?php echo htmlspecialchars($status16651); ?>"
                        data-category="<?php echo htmlspecialchars($category16651); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16651); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16651); ?>; 
                        position:absolute; top:481px; left:961px;'>
                    </div>

                    <!-- ASSET 16652 -->
                    <img src='../image.php?id=16652'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16652'
                        onclick='fetchAssetData(16652);' class="asset-image" data-id="<?php echo $assetId16652; ?>"
                        data-room="<?php echo htmlspecialchars($room16652); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16652); ?>"
                        data-status="<?php echo htmlspecialchars($status16652); ?>"
                        data-image="<?php echo base64_encode($upload_img16652); ?>"
                        data-category="<?php echo htmlspecialchars($category16652); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16652); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16652); ?>; 
                        position:absolute; top:494px; left:961px;'>
                    </div>

                    <!-- ASSET 16653 -->
                    <img src='../image.php?id=16653'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:965px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16653'
                        onclick='fetchAssetData(16653);' class="asset-image" data-id="<?php echo $assetId16653; ?>"
                        data-room="<?php echo htmlspecialchars($room16653); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16653); ?>"
                        data-image="<?php echo base64_encode($upload_img16653); ?>"
                        data-status="<?php echo htmlspecialchars($status16653); ?>"
                        data-category="<?php echo htmlspecialchars($category16653); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16653); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16653); ?>; 
                        position:absolute; top:507px; left:961px;'>
                    </div>

                    <!-- IC206A -->

                    <!-- ASSET 16534 -->
                    <img src='../image.php?id=16534'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:485px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16534' onclick='fetchAssetData(16534);'
                        class="asset-image" data-id="<?php echo $assetId16534; ?>"
                        data-room="<?php echo htmlspecialchars($room16534); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16534); ?>"
                        data-image="<?php echo base64_encode($upload_img16534); ?>"
                        data-category="<?php echo htmlspecialchars($category16534); ?>"
                        data-status="<?php echo htmlspecialchars($status16534); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16534); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16534); ?>; 
                        position:absolute; top:355px; left:495px;'>
                    </div>

                    <!-- ASSET 16535 -->
                    <img src='../image.php?id=16535'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:485px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16535' onclick='fetchAssetData(16535);'
                        class="asset-image" data-id="<?php echo $assetId16535; ?>"
                        data-room="<?php echo htmlspecialchars($room16535); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16535); ?>"
                        data-image="<?php echo base64_encode($upload_img16535); ?>"
                        data-category="<?php echo htmlspecialchars($category16535); ?>"
                        data-status="<?php echo htmlspecialchars($status16535); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16535); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16535); ?>; 
                        position:absolute; top:430px; left:495px;'>
                    </div>

                    <!-- ASSET 16542 -->
                    <img src='../image.php?id=16536'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:580px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16536' onclick='fetchAssetData(16536);'
                        class="asset-image" data-id="<?php echo $assetId16536; ?>"
                        data-room="<?php echo htmlspecialchars($room16536); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16536); ?>"
                        data-image="<?php echo base64_encode($upload_img16536); ?>"
                        data-category="<?php echo htmlspecialchars($category16536); ?>"
                        data-status="<?php echo htmlspecialchars($status16536); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16536); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16536); ?>; 
                        position:absolute; top:355px; left:590px;'>
                    </div>

                    <!-- ASSET 16539 -->
                    <img src='../image.php?id=16539'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:580px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16539' onclick='fetchAssetData(16539);'
                        class="asset-image" data-id="<?php echo $assetId16539; ?>"
                        data-room="<?php echo htmlspecialchars($room16539); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16539); ?>"
                        data-image="<?php echo base64_encode($upload_img16539); ?>"
                        data-category="<?php echo htmlspecialchars($category16539); ?>"
                        data-status="<?php echo htmlspecialchars($status16539); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16539); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16539); ?>; 
                        position:absolute; top:430px; left:590px;'>
                    </div>

                    <!-- ASSET 16538 -->
                    <img src='../image.php?id=16538'
                        style='width:15px; cursor:pointer; position:absolute; top:430px; left:720px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16538' onclick='fetchAssetData(16538);'
                        class="asset-image" data-id="<?php echo $assetId16538; ?>"
                        data-room="<?php echo htmlspecialchars($room16538); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16538); ?>"
                        data-image="<?php echo base64_encode($upload_img16538); ?>"
                        data-category="<?php echo htmlspecialchars($category16538); ?>"
                        data-status="<?php echo htmlspecialchars($status16538); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16538); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16538); ?>; 
                        position:absolute; top:430px; left:730px;'>
                    </div>

                    <!-- ASSET 16537 -->
                    <img src='../image.php?id=16537'
                        style='width:15px; cursor:pointer; position:absolute; top:355px; left:720px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16537' onclick='fetchAssetData(16537);'
                        class="asset-image" data-id="<?php echo $assetId16537; ?>"
                        data-room="<?php echo htmlspecialchars($room16537); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16537); ?>"
                        data-image="<?php echo base64_encode($upload_img16537); ?>"
                        data-category="<?php echo htmlspecialchars($category16537); ?>"
                        data-status="<?php echo htmlspecialchars($status16537); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16537); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16537); ?>; 
                        position:absolute; top:355px; left:730px;'>
                    </div>

                    <!-- ASSET 16594 -->
                    <img src='../image.php?id=16594'
                        style='width:15px; cursor:pointer; position:absolute; top:426px; left:518px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16594' onclick='fetchAssetData(16594);'
                        class="asset-image" data-id="<?php echo $assetId16594; ?>"
                        data-room="<?php echo htmlspecialchars($room16594); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16594); ?>"
                        data-image="<?php echo base64_encode($upload_img16594); ?>"
                        data-category="<?php echo htmlspecialchars($category16594); ?>"
                        data-status="<?php echo htmlspecialchars($status16594); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16594); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16594); ?>; 
                        position:absolute; top:415px; left:523px;'>
                    </div>

                    <!-- ASSET 16593 -->
                    <img src='../image.php?id=16593'
                        style='width:18px; cursor:pointer; position:absolute; top: 426px;6px; left:498px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16593' onclick='fetchAssetData(16593);'
                        class="asset-image" data-id="<?php echo $assetId16593; ?>"
                        data-room="<?php echo htmlspecialchars($room16593); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16593); ?>"
                        data-image="<?php echo base64_encode($upload_img16593); ?>"
                        data-category="<?php echo htmlspecialchars($category16593); ?>"
                        data-status="<?php echo htmlspecialchars($status16593); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16593); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16593); ?>; 
                        position:absolute; top:405px; left:764px;'>
                    </div>

                    <!-- ASSET 16542 -->
                    <img src='../image.php?id=16542'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:485px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16542' onclick='fetchAssetData(16542);'
                        class="asset-image" data-id="<?php echo $assetId16542; ?>"
                        data-room="<?php echo htmlspecialchars($room16542); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16542); ?>"
                        data-image="<?php echo base64_encode($upload_img16542); ?>"
                        data-category="<?php echo htmlspecialchars($category16542); ?>"
                        data-status="<?php echo htmlspecialchars($status16542); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16542); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16542); ?>; 
                        position:absolute; top:515px; left:495px;'>
                    </div>

                    <!-- ASSET 16541 -->
                    <img src='../image.php?id=16541'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:580px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16541' onclick='fetchAssetData(16541);'
                        class="asset-image" data-id="<?php echo $assetId16541; ?>"
                        data-room="<?php echo htmlspecialchars($room16541); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16541); ?>"
                        data-image="<?php echo base64_encode($upload_img16541); ?>"
                        data-category="<?php echo htmlspecialchars($category16541); ?>"
                        data-status="<?php echo htmlspecialchars($status16541); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16541); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16541); ?>; 
                        position:absolute; top:515px; left:590px;'>
                    </div>

                    <!-- ASSET 16540 -->
                    <img src='../image.php?id=16540'
                        style='width:15px; cursor:pointer; position:absolute; top:515px; left:720px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal16540' onclick='fetchAssetData(16540);'
                        class="asset-image" data-id="<?php echo $assetId16540; ?>"
                        data-room="<?php echo htmlspecialchars($room16540); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16540); ?>"
                        data-image="<?php echo base64_encode($upload_img16540); ?>"
                        data-category="<?php echo htmlspecialchars($category16540); ?>"
                        data-status="<?php echo htmlspecialchars($status16540); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16540); ?>">
                    <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status16540); ?>; 
                        position:absolute; top:515px; left:730px;'>
                    </div>

                    <!-- ASSET 16573 -->
                    <img src='../image.php?id=16573'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16573'
                        onclick='fetchAssetData(16573);' class="asset-image" data-id="<?php echo $assetId16573; ?>"
                        data-room="<?php echo htmlspecialchars($room16573); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16573); ?>"
                        data-image="<?php echo base64_encode($upload_img16573); ?>"
                        data-status="<?php echo htmlspecialchars($status16573); ?>"
                        data-category="<?php echo htmlspecialchars($category16573); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16573); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16573); ?>; 
                        position:absolute; top:370px; left:599px;'>
                    </div>

                    <!-- ASSET 16574 -->
                    <img src='../image.php?id=16574'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16574'
                        onclick='fetchAssetData(16574);' class="asset-image" data-id="<?php echo $assetId16574; ?>"
                        data-room="<?php echo htmlspecialchars($room16574); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16574); ?>"
                        data-image="<?php echo base64_encode($upload_img16574); ?>"
                        data-status="<?php echo htmlspecialchars($status16574); ?>"
                        data-category="<?php echo htmlspecialchars($category16574); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16574); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16574); ?>; 
                        position:absolute; top:383px; left:599px;'>
                    </div>

                    <!-- ASSET 16575 -->
                    <img src='../image.php?id=16575'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16575'
                        onclick='fetchAssetData(16575);' class="asset-image" data-id="<?php echo $assetId16575; ?>"
                        data-room="<?php echo htmlspecialchars($room16575); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16575); ?>"
                        data-image="<?php echo base64_encode($upload_img16575); ?>"
                        data-status="<?php echo htmlspecialchars($status16575); ?>"
                        data-category="<?php echo htmlspecialchars($category16575); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16575); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16575); ?>; 
                        position:absolute; top:396px; left:599px;'>
                    </div>

                    <!-- ASSET 16576 -->
                    <img src='../image.php?id=16576'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16576'
                        onclick='fetchAssetData(16576);' class="asset-image" data-id="<?php echo $assetId16576; ?>"
                        data-room="<?php echo htmlspecialchars($room16576); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16576); ?>"
                        data-image="<?php echo base64_encode($upload_img16576); ?>"
                        data-category="<?php echo htmlspecialchars($category16576); ?>"
                        data-status="<?php echo htmlspecialchars($status16576); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16576); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16576); ?>; 
                        position:absolute; top:409px; left:599px;'>
                    </div>

                    <!-- ASSET 16577 -->
                    <img src='../image.php?id=16577'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16577'
                        onclick='fetchAssetData(16577);' class="asset-image" data-id="<?php echo $assetId16577; ?>"
                        data-room="<?php echo htmlspecialchars($room16577); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16577); ?>"
                        data-image="<?php echo base64_encode($upload_img16577); ?>"
                        data-category="<?php echo htmlspecialchars($category16577); ?>"
                        data-status="<?php echo htmlspecialchars($status16577); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16577); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16577); ?>; 
                        position:absolute; top:422px; left:599px;'>
                    </div>

                    <!-- ASSET 16578 -->
                    <img src='../image.php?id=16578'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16578'
                        onclick='fetchAssetData(16578);' class="asset-image" data-id="<?php echo $assetId16578; ?>"
                        data-room="<?php echo htmlspecialchars($room16578); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16578); ?>"
                        data-image="<?php echo base64_encode($upload_img16578); ?>"
                        data-status="<?php echo htmlspecialchars($status16578); ?>"
                        data-category="<?php echo htmlspecialchars($category16578); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16578); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16578); ?>; 
                        position:absolute; top:370px; left:625px;'>
                    </div>

                    <!-- ASSET 16579 -->
                    <img src='../image.php?id=16579'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16579'
                        onclick='fetchAssetData(16579);' class="asset-image" data-id="<?php echo $assetId16579; ?>"
                        data-room="<?php echo htmlspecialchars($room16579); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16579); ?>"
                        data-image="<?php echo base64_encode($upload_img16579); ?>"
                        data-category="<?php echo htmlspecialchars($category16579); ?>"
                        data-status="<?php echo htmlspecialchars($status16579); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16579); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16579); ?>; 
                        position:absolute; top:383px; left:625px;'>
                    </div>

                    <!-- ASSET 16580 -->
                    <img src='../image.php?id=16580'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16580'
                        onclick='fetchAssetData(16580);' class="asset-image" data-id="<?php echo $assetId16580; ?>"
                        data-room="<?php echo htmlspecialchars($room16580); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16580); ?>"
                        data-image="<?php echo base64_encode($upload_img16580); ?>"
                        data-category="<?php echo htmlspecialchars($category16580); ?>"
                        data-status="<?php echo htmlspecialchars($status16580); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16580); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16580); ?>; 
                        position:absolute; top:396px; left:625px;'>
                    </div>

                    <!-- ASSET 16581 -->
                    <img src='../image.php?id=16581'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16581'
                        onclick='fetchAssetData(16581);' class="asset-image" data-id="<?php echo $assetId16581; ?>"
                        data-room="<?php echo htmlspecialchars($room16581); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16581); ?>"
                        data-image="<?php echo base64_encode($upload_img16581); ?>"
                        data-category="<?php echo htmlspecialchars($category16581); ?>"
                        data-status="<?php echo htmlspecialchars($status16581); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16581); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16581); ?>; 
                        position:absolute; top:409px; left:625px;'>
                    </div>

                    <!-- ASSET 16582 -->
                    <img src='../image.php?id=16582'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16582'
                        onclick='fetchAssetData(16582);' class="asset-image" data-id="<?php echo $assetId16582; ?>"
                        data-room="<?php echo htmlspecialchars($room16582); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16582); ?>"
                        data-image="<?php echo base64_encode($upload_img16582); ?>"
                        data-status="<?php echo htmlspecialchars($status16582); ?>"
                        data-category="<?php echo htmlspecialchars($category16582); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16582); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16582); ?>; 
                        position:absolute; top:422px; left:625px;'>
                    </div>

                    <!-- ASSET 16583 -->
                    <img src='../image.php?id=16583'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16583'
                        onclick='fetchAssetData(16583);' class="asset-image" data-id="<?php echo $assetId16583; ?>"
                        data-room="<?php echo htmlspecialchars($room16583); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16583); ?>"
                        data-image="<?php echo base64_encode($upload_img16583); ?>"
                        data-status="<?php echo htmlspecialchars($status16583); ?>"
                        data-category="<?php echo htmlspecialchars($category16583); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16583); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16583); ?>; 
                        position:absolute; top:370px; left:648px;'>
                    </div>

                    <!-- ASSET 16584 -->
                    <img src='../image.php?id=16584'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16584'
                        onclick='fetchAssetData(16584);' class="asset-image" data-id="<?php echo $assetId16584; ?>"
                        data-room="<?php echo htmlspecialchars($room16584); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16584); ?>"
                        data-image="<?php echo base64_encode($upload_img16584); ?>"
                        data-status="<?php echo htmlspecialchars($status16584); ?>"
                        data-category="<?php echo htmlspecialchars($category16584); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16584); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16584); ?>; 
                        position:absolute; top:383px; left:648px;'>
                    </div>

                    <!-- ASSET 16585 -->
                    <img src='../image.php?id=16585'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16585'
                        onclick='fetchAssetData(16585);' class="asset-image" data-id="<?php echo $assetId16585; ?>"
                        data-room="<?php echo htmlspecialchars($room16585); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16585); ?>"
                        data-image="<?php echo base64_encode($upload_img16585); ?>"
                        data-category="<?php echo htmlspecialchars($category16585); ?>"
                        data-status="<?php echo htmlspecialchars($status16585); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16585); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16585); ?>; 
                        position:absolute; top:396px; left:648px;'>
                    </div>

                    <!-- ASSET 16586 -->
                    <img src='../image.php?id=16586'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16586'
                        onclick='fetchAssetData(16586);' class="asset-image" data-id="<?php echo $assetId16586; ?>"
                        data-room="<?php echo htmlspecialchars($room16586); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16586); ?>"
                        data-image="<?php echo base64_encode($upload_img16586); ?>"
                        data-category="<?php echo htmlspecialchars($category16586); ?>"
                        data-status="<?php echo htmlspecialchars($status16586); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16586); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16586); ?>; 
                        position:absolute; top:409px; left:648px;'>
                    </div>

                    <!-- ASSET 16587 -->
                    <img src='../image.php?id=16587'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16587'
                        onclick='fetchAssetData(16587);' class="asset-image" data-id="<?php echo $assetId16587; ?>"
                        data-room="<?php echo htmlspecialchars($room16587); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16587); ?>"
                        data-image="<?php echo base64_encode($upload_img16587); ?>"
                        data-status="<?php echo htmlspecialchars($status16587); ?>"
                        data-category="<?php echo htmlspecialchars($category16587); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16587); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16587); ?>; 
                        position:absolute; top:422px; left:648px;'>
                    </div>

                    <!-- ASSET 16588 -->
                    <img src='../image.php?id=16588'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16588'
                        onclick='fetchAssetData(16588);' class="asset-image" data-id="<?php echo $assetId16588; ?>"
                        data-room="<?php echo htmlspecialchars($room16588); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16588); ?>"
                        data-image="<?php echo base64_encode($upload_img16588); ?>"
                        data-status="<?php echo htmlspecialchars($status16588); ?>"
                        data-category="<?php echo htmlspecialchars($category16588); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16588); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16588); ?>; 
                        position:absolute; top:370px; left:671px;'>
                    </div>

                    <!-- ASSET 16589 -->
                    <img src='../image.php?id=16589'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16589'
                        onclick='fetchAssetData(16589);' class="asset-image" data-id="<?php echo $assetId16589; ?>"
                        data-room="<?php echo htmlspecialchars($room16589); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16589); ?>"
                        data-image="<?php echo base64_encode($upload_img16589); ?>"
                        data-status="<?php echo htmlspecialchars($status16589); ?>"
                        data-category="<?php echo htmlspecialchars($category16589); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16589); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16589); ?>; 
                        position:absolute; top:383px; left:671px;'>
                    </div>

                    <!-- ASSET 16590 -->
                    <img src='../image.php?id=16590'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16590'
                        onclick='fetchAssetData(16590);' class="asset-image" data-id="<?php echo $assetId16590; ?>"
                        data-room="<?php echo htmlspecialchars($room16590); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16590); ?>"
                        data-image="<?php echo base64_encode($upload_img16590); ?>"
                        data-category="<?php echo htmlspecialchars($category16590); ?>"
                        data-status="<?php echo htmlspecialchars($status16590); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16590); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16590); ?>; 
                        position:absolute; top:396px; left:671px;'>
                    </div>

                    <!-- ASSET 16591 -->
                    <img src='../image.php?id=16591'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16591'
                        onclick='fetchAssetData(16591);' class="asset-image" data-id="<?php echo $assetId16591; ?>"
                        data-room="<?php echo htmlspecialchars($room16591); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16591); ?>"
                        data-image="<?php echo base64_encode($upload_img16591); ?>"
                        data-status="<?php echo htmlspecialchars($status16591); ?>"
                        data-category="<?php echo htmlspecialchars($category16591); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16591); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16591); ?>; 
                        position:absolute; top:409px; left:671px;'>
                    </div>

                    <!-- ASSET 16592 -->
                    <img src='../image.php?id=16592'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16592'
                        onclick='fetchAssetData(16592);' class="asset-image" data-id="<?php echo $assetId16592; ?>"
                        data-room="<?php echo htmlspecialchars($room16592); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16592); ?>"
                        data-image="<?php echo base64_encode($upload_img16592); ?>"
                        data-status="<?php echo htmlspecialchars($status16592); ?>"
                        data-category="<?php echo htmlspecialchars($category16592); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16592); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16592); ?>; 
                        position:absolute; top:422px; left:671px;'>
                    </div>

                    <!-- ASSET 16543 -->
                    <img src='../image.php?id=16543'
                        style='width:18px; cursor:pointer; position:absolute; top:360px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16543'
                        onclick='fetchAssetData(16543);' class="asset-image" data-id="<?php echo $assetId16543; ?>"
                        data-room="<?php echo htmlspecialchars($room16543); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16543); ?>"
                        data-image="<?php echo base64_encode($upload_img16543); ?>"
                        data-status="<?php echo htmlspecialchars($status16543); ?>"
                        data-category="<?php echo htmlspecialchars($category16543); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16543); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16543); ?>; 
                        position:absolute; top:370px; left:694px;'>
                    </div>


                    <!-- ASSET 16544 -->
                    <img src='../image.php?id=16544'
                        style='width:18px; cursor:pointer; position:absolute; top:373px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16544'
                        onclick='fetchAssetData(16544);' class="asset-image" data-id="<?php echo $assetId16544; ?>"
                        data-room="<?php echo htmlspecialchars($room16544); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16544); ?>"
                        data-status="<?php echo htmlspecialchars($status16544); ?>"
                        data-image="<?php echo base64_encode($upload_img16544); ?>"
                        data-category="<?php echo htmlspecialchars($category16544); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16544); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16544); ?>; 
                        position:absolute; top:383px; left:694px;'>
                    </div>

                    <!-- ASSET 16545 -->
                    <img src='../image.php?id=16545'
                        style='width:18px; cursor:pointer; position:absolute; top:386px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16545'
                        onclick='fetchAssetData(16545);' class="asset-image" data-id="<?php echo $assetId16545; ?>"
                        data-room="<?php echo htmlspecialchars($room16545); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16545); ?>"
                        data-image="<?php echo base64_encode($upload_img16545); ?>"
                        data-status="<?php echo htmlspecialchars($status16545); ?>"
                        data-category="<?php echo htmlspecialchars($category16545); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16545); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16545); ?>; 
                        position:absolute; top:396px; left:694px;'>
                    </div>

                    <!-- ASSET 16546 -->
                    <img src='../image.php?id=16546'
                        style='width:18px; cursor:pointer; position:absolute; top:399px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16546'
                        onclick='fetchAssetData(16546);' class="asset-image" data-id="<?php echo $assetId16546; ?>"
                        data-room="<?php echo htmlspecialchars($room16546); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16546); ?>"
                        data-image="<?php echo base64_encode($upload_img16546); ?>"
                        data-category="<?php echo htmlspecialchars($category16546); ?>"
                        data-status="<?php echo htmlspecialchars($status16546); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16546); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16546); ?>; 
                        position:absolute; top:409px; left:694px;'>
                    </div>

                    <!-- ASSET 16547 -->
                    <img src='../image.php?id=16547'
                        style='width:18px; cursor:pointer; position:absolute; top:412px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16547'
                        onclick='fetchAssetData(16547);' class="asset-image" data-id="<?php echo $assetId16547; ?>"
                        data-room="<?php echo htmlspecialchars($room16547); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16547); ?>"
                        data-image="<?php echo base64_encode($upload_img16547); ?>"
                        data-category="<?php echo htmlspecialchars($category16547); ?>"
                        data-status="<?php echo htmlspecialchars($status16547); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16547); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16547); ?>; 
                        position:absolute; top:422px; left:694px;'>
                    </div>

                    <!-- ASSET 16548 -->
                    <img src='../image.php?id=16548'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16548'
                        onclick='fetchAssetData(16548);' class="asset-image" data-id="<?php echo $assetId16548; ?>"
                        data-room="<?php echo htmlspecialchars($room16548); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16548); ?>"
                        data-image="<?php echo base64_encode($upload_img16548); ?>"
                        data-status="<?php echo htmlspecialchars($status16548); ?>"
                        data-category="<?php echo htmlspecialchars($category16548); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16548); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16548); ?>; 
                        position:absolute; top:455px; left:599px;'>
                    </div>

                    <!-- ASSET 16549 -->
                    <img src='../image.php?id=16549'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16549'
                        onclick='fetchAssetData(16549);' class="asset-image" data-id="<?php echo $assetId16549; ?>"
                        data-room="<?php echo htmlspecialchars($room16549); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16549); ?>"
                        data-image="<?php echo base64_encode($upload_img16549); ?>"
                        data-category="<?php echo htmlspecialchars($category16549); ?>"
                        data-status="<?php echo htmlspecialchars($status16549); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16549); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16549); ?>; 
                        position:absolute; top:468px; left:599px;'>
                    </div>

                    <!-- ASSET 16550 -->
                    <img src='../image.php?id=16550'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16550'
                        onclick='fetchAssetData(16550);' class="asset-image" data-id="<?php echo $assetId16550; ?>"
                        data-room="<?php echo htmlspecialchars($room16550); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16550); ?>"
                        data-image="<?php echo base64_encode($upload_img16550); ?>"
                        data-status="<?php echo htmlspecialchars($status16550); ?>"
                        data-category="<?php echo htmlspecialchars($category16550); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16550); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16550); ?>; 
                        position:absolute; top:481px; left:599px;'>
                    </div>

                    <!-- ASSET 16551 -->
                    <img src='../image.php?id=16551'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16551'
                        onclick='fetchAssetData(16551);' class="asset-image" data-id="<?php echo $assetId16551; ?>"
                        data-room="<?php echo htmlspecialchars($room16551); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16551); ?>"
                        data-status="<?php echo htmlspecialchars($status16551); ?>"
                        data-image="<?php echo base64_encode($upload_img16551); ?>"
                        data-category="<?php echo htmlspecialchars($category16551); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16551); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16551); ?>; 
                        position:absolute; top:494px; left:599px;'>
                    </div>

                    <!-- ASSET 16552 -->
                    <img src='../image.php?id=16552'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:603px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16552'
                        onclick='fetchAssetData(16552);' class="asset-image" data-id="<?php echo $assetId16552; ?>"
                        data-room="<?php echo htmlspecialchars($room16552); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16552); ?>"
                        data-image="<?php echo base64_encode($upload_img16552); ?>"
                        data-status="<?php echo htmlspecialchars($status16552); ?>"
                        data-category="<?php echo htmlspecialchars($category16552); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16552); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16552); ?>; 
                        position:absolute; top:507px; left:599px;'>
                    </div>

                    <!-- ASSET 16553 -->
                    <img src='../image.php?id=16553'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16553'
                        onclick='fetchAssetData(16553);' class="asset-image" data-id="<?php echo $assetId16553; ?>"
                        data-room="<?php echo htmlspecialchars($room16553); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16553); ?>"
                        data-image="<?php echo base64_encode($upload_img16553); ?>"
                        data-status="<?php echo htmlspecialchars($status16553); ?>"
                        data-category="<?php echo htmlspecialchars($category16553); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16553); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16553); ?>; 
                        position:absolute; top:455px; left:625px;'>
                    </div>

                    <!-- ASSET 16554 -->
                    <img src='../image.php?id=16554'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16554'
                        onclick='fetchAssetData(16554);' class="asset-image" data-id="<?php echo $assetId16554; ?>"
                        data-room="<?php echo htmlspecialchars($room16554); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16554); ?>"
                        data-image="<?php echo base64_encode($upload_img16554); ?>"
                        data-status="<?php echo htmlspecialchars($status16554); ?>"
                        data-category="<?php echo htmlspecialchars($category16554); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16554); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16554); ?>; 
                        position:absolute; top:468px; left:625px;'>
                    </div>

                    <!-- ASSET 16555 -->
                    <img src='../image.php?id=16555'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16555'
                        onclick='fetchAssetData(16555);' class="asset-image" data-id="<?php echo $assetId16555; ?>"
                        data-room="<?php echo htmlspecialchars($room16555); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16555); ?>"
                        data-image="<?php echo base64_encode($upload_img16555); ?>"
                        data-status="<?php echo htmlspecialchars($status16555); ?>"
                        data-category="<?php echo htmlspecialchars($category16555); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16555); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16555); ?>; 
                        position:absolute; top:481px; left:625px;'>
                    </div>

                    <!-- ASSET 16556 -->
                    <img src='../image.php?id=16556'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16556'
                        onclick='fetchAssetData(16556);' class="asset-image" data-id="<?php echo $assetId16556; ?>"
                        data-room="<?php echo htmlspecialchars($room16556); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16556); ?>"
                        data-image="<?php echo base64_encode($upload_img16556); ?>"
                        data-category="<?php echo htmlspecialchars($category16556); ?>"
                        data-status="<?php echo htmlspecialchars($status16556); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16556); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16556); ?>; 
                        position:absolute; top:494px; left:625px;'>
                    </div>

                    <!-- ASSET 16557 -->
                    <img src='../image.php?id=16557'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:629px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16557'
                        onclick='fetchAssetData(16557);' class="asset-image" data-id="<?php echo $assetId16557; ?>"
                        data-room="<?php echo htmlspecialchars($room16557); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16557); ?>"
                        data-image="<?php echo base64_encode($upload_img16557); ?>"
                        data-status="<?php echo htmlspecialchars($status16557); ?>"
                        data-category="<?php echo htmlspecialchars($category16557); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16557); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16557); ?>; 
                        position:absolute; top:507px; left:625px;'>
                    </div>

                    <!-- ASSET 16558 -->
                    <img src='../image.php?id=16558'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16558'
                        onclick='fetchAssetData(16558);' class="asset-image" data-id="<?php echo $assetId16558; ?>"
                        data-room="<?php echo htmlspecialchars($room16558); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16558); ?>"
                        data-image="<?php echo base64_encode($upload_img16558); ?>"
                        data-status="<?php echo htmlspecialchars($status16558); ?>"
                        data-category="<?php echo htmlspecialchars($category16558); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16558); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16558); ?>; 
                        position:absolute; top:455px; left:648px;'>
                    </div>

                    <!-- ASSET 16559 -->
                    <img src='../image.php?id=16559'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16559'
                        onclick='fetchAssetData(16559);' class="asset-image" data-id="<?php echo $assetId16559; ?>"
                        data-room="<?php echo htmlspecialchars($room16559); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16559); ?>"
                        data-status="<?php echo htmlspecialchars($status16559); ?>"
                        data-image="<?php echo base64_encode($upload_img16559); ?>"
                        data-category="<?php echo htmlspecialchars($category16559); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16559); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16559); ?>; 
                        position:absolute; top:468px; left:648px;'>
                    </div>


                    <!-- ASSET 16560 -->
                    <img src='../image.php?id=16560'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16560'
                        onclick='fetchAssetData(16560);' class="asset-image" data-id="<?php echo $assetId16560; ?>"
                        data-room="<?php echo htmlspecialchars($room16560); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16560); ?>"
                        data-image="<?php echo base64_encode($upload_img16560); ?>"
                        data-status="<?php echo htmlspecialchars($status16560); ?>"
                        data-category="<?php echo htmlspecialchars($category16560); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16560); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16560); ?>; 
                        position:absolute; top:481px; left:648px;'>
                    </div>

                    <!-- ASSET 16561 -->
                    <img src='../image.php?id=16561'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16561'
                        onclick='fetchAssetData(16561);' class="asset-image" data-id="<?php echo $assetId16561; ?>"
                        data-room="<?php echo htmlspecialchars($room16561); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16561); ?>"
                        data-status="<?php echo htmlspecialchars($status16561); ?>"
                        data-image="<?php echo base64_encode($upload_img16561); ?>"
                        data-category="<?php echo htmlspecialchars($category16561); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16561); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16561); ?>; 
                        position:absolute; top:494px; left:648px;'>
                    </div>

                    <!-- ASSET 16562 -->
                    <img src='../image.php?id=16562'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:652px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16562'
                        onclick='fetchAssetData(16562);' class="asset-image" data-id="<?php echo $assetId16562; ?>"
                        data-room="<?php echo htmlspecialchars($room16562); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16562); ?>"
                        data-image="<?php echo base64_encode($upload_img16562); ?>"
                        data-status="<?php echo htmlspecialchars($status16562); ?>"
                        data-category="<?php echo htmlspecialchars($category16562); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16562); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16562); ?>; 
                        position:absolute; top:507px; left:648px;'>
                    </div>

                    <!-- ASSET 16563 -->
                    <img src='../image.php?id=16563'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16563'
                        onclick='fetchAssetData(16563);' class="asset-image" data-id="<?php echo $assetId16563; ?>"
                        data-room="<?php echo htmlspecialchars($room16563); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16563); ?>"
                        data-image="<?php echo base64_encode($upload_img16563); ?>"
                        data-status="<?php echo htmlspecialchars($status16563); ?>"
                        data-category="<?php echo htmlspecialchars($category16563); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16563); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16563); ?>; 
                        position:absolute; top:455px; left:671px;'>
                    </div>

                    <!-- ASSET 16564 -->
                    <img src='../image.php?id=16564'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16564'
                        onclick='fetchAssetData(16564);' class="asset-image" data-id="<?php echo $assetId16564; ?>"
                        data-room="<?php echo htmlspecialchars($room16564); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16564); ?>"
                        data-image="<?php echo base64_encode($upload_img16564); ?>"
                        data-status="<?php echo htmlspecialchars($status16564); ?>"
                        data-category="<?php echo htmlspecialchars($category16564); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16564); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16564); ?>; 
                        position:absolute; top:468px; left:671px;'>
                    </div>

                    <!-- ASSET 16565 -->
                    <img src='../image.php?id=16565'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16565'
                        onclick='fetchAssetData(16565);' class="asset-image" data-id="<?php echo $assetId16565; ?>"
                        data-room="<?php echo htmlspecialchars($room16565); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16565); ?>"
                        data-image="<?php echo base64_encode($upload_img16565); ?>"
                        data-status="<?php echo htmlspecialchars($status16565); ?>"
                        data-category="<?php echo htmlspecialchars($category16565); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16565); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16565); ?>; 
                        position:absolute; top:481px; left:671px;'>
                    </div>

                    <!-- ASSET 16566-->
                    <img src='../image.php?id=16566'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16566'
                        onclick='fetchAssetData(16566);' class="asset-image" data-id="<?php echo $assetId16566; ?>"
                        data-room="<?php echo htmlspecialchars($room16566); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16566); ?>"
                        data-image="<?php echo base64_encode($upload_img16566); ?>"
                        data-status="<?php echo htmlspecialchars($status16566); ?>"
                        data-category="<?php echo htmlspecialchars($category16566); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16566); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16566); ?>; 
                        position:absolute; top:494px; left:671px;'>
                    </div>

                    <!-- ASSET 16567 -->
                    <img src='../image.php?id=16567'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:675px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16567'
                        onclick='fetchAssetData(16567);' class="asset-image" data-id="<?php echo $assetId16567; ?>"
                        data-room="<?php echo htmlspecialchars($room16567); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16567); ?>"
                        data-image="<?php echo base64_encode($upload_img16567); ?>"
                        data-status="<?php echo htmlspecialchars($status16567); ?>"
                        data-category="<?php echo htmlspecialchars($category16567); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16567); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16567); ?>; 
                        position:absolute; top:507px; left:671px;'>
                    </div>

                    <!-- ASSET 16568 -->
                    <img src='../image.php?id=16568'
                        style='width:18px; cursor:pointer; position:absolute; top:445px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16568'
                        onclick='fetchAssetData(16568);' class="asset-image" data-id="<?php echo $assetId16568; ?>"
                        data-room="<?php echo htmlspecialchars($room16568); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16568); ?>"
                        data-image="<?php echo base64_encode($upload_img16568); ?>"
                        data-status="<?php echo htmlspecialchars($status16568); ?>"
                        data-category="<?php echo htmlspecialchars($category16568); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16568); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16568); ?>; 
                        position:absolute; top:455px; left:694px;'>
                    </div>

                    <!-- ASSET 16569 -->
                    <img src='../image.php?id=16569'
                        style='width:18px; cursor:pointer; position:absolute; top:458px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16569'
                        onclick='fetchAssetData(16569);' class="asset-image" data-id="<?php echo $assetId16569; ?>"
                        data-room="<?php echo htmlspecialchars($room16569); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16569); ?>"
                        data-image="<?php echo base64_encode($upload_img16569); ?>"
                        data-status="<?php echo htmlspecialchars($status16569); ?>"
                        data-category="<?php echo htmlspecialchars($category16569); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16569); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16569); ?>; 
                        position:absolute; top:468px; left:694px;'>
                    </div>

                    <!-- ASSET 16570 -->
                    <img src='../image.php?id=16570'
                        style='width:18px; cursor:pointer; position:absolute; top:471px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16570'
                        onclick='fetchAssetData(16570);' class="asset-image" data-id="<?php echo $assetId16570; ?>"
                        data-room="<?php echo htmlspecialchars($room16570); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16570); ?>"
                        data-image="<?php echo base64_encode($upload_img16570); ?>"
                        data-status="<?php echo htmlspecialchars($status16570); ?>"
                        data-category="<?php echo htmlspecialchars($category16570); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16570); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16570); ?>; 
                        position:absolute; top:481px; left:694px;'>
                    </div>

                    <!-- ASSET 16571 -->
                    <img src='../image.php?id=16571'
                        style='width:18px; cursor:pointer; position:absolute; top:484px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16571'
                        onclick='fetchAssetData(16571);' class="asset-image" data-id="<?php echo $assetId16571; ?>"
                        data-room="<?php echo htmlspecialchars($room16571); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16571); ?>"
                        data-status="<?php echo htmlspecialchars($status16571); ?>"
                        data-image="<?php echo base64_encode($upload_img16571); ?>"
                        data-category="<?php echo htmlspecialchars($category16571); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16571); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16571); ?>; 
                        position:absolute; top:494px; left:694px;'>
                    </div>

                    <!-- ASSET 16572 -->
                    <img src='../image.php?id=16572'
                        style='width:18px; cursor:pointer; position:absolute; top:497px; left:698px; transform: rotate(180deg);'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16572'
                        onclick='fetchAssetData(16572);' class="asset-image" data-id="<?php echo $assetId16572; ?>"
                        data-room="<?php echo htmlspecialchars($room16572); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16572); ?>"
                        data-image="<?php echo base64_encode($upload_img16572); ?>"
                        data-status="<?php echo htmlspecialchars($status16572); ?>"
                        data-category="<?php echo htmlspecialchars($category16572); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16572); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16572); ?>; 
                        position:absolute; top:507px; left:694px;'>
                    </div>



                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>
                </div>

            </div>
            </div>
            </div>
            </div>
            </div>
        </main>
        <?php

                // Function to generate modal structure for a given asset
                function generateModal($assetId, $room, $floor, $upload_img, $status, $category, $assignedName, $assignedBy, $description)
                {
                    ?>
                    <!-- Modal structure for asset with ID <?php echo $assetId; ?> -->
                    <div class='modal fade' id='imageModal<?php echo $assetId; ?>' tabindex='-1'
                        aria-labelledby='imageModalLabel<?php echo $assetId; ?>' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal'
                                        aria-label='Close'></button>
                                </div>
                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId); ?>">
                                        <!--START DIV FOR IMAGE -->
                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img); ?>"
                                                alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->
                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId"
                                                value="<?php echo htmlspecialchars($assetId); ?>" readonly />
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date"
                                                value="<?php echo htmlspecialchars($date); ?>" readonly />
                                        </div>
                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room"
                                                value="<?php echo htmlspecialchars($room); ?>" readonly />
                                        </div>
                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building"
                                                name="building" value="<?php echo htmlspecialchars($building); ?>"
                                                readonly />
                                        </div>
                                        <!--End of Second Row-->
                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor"
                                                value="<?php echo htmlspecialchars($floor); ?>" readonly />
                                        </div>
                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category"
                                                name="category" value="<?php echo htmlspecialchars($category); ?>"
                                                readonly />
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="images" class="form-label">Images:</label>
                                            <input type="text" class="form-control" id="" name="images" readonly />
                                        </div>
                                        <!--End of Third Row-->
                                        <!--Fourth Row-->
                                        <div class="col-2 ">
                                            <label for="status" class="form-label">Status:</label>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="status" name="status">
                                                <option value="Working" <?php echo ($status == 'Working') ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status == 'Under Maintenance') ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status == 'For Replacement') ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status == 'Need Repair') ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName"
                                                value="<?php echo htmlspecialchars($assignedName); ?>" readonly />
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                                value="<?php echo htmlspecialchars($assignedBy); ?>" readonly />
                                        </div>
                                        <!--End of Fourth Row-->
                                        <!--Fifth Row-->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description"
                                                value="<?php echo htmlspecialchars($description); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->
                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-9">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                        </div>
                                        <!--End of Sixth Row-->
                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                                data-bs-target="#staticBackdrop<?php echo $assetId; ?>">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table <?php echo $assetId; ?>-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop<?php echo $assetId; ?>" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn"
                                                name="edit<?php echo $assetId; ?>">Yes</button>
                                            <button type="button" class="btn close-popups"
                                                data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php
                }

                // Call the generateModal function for each asset
                foreach ($assetIds as $id) {
                    generateModal($id, ${'room' . $id}, ${'floor' . $id}, ${'upload_img' . $id}, ${'status' . $id}, ${'category' . $id}, ${'assignedName' . $id}, ${'assignedBy' . $id}, ${'description' . $id});
                }
                ?>
    </section>
    <!--Start of JS Hover-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const assetImages = document.querySelectorAll('.asset-image');
            const hoverElement = document.getElementById('hover-asset');

            assetImages.forEach(image => {
                image.addEventListener('mouseenter', function () {
                    const id = this.dataset.id;
                    const room = this.dataset.room;
                    const floor = this.dataset.floor;
                    const base64Data = this.dataset.image;
                    const category = this.dataset.category; // Get the category from the data attribute
                    const assignedName = this.dataset.assignedname; // Add this line to get the assignedName from the data attribute

                    let imageHTML = '';
                    if (base64Data && base64Data.trim() !== '') {
                        const imageSrc = "data:image/jpeg;base64," + base64Data;
                        imageHTML = `<img src="${imageSrc}" alt="Asset Image">`;
                    } else {
                        imageHTML = '<p class="NoImage">No Image uploaded</p>';
                    }

                    // Update hover element's content
                    hoverElement.innerHTML = `
                    <div class="top-side-hover">
                        <div class="center-content-hover">
                            ${imageHTML}
                        </div>
                        <input type="text" class="form-control input-hover" id="category-hover" value="${category}" readonly />
                    </div>

                    <div class="hover-location">

                        <div class ="hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover">Tracking #:</label>
                            <input type="text" class="form-control input-hover1 hover-input" id="assetId" value="${id}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Room:</label>
                            <input type="text" class="form-control input-hover1 room-hover" id="room" value="${room}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Floor:</label>
                            <input type="text" class="form-control input-hover1" id="floor" value="${floor}" readonly />
                        </div>

                    ${assignedName && assignedName.trim() !== '' ? `
                        <div>
                            <label for="assignedNameHover${id}" class="form-label TrackingHover">Assigned To:</label>
                            <input type="text" class="form-control input-hover1" id="assignedName" value="${assignedName}" readonly />
                        </div>
                     ` : ''
                        }
                    </div>
            `;

                    // Show hover element
                    hoverElement.style.display = 'block';
                });

                image.addEventListener('mouseleave', function () {
                    // Hide hover element
                    hoverElement.style.display = 'none';
                });
            });
        });


    </script>
    <script>
        $(document).ready(function () {
            $('.notification-item').on('click', function (e) {
                e.preventDefault();
                var activityId = $(this).data('activity-id');
                var notificationItem = $(this); // Store the clicked element

                $.ajax({
                    type: "POST",
                    url: "update_single_notification.php", // The URL to the PHP file
                    data: {
                        activityId: activityId
                    },
                    success: function (response) {
                        if (response.trim() === "Notification updated successfully") {
                            // If the notification is updated successfully, remove the clicked element
                            notificationItem.remove();

                            // Update the notification count
                            var countElement = $('#noti_number');
                            var count = parseInt(countElement.text()) || 0;
                            countElement.text(count > 1 ? count - 1 : '');
                        } else {
                            // Handle error
                            console.error("Failed to update notification:", response);
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle AJAX error
                        console.error("AJAX error:", status, error);
                    }
                });
            });
        });
    </script>

    <!-- Start of JS Hover -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const assetImages = document.querySelectorAll('.asset-image');
            const hoverElement = document.getElementById('hover-asset');

            assetImages.forEach(image => {
                image.addEventListener('mouseenter', function () {
                    const id = this.dataset.id;
                    const room = this.dataset.room;
                    const floor = this.dataset.floor;
                    const base64Data = this.dataset.image;
                    const category = this.dataset.category; // Get the category from the data attribute
                    const assignedName = this.dataset.assignedname; // Add this line to get the assignedName from the data attribute

                    let imageHTML = '';
                    if (base64Data && base64Data.trim() !== '') {
                        const imageSrc = "data:image/jpeg;base64," + base64Data;
                        imageHTML = `<img src="${imageSrc}" alt="Asset Image">`;
                    } else {
                        imageHTML = '<p class="NoImage">No Image uploaded</p>';
                    }

                    // Update hover element's content
                    hoverElement.innerHTML = `
                    <div class="top-side-hover">
                        <div class="center-content-hover">
                            ${imageHTML}
                        </div>
                        <input type="text" class="form-control input-hover" id="category-hover" value="${category}" readonly />
                    </div>

                    <div class="hover-location">

                        <div class ="hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover">Tracking #:</label>
                            <input type="text" class="form-control input-hover1 hover-input" id="assetId" value="${id}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Room:</label>
                            <input type="text" class="form-control input-hover1 room-hover" id="room" value="${room}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Floor:</label>
                            <input type="text" class="form-control input-hover1" id="floor" value="${floor}" readonly />
                        </div>

                    ${assignedName && assignedName.trim() !== '' ? `
                        <div>
                            <label for="assignedNameHover${id}" class="form-label TrackingHover">Assigned To:</label>
                            <input type="text" class="form-control input-hover1" id="assignedName" value="${assignedName}" readonly />
                        </div>
                     ` : ''
                        }
                    </div>
            `;

                    // Show hover element
                    hoverElement.style.display = 'block';
                });

                image.addEventListener('mouseleave', function () {
                    // Hide hover element
                    hoverElement.style.display = 'none';
                });
            });
        });


    </script>


    <script>
        $(document).ready(function () {
            var urlParams = new URLSearchParams(window.location.search);
            var assetId = urlParams.get('assetId'); // Get the assetId from the URL

            if (assetId) {
                var modalId = '#imageModal' + assetId;
                $(modalId).modal('show'); // Open the modal with the corresponding ID
            }
        });
    </script>
    <!-- FOR LEGEND FILTER -->
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const legendItems = document.querySelectorAll('.legend-item button');
            let activeStatuses = []; // Keep track of active statuses

            legendItems.forEach(item => {
                item.addEventListener('click', function () {
                    const legendItem = this.closest('.legend-item');
                    const status = legendItem.getAttribute('data-status');
                    // Toggle the active status in the array
                    const isActive = activeStatuses.includes(status);
                    if (isActive) {
                        // Remove the status if it's already active
                        activeStatuses = activeStatuses.filter(s => s !== status);
                    } else {
                        // Add the status if it's not already active
                        activeStatuses.push(status);
                    }
                    // Toggle visibility of assets
                    toggleAssetVisibility(status);
                    // Update the opacity of legend items
                    updateLegendItems();
                });
            });

            function toggleAssetVisibility(status) {
                const assets = document.querySelectorAll(`.asset-image[data-status="${status}"]`);
                assets.forEach(asset => {
                    const isHidden = asset.classList.contains('hidden-asset');
                    const statusIndicator = asset.nextElementSibling;

                    if (isHidden) {
                        asset.classList.remove('hidden-asset');
                        if (statusIndicator) {
                            statusIndicator.classList.remove('hidden-asset');
                        }
                    } else {
                        asset.classList.add('hidden-asset');
                        if (statusIndicator) {
                            statusIndicator.classList.add('hidden-asset');
                        }
                    }
                });
            }

            function updateLegendItems() {
                // Update the opacity of all legend items based on activeStatuses
                const allLegendItems = document.querySelectorAll('.legend-item');
                allLegendItems.forEach(legendItem => {
                    const status = legendItem.getAttribute('data-status');
                    if (activeStatuses.includes(status)) {
                        // If the status is active, change opacity to 50%
                        legendItem.style.opacity = '0.2';
                    } else {
                        // If the status is not active, revert opacity to 100%
                        legendItem.style.opacity = '1';
                    }
                });
            }
        });
    </script>

    <script src="../../../src/js/main.js"></script>
        <script src="../../../src/js/logoutMap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>