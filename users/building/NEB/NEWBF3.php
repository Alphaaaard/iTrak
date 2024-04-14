<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 


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
               WHERE al.tab='Report' AND al.seen = '0' AND al.accountID != ?
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


    //FOR ID 1 SOFA
    $sql1 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = 1";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();
    $assetId1 = $row1['assetId'];
    $category1 = $row1['category'];
    $date1 = $row1['date'];
    $building1 = $row1['building'];
    $floor1 = $row1['floor'];
    $room1 = $row1['room'];
    $status1 = $row1['status'];
    $assignedName1 = $row1['assignedName'];
    $assignedBy1 = $row1['assignedBy'];
    $upload_img1 = $row1['upload_img'];
    $description1 = $row1['description'];

    //FOR ID 2 SOFA
    $sql2 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = 2";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();
    $assetId2 = $row2['assetId'];
    $category2 = $row2['category'];
    $date2 = $row2['date'];
    $building2 = $row2['building'];
    $floor2 = $row2['floor'];
    $room2 = $row2['room'];
    $status2 = $row2['status'];
    $assignedName2 = $row2['assignedName'];
    $assignedBy2 = $row2['assignedBy'];
    $upload_img2 = $row2['upload_img'];
    $description2 = $row2['description'];

    //FOR ID 3 SOFA
    $sql3 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 3";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $row3 = $result3->fetch_assoc();
    $assetId3 = $row3['assetId'];
    $category3 = $row3['category'];
    $date3 = $row3['date'];
    $building3 = $row3['building'];
    $floor3 = $row3['floor'];
    $room3 = $row3['room'];
    $status3 = $row3['status'];
    $assignedName3 = $row3['assignedName'];
    $assignedBy3 = $row3['assignedBy'];
    $upload_img3 = $row3['upload_img'];
    $description3 = $row3['description'];


    //FOR ID 4 BED
    $sql4 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 4";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $row4 = $result4->fetch_assoc();
    $assetId4 = $row4['assetId'];
    $category4 = $row4['category'];
    $date4 = $row4['date'];
    $building4 = $row4['building'];
    $floor4 = $row4['floor'];
    $room4 = $row4['room'];
    $status4 = $row4['status'];
    $assignedName4 = $row4['assignedName'];
    $assignedBy4 = $row4['assignedBy'];
    $upload_img4 = $row4['upload_img'];
    $description4 = $row4['description'];


    //FOR ID 5 BED
    $sql5 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->execute();
    $result5 = $stmt5->get_result();
    $row5 = $result5->fetch_assoc();
    $assetId5 = $row5['assetId'];
    $category5 = $row5['category'];
    $date5 = $row5['date'];
    $building5 = $row5['building'];
    $floor5 = $row5['floor'];
    $room5 = $row5['room'];
    $status5 = $row5['status'];
    $assignedName5 = $row5['assignedName'];
    $assignedBy5 = $row5['assignedBy'];
    $upload_img5 = $row5['upload_img'];
    $description5 = $row5['description'];

    //FOR ID 6 BED
    $sql6 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6";
    $stmt6 = $conn->prepare($sql6);
    $stmt6->execute();
    $result6 = $stmt6->get_result();
    $row6 = $result6->fetch_assoc();
    $assetId6 = $row6['assetId'];
    $category6 = $row6['category'];
    $date6 = $row6['date'];
    $building6 = $row6['building'];
    $floor6 = $row6['floor'];
    $room6 = $row6['room'];
    $status6 = $row6['status'];
    $assignedName6 = $row6['assignedName'];
    $assignedBy6 = $row6['assignedBy'];
    $upload_img6 = $row6['upload_img'];
    $description6 = $row6['description'];


    //FOR ID 7 BED
    $sql7 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7";
    $stmt7 = $conn->prepare($sql7);
    $stmt7->execute();
    $result7 = $stmt7->get_result();
    $row7 = $result7->fetch_assoc();
    $assetId7 = $row7['assetId'];
    $category7 = $row7['category'];
    $date7 = $row7['date'];
    $building7 = $row7['building'];
    $floor7 = $row7['floor'];
    $room7 = $row7['room'];
    $status7 = $row7['status'];
    $assignedName7 = $row7['assignedName'];
    $assignedBy7 = $row7['assignedBy'];
    $upload_img7 = $row7['upload_img'];
    $description7 = $row7['description'];


    //FOR ID 8 BED
    $sql8 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 8";
    $stmt8 = $conn->prepare($sql8);
    $stmt8->execute();
    $result8 = $stmt8->get_result();
    $row8 = $result8->fetch_assoc();
    $assetId8 = $row8['assetId'];
    $category8 = $row8['category'];
    $date8 = $row8['date'];
    $building8 = $row8['building'];
    $floor8 = $row8['floor'];
    $room8 = $row8['room'];
    $status8 = $row8['status'];
    $assignedName8 = $row8['assignedName'];
    $assignedBy8 = $row8['assignedBy'];
    $upload_img8 = $row8['upload_img'];
    $description8 = $row8['description'];


    //FOR ID 9 BED
    $sql9 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 9";
    $stmt9 = $conn->prepare($sql9);
    $stmt9->execute();
    $result9 = $stmt9->get_result();
    $row9 = $result9->fetch_assoc();
    $assetId9 = $row9['assetId'];
    $category9 = $row9['category'];
    $date9 = $row9['date'];
    $building9 = $row9['building'];
    $floor9 = $row9['floor'];
    $room9 = $row9['room'];
    $status9 = $row9['status'];
    $assignedName9 = $row9['assignedName'];
    $assignedBy9 = $row9['assignedBy'];
    $upload_img9 = $row9['upload_img'];
    $description9 = $row9['description'];


    //FOR ID 10 TOILET SEAT
    $sql10 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 10";
    $stmt10 = $conn->prepare($sql10);
    $stmt10->execute();
    $result10 = $stmt10->get_result();
    $row10 = $result10->fetch_assoc();
    $assetId10 = $row10['assetId'];
    $category10 = $row10['category'];
    $date10 = $row10['date'];
    $building10 = $row10['building'];
    $floor10 = $row10['floor'];
    $room10 = $row10['room'];
    $status10 = $row10['status'];
    $assignedName10 = $row10['assignedName'];
    $assignedBy10 = $row10['assignedBy'];
    $upload_img10 = $row10['upload_img'];
    $description10 = $row10['description'];

    //FOR ID 11 TOILET SEAT
    $sql11 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11";
    $stmt11 = $conn->prepare($sql11);
    $stmt11->execute();
    $result11 = $stmt11->get_result();
    $row11 = $result11->fetch_assoc();
    $assetId11 = $row11['assetId'];
    $category11 = $row11['category'];
    $date11 = $row11['date'];
    $building11 = $row11['building'];
    $floor11 = $row11['floor'];
    $room11 = $row11['room'];
    $status11 = $row11['status'];
    $assignedName11 = $row11['assignedName'];
    $assignedBy11 = $row11['assignedBy'];
    $upload_img11 = $row11['upload_img'];
    $description11 = $row11['description'];

    //FOR ID 12 TOILET SEAT
    $sql12 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 12";
    $stmt12 = $conn->prepare($sql12);
    $stmt12->execute();
    $result12 = $stmt12->get_result();
    $row12 = $result12->fetch_assoc();
    $assetId12 = $row12['assetId'];
    $category12 = $row12['category'];
    $date12 = $row12['date'];
    $building12 = $row12['building'];
    $floor12 = $row12['floor'];
    $room12 = $row12['room'];
    $status12 = $row12['status'];
    $assignedName12 = $row12['assignedName'];
    $assignedBy12 = $row12['assignedBy'];
    $upload_img12 = $row12['upload_img'];
    $description12 = $row12['description'];

    //FOR ID 898 
    $sql898 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 898";
    $stmt898 = $conn->prepare($sql898);
    $stmt898->execute();
    $result898 = $stmt898->get_result();
    $row898 = $result898->fetch_assoc();
    $assetId898 = $row898['assetId'];
    $category898 = $row898['category'];
    $date898 = $row898['date'];
    $building898 = $row898['building'];
    $floor898 = $row898['floor'];
    $room898 = $row898['room'];
    $status898 = $row898['status'];
    $assignedName898 = $row898['assignedName'];
    $assignedBy898 = $row898['assignedBy'];
    $upload_img898 = $row898['upload_img'];
    $description898 = $row898['description'];

    //FOR ID 899 
    $sql899 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 899";
    $stmt899 = $conn->prepare($sql899);
    $stmt899->execute();
    $result899 = $stmt899->get_result();
    $row899 = $result899->fetch_assoc();
    $assetId899 = $row899['assetId'];
    $category899 = $row899['category'];
    $date899 = $row899['date'];
    $building899 = $row899['building'];
    $floor899 = $row899['floor'];
    $room899 = $row899['room'];
    $status899 = $row899['status'];
    $assignedName899 = $row899['assignedName'];
    $assignedBy899 = $row899['assignedBy'];
    $upload_img899 = $row899['upload_img'];
    $description899 = $row899['description'];

    //FOR ID 901 
    $sql901 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 901";
    $stmt901 = $conn->prepare($sql901);
    $stmt901->execute();
    $result901 = $stmt901->get_result();
    $row901 = $result901->fetch_assoc();
    $assetId901 = $row901['assetId'];
    $category901 = $row901['category'];
    $date901 = $row901['date'];
    $building901 = $row901['building'];
    $floor901 = $row901['floor'];
    $room901 = $row901['room'];
    $status901 = $row901['status'];
    $assignedName901 = $row901['assignedName'];
    $assignedBy901 = $row901['assignedBy'];
    $upload_img901 = $row901['upload_img'];
    $description901 = $row901['description'];

    //FOR ID 6866 TOILET SEAT
    $sql6866 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6866";
    $stmt6866 = $conn->prepare($sql6866);
    $stmt6866->execute();
    $result6866 = $stmt6866->get_result();
    $row6866 = $result6866->fetch_assoc();
    $assetId6866 = $row6866['assetId'];
    $category6866 = $row6866['category'];
    $date6866 = $row6866['date'];
    $building6866 = $row6866['building'];
    $floor6866 = $row6866['floor'];
    $room6866 = $row6866['room'];
    $status6866 = $row6866['status'];
    $assignedName6866 = $row6866['assignedName'];
    $assignedBy6866 = $row6866['assignedBy'];
    $upload_img6866 = $row6866['upload_img'];
    $description6866 = $row6866['description'];

    //FOR ID 7262 TOILET SEAT
    $sql7262 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7262";
    $stmt7262 = $conn->prepare($sql7262);
    $stmt7262->execute();
    $result7262 = $stmt7262->get_result();
    $row7262 = $result7262->fetch_assoc();
    $assetId7262 = $row7262['assetId'];
    $category7262 = $row7262['category'];
    $date7262 = $row7262['date'];
    $building7262 = $row7262['building'];
    $floor7262 = $row7262['floor'];
    $room7262 = $row7262['room'];
    $status7262 = $row7262['status'];
    $assignedName7262 = $row7262['assignedName'];
    $assignedBy7262 = $row7262['assignedBy'];
    $upload_img7262 = $row7262['upload_img'];
    $description7262 = $row7262['description'];


    //FOR ID 1
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit1'])) {
        // Get form data
        $assetId1 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status1 = $_POST['status']; // Get the status from the form
        $description1 = $_POST['description']; // Get the description from the form
        $room1 = $_POST['room']; // Get the room from the form
        $assignedBy1 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName1 = $status1 === 'Need Repair' ? '' : $assignedName1;

        // Prepare SQL query to update the asset
        $sql1 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param('sssssi', $status1, $assignedName1, $assignedBy1, $description1, $room1, $assetId1);

        if ($stmt1->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId1 to $status1.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt1->close();
    }

    //FOR ID 2
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2'])) {
        // Get form data
        $assetId2 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2 = $_POST['status']; // Get the status from the form
        $description2 = $_POST['description']; // Get the description from the form
        $room2 = $_POST['room']; // Get the room from the form
        $assignedBy2 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2 = $status2 === 'Need Repair' ? '' : $assignedName2;

        // Prepare SQL query to update the asset
        $sql2 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('sssssi', $status2, $assignedName2, $assignedBy2, $description2, $room2, $assetId2);

        if ($stmt2->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2 to $status2.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2->close();
    }

    //FOR ID 3
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit3'])) {
        // Get form data
        $assetId3 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status3 = $_POST['status']; // Get the status from the form
        $description3 = $_POST['description']; // Get the description from the form
        $room3 = $_POST['room']; // Get the room from the form
        $assignedBy3 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName3 = $status3 === 'Need Repair' ? '' : $assignedName3;

        // Prepare SQL query to update the asset
        $sql3 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param('sssssi', $status3, $assignedName3, $assignedBy3, $description3, $room3, $assetId3);

        if ($stmt3->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId3 to $status3.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt3->close();
    }

    //FOR ID 4
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit4'])) {
        // Get form data
        $assetId4 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status4 = $_POST['status']; // Get the status from the form
        $description4 = $_POST['description']; // Get the description from the form
        $room4 = $_POST['room']; // Get the room from the form
        $assignedBy4 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName4 = $status4 === 'Need Repair' ? '' : $assignedName4;

        // Prepare SQL query to update the asset
        $sql4 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param('sssssi', $status4, $assignedName4,  $assignedBy4, $description4, $room4, $assetId4);

        if ($stmt4->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId4 to $status4.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt4->close();
    }

    //FOR ID 5
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5'])) {
        // Get form data
        $assetId5 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5 = $_POST['status']; // Get the status from the form
        $description5 = $_POST['description']; // Get the description from the form
        $room5 = $_POST['room']; // Get the room from the form
        $assignedBy5 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5 = $status5 === 'Need Repair' ? '' : $assignedName5;

        // Prepare SQL query to update the asset
        $sql5 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5 = $conn->prepare($sql5);
        $stmt5->bind_param('sssssi', $status5, $assignedName5,   $assignedBy5, $description5, $room5, $assetId5);

        if ($stmt5->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5 to $status5.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5->close();
    }

    //FOR ID 6
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6'])) {
        // Get form data
        $assetId6 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6 = $_POST['status']; // Get the status from the form
        $description6 = $_POST['description']; // Get the description from the form
        $room6 = $_POST['room']; // Get the room from the form
        $assignedBy6 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6 = $status6 === 'Need Repair' ? '' : $assignedName6;

        // Prepare SQL query to update the asset
        $sql6 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6 = $conn->prepare($sql6);
        $stmt6->bind_param('sssssi', $status6, $assignedName6, $assignedBy6, $description6, $room6, $assetId6);

        if ($stmt6->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6 to $status6.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6->close();
    }

    //FOR ID 7
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7'])) {
        // Get form data
        $assetId7 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7 = $_POST['status']; // Get the status from the form
        $description7 = $_POST['description']; // Get the description from the form
        $room7 = $_POST['room']; // Get the room from the form
        $assignedBy7 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7 = $status7 === 'Need Repair' ? '' : $assignedName7;

        // Prepare SQL query to update the asset
        $sql7 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7 = $conn->prepare($sql7);
        $stmt7->bind_param('sssssi', $status7, $assignedName7, $description7, $assignedBy7, $room7, $assetId7);

        if ($stmt7->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7 to $status7.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7->close();
    }

    //FOR ID 8
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit8'])) {
        // Get form data
        $assetId8 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status8 = $_POST['status']; // Get the status from the form
        $description8 = $_POST['description']; // Get the description from the form
        $room8 = $_POST['room']; // Get the room from the form
        $assignedBy8 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName8 = $status8 === 'Need Repair' ? '' : $assignedName8;

        // Prepare SQL query to update the asset
        $sql8 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt8 = $conn->prepare($sql8);
        $stmt8->bind_param('sssssi', $status8, $assignedName8, $assignedBy8, $description8, $room8, $assetId8);

        if ($stmt8->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId8 to $status8.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt8->close();
    }

    //FOR ID 9
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit9'])) {
        // Get form data
        $assetId9 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status9 = $_POST['status']; // Get the status from the form
        $description9 = $_POST['description']; // Get the description from the form
        $room9 = $_POST['room']; // Get the room from the form
        $assignedBy9 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName9 = $status9 === 'Need Repair' ? '' : $assignedName9;

        // Prepare SQL query to update the asset
        $sql9 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt9 = $conn->prepare($sql9);
        $stmt9->bind_param('sssssi', $status9, $assignedName9, $assignedBy9, $description9, $room9, $assetId9);

        if ($stmt9->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId9 to $status9.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt9->close();
    }

    //FOR ID 10
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit10'])) {
        // Get form data
        $assetId10 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status10 = $_POST['status']; // Get the status from the form
        $description10 = $_POST['description']; // Get the description from the form
        $room10 = $_POST['room']; // Get the room from the form
        $assignedBy10 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName10 = $status10 === 'Need Repair' ? '' : $assignedName10;

        // Prepare SQL query to update the asset
        $sql10 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt10 = $conn->prepare($sql10);
        $stmt10->bind_param('sssssi', $status10, $assignedName10, $assignedBy10, $description10, $room10, $assetId10);

        if ($stmt10->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId10 to $status10.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt10->close();
    }

    //FOR ID 11
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit11'])) {
        // Get form data
        $assetId11 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status11 = $_POST['status']; // Get the status from the form
        $description11 = $_POST['description']; // Get the description from the form
        $room11 = $_POST['room']; // Get the room from the form
        $assignedBy11 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName11 = $status11 === 'Need Repair' ? '' : $assignedName11;

        // Prepare SQL query to update the asset
        $sql11 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt11 = $conn->prepare($sql11);
        $stmt11->bind_param('sssssi', $status11, $assignedName11, $assignedBy11, $description11, $room11, $assetId11);

        if ($stmt11->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId11 to $status11.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt11->close();
    }

    //FOR ID 12
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit12'])) {
        // Get form data
        $assetId12 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status12 = $_POST['status']; // Get the status from the form
        $description12 = $_POST['description']; // Get the description from the form
        $room12 = $_POST['room']; // Get the room from the form
        $assignedBy12 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName12 = $status12 === 'Need Repair' ? '' : $assignedName12;

        // Prepare SQL query to update the asset
        $sql12 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt12 = $conn->prepare($sql12);
        $stmt12->bind_param('sssssi', $status12, $assignedName12, $assignedBy12, $description12, $room12, $assetId12);

        if ($stmt12->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId12 to $status12.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt12->close();
    }

    //FOR ID 898
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit898'])) {
        // Get form data
        $assetId898 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status898 = $_POST['status']; // Get the status from the form
        $description898 = $_POST['description']; // Get the description from the form
        $room898 = $_POST['room']; // Get the room from the form
        $assignedBy898 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName898 = $status898 === 'Need Repair' ? '' : $assignedName898;

        // Prepare SQL query to update the asset
        $sql898 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt898 = $conn->prepare($sql898);
        $stmt898->bind_param('sssssi', $status898, $assignedName898, $assignedBy898, $description898, $room898, $assetId898);

        if ($stmt898->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId898 to $status898.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt898->close();
    }

    //FOR ID 899
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit899'])) {
        // Get form data
        $assetId899 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status899 = $_POST['status']; // Get the status from the form
        $description899 = $_POST['description']; // Get the description from the form
        $room899 = $_POST['room']; // Get the room from the form
        $assignedBy899 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName899 = $status899 === 'Need Repair' ? '' : $assignedName899;

        // Prepare SQL query to update the asset
        $sql899 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt899 = $conn->prepare($sql899);
        $stmt899->bind_param('sssssi', $status899, $assignedName899, $assignedBy899, $description899, $room899, $assetId899);

        if ($stmt899->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId899 to $status899.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt899->close();
    }

    //FOR ID 901
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit901'])) {
        // Get form data
        $assetId901 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status901 = $_POST['status']; // Get the status from the form
        $description901 = $_POST['description']; // Get the description from the form
        $room901 = $_POST['room']; // Get the room from the form
        $assignedBy901 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName901 = $status901 === 'Need Repair' ? '' : $assignedName901;

        // Prepare SQL query to update the asset
        $sql901 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt901 = $conn->prepare($sql901);
        $stmt901->bind_param('sssssi', $status901, $assignedName901, $assignedBy901, $description901, $room901, $assetId901);

        if ($stmt901->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId901 to $status901.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt901->close();
    }

    //FOR ID 6866
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6866'])) {
        // Get form data
        $assetId6866 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6866 = $_POST['status']; // Get the status from the form
        $description6866 = $_POST['description']; // Get the description from the form
        $room6866 = $_POST['room']; // Get the room from the form
        $assignedBy6866 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6866 = $status6866 === 'Need Repair' ? '' : $assignedName6866;

        // Prepare SQL query to update the asset
        $sql6866 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6866 = $conn->prepare($sql6866);
        $stmt6866->bind_param('sssssi', $status6866, $assignedName6866, $assignedBy6866, $description6866, $room6866, $assetId6866);

        if ($stmt6866->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6866 to $status6866.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6866->close();
    }

    //FOR ID 7262
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7262'])) {
        // Get form data
        $assetId7262 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7262 = $_POST['status']; // Get the status from the form
        $description7262 = $_POST['description']; // Get the description from the form
        $room7262 = $_POST['room']; // Get the room from the form
        $assignedBy7262 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7262 = $status7262 === 'Need Repair' ? '' : $assignedName7262;

        // Prepare SQL query to update the asset
        $sql7262 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7262 = $conn->prepare($sql7262);
        $stmt7262->bind_param('sssssi', $status7262, $assignedName7262, $assignedBy7262, $description7262, $room7262, $assetId7262);

        if ($stmt7262->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7262 to $status7262.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: NEWBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7262->close();
    }


    function getStatusColor($status)
    {
        switch ($status) {
            case 'Working':
                return 'green';
            case 'Under Maintenance':
                return 'yellow';
            case 'Need Repair':
                return 'blue';
            case 'For Replacement':
                return 'red';
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
                header("Location: NEWBF1.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload image. Error: " . $_FILES['upload_img']['error'] . "');</script>";
        }
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
               WHERE al.tab='Report' AND al.seen = '0' AND al.accountID != ?
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

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Map</title>
        <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/NEB/NEWBF1.css" />
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>

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
                            <span id="noti_number"><?php echo $unseenCount; ?></span>

                            </td>
                            </tr>
                            </table>
                            <script type="text/javascript">
                                function loadDoc() {


                                    setInterval(function() {

                                        var xhttp = new XMLHttpRequest();
                                        xhttp.onreadystatechange = function() {
                                            if (this.readyState == 4 && this.status == 200) {
                                                document.getElementById("noti_number").innerHTML = this.responseText;
                                            }
                                        };
                                        xhttp.open("GET", "../../administrator/update_single_notification.php", true);
                                        xhttp.send();

                                    }, 10);


                                }
                                loadDoc();
                            </script>

                        </a>



                        <div class="dropdown-content" id="notification-dropdown-content">
                            <h6 class="dropdown-header">Alerts Center</h6>
                            <!-- PHP code to display notifications will go here -->
                            <?php
                            if ($resultLatestLogs && $resultLatestLogs->num_rows > 0) {
                                while ($row = $resultLatestLogs->fetch_assoc()) {
                                    $adminName = $row["adminFirstName"] . ' ' . $row["adminLastName"];
                                    $actionText = $row["action"];

                                    // Initialize the notification text as empty
                                    $notificationText = "";

                                    // Check for 'Assigned maintenance personnel' action
                                    if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
                                        $assignedName = $matches[1];
                                        $assetId = $matches[2];
                                        $notificationText = "Admin $adminName assigned $assignedName to asset ID $assetId";
                                    }
                                    // Check for 'Changed status of asset ID' action
                                    elseif (preg_match('/Changed status of asset ID (\d+) to (.+)/', $actionText, $matches)) {
                                        $assetId = $matches[1];
                                        $newStatus = $matches[2];
                                        $notificationText = "Admin $adminName changed status of asset ID $assetId to $newStatus";
                                    }

                                    // If notification text is set, echo the notification
                                    if (!empty($notificationText)) {
                                        // HTML for notification item
                                        echo '<a href="#" class="notification-item" data-activity-id="' . $row["activityId"] . '">' . htmlspecialchars($notificationText) . '</a>';
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
                        <img class="Floor-container-1" src="../../../src/floors/newAcademicB/NAB3F.png" alt="">

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/BED.jpg" alt="" class="legend-img">
                                <p>BED</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/DESK.jpg" alt="" class="legend-img">
                                <p>DESK</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/SOFA.jpg" alt="" class="legend-img">
                                <p>SOFA</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/TABLE.jpg" alt="" class="legend-img">
                                <p>TABLE</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt="" class="legend-img">
                                <p>TOILET SEAT</p>
                            </div>
                        </div>

                        <div class="map-nav">
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

                        <!-- ASSET 1 -->
                        <img src='../image.php?id=1' style='width:40px; cursor:pointer; position:absolute; top:212px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal1' onclick='fetchAssetData(1);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status1); ?>; 
                        position:absolute; top:212px; left:470px;'>
                        </div>

                        <!-- ASSET 2 -->
                        <img src='../image.php?id=2' style='width:40px; cursor:pointer; position:absolute; top:245px; left:400px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2' onclick='fetchAssetData(2);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2); ?>; 
                        position:absolute; top:230px; left:410px;'>
                        </div>

                        <!-- ASSET 3 -->
                        <img src='../image.php?id=3' style='width:40px; cursor:pointer; position:absolute; top:270px; left:435px; transform: rotate(-180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal3' onclick='fetchAssetData(3);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status3); ?>; 
                        position:absolute; top:270px; left:470px;'>
                        </div>

                        <!-- ASSET 4 -->
                        <img src='../image.php?id=4' style='width:18px; cursor:pointer; position:absolute; top:195px; left:1020px; transform: rotate(-90deg); ' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal4' onclick='fetchAssetData(4);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status4); ?>; 
                        position:absolute; top:215px; left:1040px;'>
                        </div>

                        <!-- ASSET 5 -->
                        <img src='../image.php?id=5' style='width:18px; cursor:pointer; position:absolute; top:165px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5' onclick='fetchAssetData(5);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5); ?>; 
                        position:absolute; top:185px; left:1040px;'>
                        </div>

                        <!-- ASSET 6 -->
                        <img src='../image.php?id=6' style='width:18px; cursor:pointer; position:absolute; top:281px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6' onclick='fetchAssetData(6);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status6); ?>; 
                        position:absolute; top:300px; left:1040px;'>
                        </div>

                        <!-- ASSET 7 -->
                        <img src='../image.php?id=7' style='width:18px; cursor:pointer; position:absolute; top:253px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7' onclick='fetchAssetData(7);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7); ?>; 
                        position:absolute; top:272px; left:1040px;'>
                        </div>


                        <!-- ASSET 8 -->
                        <img src='../image.php?id=8' style='width:18px; cursor:pointer; position:absolute; top:225px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal8' onclick='fetchAssetData(8);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status8); ?>; 
                        position:absolute; top:245px; left:1040px;'>
                        </div>


                        <!-- ASSET 9 -->
                        <img src='../image.php?id=9' style='width:18px; cursor:pointer; position:absolute; top:225px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9' onclick='fetchAssetData(9);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9); ?>; 
                        position:absolute; top:245px; left:1040px;'>
                        </div>


                        <!-- ASSET 10 -->
                        <img src='../image.php?id=10' style='width:15px; cursor:pointer; position:absolute; top:68px; left:467px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10' onclick='fetchAssetData(10);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10); ?>; 
                        position:absolute; top:68px; left:463px;'>
                        </div>


                        <!-- ASSET 11 -->
                        <img src='../image.php?id=11' style='width:15px; cursor:pointer; position:absolute; top:68px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11' onclick='fetchAssetData(11);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status11); ?>; 
                        position:absolute; top:68px; left:495px;'>
                        </div>


                        <!-- ASSET 12 -->
                        <img src='../image.php?id=12' style='width:12px; cursor:pointer; position:absolute; top:200px; left:530px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12' onclick='fetchAssetData(12);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status12); ?>; 
                        position:absolute; top:200px; left:530px;'>
                        </div>


                        <!-- ASSETS THAT ARE TABLE BLUE -->
                        <!-- ASSET 6866 TABLE BLUE -->
                        <img src='../image.php?id=6866' style='width:35px; cursor:pointer; position:absolute; top:225px; left:690px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6866' onclick='fetchAssetData(6866);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status6866); ?>; 
                        position:absolute; top:225px; left:717px;'>
                        </div>

                        <!-- ASSET 898 -->
                        <img src='../image.php?id=898' style='width:34px; cursor:pointer; position:absolute; top:225.2px; left:656px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal898' onclick='fetchAssetData(898);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status898); ?>; 
                        position:absolute; top:225px; left:656px;'>
                        </div>

                        <!-- ASSET 899 -->
                        <img src='../image.php?id=899' style='width:34px; cursor:pointer; position:absolute; top:172px; left:640px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal899' onclick='fetchAssetData(899);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status899); ?>; 
                        position:absolute; top:170px; left:665px;'>
                        </div>

                        <!-- WALANG 900 SINCE IBANG ROOM NA -->

                        <!-- ASSET 901 -->
                        <img src='../image.php?id=901' style='width:34px; cursor:pointer; position:absolute; top:278px; left:822px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal901' onclick='fetchAssetData(901);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status901); ?>; 
                        position:absolute; top:278px; left:822px;'>
                        </div>

                        <!-- ASSET 7262 -->
                        <img src='../image.php?id=7262' style='width:34px; cursor:pointer; position:absolute; top:100px; left:822px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7262' onclick='fetchAssetData(7262);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7262); ?>; 
                        position:absolute; top:100px; left:822px;'>
                        </div>


                    </div>



                    <!-- Modal structure for id 1-->
                    <div class='modal fade' id='imageModal1' tabindex='-1' aria-labelledby='imageModalLabel1' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId1); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img1); ?>" alt="No Image">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId1); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date1); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room1); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building1); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor1); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category1); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status1 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status1 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status1 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status1 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName1); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy1); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description1); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">
                                                Save
                                            </button>
                                        </div>
                                </div>
                                <!-- Modal footer -->

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 1-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit1">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>



                    <!-- Modal structure for id 2-->
                    <div class='modal fade' id='imageModal2' tabindex='-1' aria-labelledby='imageModalLabel2' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2); ?>" alt="No Image">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                Save
                                            </button>
                                        </div>
                                </div>
                                <!-- Modal footer -->

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 3-->
                    <div class='modal fade' id='imageModal3' tabindex='-1' aria-labelledby='imageModalLabel3' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId3); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img3); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId3); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date3); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room3); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building3); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor3); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category3); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status3 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status3 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status3 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status3 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName3); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy3); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description3); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop3">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 3-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit3">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>



                    <!-- Modal structure for id 4-->
                    <div class='modal fade' id='imageModal4' tabindex='-1' aria-labelledby='imageModalLabel4' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId4); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img4); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId4); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date4); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room4); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building4); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor4); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category4); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status4 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status4 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status4 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status4 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName4); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy4); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description4); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <div class="button-submit-container">

                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop4">
                                                Save
                                            </button>
                                        </div>
                                </div>
                                <!-- Modal footer -->

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 4-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit4">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 5-->
                    <div class='modal fade' id='imageModal5' tabindex='-1' aria-labelledby='imageModalLabel5' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status5 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status5 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status5 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status5 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5">
                                                Save
                                            </button>
                                        </div>
                                </div>
                                <!-- Modal footer -->

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 5-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit5">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 6-->
                    <div class='modal fade' id='imageModal6' tabindex='-1' aria-labelledby='imageModalLabel6' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId6); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img6); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId6); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date6); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room6); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building6); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor6); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category6); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status6 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status6 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status6 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status6 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName6); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy6); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description6); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop6">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 6-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop6" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit6">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 7-->
                    <div class='modal fade' id='imageModal7' tabindex='-1' aria-labelledby='imageModalLabel7' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId7); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId7); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date7); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room7); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building7); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor7); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category7); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status7 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status7 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status7 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status7 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName7); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy7); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description7); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop7">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="map-alert">
                        <!--Edit for table 7-->
                        <div class="modal fade" id="staticBackdrop7" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit7">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 8-->
                    <div class='modal fade' id='imageModal8' tabindex='-1' aria-labelledby='imageModalLabel8' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId8); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img8); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId8); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date8); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room8); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building8); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor8); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category8); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status8 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status8 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status8 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status8 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName8); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy8); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description8); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2 Upload">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" accept="image/*" capture="user" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop8">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 8-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop8" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit8">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 9-->
                    <div class='modal fade' id='imageModal9' tabindex='-1' aria-labelledby='imageModalLabel9' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId9); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img9); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId9); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date9); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room9); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building9); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor9); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category9); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status9 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status9 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status9 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status9 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName9); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy9); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description9); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop9">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 9-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop9" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit9">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>



                    <!-- Modal structure for id 10-->
                    <div class='modal fade' id='imageModal10' tabindex='-1' aria-labelledby='imageModalLabel10' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId10); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img10); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId10); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date10); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room10); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building10); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor10); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category10); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status10 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status10 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status10 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status10 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName10); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy10); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description10); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop10">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 10-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop10" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit10">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 11-->
                    <div class='modal fade' id='imageModal11' tabindex='-1' aria-labelledby='imageModalLabel11' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId11); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId11); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date11); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room11); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building11); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor11); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category11); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status11 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status11 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status11 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status11 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName11); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy11); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description11); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop11">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 11-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop11" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit11">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 12-->
                    <div class='modal fade' id='imageModal12' tabindex='-1' aria-labelledby='imageModalLabel12' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId12); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img12); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId12); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date12); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room12); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building12); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor12); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category12); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status12 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status12 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status12 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status12 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName12); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy12); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description12); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop12">
                                                Save
                                            </button>
                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--Edit for table 12-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop12" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit12">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 898-->
                    <div class='modal fade' id='imageModal898' tabindex='-1' aria-labelledby='imageModalLabel898' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId898); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img898); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId898); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date898); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room898); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building898); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor898); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category898); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status898 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status898 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status898 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status898 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName898); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy898); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description898); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop898">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 898-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop898" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit898">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 899-->
                    <div class='modal fade' id='imageModal899' tabindex='-1' aria-labelledby='imageModalLabel899' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId899); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img899); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId899); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date899); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room899); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building899); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor899); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category899); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status899 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status899 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status899 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status899 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName899); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy899); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description899); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop899">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 899-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop899" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit899">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 901-->
                    <div class='modal fade' id='imageModal901' tabindex='-1' aria-labelledby='imageModalLabel901' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId901); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img901); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId901); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date901); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room901); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building901); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor901); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category901); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status901 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status901 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status901 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status901 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName901); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy901); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description901); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop901">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 901-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop901" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit901">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>


                    <!-- Modal structure for id 6866-->
                    <div class='modal fade' id='imageModal6866' tabindex='-1' aria-labelledby='imageModalLabel6866' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId6866); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img6866); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId6866); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date6866); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room6866); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building6866); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor6866); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category6866); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status6866 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status6866 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status6866 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status6866 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName6866); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy6866); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description6866); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop6866">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 6866-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop6866" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit6866">Yes</button>
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
        <script>
            // Find all input elements with ID 'description'
            var inputElements = document.querySelectorAll('input#description');

            // Iterate through each input element
            inputElements.forEach(function(inputElement) {
                // Create a new textarea element
                var textareaElement = document.createElement('textarea');

                // Copy attributes from the input element
                textareaElement.className = inputElement.className;
                textareaElement.id = inputElement.id;
                textareaElement.name = inputElement.name;
                textareaElement.value = inputElement.value;

                // Replace the input element with the textarea element
                inputElement.parentNode.replaceChild(textareaElement, inputElement);
            });
        </script>

        <script src="../../../src/js/main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>