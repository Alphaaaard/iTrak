<?php
session_start();
include_once ("../../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 3) {
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
   WHERE al.tab = 'General' AND al.p_seen = '0' AND al.action LIKE 'Assigned maintenance personnel%' AND al.action LIKE ? AND al.accountID != ?
   ORDER BY al.date DESC 
   LIMIT 5"; // Set limit to 5

    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("si", $pattern, $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();

    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs 
WHERE p_seen = '0' AND accountID != ? AND action LIKE 'Assigned maintenance personnel%' AND action LIKE ?";
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("is", $loggedInAccountId, $pattern);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();

    //FOR ID 7098
    $sql7098 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7098";
    $stmt7098 = $conn->prepare($sql7098);
    $stmt7098->execute();
    $result7098 = $stmt7098->get_result();
    $row7098 = $result7098->fetch_assoc();
    $assetId7098 = $row7098['assetId'];
    $category7098 = $row7098['category'];
    $date7098 = $row7098['date'];
    $building7098 = $row7098['building'];
    $floor7098 = $row7098['floor'];
    $room7098 = $row7098['room'];
    $status7098 = $row7098['status'];
    $assignedName7098 = $row7098['assignedName'];
    $assignedBy7098 = $row7098['assignedBy'];
    $upload_img7098 = $row7098['upload_img'];
    $description7098 = $row7098['description'];

    //FOR ID 7099
    $sql7099 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7099";
    $stmt7099 = $conn->prepare($sql7099);
    $stmt7099->execute();
    $result7099 = $stmt7099->get_result();
    $row7099 = $result7099->fetch_assoc();
    $assetId7099 = $row7099['assetId'];
    $category7099 = $row7099['category'];
    $date7099 = $row7099['date'];
    $building7099 = $row7099['building'];
    $floor7099 = $row7099['floor'];
    $room7099 = $row7099['room'];
    $status7099 = $row7099['status'];
    $assignedName7099 = $row7099['assignedName'];
    $assignedBy7099 = $row7099['assignedBy'];
    $upload_img7099 = $row7099['upload_img'];
    $description7099 = $row7099['description'];

    //FOR ID 7100
    $sql7100 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7100";
    $stmt7100 = $conn->prepare($sql7100);
    $stmt7100->execute();
    $result7100 = $stmt7100->get_result();
    $row7100 = $result7100->fetch_assoc();
    $assetId7100 = $row7100['assetId'];
    $category7100 = $row7100['category'];
    $date7100 = $row7100['date'];
    $building7100 = $row7100['building'];
    $floor7100 = $row7100['floor'];
    $room7100 = $row7100['room'];
    $status7100 = $row7100['status'];
    $assignedName7100 = $row7100['assignedName'];
    $assignedBy7100 = $row7100['assignedBy'];
    $upload_img7100 = $row7100['upload_img'];
    $description7100 = $row7100['description'];

    //FOR ID 7101
    $sql7101 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7101";
    $stmt7101 = $conn->prepare($sql7101);
    $stmt7101->execute();
    $result7101 = $stmt7101->get_result();
    $row7101 = $result7101->fetch_assoc();
    $assetId7101 = $row7101['assetId'];
    $category7101 = $row7101['category'];
    $date7101 = $row7101['date'];
    $building7101 = $row7101['building'];
    $floor7101 = $row7101['floor'];
    $room7101 = $row7101['room'];
    $status7101 = $row7101['status'];
    $assignedName7101 = $row7101['assignedName'];
    $assignedBy7101 = $row7101['assignedBy'];
    $upload_img7101 = $row7101['upload_img'];
    $description7101 = $row7101['description'];

    //FOR ID 7102
    $sql7102 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7102";
    $stmt7102 = $conn->prepare($sql7102);
    $stmt7102->execute();
    $result7102 = $stmt7102->get_result();
    $row7102 = $result7102->fetch_assoc();
    $assetId7102 = $row7102['assetId'];
    $category7102 = $row7102['category'];
    $date7102 = $row7102['date'];
    $building7102 = $row7102['building'];
    $floor7102 = $row7102['floor'];
    $room7102 = $row7102['room'];
    $status7102 = $row7102['status'];
    $assignedName7102 = $row7102['assignedName'];
    $assignedBy7102 = $row7102['assignedBy'];
    $upload_img7102 = $row7102['upload_img'];
    $description7102 = $row7102['description'];

    //FOR ID 7103
    $sql7103 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7103";
    $stmt7103 = $conn->prepare($sql7103);
    $stmt7103->execute();
    $result7103 = $stmt7103->get_result();
    $row7103 = $result7103->fetch_assoc();
    $assetId7103 = $row7103['assetId'];
    $category7103 = $row7103['category'];
    $date7103 = $row7103['date'];
    $building7103 = $row7103['building'];
    $floor7103 = $row7103['floor'];
    $room7103 = $row7103['room'];
    $status7103 = $row7103['status'];
    $assignedName7103 = $row7103['assignedName'];
    $assignedBy7103 = $row7103['assignedBy'];
    $upload_img7103 = $row7103['upload_img'];
    $description7103 = $row7103['description'];

    //FOR ID 7104
    $sql7104 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7104";
    $stmt7104 = $conn->prepare($sql7104);
    $stmt7104->execute();
    $result7104 = $stmt7104->get_result();
    $row7104 = $result7104->fetch_assoc();
    $assetId7104 = $row7104['assetId'];
    $category7104 = $row7104['category'];
    $date7104 = $row7104['date'];
    $building7104 = $row7104['building'];
    $floor7104 = $row7104['floor'];
    $room7104 = $row7104['room'];
    $status7104 = $row7104['status'];
    $assignedName7104 = $row7104['assignedName'];
    $assignedBy7104 = $row7104['assignedBy'];
    $upload_img7104 = $row7104['upload_img'];
    $description7104 = $row7104['description'];

    //FOR ID 7105
    $sql7105 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7105";
    $stmt7105 = $conn->prepare($sql7105);
    $stmt7105->execute();
    $result7105 = $stmt7105->get_result();
    $row7105 = $result7105->fetch_assoc();
    $assetId7105 = $row7105['assetId'];
    $category7105 = $row7105['category'];
    $date7105 = $row7105['date'];
    $building7105 = $row7105['building'];
    $floor7105 = $row7105['floor'];
    $room7105 = $row7105['room'];
    $status7105 = $row7105['status'];
    $assignedName7105 = $row7105['assignedName'];
    $assignedBy7105 = $row7105['assignedBy'];
    $upload_img7105 = $row7105['upload_img'];
    $description7105 = $row7105['description'];

    //FOR ID 7106
    $sql7106 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7106";
    $stmt7106 = $conn->prepare($sql7106);
    $stmt7106->execute();
    $result7106 = $stmt7106->get_result();
    $row7106 = $result7106->fetch_assoc();
    $assetId7106 = $row7106['assetId'];
    $category7106 = $row7106['category'];
    $date7106 = $row7106['date'];
    $building7106 = $row7106['building'];
    $floor7106 = $row7106['floor'];
    $room7106 = $row7106['room'];
    $status7106 = $row7106['status'];
    $assignedName7106 = $row7106['assignedName'];
    $assignedBy7106 = $row7106['assignedBy'];
    $upload_img7106 = $row7106['upload_img'];
    $description7106 = $row7106['description'];

    //FOR ID 7270
    $sql7270 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7270";
    $stmt7270 = $conn->prepare($sql7270);
    $stmt7270->execute();
    $result7270 = $stmt7270->get_result();
    $row7270 = $result7270->fetch_assoc();
    $assetId7270 = $row7270['assetId'];
    $category7270 = $row7270['category'];
    $date7270 = $row7270['date'];
    $building7270 = $row7270['building'];
    $floor7270 = $row7270['floor'];
    $room7270 = $row7270['room'];
    $status7270 = $row7270['status'];
    $assignedName7270 = $row7270['assignedName'];
    $assignedBy7270 = $row7270['assignedBy'];
    $upload_img7270 = $row7270['upload_img'];
    $description7270 = $row7270['description'];

    //FOR ID 7271
    $sql7271 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7271";
    $stmt7271 = $conn->prepare($sql7271);
    $stmt7271->execute();
    $result7271 = $stmt7271->get_result();
    $row7271 = $result7271->fetch_assoc();
    $assetId7271 = $row7271['assetId'];
    $category7271 = $row7271['category'];
    $date7271 = $row7271['date'];
    $building7271 = $row7271['building'];
    $floor7271 = $row7271['floor'];
    $room7271 = $row7271['room'];
    $status7271 = $row7271['status'];
    $assignedName7271 = $row7271['assignedName'];
    $assignedBy7271 = $row7271['assignedBy'];
    $upload_img7271 = $row7271['upload_img'];
    $description7271 = $row7271['description'];

    //FOR ID 7098
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7098'])) {
        // Get form data
        $assetId7098 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7098 = $_POST['status']; // Get the status from the form
        $description7098 = $_POST['description']; // Get the description from the form
        $room7098 = $_POST['room']; // Get the room from the form
        $assignedBy7098 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7098 = $status7098 === 'Need Repair' ? '' : $assignedName7098;

        // Prepare SQL query to update the asset
        $sql7098 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7098 = $conn->prepare($sql7098);
        $stmt7098->bind_param('sssssi', $status7098, $assignedName7098, $assignedBy7098, $description7098, $room7098, $assetId7098);

        if ($stmt7098->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7098 to $status7098.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7098->close();
    }

    //FOR ID 7099
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7099'])) {
        // Get form data
        $assetId7099 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7099 = $_POST['status']; // Get the status from the form
        $description7099 = $_POST['description']; // Get the description from the form
        $room7099 = $_POST['room']; // Get the room from the form
        $assignedBy7099 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7099 = $status7099 === 'Need Repair' ? '' : $assignedName7099;

        // Prepare SQL query to update the asset
        $sql7099 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7099 = $conn->prepare($sql7099);
        $stmt7099->bind_param('sssssi', $status7099, $assignedName7099, $assignedBy7099, $description7099, $room7099, $assetId7099);

        if ($stmt7099->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7099 to $status7099.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7099->close();
    }

    //FOR ID 7100
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7100'])) {
        // Get form data
        $assetId7100 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7100 = $_POST['status']; // Get the status from the form
        $description7100 = $_POST['description']; // Get the description from the form
        $room7100 = $_POST['room']; // Get the room from the form
        $assignedBy7100 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7100 = $status7100 === 'Need Repair' ? '' : $assignedName7100;

        // Prepare SQL query to update the asset
        $sql7100 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7100 = $conn->prepare($sql7100);
        $stmt7100->bind_param('sssssi', $status7100, $assignedName7100, $assignedBy7100, $description7100, $room7100, $assetId7100);

        if ($stmt7100->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7100 to $status7100.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7100->close();
    }

    //FOR ID 7101
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7101'])) {
        // Get form data
        $assetId7101 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7101 = $_POST['status']; // Get the status from the form
        $description7101 = $_POST['description']; // Get the description from the form
        $room7101 = $_POST['room']; // Get the room from the form
        $assignedBy7101 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7101 = $status7101 === 'Need Repair' ? '' : $assignedName7101;

        // Prepare SQL query to update the asset
        $sql7101 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7101 = $conn->prepare($sql7101);
        $stmt7101->bind_param('sssssi', $status7101, $assignedName7101, $assignedBy7101, $description7101, $room7101, $assetId7101);

        if ($stmt7101->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7101 to $status7101.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7101->close();
    }

    //FOR ID 7102
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7102'])) {
        // Get form data
        $assetId7102 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7102 = $_POST['status']; // Get the status from the form
        $description7102 = $_POST['description']; // Get the description from the form
        $room7102 = $_POST['room']; // Get the room from the form
        $assignedBy7102 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7102 = $status7102 === 'Need Repair' ? '' : $assignedName7102;

        // Prepare SQL query to update the asset
        $sql7102 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7102 = $conn->prepare($sql7102);
        $stmt7102->bind_param('sssssi', $status7102, $assignedName7102, $assignedBy7102, $description7102, $room7102, $assetId7102);

        if ($stmt7102->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7102 to $status7102.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7102->close();
    }

    //FOR ID 7103
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7103'])) {
        // Get form data
        $assetId7103 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7103 = $_POST['status']; // Get the status from the form
        $description7103 = $_POST['description']; // Get the description from the form
        $room7103 = $_POST['room']; // Get the room from the form
        $assignedBy7103 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7103 = $status7103 === 'Need Repair' ? '' : $assignedName7103;

        // Prepare SQL query to update the asset
        $sql7103 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7103 = $conn->prepare($sql7103);
        $stmt7103->bind_param('sssssi', $status7103, $assignedName7103, $assignedBy7103, $description7103, $room7103, $assetId7103);

        if ($stmt7103->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7103 to $status7103.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7103->close();
    }

    //FOR ID 7104
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7104'])) {
        // Get form data
        $assetId7104 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7104 = $_POST['status']; // Get the status from the form
        $description7104 = $_POST['description']; // Get the description from the form
        $room7104 = $_POST['room']; // Get the room from the form
        $assignedBy7104 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7104 = $status7104 === 'Need Repair' ? '' : $assignedName7104;

        // Prepare SQL query to update the asset
        $sql7104 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7104 = $conn->prepare($sql7104);
        $stmt7104->bind_param('sssssi', $status7104, $assignedName7104, $assignedBy7104, $description7104, $room7104, $assetId7104);

        if ($stmt7104->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7104 to $status7104.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7104->close();
    }

    //FOR ID 7105
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7105'])) {
        // Get form data
        $assetId7105 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7105 = $_POST['status']; // Get the status from the form
        $description7105 = $_POST['description']; // Get the description from the form
        $room7105 = $_POST['room']; // Get the room from the form
        $assignedBy7105 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7105 = $status7105 === 'Need Repair' ? '' : $assignedName7105;

        // Prepare SQL query to update the asset
        $sql7105 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7105 = $conn->prepare($sql7105);
        $stmt7105->bind_param('sssssi', $status7105, $assignedName7105, $assignedBy7105, $description7105, $room7105, $assetId7105);

        if ($stmt7105->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7105 to $status7105.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7105->close();
    }

    //FOR ID 7106
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7106'])) {
        // Get form data
        $assetId7106 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7106 = $_POST['status']; // Get the status from the form
        $description7106 = $_POST['description']; // Get the description from the form
        $room7106 = $_POST['room']; // Get the room from the form
        $assignedBy7106 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7106 = $status7106 === 'Need Repair' ? '' : $assignedName7106;

        // Prepare SQL query to update the asset
        $sql7106 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7106 = $conn->prepare($sql7106);
        $stmt7106->bind_param('sssssi', $status7106, $assignedName7106, $assignedBy7106, $description7106, $room7106, $assetId7106);

        if ($stmt7106->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7106 to $status7106.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7106->close();
    }

    //FOR ID 7270
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7270'])) {
        // Get form data
        $assetId7270 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7270 = $_POST['status']; // Get the status from the form
        $description7270 = $_POST['description']; // Get the description from the form
        $room7270 = $_POST['room']; // Get the room from the form
        $assignedBy7270 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7270 = $status7270 === 'Need Repair' ? '' : $assignedName7270;

        // Prepare SQL query to update the asset
        $sql7270 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7270 = $conn->prepare($sql7270);
        $stmt7270->bind_param('sssssi', $status7270, $assignedName7270, $assignedBy7270, $description7270, $room7270, $assetId7270);

        if ($stmt7270->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7270 to $status7270.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7270->close();
    }

    //FOR ID 7271
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7271'])) {
        // Get form data
        $assetId7271 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7271 = $_POST['status']; // Get the status from the form
        $description7271 = $_POST['description']; // Get the description from the form
        $room7271 = $_POST['room']; // Get the room from the form
        $assignedBy7271 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7271 = $status7271 === 'Need Repair' ? '' : $assignedName7271;

        // Prepare SQL query to update the asset
        $sql7271 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7271 = $conn->prepare($sql7271);
        $stmt7271->bind_param('sssssi', $status7271, $assignedName7271, $assignedBy7271, $description7271, $room7271, $assetId7271);

        if ($stmt7271->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7271 to $status7271.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: CHBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7271->close();
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
                header("Location: CHBF1.php");
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../src/js/locationTracker.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
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
                    <img src="../../../src/floors/chinesebB/ChineseB1F.png" alt="" class="Floor-container">
                    <div class="map-nav">
                        <a href="../../personnel/map.php" class="closeFloor"><i
                                class="bi bi-box-arrow-left"></i></i></a>
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

                    <!-- ASSET 7098 -->
                    <img src='../image.php?id=7098'
                        style='width:45px; cursor:pointer; position:absolute; top:120px; left:180px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7098' onclick='fetchAssetData(7098);'
                        class="asset-image" data-id="<?php echo $assetId7098; ?>"
                        data-room="<?php echo htmlspecialchars($room7098); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7098); ?>"
                        data-image="<?php echo base64_encode($upload_img7098); ?>"
                        data-category="<?php echo htmlspecialchars($category7098); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7098); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7098); ?>; 
    position:absolute; top:120px; left:180px;'>
                    </div>

                    <!-- ASSET 7099 -->
                    <img src='../image.php?id=7099'
                        style='width:45px; cursor:pointer; position:absolute; top:275px; left:180px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7099' onclick='fetchAssetData(7099);'
                        class="asset-image" data-id="<?php echo $assetId7099; ?>"
                        data-room="<?php echo htmlspecialchars($room7099); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7099); ?>"
                        data-image="<?php echo base64_encode($upload_img7099); ?>"
                        data-category="<?php echo htmlspecialchars($category7099); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7099); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7099); ?>; 
    position:absolute; top:275px; left:180px;'>
                    </div>

                    <!-- ASSET 7100 -->
                    <img src='../image.php?id=7100'
                        style='width:45px; cursor:pointer; position:absolute; top:425px; left:180px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7100' onclick='fetchAssetData(7100);'
                        class="asset-image" data-id="<?php echo $assetId7100; ?>"
                        data-room="<?php echo htmlspecialchars($room7100); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7100); ?>"
                        data-image="<?php echo base64_encode($upload_img7100); ?>"
                        data-category="<?php echo htmlspecialchars($category7100); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7100); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7100); ?>; 
    position:absolute; top:425px; left:180px;'>
                    </div>

                    <!-- ASSET 7101 -->
                    <img src='../image.php?id=7101'
                        style='width:45px; cursor:pointer; position:absolute; top:120px; left:590px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7101' onclick='fetchAssetData(7101);'
                        class="asset-image" data-id="<?php echo $assetId7101; ?>"
                        data-room="<?php echo htmlspecialchars($room7101); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7101); ?>"
                        data-image="<?php echo base64_encode($upload_img7101); ?>"
                        data-category="<?php echo htmlspecialchars($category7101); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7101); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7101); ?>; 
    position:absolute; top:120px; left:590px;'>
                    </div>


                    <!-- ASSET 7102 -->
                    <img src='../image.php?id=7102'
                        style='width:45px; cursor:pointer; position:absolute; top:275px; left:590px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7102' onclick='fetchAssetData(7102);'
                        class="asset-image" data-id="<?php echo $assetId7102; ?>"
                        data-room="<?php echo htmlspecialchars($room7102); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7102); ?>"
                        data-image="<?php echo base64_encode($upload_img7102); ?>"
                        data-category="<?php echo htmlspecialchars($category7102); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7102); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7102); ?>; 
    position:absolute; top:275px; left:590px;'>
                    </div>

                    <!-- ASSET 7103 -->
                    <img src='../image.php?id=7103'
                        style='width:45px; cursor:pointer; position:absolute; top:425px; left:590px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7103' onclick='fetchAssetData(7103);'
                        class="asset-image" data-id="<?php echo $assetId7103; ?>"
                        data-room="<?php echo htmlspecialchars($room7103); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7103); ?>"
                        data-image="<?php echo base64_encode($upload_img7103); ?>"
                        data-category="<?php echo htmlspecialchars($category7103); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7103); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7103); ?>; 
    position:absolute; top:425px; left:590px;'>
                    </div>

                    <!-- ASSET 7104 -->
                    <img src='../image.php?id=7104'
                        style='width:45px; cursor:pointer; position:absolute; top:125px; left:1000px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7104' onclick='fetchAssetData(7104);'
                        class="asset-image" data-id="<?php echo $assetId7104; ?>"
                        data-room="<?php echo htmlspecialchars($room7104); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7104); ?>"
                        data-image="<?php echo base64_encode($upload_img7104); ?>"
                        data-category="<?php echo htmlspecialchars($category7104); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7104); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7104); ?>; 
    position:absolute; top:125px; left:1000px;'>
                    </div>

                    <!-- ASSET 7105 -->
                    <img src='../image.php?id=7105'
                        style='width:45px; cursor:pointer; position:absolute; top:275px; left:1000px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7105' onclick='fetchAssetData(7105);'
                        class="asset-image" data-id="<?php echo $assetId7105; ?>"
                        data-room="<?php echo htmlspecialchars($room7105); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7105); ?>"
                        data-image="<?php echo base64_encode($upload_img7105); ?>"
                        data-category="<?php echo htmlspecialchars($category7105); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7105); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7105); ?>; 
    position:absolute; top:275px; left:1000px;'>
                    </div>


                    <!-- ASSET 7106 -->
                    <img src='../image.php?id=7106'
                        style='width:45px; cursor:pointer; position:absolute; top:425px; left:1000px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7106' onclick='fetchAssetData(7106);'
                        class="asset-image" data-id="<?php echo $assetId7106; ?>"
                        data-room="<?php echo htmlspecialchars($room7106); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7106); ?>"
                        data-image="<?php echo base64_encode($upload_img7106); ?>"
                        data-category="<?php echo htmlspecialchars($category7106); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7106); ?>">
                    <div style='width:11px; height:11px; border-radius:50%; background-color: <?php echo getStatusColor($status7106); ?>; 
    position:absolute; top:425px; left:1000px;'>
                    </div>

                    <!-- ASSET 7270 -->
                    <img src='../image.php?id=7270'
                        style='width:65px; cursor:pointer; position:absolute; top:255px; left:370px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7270' onclick='fetchAssetData(7270);'
                        class="asset-image" data-id="<?php echo $assetId7270; ?>"
                        data-room="<?php echo htmlspecialchars($room7270); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7270); ?>"
                        data-image="<?php echo base64_encode($upload_img7270); ?>"
                        data-category="<?php echo htmlspecialchars($category7270); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7270); ?>">
                    <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7270); ?>; 
    position:absolute; top:255px; left:370px;'>
                    </div>

                    <!-- ASSET 7271 -->
                    <img src='../image.php?id=7271'
                        style='width:65px; cursor:pointer; position:absolute; top:255px; left:780px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal7271' onclick='fetchAssetData(7271);'
                        class="asset-image" data-id="<?php echo $assetId7271; ?>"
                        data-room="<?php echo htmlspecialchars($room7271); ?>"
                        data-floor="<?php echo htmlspecialchars($floor7271); ?>"
                        data-image="<?php echo base64_encode($upload_img7271); ?>"
                        data-category="<?php echo htmlspecialchars($category7271); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName7271); ?>">
                    <div style='width:13px; height:13px; border-radius:50%; background-color: <?php echo getStatusColor($status7271); ?>; 
    position:absolute; top:255px; left:780px;'>
                    </div>


                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>

                    <!--End of hover-->


                </div>
                <!-- Modal structure for id 7098-->
                <div class='modal fade' id='imageModal7098' tabindex='-1' aria-labelledby='imageModalLabel7098'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7098); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7098); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7098); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7098); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7098); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7098); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7098); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7098); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7098 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7098 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7098 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7098 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7098); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7098); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7098); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7098">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7098-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7098" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7098">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7099-->
                <div class='modal fade' id='imageModal7099' tabindex='-1' aria-labelledby='imageModalLabel7099'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7099); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7099); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7099); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7099); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7099); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7099); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7099); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7099); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7099 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7099 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7099 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7099 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7099); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7099); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7099); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7099">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7099-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7099" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7099">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7100-->
                <div class='modal fade' id='imageModal7100' tabindex='-1' aria-labelledby='imageModalLabel7100'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7100); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7100); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7100); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7100); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7100); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7100); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7100); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7100); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7100 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7100 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7100 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7100 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7100); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7100); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7100); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7100">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7100-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7100" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7100">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7101-->
                <div class='modal fade' id='imageModal7101' tabindex='-1' aria-labelledby='imageModalLabel7101'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7101); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7101); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7101); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7101); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7101); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7101); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7101); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7101); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7101 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7101 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7101 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7101 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7101); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7101); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7101); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7101">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7101-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7101" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7101">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7102-->
                <div class='modal fade' id='imageModal7102' tabindex='-1' aria-labelledby='imageModalLabel7102'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7102); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7102); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7102); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7102); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7102); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7102); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7102); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7102); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7102 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7102 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7102 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7102 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7102); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7102); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7102); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7102">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7102-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7102" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7102">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7103-->
                <div class='modal fade' id='imageModal7103' tabindex='-1' aria-labelledby='imageModalLabel7103'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7103); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7103); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7103); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7103); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7103); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7103); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7103); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7103); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7103 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7103 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7103 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7103 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7103); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7103); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7103); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7103">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7103-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7103" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7103">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7104-->
                <div class='modal fade' id='imageModal7104' tabindex='-1' aria-labelledby='imageModalLabel7104'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7104); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7104); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7104); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7104); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7104); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7104); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7104); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7104); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7104 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7104 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7104 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7104 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7104); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7104); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7104); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7104">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7104-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7104" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7104">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7105-->
                <div class='modal fade' id='imageModal7105' tabindex='-1' aria-labelledby='imageModalLabel7105'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7105); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7105); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7105); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7105); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7105); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7105); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7105); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7105); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7105 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7105 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7105 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7105 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7105); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7105); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7105); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7105">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7105-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7105" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7105">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7106-->
                <div class='modal fade' id='imageModal7106' tabindex='-1' aria-labelledby='imageModalLabel7106'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7106); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7106); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7106); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7106); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7106); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7106); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7106); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7106); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7106 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7106 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7106 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7106 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7106); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7106); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7106); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7106">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7106-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7106" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7106">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7270-->
                <div class='modal fade' id='imageModal7270' tabindex='-1' aria-labelledby='imageModalLabel7270'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7270); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7270); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7270); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7270); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7270); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7270); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7270); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7270); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7270 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7270 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7270 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7270 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7270); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7270); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7270); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7270">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7270-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7270" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7270">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 7271-->
                <div class='modal fade' id='imageModal7271' tabindex='-1' aria-labelledby='imageModalLabel7271'
                    aria-hidden='true'>
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
                                        value="<?php echo htmlspecialchars($assetId7271); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7271); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId7271); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date7271); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room7271); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building7271); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor7271); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category7271); ?>"
                                            readonly />
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
                                            <option value="Working" <?php echo ($status7271 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status7271 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status7271 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status7271 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName7271); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy7271); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                            <label for="description" class="form-label">Description:</label>
                                        </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description7271); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop7271">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 7271-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop7271" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit7271">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
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
        $(document).ready(function () {
            $('.notification-item').on('click', function (e) {
                e.preventDefault();
                var activityId = $(this).data('activity-id');
                var notificationItem = $(this); // Store the clicked element

                $.ajax({
                    type: "POST",
                    url: "../../administrator/update_single_notification.php", // The URL to the PHP file
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>