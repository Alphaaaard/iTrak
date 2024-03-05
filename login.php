<?php
session_start();
include_once("./config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user input from the form
$email = $_POST['email'];
$password = $_POST['password'];
$_SESSION['userLevel'] = $row['userLevel'];

// Store the submitted email in the session to retain it on the form
$_SESSION['login_email'] = $email;

// Protect against SQL injection
$email = mysqli_real_escape_string($conn, $email);
$password = mysqli_real_escape_string($conn, $password);

// Query to check user credentials
$query = "SELECT * FROM account WHERE email='$email' AND password='$password'";
$result = $conn->query($query);

function logLoginActivity($accountId, $action, $userName, $date, $tab, $seen, $m_seen, $p_seen)
{
    global $conn;

    $accountId = $conn->real_escape_string($accountId);
    $action = $conn->real_escape_string($action);
    $userName = $conn->real_escape_string($userName);
    $date = $conn->real_escape_string($date);
    $tab = $conn->real_escape_string($tab);

    $logMessage = "$userName $action";


    $sql = "INSERT INTO activitylogs (accountId, action, date, tab, seen, m_seen, p_seen) VALUES ('$accountId', '$logMessage', '$date', '$tab','$seen', '$m_seen', '$p_seen')";

    if ($conn->query($sql) === TRUE) {
        return true; // Successfully logged the login activity
    } else {
        return false; // Error occurred while logging the login activity
    }
}

if ($result->num_rows > 0) {
    // User is authenticated
    $row = $result->fetch_assoc();
    $userLevel = $row['userLevel'];
    $accountId = $row['accountId']; // Get the accountId from the account details

    if ($userLevel == 1) {
        // Super admin, no need to check attendance or timeout, proceed with login
        // Set user details in the session
        $_SESSION['accountId'] = $accountId;
        $_SESSION['email'] = $email;
        $_SESSION['firstName'] = $row['firstName'];
        $_SESSION['middleName'] = $row['middleName'];
        $_SESSION['lastName'] = $row['lastName'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['userLevel'] = $row['userLevel'];



        // Log the login activity
        logLoginActivity($accountId, 'logged in', $row['firstName'], date("Y-m-d H:i:s"), 'General','1','1','1');     //! UNCOMMENT LATER


        // Redirect to the super admin dashboard
        header("Location: users/administrator/dashboard.php");
        exit;
    } else if ($userLevel == 3) {
        // Personnel user
        $todayDate = date("Y-m-d"); // Today's date
        $attendanceQuery = "SELECT * FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
        $attendanceResult = $conn->query($attendanceQuery);

        if ($attendanceResult->num_rows > 0) {
            // There is a timeIn entry for today, proceed with login
            // Set user details in the session
            $_SESSION['accountId'] = $accountId;
            $_SESSION['email'] = $email;
            $_SESSION['firstName'] = $row['firstName'];
            $_SESSION['middleName'] = $row['middleName'];
            $_SESSION['lastName'] = $row['lastName'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['userLevel'] = $row['userLevel'];

            // Check if there's a timeout value for this user
            $timeoutQuery = "SELECT timeout FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
            $timeoutResult = $conn->query($timeoutQuery);
            $timeoutRow = $timeoutResult->fetch_assoc();

            if ($timeoutRow['timeout'] !== null) {
                // User has a timeout value, deny login
                $_SESSION['login_error'] = 'timeout';
                header("Location: index.php");
                exit;
            }
            logLoginActivity($accountId, 'logged in', $row['firstName'], date("Y-m-d H:i:s"), 'General','1','1','1');  


            // Redirect to the personnel dashboard
            header("Location: users/personnel/dashboard.php");
            exit;
        } else {
            // No timeIn entry for today, deny login
            $_SESSION['loginTap_error'] = 'attendance';
            header("Location: index.php");
            exit;
        }
    } else {
        // Other user levels (e.g., manager)
        // Proceed with login as before (check attendance)
        $todayDate = date("Y-m-d"); // Today's date
        $attendanceQuery = "SELECT * FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
        $attendanceResult = $conn->query($attendanceQuery);

        if ($attendanceResult->num_rows > 0) {
            // There is a timeIn entry for today, proceed with login
            // Set user details in the session
            $_SESSION['accountId'] = $accountId;
            $_SESSION['email'] = $email;
            $_SESSION['firstName'] = $row['firstName'];
            $_SESSION['middleName'] = $row['middleName'];
            $_SESSION['lastName'] = $row['lastName'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['userLevel'] = $row['userLevel'];



                // Check if there's a timeout value for this user
                $timeoutQuery = "SELECT timeout FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
                $timeoutResult = $conn->query($timeoutQuery);
                $timeoutRow = $timeoutResult->fetch_assoc();

                if ($timeoutRow['timeout'] !== null) {
                    // User has a timeout value, deny login
                    $_SESSION['login_error'] = 'timeout';
                    header("Location: index.php");
                    exit;
                }
                logLoginActivity($accountId, 'logged in', $row['firstName'], date("Y-m-d H:i:s"), 'General','1','1','1');  


            // Redirect to the appropriate landing page based on user level
            switch ($userLevel) {
                case 2:
                    header("Location: users/manager/dashboard.php");
                    exit;
            }
        } else {
            // No timeIn entry for today, deny login
            $_SESSION['loginTap_error'] = 'attendance';
            header("Location: index.php");
            exit;
        }
    }
} else {
    // Invalid credentials
    $_SESSION['loginwrong_error'] = 'credentials';
    header("Location: index.php");
    exit;
}


$conn->close();
