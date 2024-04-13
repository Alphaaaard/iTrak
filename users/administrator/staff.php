<?php
session_start();
include_once("../../config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {

  if ($_SESSION['userLevel'] != 1) {
    header("Location:error.php");
    exit;
  }

  $sql = "SELECT * FROM account";
  $result = $conn->query($sql) or die($conn->error);

  if (isset($_POST['accept'])) {
    $user_id = $_POST['accountid'];

    $select_query = "SELECT * FROM account WHERE accountId = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
      $result = $stmt->get_result();
    }
  }

  // Edit
  if (isset($_POST['edit'])) {
    $accountId = $_POST['accountid'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact = $_POST['contact'];
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $picture = $_POST['picture'];
    $userLevel = $_POST['userLevel'];

    $stmt = $conn->prepare("UPDATE `account` SET `firstName` = ?, `middleName` = ?, `lastName` = ?, `email` = ?, `password` = ?, `contact` = ?, `birthday` = ?, `role` = ?, `picture` = ?, `userLevel` = ? WHERE `accountId` = ?");
    $stmt->bind_param("ssssssssssi", $firstName, $middleName, $lastName, $email, $password, $contact, $birthday, $role, $picture, $userLevel, $accountId);

    if ($stmt->execute()) {
      exit;
    } else {
      echo "Error: " . $stmt->error;
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
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>iTrak | Staff</title>
    <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../../src/css/main.css" />
    <link rel="stylesheet" href="../../src/css/staff.css" />

    <style>
      .contact {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
      }
    </style>

    <script>
      $(document).ready(function() {

        let tabLastSelected = sessionStorage.getItem("lastPillStaff");

        if (!tabLastSelected) {
          $("#pills-manager").addClass("show active");
          $(".nav-link[data-bs-target='pills-manager']").addClass("active");
        } else {

          switch (tabLastSelected) {
            case 'pills-manager':
              $("#pills-manager").addClass("show active");
              $(".nav-link[data-bs-target='pills-manager']").addClass("active");
              break;
            case 'pills-profile':
              $("#pills-profile").addClass("show active");
              $("#pills-manager").removeClass("show active");
              $(".nav-link[data-bs-target='pills-profile']").addClass("active");
              $("#pills-manager").removeClass("show active");
              break;
          }
        }

        $(".nav-link").click(function() {
          const targetId = $(this).data("bs-target");

          sessionStorage.setItem("lastPillStaff", targetId);

          $(".tab-pane").removeClass("show active");
          $(`#${targetId}`).addClass("show active");
          $(".nav-link").removeClass("active");
          $(this).addClass("active");
        });

        const addBtn = document.querySelector("#addBtn");

        const firstName = document.getElementById("firstNameField").value.trim();
        const email = document.getElementById("emailField").value.trim();
        const password = document.getElementById("passwordField").value.trim();
        // Additional fields as necessary

        // code moved below $('#addBtn')
      });

      action = 'add'; // setting the action for registering rfid
    </script>
  </head>

  <body>
    <div id="navbar" class="">
      <nav>
        <div class="hamburger">
          <i class="bi bi-list"></i>
          <a href="#" class="brand" title="logo">
            <!-- <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i> -->
          </a>
        </div>
        <div class="content-nav">
          <div class="notification-dropdown">


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
                // Check the connection
                if ($conn->connect_error) {
                  die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
                }

                // Fetch the user's picture URL from the database
                $userId = $_SESSION['accountId'];
                $query = "SELECT picture FROM account WHERE accountId = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                  $stmt->bind_result($userPicture);
                  $stmt->fetch();

                  // Display the user profile image
                  echo "<img src='data:image/jpeg;base64," . base64_encode($userPicture) . "' title='profile-picture' />";
                } else {
                  // Fallback to displaying the user's first name
                  echo $_SESSION['firstName'];
                }

                $stmt->close();
                ?>
              </div>
              <div class="profile-name-container " id="desktop">
                <div><a class="profile-name"><?php echo $_SESSION['firstName']; ?></a></div>
                <div><a class="profile-role"><?php echo $_SESSION['role']; ?></a></div>
              </div>
            </div>
          </a>

          <div id="settings-dropdown" class="dropdown-content1">
            <div class="profile-name-container" id="mobile">
              <div><a class="profile-name"><?php echo $_SESSION['firstName']; ?></a></div>
              <div><a class="profile-role"><?php echo $_SESSION['role']; ?></a></div>
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
        <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i>
        <div class="mobile-sidebar-close">
          <i class="bi bi-arrow-left-circle"></i>
        </div>
      </div>
      <ul class="side-menu top">
        <li>
          <a href="./dashboard.php">
            <i class="bi bi-grid"></i>
            <span class="text">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="./attendance-logs.php">
            <i class="bi bi-calendar-week"></i>
            <span class="text">Attendance Logs</span>
          </a>
        </li>
        <li class="active">
          <a href="./staff.php">
            <i class="bi bi-person"></i>
            <span class="text">Staff</span>
          </a>
        </li>
        <li>
          <a href="./gps.php" class="GPS-cont">
            <div class="GPS-side-cont">
              <i class="bi bi-geo-alt"></i>
              <span class="text">GPS</span>
            </div>
            <div class="GPS-ind">
              <i class="bi bi-chevron-up"></i>
            </div>
          </a>
        </li>
        <li>
          <a href="./map.php">
            <i class="bi bi-map"></i>
            <span class="text">Map</span>
          </a>
        </li>
        <li>
          <a href="./reports.php">
            <i class="bi bi-clipboard"></i>
            <span class="text">Reports</span>
          </a>
        </li>
        <li>
          <a href="./archive.php">
            <i class="bi bi-archive"></i>
            <span class="text">Archive</span>
          </a>
        </li>
        <li>
          <a href="./activity-logs.php">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span class="text">Activity Logs</span>
          </a>
        </li>
      </ul>
    </section>

    <section id="content">
      <main>
        <div class="content-container">
          <header>
            <div class="cont-header">
              <h1 class="tab-name"></h1>
              <div class="tbl-filter">
                <div class="button-container">
                  <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#exampleModal1" onclick="checkRole()">
                    ADD
                  </button>
                </div>
                <form class="d-flex" role="search">
                  <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
                </form>
              </div>
            </div>
          </header>
          <div class="new-nav-container">
            <!--Content start of tabs-->
            <div class="new-nav">
              <ul>
                <li><a href="#" class="nav-link" id="manager-pill" data-bs-target="pills-manager">Manager</a></li>
                <li><a href="#" class="nav-link" id="personnel-pill" data-bs-target="pills-profile">Personnel</a></li>
              </ul>
            </div>

            <!-- Export button -->
            <div class="export-mob-hide">
              <form method="post" id="exportForm">
                <input type="hidden" name="status" id="statusField" value="For Replacement">
                <button type="button" id="exportBtn" class="btn btn-outline-danger">Export Data</button>
              </form>
            </div>
          </div>

          <div class="tab-content" id="myTabContent">
            <!--Maintenance Manager-->
            <div class="tab-pane fade show active" id="pills-manager" role="tabpanel" aria-labelledby="home-tab">
              <div class="table-content">
                <div class='table-header'>
                  <table>
                    <tr>
                      <th></th>
                      <th></th>
                      <th>NAME</th>
                      <th>ROLE</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </table>
                </div>
                <?php
                $filterRole = isset($_GET['filterRole']) ? $_GET['filterRole'] : 'all';
                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                $query = "SELECT accountid, picture, firstname, lastname, role FROM account WHERE role = 'Maintenance Manager'";

                if (!empty($searchTerm)) {
                  $query .= " AND (firstname LIKE ? OR lastname LIKE ?)";
                }

                $stmt = $conn->prepare($query);

                if (!empty($searchTerm)) {
                  $searchPattern = '%' . $searchTerm . '%';
                  $stmt->bind_param('ss', $searchPattern, $searchPattern);
                }

                if (!$stmt->execute()) {
                  die('Error executing the query: ' . $stmt->error);
                }

                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  echo "<div class='table-container'>";
                  while ($row = $result->fetch_assoc()) {
                    echo "<table>";
                    echo '<tr class="clickable-row" data-account-id="' . $row['accountid'] . '" onclick="getUser(' . $row['accountid'] . ')">';
                    echo '<td>' . $row['accountid'] . '</td>';
                    echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row['picture']) . '" alt="Profile Image" class="rounded-img" style="width: 50px; height: 50px;" ></td>'; // Image cell
                    echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                    echo '<td>' . $row['role'] . '</td>';
                    echo '<td>
                                                <button type="button" class="btn archive-button archive-btn arch" onclick="archive(' . $row['accountid'] . ', `' . $row['firstname'] . '`, `' . $row['lastname'] . '`, event)">ARCHIVE</button>
                                            </td>';
                    echo '</tr>';
                  }
                  echo "</table>";
                  echo "</div>";
                } else {
                  echo "<div class='no-results'>No results found</div>";
                }
                $stmt->close();
                ?>
                <script>
                  document.getElementById('search-box').addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                      event.preventDefault();
                      applyFilter();
                    }
                  });
                </script>
              </div>
            </div>

            <!--Maintenance Personnel-->
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="personnel-tab">
              <div class="table-content">
                <div class='table-header'>
                  <table>
                    <tr>
                      <th></th>
                      <th></th>
                      <th>NAME</th>
                      <th>ROLE</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </table>
                </div>
                <?php
                $filterRole = isset($_GET['filterRole']) ? $_GET['filterRole'] : 'all';
                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                $query = "SELECT accountid, picture, firstname, lastname, role FROM account WHERE role = 'Maintenance Personnel'";

                if (!empty($searchTerm)) {
                  $query .= " AND (firstname LIKE ? OR lastname LIKE ?)";
                }

                $stmt = $conn->prepare($query);

                if (!empty($searchTerm)) {
                  $searchPattern = '%' . $searchTerm . '%';
                  $stmt->bind_param('ss', $searchPattern, $searchPattern);
                }

                if (!$stmt->execute()) {
                  die('Error executing the query: ' . $stmt->error);
                }

                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                  echo "<div class='table-container'>";
                  while ($row = $result->fetch_assoc()) {
                    echo "<table>";
                    echo '<tr class="clickable-row" data-account-id="' . $row['accountid'] . '" onclick="getUser(' . $row['accountid'] . ')">';
                    echo '<td>' . $row['accountid'] . '</td>';
                    echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row['picture']) . '" alt="Profile Image" class="rounded-img" style="width: 50px; height: 50px;"></td>';
                    echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                    echo '<td>' . $row['role'] . '</td>';
                    echo '<td>
                                                <button type="button" class="btn archive-button archive-btn arch" onclick="archive(' . $row['accountid'] . ', `' . $row['firstname'] . '`, `' . $row['lastname'] . '`, event)">ARCHIVE</button>
                                            </td>';
                    echo '</tr>';
                  }
                  echo "</table>";
                  echo "</div>";
                } else {
                  echo "<div class='no-results'>No results found</div>";
                }

                $stmt->close();
                ?>
                <script>
                  document.getElementById('search-box').addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                      event.preventDefault();
                      applyFilter();
                    }
                  });
                </script>
              </div>
            </div>

            <form method="POST" action="archive_user.php" class="d-none" id="archiveDetailsForm">
              <input type="hidden" name="archiveAccId" id="archiveAccId">
            </form>

            <!-- RFID MODAL -->
            <div class="modal" id="staticBackdrop112" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-body">
                    <img src="../../src/img/taprfid.jpg" width="100%" alt="" class="Scan" />
                    <form id="rfidForm">
                      <input type="text" id="rfid" name="rfid" value="">
                    </form>
                  </div> <label class="btn btn-close-modal-emp close-modal-btn" data-bs-toggle="modal" data-bs-target="#exampleModal1"><i class="bi bi-x-lg"></i></label>
                </div>
              </div>
            </div>

            <script>
              function saveRFIDToValue(event) {
                console.log('SUBMIT??')
                event.preventDefault();

                // Get value from the RFID input
                var rfidValue = document.getElementById('rfid').value;
                document.getElementById('rfidFieldAdd').value = rfidValue;

                // Check if the RFID value is not empty
                if (rfidValue.trim() !== "") {
                  console.log("meron ba")

                  $('#rfid').val("")
                  if (action === 'add') {
                    $("#exampleModal1").modal("show");
                  } else {
                    $("#updateModal").modal("show");
                    $("#rfidFieldEdit").val(rfidValue)
                  }
                  $("#staticBackdrop112").modal("hide");
                }
              }

              document.getElementById('rfidForm').addEventListener('submit', saveRFIDToValue);
              document.getElementById('staticBackdrop112').addEventListener('shown.bs.modal', function() {
                document.getElementById('rfid').focus();
              });
            </script>



            <!-- ADD -->
            <div class="modal-parent">
              <div class="modal modal-xl fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5>Add Maintenance Staff</h5>

                      <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body">
                      <form method="post" enctype="multipart/form-data" class="row g-3" id="addForm">

                        <div class="col-4">
                          <label for="firstName" class="form-label label">First name <span class="d-none text-danger error">*</span></label>
                          <input type="text" class="form-control" id="firstNameField" name="firstName" placeholder="First Name" oninput="this.value = this.value.replace(/\d/g, '')" required />
                        </div>

                        <div class="col-4">
                          <label for="middleName" class="form-label label">Middle name <span class="d-none text-danger error">*</span></label>
                          <input type="text" class="form-control" id="middleNameField" name="middleName" placeholder="Middle Name" oninput="this.value = this.value.replace(/\d/g, '')" />
                        </div>

                        <div class="col-4">
                          <label for="lastName" class="form-label">Last name <span class="d-none text-danger error">*</span></label>
                          <input type="text" class="form-control" id="lastNameField" name="lastName" placeholder="Last Name" required oninput="this.value = this.value.replace(/\d/g, '')" />
                        </div>

                        <div class="col-4">
                          <label for="contactField" class="form-label">Contact Number <span class="d-none text-danger error">*</span></label>

                          <input type="tel" class="form-control contact" id="contactField" name="contact" required maxlength="11" value="09" title="Contact number must start with '09' and be 10 to 11 digits long" oninput="this.value = this.value.replace(/\D/g, '').substring(0, 11)" />

                        </div>

                        <div class="col-4">
                          <label for="email" class="form-label">Email <span class="d-none text-danger error">*</span></label>
                          <input type="text" class="form-control" id="emailField" name="email" placeholder="Email" required />
                        </div>

                        <div class="col-4">
                          <label for="password" class="form-label">Password <span class="d-none text-danger error">*</span></label>
                          <input type="password" class="form-control" id="passwordField" name="password" placeholder="Password" required />
                          <i class="bi bi-eye-slash" id="togglePassword"></i>
                        </div>

                        <div class="col-4">
                          <label for="birthday" class="form-label">Birthday <span class="d-none text-danger error">*</span></label>
                          <input type="date" class="form-control" id="birthdayField" name="birthday" max="2005-01-01" placeholder="Birthday" required />
                        </div>

                        <div class="col-4">
                          <label for="role" class="form-label">Role <span class="d-none text-danger error">*</span></label>
                          <input type="text" class="form-control" id="roleField" name="role" required />
                        </div>

                        <div class="col-4">
                          <label for="user_pass" class="form-label">Register RFID <span class="d-none text-danger error">*</span></label>
                          <button type="button" class="form-control btn-custom" data-bs-toggle="modal" data-bs-target="#staticBackdrop112" onclick="setAction('add');" value="123456789">SCAN</button>
                          <input type="password" name="rfidNumber" id="rfidFieldAdd" title="" value="1234567" required />
                        </div>

                        <div class="col-5">
                          <label for="picture" class="form-label">Picture <span class="d-none text-danger error">*</span></label>
                          <input type="file" class="form-control" id="pictureField" name="picture" required />
                        </div>

                        <div class="col-4">
                          <label for="role" class="form-label" style="display: none;">userLevel</label>
                          <input type="hidden" class="form-control" name="userLevel" id="userLevelField" />
                        </div>

                      </form>
                    </div>

                    <div class="footer">
                      <button type="button" class="btn add-modal-btn" id="addBtn">
                        ADD
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- UPDATE -->
            <div class="modal-parent">
              <div class="modal modal-xl fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5>Maintenance Staff Information</h5>
                      <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body ">
                      <form class="row g-3 userUpdateForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="accountId" class="accountIdEdit" id="accountIdEdit">

                        <div class="col-4">
                          <label for="firstname" class="form-label">First name</label>
                          <input type="text" class="form-control" id="firstnameEdit" name="firstname" oninput="this.value = this.value.replace(/\d/g, '')" />
                        </div>

                        <div class="col-4">
                          <label for="middlename" class="form-label">Middle name</label>
                          <input type="text" class="form-control" id="middlenameEdit" name="middlename" oninput="this.value = this.value.replace(/\d/g, '')" />
                        </div>

                        <div class="col-4">
                          <label for="lastname" class="form-label">Last name</label>
                          <input type="text" class="form-control" id="lastnameEdit" name="lastname" oninput="this.value = this.value.replace(/\d/g, '')" />
                        </div>

                        <div class="col-4">
                          <label for="contact" class="form-label">Contact Number</label>
                          <input type="text" class="form-control contact contactEdit" id="contactEdit" maxlength="11" name="contact" />
                        </div>

                        <div class="col-4">
                          <label for="email" class="form-label">Email</label>
                          <input type="text" class="form-control" id="emailEdit" name="email" />
                        </div>

                        <div class="col-4">
                          <label for="password" class="form-label">Password</label>
                          <input type="password" class="form-control" id="passwordEdit" name="password" />
                          <i class="bi bi-eye-slash" id="togglePassword"></i>
                        </div>

                        <div class="col-4">
                          <label for="birthday" class="form-label">Birthday</label>
                          <input type="date" class="form-control" id="birthdayEdit" max="2005-01-01" name="birthday" />
                        </div>

                        <div class="col-4">
                          <label for="role" class="form-label">Role</label>
                          <input type="text" class="form-control" id="roleEdit" name="role" />
                        </div>

                        <div class="col-4">
                          <label for="user_pass" class="form-label">Register RFID</label>
                          <button type="button" class="form-control btn-custom" data-bs-toggle="modal" data-bs-target="#staticBackdrop112" onclick="setAction('edit')">RFID</button>

                          <input type="password" id="rfidFieldEdit" name="rfid">
                        </div>

                        <div class="col-5">
                          <label for="picture" class="form-label">Picture:</label>
                          <input type="file" class="form-control" id="pictureEdit" name="picture" />
                        </div>
                      </form>
                    </div>

                    <div class="footer">
                      <button type="button" class="btn add-modal-btn updateBtn" id="updateBtn">
                        SAVE
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </section>

    <?php include_once 'modals/modal_layout.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../../src/js/main.js"></script>
    <script src="../../src/js/ajax.js"></script>
    <script src="../../src/js/staff.js"></script>
    <script src="../../src/js/profileModalController.js"></script>



    <script>
      $(document).ready(function() {
        $('.notification-item').on('click', function(e) {
          e.preventDefault();
          var activityId = $(this).data('activity-id');
          var notificationItem = $(this); // Store the clicked element

          $.ajax({
            type: "POST",
            url: "update_single_notification.php", // The URL to the PHP file
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
        // Bind the filter function to the input field
        $("#search-box").on("input", function() {
          var query = $(this).val().toLowerCase();
          filterTable(query);
        });

        function filterTable(query) {
          $(".table-container tbody tr").each(function() {
            var row = $(this);
            var archiveIDCell = row.find("td:eq(0)"); // Archive ID column
            var firstNameCell = row.find("td:eq(1)"); // FirstName column
            var middleNameCell = row.find("td:eq(2)"); // LastName column
            var lastNameCell = row.find("td:eq(3)"); // LastName column
            var roleCell = row.find("td:eq(10)"); // LastName column

            // Get the text content of each cell
            var archiveIDText = archiveIDCell.text().toLowerCase();
            var firstNameText = firstNameCell.text().toLowerCase();
            var middleNameText = middleNameCell.text().toLowerCase();
            var lastNameText = lastNameCell.text().toLowerCase();
            var roleText = roleCell.text().toLowerCase();

            // Check if any of the cells contain the query
            var showRow = archiveIDText.includes(query) ||
              firstNameText.includes(query) ||
              middleNameText.includes(query) ||
              lastNameText.includes(query) ||
              roleText.includes(query) ||
              archiveIDText == query || // Exact match for Archive ID
              firstNameText == query || // Exact match for FirstName
              middleNameText == query || // Exact match for LastName
              lastNameText == query || // Exact match for LastName
              roleText == query; // Exact match for LastName

            // Show or hide the row based on the result
            if (showRow) {
              row.show();
            } else {
              row.hide();
            }
          });
        }
      });
    </script>
    <script>
      const togglePassword = document.querySelector("#togglePassword");
      const password = document.querySelector("#passwordEdit");

      togglePassword.addEventListener("click", function() {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);

        // Toggle the eye icon class properly
        this.classList.toggle("bi-eye");
      });
    </script>

    <script>
      document.getElementById('exportBtn').addEventListener('click', function() {
        console.log("Export button clicked"); // Debugging line
        var searchQuery = document.getElementById('search-box').value; // Get the value of the search box
        var formData = new FormData(document.getElementById('exportForm'));

        // Directly determine the role based on the active tab class
        var activeTab = document.querySelector('.nav-link.active').getAttribute('data-bs-target');
        var role = activeTab === 'pills-manager' ? 'Maintenance Manager' : 'Maintenance Personnel';

        formData.append('searchQuery', searchQuery); // Include the search query in the FormData
        formData.append('role', role); // Append determined role to formData

        Swal.fire({
          title: 'Choose the file format',
          showDenyButton: true,
          confirmButtonText: 'PDF',
          denyButtonText: 'Excel',
        }).then((result) => {
          if (result.isConfirmed) {
            formData.append('submit', 'Export to PDF');
            performExport(formData, 'export-pdf-staffs.php');
          } else if (result.isDenied) {
            formData.append('submit', 'Export to Excel');
            performExport(formData, 'export-excel-staffs.php');
          }
        });
      });

      function performExport(formData, endpoint) {
        Swal.fire({
          title: 'Exporting...',
          html: 'Please wait while the file is being generated.',
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          },
        });

        fetch(endpoint, {
            method: 'POST',
            body: formData,
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.blob();
          })
          .then(blob => {
            const role = formData.get('role').replace(/ /g, '-'); // Get the role and replace spaces with hyphens for the file name
            const fileExtension = getFileExtension(endpoint);
            const fileName = `${role}.${fileExtension}`;

            const downloadUrl = window.URL.createObjectURL(blob);
            const downloadLink = document.createElement('a');
            downloadLink.href = downloadUrl;
            downloadLink.download = fileName;
            document.body.appendChild(downloadLink);
            downloadLink.click();

            window.URL.revokeObjectURL(downloadUrl);
            document.body.removeChild(downloadLink);

            Swal.fire({
              title: 'Exporting Done',
              text: 'Your file has been successfully generated.',
              icon: 'success',
              confirmButtonText: 'OK'
            });
          })
          .catch(error => {
            Swal.fire({
              title: 'Error',
              text: 'There was an issue generating the file.',
              icon: 'error',
              confirmButtonText: 'OK'
            });
          });
      }

      function getFileExtension(endpoint) {
        if (endpoint.includes('pdf')) return 'pdf';
        if (endpoint.includes('excel')) return 'xlsx';
        return '';
      }
    </script>
  </body>

  </html>