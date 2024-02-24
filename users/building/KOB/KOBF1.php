<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {

   //FOR ID 1
   $sql = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = 1";
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

 
  //FOR ID 1
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
   // Get form data
   $assetId = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
   $status = $_POST['status']; // Get the status from the form
   $description = $_POST['description']; // Get the description from the form
   $room = $_POST['room']; // Get the room from the form
  

   // Check if status is "Need Repair" and set "Assigned Name" to none
   $assignedName = $status === 'Need Repair' ? '' : $assignedName;

   // Prepare SQL query to update the asset
   $sql = "UPDATE asset SET status = ?, assignedName = ?, description = ?, room = ? WHERE assetId = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param('ssssi', $status, $assignedName, $description, $room, $assetId);

   if ($stmt->execute()) {
       // Update success
       echo "<script>alert('Asset updated successfully!');</script>";
   } else {
       // Update failed
       echo "<script>alert('Failed to update asset.');</script>";
   }
   $stmt->close();
}



   function getStatusColor($status) {
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
                            <i class="bi bi-bell"></i>
                            <span class="num"></span>
                        </a>
                        <div class="dropdown-content" id="notification-dropdown-content">
                            <h6 class="dropdown-header">Alerts Center</h6>
                            <a href="#">May hindi nagbuhos sa Cr sa Belmonte building</a>
                            <a href="#">Notification 2</a>
                            <a href="#">Notification 3</a>
                            <a href="#" class="view-all">View All</a>
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
                    <img class="Floor-container-1" src="../../../src/floors/korPhil/Korphil1F.png" alt="">
                    <!-- ASSETS -->

                    <!-- ASSET 1 -->
                    <img src='../image.php?id=1'
                        style='width:40px; cursor:pointer; position:absolute; top:214px; left:435px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal1' onclick='fetchAssetData(1);'>
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status); ?>; 
                        position:absolute; top:214px; left:470px;'>
                    </div>
                    
                </div>

                  <!-- Modal structure for id 1 -->
                  <div class='modal fade' id='imageModal1' tabindex='-1' aria-labelledby='imageModalLabel1'
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
                                        <label for="date" class="form-label" >Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-4">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building"
                                            value="<?php echo htmlspecialchars($building); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                     <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category"
                                            value="<?php echo htmlspecialchars($category); ?>" readonly />
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

                                    <div class="col-10">
                                    <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status=='Working' )
                                                ? 'selected="selected"' : '' ; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status=='Under Maintenance' )
                                                ? 'selected="selected"' : '' ; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status=='For Replacement' )
                                                ? 'selected="selected"' : '' ; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status=='Need Repair' )
                                                ? 'selected="selected"' : '' ; ?>>Need Repair</option>
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
                                      <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div>
                                    <div class="col-9">
                                    <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description); ?>"  />
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
                            </div>
                            <!-- Modal footer -->
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop1">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 1-->
                <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                Are you sure you want to save changes?
                                <div class="modal-popups">
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    <button type="submit" class="btn add-modal-btn" name="edit">Yes</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                    
                               

                    
                </div>
            </main>
        </section>

        <script src="../../../src/js/main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>