<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 




// ******************************************BASAHIN NYO MUNA TO**********************************************

// SQL = 910
// SQL2 = 911
// SQL3 = 912
// SQL4 = 913
// SQL5 = 914
// SQL6 = 915
// SQL7 = 916
// SQL8 = 917
// SQL9 = 918
// SQL10 = 9
// SQL11 = 9
// SQL12 = 9
// SQL6886 = 9


// ******************************************BASAHIN NYO MUNA TO**********************************************


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




    //FOR ID 910 LIGHTS
    $sql = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = 910";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $assetId = $row['assetId'];
    $category = $row['category'];
    $date = $row['date'];
    $building = $row['building'];
    $floor = $row['floor'];
    $room = $row['room'];
    $status = $row['status'];
    $assignedName = $row['assignedName'];
    $assignedBy = $row['assignedBy'];
    $upload_img = $row['upload_img'];
    $description = $row['description'];

    //FOR ID 911 LIGHTS
    $sql2 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = 911";
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

     //FOR ID 912 LIGHTS
    $sql3 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 912";
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


   //FOR ID 913 LIGHTS
    $sql4 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 913";
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


 //FOR ID 914 LIGHTS
    $sql5 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 914";
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

  //FOR ID 915 LIGHTS
    $sql6 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 915";
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


 //FOR ID 916 LIGHTS
    $sql7 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 916";
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


    //FOR ID 917 BED
    $sql8 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 917";
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


    //FOR ID 918 BED
    $sql9 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 918";
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


    //FOR ID 6866 TABLE
    // $sql6866 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6866";
    // $stmt6866 = $conn->prepare($sql6866);
    // $stmt6866->execute();
    // $result6866 = $stmt6866->get_result();
    // $row6866 = $result6866->fetch_assoc();
    // $assetId6866 = $row6866['assetId'];
    // $category6866 = $row6866['category'];
    // $date6866 = $row6866['date'];
    // $building6866 = $row6866['building'];
    // $floor6866 = $row6866['floor'];
    // $room6866 = $row6866['room'];
    // $status6866 = $row6866['status'];
    // $assignedName6866 = $row6866['assignedName'];
    // $assignedBy6866 = $row6866['assignedBy'];
    // $upload_img6866 = $row6866['upload_img'];
    // $description6866 = $row6866['description'];


    //FOR ID 1
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
        // Get form data
        $assetId = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status = $_POST['status']; // Get the status from the form
        $description = $_POST['description']; // Get the description from the form
        $room = $_POST['room']; // Get the room from the form
        $assignedBy = $_POST['assignedBy'];
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
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt->close();
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
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
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt12->close();
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
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6866->close();
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
                header("Location: TEBF1.php");
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
        <title>Dashboard</title>
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
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><img src="../../../src/icons/Profile.svg" alt="" class="profile-icons">Profile</a>
                        <a class="profile-hover" href="#"><img src="../../../src/icons/Logout.svg" alt="" class="profile-icons">Settings</a>
                        <a class="profile-hover" href="#" id="logoutBtn"><img src="../../../src/icons/Settings.svg" alt="" class="profile-icons">Logout</a>
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
                        <a href="../../administrator/map.php" class="closeFloor"><i class="bi bi-arrow-left"></i></a>
                        <!-- FLOOR PLAN -->
                        <img class="Floor-container-1" src="../../../src/floors/techvocB/TV1F.png" alt="">
                        <!-- assetss -->

                        <!-- ASSET 910 -->
                        <img src="../image.php?id=910" style="width:15px; cursor:pointer; position:absolute; top:85px; left:610px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal910" onclick="fetchAssetData(910);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status); ?>; 
                        position:absolute; top:85px; left:620px;'>
                        </div>

                          <!-- ASSET 911 -->
                          <img src="../image.php?id=911" style="width:15px; cursor:pointer; position:absolute; top:115px; left:610px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal911" onclick="fetchAssetData(911);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2); ?>; 
                        position:absolute; top:115px; left:620px;'>
                        </div>

                        <!-- ASSET 912 -->
                          <img src="../image.php?id=912" style="width:15px; cursor:pointer; position:absolute; top:107px; left:720px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal912" onclick="fetchAssetData(912);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status3); ?>; 
                        position:absolute; top:107px; left:730px;'>
                        </div>

                          <!-- ASSET 913 -->
                          <img src="../image.php?id=913" style="width:15px; cursor:pointer; position:absolute; top:85px; left:810px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal913" onclick="fetchAssetData(913);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status4); ?>; 
                        position:absolute; top:85px; left:820px;'>
                        </div>

                        <!-- ASSET 914 -->
                        <img src="../image.php?id=914" style="width:15px; cursor:pointer; position:absolute; top:135px; left:810px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal914" onclick="fetchAssetData(914);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5); ?>; 
                        position:absolute; top:135px; left:820px;'>
                        </div>



                        <!-- ASSET 915 -->
                        <img src="../image.php?id=915" style="width:15px; cursor:pointer; position:absolute; top:160px; left:640px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal915" onclick="fetchAssetData(915);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status6); ?>; 
                        position:absolute; top:160px; left:650px;'>
                        </div>

                        <!-- ASSET 916 -->
                        <img src="../image.php?id=916" style="width:15px; cursor:pointer; position:absolute; top:157px; left:767px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal916" onclick="fetchAssetData(916);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7); ?>; 
                        position:absolute; top:157px; left:777px;'>
                        </div>

                        <!-- ASSET 917 -->
                        <img src="../image.php?id=917" style="width:35px; cursor:pointer; position:absolute; top:95px; left:650px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal917" onclick="fetchAssetData(917);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status8); ?>; 
                        position:absolute; top:95px; left:672px;'>
                        </div>

                           <!-- ASSET 918 -->
                        <img src="../image.php?id=918" style="width:35px; cursor:pointer; position:absolute; top:95px; left:770px; " alt="Asset Image" data-bs-toggle="modal" data-bs-target="#imageModal918" onclick="fetchAssetData(918);">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9); ?>; 
                        position:absolute; top:95px; left:792px;'>
                        </div>

                    </div>



                    <!-- Modal structure for id 910 -->
                    <div class='modal fade' id='imageModal910' tabindex='-1' aria-labelledby='imageModalLabel910' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img); ?>" alt="No Image">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="images" class="form-label">Images:</label>
                                            <input type="text" class="form-control" id="" name="images" readonly />
                                        </div>

                                        <!--End of Third Row-->

                                        <!--Fourth Row-->
                                        <div class="col-2">
                                            <label for="status" class="form-label">Status:</label>
                                        </div>

                                        <div class="col-6">
                                            <select class="form-select" id="status" name="status">
                                                <option value="Working" <?php echo ($status == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>" />
                                        </div>
                                        <!--End of Fifth Row-->

                                        <!--Sixth Row-->
                                        <div class="col-2">
                                            <label for="upload_img" class="form-label">Upload:</label>
                                        </div>
                                        <div class="col-10">
                                            <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                        </div>
                                        <!--End of Sixth Row-->

                                        <!-- Modal footer -->
                                        <div class="button-submit-container">
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 1-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>



                <!-- Modal structure for id 911-->
                <div class='modal fade' id='imageModal911' tabindex='-1' aria-labelledby='imageModalLabel911' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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


                <!-- Modal structure for id 912-->
                <div class='modal fade' id='imageModal912' tabindex='-1' aria-labelledby='imageModalLabel912' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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



                <!-- Modal structure for id 913-->
                <div class='modal fade' id='imageModal913' tabindex='-1' aria-labelledby='imageModalLabel913' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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


                <!-- Modal structure for id 914-->
                <div class='modal fade' id='imageModal914' tabindex='-1' aria-labelledby='imageModalLabel914' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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


                <!-- Modal structure for id 915-->
                <div class='modal fade' id='imageModal915' tabindex='-1' aria-labelledby='imageModalLabel915' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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

                <!-- Modal structure for id 916-->
                <div class='modal fade' id='imageModal916' tabindex='-1' aria-labelledby='imageModalLabel916' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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


                <!-- Modal structure for id 917-->
                <div class='modal fade' id='imageModal917' tabindex='-1' aria-labelledby='imageModalLabel917' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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


                <!-- Modal structure for id 918-->
                <div class='modal fade' id='imageModal918' tabindex='-1' aria-labelledby='imageModalLabel918' aria-hidden='true'>
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
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
        <script src="../../../src/js/main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>