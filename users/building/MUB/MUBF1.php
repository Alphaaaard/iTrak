<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {
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

    //FOR ID 7086
    $sql7086 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7086";
    $stmt7086 = $conn->prepare($sql7086);
    $stmt7086->execute();
    $result7086 = $stmt7086->get_result();
    $row7086 = $result7086->fetch_assoc();
    $assetId7086 = $row7086['assetId'];
    $category7086 = $row7086['category'];
    $date7086 = $row7086['date'];
    $building7086 = $row7086['building'];
    $floor7086 = $row7086['floor'];
    $room7086 = $row7086['room'];
    $status7086 = $row7086['status'];
    $assignedName7086 = $row7086['assignedName'];
    $assignedBy7086 = $row7086['assignedBy'];
    $upload_img7086 = $row7086['upload_img'];
    $description7086 = $row7086['description'];

    //FOR ID 7087
    $sql7087 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7087";
    $stmt7087 = $conn->prepare($sql7087);
    $stmt7087->execute();
    $result7087 = $stmt7087->get_result();
    $row7087 = $result7087->fetch_assoc();
    $assetId7087 = $row7087['assetId'];
    $category7087 = $row7087['category'];
    $date7087 = $row7087['date'];
    $building7087 = $row7087['building'];
    $floor7087 = $row7087['floor'];
    $room7087 = $row7087['room'];
    $status7087 = $row7087['status'];
    $assignedName7087 = $row7087['assignedName'];
    $assignedBy7087 = $row7087['assignedBy'];
    $upload_img7087 = $row7087['upload_img'];
    $description7087 = $row7087['description'];

    //FOR ID 7088
    $sql7088 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7088";
    $stmt7088 = $conn->prepare($sql7088);
    $stmt7088->execute();
    $result7088 = $stmt7088->get_result();
    $row7088 = $result7088->fetch_assoc();
    $assetId7088 = $row7088['assetId'];
    $category7088 = $row7088['category'];
    $date7088 = $row7088['date'];
    $building7088 = $row7088['building'];
    $floor7088 = $row7088['floor'];
    $room7088 = $row7088['room'];
    $status7088 = $row7088['status'];
    $assignedName7088 = $row7088['assignedName'];
    $assignedBy7088 = $row7088['assignedBy'];
    $upload_img7088 = $row7088['upload_img'];
    $description7088 = $row7088['description'];

    //FOR ID 7089
    $sql7089 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7089";
    $stmt7089 = $conn->prepare($sql7089);
    $stmt7089->execute();
    $result7089 = $stmt7089->get_result();
    $row7089 = $result7089->fetch_assoc();
    $assetId7089 = $row7089['assetId'];
    $category7089 = $row7089['category'];
    $date7089 = $row7089['date'];
    $building7089 = $row7089['building'];
    $floor7089 = $row7089['floor'];
    $room7089 = $row7089['room'];
    $status7089 = $row7089['status'];
    $assignedName7089 = $row7089['assignedName'];
    $assignedBy7089 = $row7089['assignedBy'];
    $upload_img7089 = $row7089['upload_img'];
    $description7089 = $row7089['description'];

    //FOR ID 7090
    $sql7090 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7090";
    $stmt7090 = $conn->prepare($sql7090);
    $stmt7090->execute();
    $result7090 = $stmt7090->get_result();
    $row7090 = $result7090->fetch_assoc();
    $assetId7090 = $row7090['assetId'];
    $category7090 = $row7090['category'];
    $date7090 = $row7090['date'];
    $building7090 = $row7090['building'];
    $floor7090 = $row7090['floor'];
    $room7090 = $row7090['room'];
    $status7090 = $row7090['status'];
    $assignedName7090 = $row7090['assignedName'];
    $assignedBy7090 = $row7090['assignedBy'];
    $upload_img7090 = $row7090['upload_img'];
    $description7090 = $row7090['description'];

    //FOR ID 7091
    $sql7091 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7091";
    $stmt7091 = $conn->prepare($sql7091);
    $stmt7091->execute();
    $result7091 = $stmt7091->get_result();
    $row7091 = $result7091->fetch_assoc();
    $assetId7091 = $row7091['assetId'];
    $category7091 = $row7091['category'];
    $date7091 = $row7091['date'];
    $building7091 = $row7091['building'];
    $floor7091 = $row7091['floor'];
    $room7091 = $row7091['room'];
    $status7091 = $row7091['status'];
    $assignedName7091 = $row7091['assignedName'];
    $assignedBy7091 = $row7091['assignedBy'];
    $upload_img7091 = $row7091['upload_img'];
    $description7091 = $row7091['description'];

    //FOR ID 7092
    $sql7092 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7092";
    $stmt7092 = $conn->prepare($sql7092);
    $stmt7092->execute();
    $result7092 = $stmt7092->get_result();
    $row7092 = $result7092->fetch_assoc();
    $assetId7092 = $row7092['assetId'];
    $category7092 = $row7092['category'];
    $date7092 = $row7092['date'];
    $building7092 = $row7092['building'];
    $floor7092 = $row7092['floor'];
    $room7092 = $row7092['room'];
    $status7092 = $row7092['status'];
    $assignedName7092 = $row7092['assignedName'];
    $assignedBy7092 = $row7092['assignedBy'];
    $upload_img7092 = $row7092['upload_img'];
    $description7092 = $row7092['description'];

    //FOR ID 7093
    $sql7093 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7093";
    $stmt7093 = $conn->prepare($sql7093);
    $stmt7093->execute();
    $result7093 = $stmt7093->get_result();
    $row7093 = $result7093->fetch_assoc();
    $assetId7093 = $row7093['assetId'];
    $category7093 = $row7093['category'];
    $date7093 = $row7093['date'];
    $building7093 = $row7093['building'];
    $floor7093 = $row7093['floor'];
    $room7093 = $row7093['room'];
    $status7093 = $row7093['status'];
    $assignedName7093 = $row7093['assignedName'];
    $assignedBy7093 = $row7093['assignedBy'];
    $upload_img7093 = $row7093['upload_img'];
    $description7093 = $row7093['description'];

    //FOR ID 7094
    $sql7094 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7094";
    $stmt7094 = $conn->prepare($sql7094);
    $stmt7094->execute();
    $result7094 = $stmt7094->get_result();
    $row7094 = $result7094->fetch_assoc();
    $assetId7094 = $row7094['assetId'];
    $category7094 = $row7094['category'];
    $date7094 = $row7094['date'];
    $building7094 = $row7094['building'];
    $floor7094 = $row7094['floor'];
    $room7094 = $row7094['room'];
    $status7094 = $row7094['status'];
    $assignedName7094 = $row7094['assignedName'];
    $assignedBy7094 = $row7094['assignedBy'];
    $upload_img7094 = $row7094['upload_img'];
    $description7094 = $row7094['description'];

    //FOR ID 7095
    $sql7095 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7095";
    $stmt7095 = $conn->prepare($sql7095);
    $stmt7095->execute();
    $result7095 = $stmt7095->get_result();
    $row7095 = $result7095->fetch_assoc();
    $assetId7095 = $row7095['assetId'];
    $category7095 = $row7095['category'];
    $date7095 = $row7095['date'];
    $building7095 = $row7095['building'];
    $floor7095 = $row7095['floor'];
    $room7095 = $row7095['room'];
    $status7095 = $row7095['status'];
    $assignedName7095 = $row7095['assignedName'];
    $assignedBy7095 = $row7095['assignedBy'];
    $upload_img7095 = $row7095['upload_img'];
    $description7095 = $row7095['description'];

    //FOR ID 7096
    $sql7096 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7096";
    $stmt7096 = $conn->prepare($sql7096);
    $stmt7096->execute();
    $result7096 = $stmt7096->get_result();
    $row7096 = $result7096->fetch_assoc();
    $assetId7096 = $row7096['assetId'];
    $category7096 = $row7096['category'];
    $date7096 = $row7096['date'];
    $building7096 = $row7096['building'];
    $floor7096 = $row7096['floor'];
    $room7096 = $row7096['room'];
    $status7096 = $row7096['status'];
    $assignedName7096 = $row7096['assignedName'];
    $assignedBy7096 = $row7096['assignedBy'];
    $upload_img7096 = $row7096['upload_img'];
    $description7096 = $row7096['description'];

    //FOR ID 7097
    $sql7097 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7097";
    $stmt7097 = $conn->prepare($sql7097);
    $stmt7097->execute();
    $result7097 = $stmt7097->get_result();
    $row7097 = $result7097->fetch_assoc();
    $assetId7097 = $row7097['assetId'];
    $category7097 = $row7097['category'];
    $date7097 = $row7097['date'];
    $building7097 = $row7097['building'];
    $floor7097 = $row7097['floor'];
    $room7097 = $row7097['room'];
    $status7097 = $row7097['status'];
    $assignedName7097 = $row7097['assignedName'];
    $assignedBy7097 = $row7097['assignedBy'];
    $upload_img7097 = $row7097['upload_img'];
    $description7097 = $row7097['description'];

    //FOR ID 7080
    $sql7080 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7080";
    $stmt7080 = $conn->prepare($sql7080);
    $stmt7080->execute();
    $result7080 = $stmt7080->get_result();
    $row7080 = $result7080->fetch_assoc();
    $assetId7080 = $row7080['assetId'];
    $category7080 = $row7080['category'];
    $date7080 = $row7080['date'];
    $building7080 = $row7080['building'];
    $floor7080 = $row7080['floor'];
    $room7080 = $row7080['room'];
    $status7080 = $row7080['status'];
    $assignedName7080 = $row7080['assignedName'];
    $assignedBy7080 = $row7080['assignedBy'];
    $upload_img7080 = $row7080['upload_img'];
    $description7080 = $row7080['description'];

    //FOR ID 7081
    $sql7081 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7081";
    $stmt7081 = $conn->prepare($sql7081);
    $stmt7081->execute();
    $result7081 = $stmt7081->get_result();
    $row7081 = $result7081->fetch_assoc();
    $assetId7081 = $row7081['assetId'];
    $category7081 = $row7081['category'];
    $date7081 = $row7081['date'];
    $building7081 = $row7081['building'];
    $floor7081 = $row7081['floor'];
    $room7081 = $row7081['room'];
    $status7081 = $row7081['status'];
    $assignedName7081 = $row7081['assignedName'];
    $assignedBy7081 = $row7081['assignedBy'];
    $upload_img7081 = $row7081['upload_img'];
    $description7081 = $row7081['description'];

    //FOR ID 7082
    $sql7082 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7082";
    $stmt7082 = $conn->prepare($sql7082);
    $stmt7082->execute();
    $result7082 = $stmt7082->get_result();
    $row7082 = $result7082->fetch_assoc();
    $assetId7082 = $row7082['assetId'];
    $category7082 = $row7082['category'];
    $date7082 = $row7082['date'];
    $building7082 = $row7082['building'];
    $floor7082 = $row7082['floor'];
    $room7082 = $row7082['room'];
    $status7082 = $row7082['status'];
    $assignedName7082 = $row7082['assignedName'];
    $assignedBy7082 = $row7082['assignedBy'];
    $upload_img7082 = $row7082['upload_img'];
    $description7082 = $row7082['description'];

    //FOR ID 7083
    $sql7083 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7083";
    $stmt7083 = $conn->prepare($sql7083);
    $stmt7083->execute();
    $result7083 = $stmt7083->get_result();
    $row7083 = $result7083->fetch_assoc();
    $assetId7083 = $row7083['assetId'];
    $category7083 = $row7083['category'];
    $date7083 = $row7083['date'];
    $building7083 = $row7083['building'];
    $floor7083 = $row7083['floor'];
    $room7083 = $row7083['room'];
    $status7083 = $row7083['status'];
    $assignedName7083 = $row7083['assignedName'];
    $assignedBy7083 = $row7083['assignedBy'];
    $upload_img7083 = $row7083['upload_img'];
    $description7083 = $row7083['description'];

    //FOR ID 7084
    $sql7084 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7084";
    $stmt7084 = $conn->prepare($sql7084);
    $stmt7084->execute();
    $result7084 = $stmt7084->get_result();
    $row7084 = $result7084->fetch_assoc();
    $assetId7084 = $row7084['assetId'];
    $category7084 = $row7084['category'];
    $date7084 = $row7084['date'];
    $building7084 = $row7084['building'];
    $floor7084 = $row7084['floor'];
    $room7084 = $row7084['room'];
    $status7084 = $row7084['status'];
    $assignedName7084 = $row7084['assignedName'];
    $assignedBy7084 = $row7084['assignedBy'];
    $upload_img7084 = $row7084['upload_img'];
    $description7084 = $row7084['description'];

    //FOR ID 7085
    $sql7085 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7085";
    $stmt7085 = $conn->prepare($sql7085);
    $stmt7085->execute();
    $result7085 = $stmt7085->get_result();
    $row7085 = $result7085->fetch_assoc();
    $assetId7085 = $row7085['assetId'];
    $category7085 = $row7085['category'];
    $date7085 = $row7085['date'];
    $building7085 = $row7085['building'];
    $floor7085 = $row7085['floor'];
    $room7085 = $row7085['room'];
    $status7085 = $row7085['status'];
    $assignedName7085 = $row7085['assignedName'];
    $assignedBy7085 = $row7085['assignedBy'];
    $upload_img7085 = $row7085['upload_img'];
    $description7085 = $row7085['description'];

    //FOR ID 7086
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7086'])) {
        // Get form data
        $assetId7086 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7086 = $_POST['status']; // Get the status from the form
        $description7086 = $_POST['description']; // Get the description from the form
        $room7086 = $_POST['room']; // Get the room from the form
        $assignedBy7086 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7086 = $status7086 === 'Need Repair' ? '' : $assignedName7086;

        // Prepare SQL query to update the asset
        $sql7086 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7086 = $conn->prepare($sql7086);
        $stmt7086->bind_param('sssssi', $status7086, $assignedName7086, $assignedBy7086, $description7086, $room7086, $assetId7086);

        if ($stmt7086->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7086 to $status7086.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7086->close();
    }

    //FOR ID 7087
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7087'])) {
        // Get form data
        $assetId7087 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7087 = $_POST['status']; // Get the status from the form
        $description7087 = $_POST['description']; // Get the description from the form
        $room7087 = $_POST['room']; // Get the room from the form
        $assignedBy7087 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7087 = $status7087 === 'Need Repair' ? '' : $assignedName7087;

        // Prepare SQL query to update the asset
        $sql7087 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7087 = $conn->prepare($sql7087);
        $stmt7087->bind_param('sssssi', $status7087, $assignedName7087, $assignedBy7087, $description7087, $room7087, $assetId7087);

        if ($stmt7087->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7087 to $status7087.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7087->close();
    }

    //FOR ID 7088
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7088'])) {
        // Get form data
        $assetId7088 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7088 = $_POST['status']; // Get the status from the form
        $description7088 = $_POST['description']; // Get the description from the form
        $room7088 = $_POST['room']; // Get the room from the form
        $assignedBy7088 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7088 = $status7088 === 'Need Repair' ? '' : $assignedName7088;

        // Prepare SQL query to update the asset
        $sql7088 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7088 = $conn->prepare($sql7088);
        $stmt7088->bind_param('sssssi', $status7088, $assignedName7088, $assignedBy7088, $description7088, $room7088, $assetId7088);

        if ($stmt7088->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7088 to $status7088.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7088->close();
    }

    //FOR ID 7089
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7089'])) {
        // Get form data
        $assetId7089 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7089 = $_POST['status']; // Get the status from the form
        $description7089 = $_POST['description']; // Get the description from the form
        $room7089 = $_POST['room']; // Get the room from the form
        $assignedBy7089 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7089 = $status7089 === 'Need Repair' ? '' : $assignedName7089;

        // Prepare SQL query to update the asset
        $sql7089 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7089 = $conn->prepare($sql7089);
        $stmt7089->bind_param('sssssi', $status7089, $assignedName7089, $assignedBy7089, $description7089, $room7089, $assetId7089);

        if ($stmt7089->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7089 to $status7089.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7089->close();
    }

    //FOR ID 7090
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7090'])) {
        // Get form data
        $assetId7090 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7090 = $_POST['status']; // Get the status from the form
        $description7090 = $_POST['description']; // Get the description from the form
        $room7090 = $_POST['room']; // Get the room from the form
        $assignedBy7090 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7090 = $status7090 === 'Need Repair' ? '' : $assignedName7090;

        // Prepare SQL query to update the asset
        $sql7090 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7090 = $conn->prepare($sql7090);
        $stmt7090->bind_param('sssssi', $status7090, $assignedName7090, $assignedBy7090, $description7090, $room7090, $assetId7090);

        if ($stmt7090->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7090 to $status7090.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7090->close();
    }

    //FOR ID 7091
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7091'])) {
        // Get form data
        $assetId7091 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7091 = $_POST['status']; // Get the status from the form
        $description7091 = $_POST['description']; // Get the description from the form
        $room7091 = $_POST['room']; // Get the room from the form
        $assignedBy7091 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7091 = $status7091 === 'Need Repair' ? '' : $assignedName7091;

        // Prepare SQL query to update the asset
        $sql7091 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7091 = $conn->prepare($sql7091);
        $stmt7091->bind_param('sssssi', $status7091, $assignedName7091, $assignedBy7091, $description7091, $room7091, $assetId7091);

        if ($stmt7091->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7091 to $status7091.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7091->close();
    }

    //FOR ID 7092
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7092'])) {
        // Get form data
        $assetId7092 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7092 = $_POST['status']; // Get the status from the form
        $description7092 = $_POST['description']; // Get the description from the form
        $room7092 = $_POST['room']; // Get the room from the form
        $assignedBy7092 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7092 = $status7092 === 'Need Repair' ? '' : $assignedName7092;

        // Prepare SQL query to update the asset
        $sql7092 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7092 = $conn->prepare($sql7092);
        $stmt7092->bind_param('sssssi', $status7092, $assignedName7092, $assignedBy7092, $description7092, $room7092, $assetId7092);

        if ($stmt7092->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7092 to $status7092.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7092->close();
    }

    //FOR ID 7093
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7093'])) {
        // Get form data
        $assetId7093 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7093 = $_POST['status']; // Get the status from the form
        $description7093 = $_POST['description']; // Get the description from the form
        $room7093 = $_POST['room']; // Get the room from the form
        $assignedBy7093 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7093 = $status7093 === 'Need Repair' ? '' : $assignedName7093;

        // Prepare SQL query to update the asset
        $sql7093 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7093 = $conn->prepare($sql7093);
        $stmt7093->bind_param('sssssi', $status7093, $assignedName7093, $assignedBy7093, $description7093, $room7093, $assetId7093);

        if ($stmt7093->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7093 to $status7093.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7093->close();
    }

    //FOR ID 7094
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7094'])) {
        // Get form data
        $assetId7094 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7094 = $_POST['status']; // Get the status from the form
        $description7094 = $_POST['description']; // Get the description from the form
        $room7094 = $_POST['room']; // Get the room from the form
        $assignedBy7094 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7094 = $status7094 === 'Need Repair' ? '' : $assignedName7094;

        // Prepare SQL query to update the asset
        $sql7094 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7094 = $conn->prepare($sql7094);
        $stmt7094->bind_param('sssssi', $status7094, $assignedName7094, $assignedBy7094, $description7094, $room7094, $assetId7094);

        if ($stmt7094->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7094 to $status7094.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7094->close();
    }

    //FOR ID 7095
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7095'])) {
        // Get form data
        $assetId7095 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7095 = $_POST['status']; // Get the status from the form
        $description7095 = $_POST['description']; // Get the description from the form
        $room7095 = $_POST['room']; // Get the room from the form
        $assignedBy7095 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7095 = $status7095 === 'Need Repair' ? '' : $assignedName7095;

        // Prepare SQL query to update the asset
        $sql7095 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7095 = $conn->prepare($sql7095);
        $stmt7095->bind_param('sssssi', $status7095, $assignedName7095, $assignedBy7095, $description7095, $room7095, $assetId7095);

        if ($stmt7095->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7095 to $status7095.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7095->close();
    }

    //FOR ID 7096
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7096'])) {
        // Get form data
        $assetId7096 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7096 = $_POST['status']; // Get the status from the form
        $description7096 = $_POST['description']; // Get the description from the form
        $room7096 = $_POST['room']; // Get the room from the form
        $assignedBy7096 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7096 = $status7096 === 'Need Repair' ? '' : $assignedName7096;

        // Prepare SQL query to update the asset
        $sql7096 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7096 = $conn->prepare($sql7096);
        $stmt7096->bind_param('sssssi', $status7096, $assignedName7096, $assignedBy7096, $description7096, $room7096, $assetId7096);

        if ($stmt7096->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7096 to $status7096.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7096->close();
    }

    //FOR ID 7097
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7097'])) {
        // Get form data
        $assetId7097 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7097 = $_POST['status']; // Get the status from the form
        $description7097 = $_POST['description']; // Get the description from the form
        $room7097 = $_POST['room']; // Get the room from the form
        $assignedBy7097 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7097 = $status7097 === 'Need Repair' ? '' : $assignedName7097;

        // Prepare SQL query to update the asset
        $sql7097 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7097 = $conn->prepare($sql7097);
        $stmt7097->bind_param('sssssi', $status7097, $assignedName7097, $assignedBy7097, $description7097, $room7097, $assetId7097);

        if ($stmt7097->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7097 to $status7097.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7097->close();
    }

    //FOR ID 7081
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7081'])) {
        // Get form data
        $assetId7081 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7081 = $_POST['status']; // Get the status from the form
        $description7081 = $_POST['description']; // Get the description from the form
        $room7081 = $_POST['room']; // Get the room from the form
        $assignedBy7081 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7081 = $status7081 === 'Need Repair' ? '' : $assignedName7081;

        // Prepare SQL query to update the asset
        $sql7081 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7081 = $conn->prepare($sql7081);
        $stmt7081->bind_param('sssssi', $status7081, $assignedName7081, $assignedBy7081, $description7081, $room7081, $assetId7081);

        if ($stmt7081->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7081 to $status7081.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7081->close();
    }

    //FOR ID 7080
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7080'])) {
        // Get form data
        $assetId7080 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7080 = $_POST['status']; // Get the status from the form
        $description7080 = $_POST['description']; // Get the description from the form
        $room7080 = $_POST['room']; // Get the room from the form
        $assignedBy7080 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7080 = $status7080 === 'Need Repair' ? '' : $assignedName7080;

        // Prepare SQL query to update the asset
        $sql7080 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7080 = $conn->prepare($sql7080);
        $stmt7080->bind_param('sssssi', $status7080, $assignedName7080, $assignedBy7080, $description7080, $room7080, $assetId7080);

        if ($stmt7080->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7080 to $status7080.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7080->close();
    }

    //FOR ID 7082
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7082'])) {
        // Get form data
        $assetId7082 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7082 = $_POST['status']; // Get the status from the form
        $description7082 = $_POST['description']; // Get the description from the form
        $room7082 = $_POST['room']; // Get the room from the form
        $assignedBy7082 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7082 = $status7082 === 'Need Repair' ? '' : $assignedName7082;

        // Prepare SQL query to update the asset
        $sql7082 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7082 = $conn->prepare($sql7082);
        $stmt7082->bind_param('sssssi', $status7082, $assignedName7082, $assignedBy7082, $description7082, $room7082, $assetId7082);

        if ($stmt7082->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7082 to $status7082.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7082->close();
    }

    //FOR ID 7083
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7083'])) {
        // Get form data
        $assetId7083 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7083 = $_POST['status']; // Get the status from the form
        $description7083 = $_POST['description']; // Get the description from the form
        $room7083 = $_POST['room']; // Get the room from the form
        $assignedBy7083 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7083 = $status7083 === 'Need Repair' ? '' : $assignedName7083;

        // Prepare SQL query to update the asset
        $sql7083 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7083 = $conn->prepare($sql7083);
        $stmt7083->bind_param('sssssi', $status7083, $assignedName7083, $assignedBy7083, $description7083, $room7083, $assetId7083);

        if ($stmt7083->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7083 to $status7083.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7083->close();
    }

    //FOR ID 7084
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7084'])) {
        // Get form data
        $assetId7084 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7084 = $_POST['status']; // Get the status from the form
        $description7084 = $_POST['description']; // Get the description from the form
        $room7084 = $_POST['room']; // Get the room from the form
        $assignedBy7084 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7084 = $status7084 === 'Need Repair' ? '' : $assignedName7084;

        // Prepare SQL query to update the asset
        $sql7084 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7084 = $conn->prepare($sql7084);
        $stmt7084->bind_param('sssssi', $status7084, $assignedName7084, $assignedBy7084, $description7084, $room7084, $assetId7084);

        if ($stmt7084->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7084 to $status7084.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MUBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7084->close();
    }

    //FOR ID 7085
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7085'])) {
        // Get form data
        $assetId7085 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7085 = $_POST['status']; // Get the status from the form
        $description7085 = $_POST['description']; // Get the description from the form
        $room7085 = $_POST['room']; // Get the room from the form
        $assignedBy7085 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7085 = $status7085 === 'Need Repair' ? '' : $assignedName7085;

        // Prepare SQL query to update the asset
        $sql7085 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7085 = $conn->prepare($sql7085);
        $stmt7085->bind_param('sssssi', $status7085, $assignedName7085, $assignedBy7085, $description7085, $room7085, $assetId7085);

        if ($stmt7085->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7085 to $status7085.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: MBUF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7085->close();
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
                header("Location: MUBF1.php");
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
        <title>Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/BEB/BEBF1.css" />
        <link rel="stylesheet" href="../../../src/css/map.css" />
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
                            <?php if ($unseenCount > 0) : ?>
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
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><i class="bi bi-person profile-icons"></i>Profile</a>
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
                <li>
                    <a href="../../administrator/gps.php">
                        <i class="bi bi-geo-alt"></i>
                        <span class="text">GPS</span>
                    </a>
                </li>
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
        <section id="content">
            <main>
                <div class="content-container" id="content-container">
                    <div id="belmonte-F1" class="content">
                        <!-- FLOOR PLAN -->
                        <img src="../../../src/floors/multipurpose/Multipurpose1F.png" alt="" class="Floor-container">

                        <div class="legend-button" id="legendButton">
                            <i class="bi bi-info-circle"></i>
                        </div>

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/AC.jpg" alt="" class="legend-img">
                                <p>AIRCON</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/B-TABLE.jpg" alt="" class="legend-img">
                                <p>TABLE</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt="" class="legend-img">
                                <p>TOILET-SEAT</p>
                            </div>
                        </div>


                        <div class="map-nav">
                            <a href="../../administrator/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>
                            <div class="map-legend">
                                <div class="legend-color-green"></div>
                                <p>Working</p>
                                <div class="legend-color-under-maintenance"></div>
                                <p>Under maintenance</p>
                                <div class="legend-color-need-repair"></div>
                                <p>Need repair</p>
                                <div class="legend-color-for-replacement"></div>
                                <p>For replacement</p>
                            </div>
                        </div>
                        <!-- ASSETS -->

                        <!-- ASSET 7086 -->
                        <img src='../image.php?id=7086' style='width:60px; cursor:pointer; position:absolute; top:145px; left:230px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7086' onclick='fetchAssetData(7086);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7086); ?>; 
                        position:absolute; top:145px; left:230px;'>
                        </div>

                        <!-- ASSET 7087 -->
                        <img src='../image.php?id=7087' style='width:60px; cursor:pointer; position:absolute; top:145px; left:355px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7087' onclick='fetchAssetData(7087);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7087); ?>; 
                        position:absolute; top:145px; left:355px;'>
                        </div>

                        <!-- ASSET 7088 -->
                        <img src='../image.php?id=7088' style='width:60PX; cursor:pointer; position:absolute; top:380px; left:230px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7088' onclick='fetchAssetData(7088);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7088); ?>; 
                        position:absolute; top:380px; left:230px;'>
                        </div>

                        <!-- ASSET 7089 -->
                        <img src='../image.php?id=7089' style='width:60PX; cursor:pointer; position:absolute; top:380px; left:355px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7089' onclick='fetchAssetData(7089);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7089); ?>; 
                        position:absolute; top:380px; left:355px;'>
                        </div>

                        <!-- ASSET 7090 -->
                        <img src='../image.php?id=7090' style='width:60px; cursor:pointer; position:absolute; top:145px; left:555px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7090' onclick='fetchAssetData(7090);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7090); ?>; 
                        position:absolute; top:145px; left:555px;'>
                        </div>

                        <!-- ASSET 7091 -->
                        <img src='../image.php?id=7091' style='width:60px; cursor:pointer; position:absolute; top:145px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7091' onclick='fetchAssetData(7091);'>
                        <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7091); ?>; 
                        position:absolute; top:145px; left:680px;'>

                            <!-- ASSET 7092 -->
                            <img src='../image.php?id=7092' style='width:60px; cursor:pointer; position:absolute; top:235px; left:-125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7092' onclick='fetchAssetData(7092);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7092); ?>; 
                        position:absolute; top:235px; left:-125px;'>
                            </div>

                            <!-- ASSET 7093 -->
                            <img src='../image.php?id=7093' style='width:60px; cursor:pointer; position:absolute; top:235px; left:0px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7093' onclick='fetchAssetData(7093);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7093); ?>; 
                        position:absolute; top:235px; left:0px;'>
                            </div>

                            <!-- ASSET 7094 -->
                            <img src='../image.php?id=7094' style='width:60px; cursor:pointer; position:absolute; top:0px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7094' onclick='fetchAssetData(7094);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7094); ?>; 
                        position:absolute; top:0px; left:200px;'>
                            </div>

                            <!-- ASSET 7095 -->
                            <img src='../image.php?id=7095' style='width:60px; cursor:pointer; position:absolute; top:0px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7095' onclick='fetchAssetData(7095);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7095); ?>; 
                        position:absolute; top:0px; left:325px;'>
                            </div>

                            <!-- ASSET 7096 -->
                            <img src='../image.php?id=7096' style='width:60px; cursor:pointer; position:absolute; top:235px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7096' onclick='fetchAssetData(7096);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7096); ?>; 
                        position:absolute; top:235px; left:200px;'>
                            </div>

                            <!-- ASSET 7097 -->
                            <img src='../image.php?id=7097' style='width:60px; cursor:pointer; position:absolute; top:235px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7097' onclick='fetchAssetData(7097);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7097); ?>; 
                        position:absolute; top:235px; left:325px;'>
                            </div>

                            <!-- ASSET 7080 -->
                            <img src='../image.php?id=7080' style='width:65px; cursor:pointer; position:absolute; top:60px; left:-390px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7080' onclick='fetchAssetData(7080);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7080); ?>; 
                        position:absolute; top:60px; left:-390px;'>
                            </div>

                            <!-- ASSET 7081 -->
                            <img src='../image.php?id=7081' style='width:65px; cursor:pointer; position:absolute; top:60px; left:-65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7081' onclick='fetchAssetData(7081);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7081); ?>; 
                        position:absolute; top:60px; left:-65px;'>
                            </div>

                            <!-- ASSET 7082 -->
                            <img src='../image.php?id=7082' style='width:65px; cursor:pointer; position:absolute; top:60px; left:260px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7082' onclick='fetchAssetData(7082);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7082); ?>; 
                        position:absolute; top:60px; left:260px;'>
                            </div>

                            <!-- ASSET 7083 -->
                            <img src='../image.php?id=7083' style='width:65px; cursor:pointer; position:absolute; top:160px; left:-390px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7083' onclick='fetchAssetData(7083);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7083); ?>; 
                        position:absolute; top:160px; left:-390px;'>
                            </div>

                            <!-- ASSET 7084 -->
                            <img src='../image.php?id=7084' style='width:65px; cursor:pointer; position:absolute; top:160px; left:-65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7084' onclick='fetchAssetData(7084);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7084); ?>; 
                        position:absolute; top:160px; left:-65px;'>
                            </div>

                            <!-- ASSET 7085 -->
                            <img src='../image.php?id=7085' style='width:65px; cursor:pointer; position:absolute; top:160px; left:260px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7085' onclick='fetchAssetData(7085);'>
                            <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7085); ?>; 
                        position:absolute; top:160px; left:260px;'>
                            </div>

                        </div>
                        <!-- Modal structure for id 7086-->
                        <div class='modal fade' id='imageModal7086' tabindex='-1' aria-labelledby='imageModalLabel7086' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7086); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7086); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7086); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7086); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7086); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7086); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7086); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7086); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7086 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7086 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7086 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7086 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7086); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7086); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7086); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7086">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7086-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7086" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7086">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7087-->
                        <div class='modal fade' id='imageModal7087' tabindex='-1' aria-labelledby='imageModalLabel7087' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7087); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7087); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7087); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7087); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7087); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7087); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7087); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7087); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7087 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7087 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7087 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7087 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7087); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7087); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7087); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7087">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7087-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7087" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7087">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7088-->
                        <div class='modal fade' id='imageModal7088' tabindex='-1' aria-labelledby='imageModalLabel7088' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7088); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7088); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7088); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7088); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7088); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7088); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7088); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7088); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7088 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7088 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7088 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7088 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7088); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7088); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7088); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7088">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7088-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7088" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7088">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7089-->
                        <div class='modal fade' id='imageModal7089' tabindex='-1' aria-labelledby='imageModalLabel7089' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7089); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7089); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7089); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7089); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7089); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7089); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7089); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7089); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7089 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7089 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7089 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7089 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7089); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7089); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7089); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7089">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7089-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7089" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7089">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7090-->
                        <div class='modal fade' id='imageModal7090' tabindex='-1' aria-labelledby='imageModalLabel7090' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7090); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7090); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7090); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7090); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7090); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7090); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7090); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7090); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7090 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7090 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7090 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7090 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7090); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7090); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7090); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7090">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7090-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7090" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7090">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7091-->
                        <div class='modal fade' id='imageModal7091' tabindex='-1' aria-labelledby='imageModalLabel7091' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7091); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7091); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7091); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7091); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7091); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7091); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7091); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7091); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7091 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7091 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7091 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7091 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7091); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7091); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7091); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7091">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7091-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7091" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7091">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7092-->
                        <div class='modal fade' id='imageModal7092' tabindex='-1' aria-labelledby='imageModalLabel7092' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7092); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7092); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7092); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7092); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7092); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7092); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7092); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7092); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7092 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7092 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7092 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7092 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7092); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7092); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7092); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7092">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7092-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7092" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7092">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7093-->
                        <div class='modal fade' id='imageModal7093' tabindex='-1' aria-labelledby='imageModalLabel7093' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7093); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7093); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7093); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7093); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7093); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7093); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7093); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7093); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7093 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7093 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7093 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7093 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7093); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7093); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7093); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7093">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7093-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7093" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7093">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7094-->
                        <div class='modal fade' id='imageModal7094' tabindex='-1' aria-labelledby='imageModalLabel7094' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7094); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7094); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7094); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7094); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7094); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7094); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7094); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7094); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7094 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7094 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7094 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7094 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7094); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7094); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7094); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7094">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7094-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7094" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7094">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7095-->
                        <div class='modal fade' id='imageModal7095' tabindex='-1' aria-labelledby='imageModalLabel7095' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7095); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7095); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7095); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7095); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7095); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7095); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7095); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7095); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7095 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7095 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7095 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7095 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7095); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7095); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7095); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7095">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7095-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7095" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7095">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7096-->
                        <div class='modal fade' id='imageModal7096' tabindex='-1' aria-labelledby='imageModalLabel7096' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7096); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7096); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7096); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7096); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7096); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7096); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7096); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7096); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7096 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7096 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7096 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7096 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7096); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7096); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7096); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7096">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7096-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7096" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7096">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7097-->
                        <div class='modal fade' id='imageModal7097' tabindex='-1' aria-labelledby='imageModalLabel7097' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7097); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7097); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7097); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7097); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7097); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7097); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7097); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7097); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7097 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7097 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7097 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7097 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7097); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7097); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7097); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7097">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7097-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7097" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7097">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7080-->
                        <div class='modal fade' id='imageModal7080' tabindex='-1' aria-labelledby='imageModalLabel7080' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7080); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7080); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7080); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7080); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7080); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7080); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7080); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7080); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7080 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7080 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7080 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7080 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7080); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7080); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7080); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7080">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7080-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7080" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7080">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7081-->
                        <div class='modal fade' id='imageModal7081' tabindex='-1' aria-labelledby='imageModalLabel7081' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7081); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7081); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7081); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7081); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7081); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7081); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7081); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7081); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7081 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7081 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7081 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7081 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7081); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7081); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7081); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7081">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7081-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7081" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7081">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7082-->
                        <div class='modal fade' id='imageModal7082' tabindex='-1' aria-labelledby='imageModalLabel7082' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7082); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7082); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7082); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7082); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7082); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7082); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7082); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7082); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7082 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7082 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7082 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7082 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7082); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7082); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7082); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7082">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7082-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7082" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7082">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7083-->
                        <div class='modal fade' id='imageModal7083' tabindex='-1' aria-labelledby='imageModalLabel7083' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7083); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7083); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7083); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7083); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7083); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7083); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7083); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7083); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7083 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7083 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7083 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7083 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7083); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7083); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7083); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7083">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7083-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7083" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7083">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7084-->
                        <div class='modal fade' id='imageModal7084' tabindex='-1' aria-labelledby='imageModalLabel7084' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7084); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7084); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7084); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7084); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7084); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7084); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7084); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7084); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7084 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7084 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7084 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7084 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7084); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7084); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7084); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7084">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7084-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7084" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7084">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal structure for id 7085-->
                        <div class='modal fade' id='imageModal7085' tabindex='-1' aria-labelledby='imageModalLabel7085' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7085); ?>">
                                            <!--START DIV FOR IMAGE -->

                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7085); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->

                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7085); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7085); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7085); ?>" readonly />
                                            </div>


                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7085); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->

                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7085); ?>" readonly />
                                            </div>

                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7085); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="images" class="form-label">Images:</label>
                                                <input type="text" class="form-control" id="" name="images" readonly />
                                            </div>

                                            <!--End of Third Row-->

                                            <!--Fourth Row-->
                                            <div class="col-2 Upload">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>

                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status7085 == 'Working')
                                                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status7085 == 'Under Maintenance')
                                                                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status7085 == 'For Replacement')
                                                                                        ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status7085 == 'Need Repair')
                                                                                    ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7085); ?>" readonly />
                                            </div>

                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7085); ?>" readonly />
                                            </div>

                                            <!--End of Fourth Row-->

                                            <!--Fifth Row-->
                                            <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7085); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->

                                            <!--Sixth Row-->
                                            <div class="col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                            </div>
                                            <!--End of Sixth Row-->

                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7085">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table 7085-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop7085" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit7085">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                    </div>
            </main>
        </section>
        <script>
            $(document).ready(function() {
                $('.notification-item').on('click', function(e) {
                    e.preventDefault();
                    var activityId = $(this).data('activity-id');
                    var notificationItem = $(this); // Store the clicked element

                    $.ajax({
                        type: "POST",
                        url: "../../administrator/update_single_notification.php", // The URL to the PHP file
                        data: {
                            activityId: activityId
                        },
                        success: function(response) {
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
                        error: function(xhr, status, error) {
                            // Handle AJAX error
                            console.error("AJAX error:", status, error);
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                var urlParams = new URLSearchParams(window.location.search);
                var assetId = urlParams.get('assetId'); // Get the assetId from the URL

                if (assetId) {
                    var modalId = '#imageModal' + assetId;
                    $(modalId).modal('show'); // Open the modal with the corresponding ID
                }
            });
        </script>
        <script src="../../../src/js/main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>