<?php
session_start();
include_once ("../../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 




// ******************************************BASAHIN NYO MUNA TO**********************************************

// SQL = 910        // SQL906 = 906     // SQL930 = 930     // SQL943 = 943
// SQL2 = 911       // SQL907 = 907     // SQL931 = 931     // SQL944 = 944
// SQL3 = 912       // SQL908 = 908     // SQL932 = 932     // SQL945 = 945
// SQL4 = 913       // SQL909 = 909     // SQL933 = 933     // SQL946 = 946
// SQL5 = 914       // SQL920 = 920     // SQL934 = 934     // SQL947 = 947
// SQL6 = 915       // SQL921 = 921     // SQL935 = 935     // SQL948 = 948
// SQL7 = 916       // SQL922 = 922     // SQL936 = 936     // SQL949 = 949
// SQL8 = 917       // SQL923 = 922     // SQL937 = 937     // SQL950 = 950
// SQL9 = 918       // SQL924 = 923     // SQL938 = 938     // SQL951 = 951
// SQL10 = 919      // SQL926 = 926     // SQL939 = 939     // SQL952 = 952
// SQL11 = 903      // SQL927 = 927     // SQL940 = 940     // SQL953 = 953
// SQL12 = 904      // SQL928 = 928     // SQL941 = 941     // SQL954 = 954
// SQL6886 = 905    // SQL929 = 929     // SQL942 = 942


// ******************************************BASAHIN NYO MUNA TO**********************************************


if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
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


    //FOR ID 919 TOILET SEAT
    $sql10 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 919";
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

    //FOR ID 903 TOILET SEAT
    $sql11 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 903";
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

    //FOR ID 904 TOILET SEAT
    $sql12 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 904";
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


    // FOR ID 905 TABLE
    $sql6866 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 905";
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


    //FOR ID 906 lights
    $sql906 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 906";
    $stmt906 = $conn->prepare($sql906);
    $stmt906->execute();
    $result906 = $stmt906->get_result();
    $row906 = $result906->fetch_assoc();
    $assetId906 = $row906['assetId'];
    $category906 = $row906['category'];
    $date906 = $row906['date'];
    $building906 = $row906['building'];
    $floor906 = $row906['floor'];
    $room906 = $row906['room'];
    $status906 = $row906['status'];
    $assignedName906 = $row906['assignedName'];
    $assignedBy906 = $row906['assignedBy'];
    $upload_img906 = $row906['upload_img'];
    $description906 = $row906['description'];



    //FOR ID 907 lights
    $sql907 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 907";
    $stmt907 = $conn->prepare($sql907);
    $stmt907->execute();
    $result907 = $stmt907->get_result();
    $row907 = $result907->fetch_assoc();
    $assetId907 = $row907['assetId'];
    $category907 = $row907['category'];
    $date907 = $row907['date'];
    $building907 = $row907['building'];
    $floor907 = $row907['floor'];
    $room907 = $row907['room'];
    $status907 = $row907['status'];
    $assignedName907 = $row907['assignedName'];
    $assignedBy907 = $row907['assignedBy'];
    $upload_img907 = $row907['upload_img'];
    $description907 = $row907['description'];


    //FOR ID 908 lights
    $sql908 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 908";
    $stmt908 = $conn->prepare($sql908);
    $stmt908->execute();
    $result908 = $stmt908->get_result();
    $row908 = $result908->fetch_assoc();
    $assetId908 = $row908['assetId'];
    $category908 = $row908['category'];
    $date908 = $row908['date'];
    $building908 = $row908['building'];
    $floor908 = $row908['floor'];
    $room908 = $row908['room'];
    $status908 = $row908['status'];
    $assignedName908 = $row908['assignedName'];
    $assignedBy908 = $row908['assignedBy'];
    $upload_img908 = $row908['upload_img'];
    $description908 = $row908['description'];



    //FOR ID 909 lights
    $sql909 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 909";
    $stmt909 = $conn->prepare($sql909);
    $stmt909->execute();
    $result909 = $stmt909->get_result();
    $row909 = $result909->fetch_assoc();
    $assetId909 = $row909['assetId'];
    $category909 = $row909['category'];
    $date909 = $row909['date'];
    $building909 = $row909['building'];
    $floor909 = $row909['floor'];
    $room909 = $row909['room'];
    $status909 = $row909['status'];
    $assignedName909 = $row909['assignedName'];
    $assignedBy909 = $row909['assignedBy'];
    $upload_img909 = $row909['upload_img'];
    $description909 = $row909['description'];


    //FOR ID 920 aircon
    $sql920 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 920";
    $stmt920 = $conn->prepare($sql920);
    $stmt920->execute();
    $result920 = $stmt920->get_result();
    $row920 = $result920->fetch_assoc();
    $assetId920 = $row920['assetId'];
    $category920 = $row920['category'];
    $date920 = $row920['date'];
    $building920 = $row920['building'];
    $floor920 = $row920['floor'];
    $room920 = $row920['room'];
    $status920 = $row920['status'];
    $assignedName920 = $row920['assignedName'];
    $assignedBy920 = $row920['assignedBy'];
    $upload_img920 = $row920['upload_img'];
    $description920 = $row920['description'];



    //FOR ID 921 aircon
    $sql921 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 921";
    $stmt921 = $conn->prepare($sql921);
    $stmt921->execute();
    $result921 = $stmt921->get_result();
    $row921 = $result921->fetch_assoc();
    $assetId921 = $row921['assetId'];
    $category921 = $row921['category'];
    $date921 = $row921['date'];
    $building921 = $row921['building'];
    $floor921 = $row921['floor'];
    $room921 = $row921['room'];
    $status921 = $row921['status'];
    $assignedName921 = $row921['assignedName'];
    $assignedBy921 = $row921['assignedBy'];
    $upload_img921 = $row921['upload_img'];
    $description921 = $row921['description'];

    //FOR ID 922 lights
    $sql922 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 922";
    $stmt922 = $conn->prepare($sql922);
    $stmt922->execute();
    $result922 = $stmt922->get_result();
    $row922 = $result922->fetch_assoc();
    $assetId922 = $row922['assetId'];
    $category922 = $row922['category'];
    $date922 = $row922['date'];
    $building922 = $row922['building'];
    $floor922 = $row922['floor'];
    $room922 = $row922['room'];
    $status922 = $row922['status'];
    $assignedName922 = $row922['assignedName'];
    $assignedBy922 = $row922['assignedBy'];
    $upload_img922 = $row922['upload_img'];
    $description922 = $row922['description'];



    //FOR ID 923 lights
    $sql923 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 923";
    $stmt923 = $conn->prepare($sql923);
    $stmt923->execute();
    $result923 = $stmt923->get_result();
    $row923 = $result923->fetch_assoc();
    $assetId923 = $row923['assetId'];
    $category923 = $row923['category'];
    $date923 = $row923['date'];
    $building923 = $row923['building'];
    $floor923 = $row923['floor'];
    $room923 = $row923['room'];
    $status923 = $row923['status'];
    $assignedName923 = $row923['assignedName'];
    $assignedBy923 = $row923['assignedBy'];
    $upload_img923 = $row923['upload_img'];
    $description923 = $row923['description'];




    //FOR ID 924 lights
    $sql924 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 924";
    $stmt924 = $conn->prepare($sql924);
    $stmt924->execute();
    $result924 = $stmt924->get_result();
    $row924 = $result924->fetch_assoc();
    $assetId924 = $row924['assetId'];
    $category924 = $row924['category'];
    $date924 = $row924['date'];
    $building924 = $row924['building'];
    $floor924 = $row924['floor'];
    $room924 = $row924['room'];
    $status924 = $row924['status'];
    $assignedName924 = $row924['assignedName'];
    $assignedBy924 = $row924['assignedBy'];
    $upload_img924 = $row924['upload_img'];
    $description924 = $row924['description'];




    //FOR ID 925 lights
    $sql925 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 925";
    $stmt925 = $conn->prepare($sql925);
    $stmt925->execute();
    $result925 = $stmt925->get_result();
    $row925 = $result925->fetch_assoc();
    $assetId925 = $row925['assetId'];
    $category925 = $row925['category'];
    $date925 = $row925['date'];
    $building925 = $row925['building'];
    $floor925 = $row925['floor'];
    $room925 = $row925['room'];
    $status925 = $row925['status'];
    $assignedName925 = $row925['assignedName'];
    $assignedBy925 = $row925['assignedBy'];
    $upload_img925 = $row925['upload_img'];
    $description925 = $row925['description'];

    //FOR ID 926 lights
    $sql926 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 926";
    $stmt926 = $conn->prepare($sql926);
    $stmt926->execute();
    $result926 = $stmt926->get_result();
    $row926 = $result926->fetch_assoc();
    $assetId926 = $row926['assetId'];
    $category926 = $row926['category'];
    $date926 = $row926['date'];
    $building926 = $row926['building'];
    $floor926 = $row926['floor'];
    $room926 = $row926['room'];
    $status926 = $row926['status'];
    $assignedName926 = $row926['assignedName'];
    $assignedBy926 = $row926['assignedBy'];
    $upload_img926 = $row926['upload_img'];
    $description926 = $row926['description'];


    //FOR ID 927 lights
    $sql927 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 927";
    $stmt927 = $conn->prepare($sql927);
    $stmt927->execute();
    $result927 = $stmt927->get_result();
    $row927 = $result927->fetch_assoc();
    $assetId927 = $row927['assetId'];
    $category927 = $row927['category'];
    $date927 = $row927['date'];
    $building927 = $row927['building'];
    $floor927 = $row927['floor'];
    $room927 = $row927['room'];
    $status927 = $row927['status'];
    $assignedName927 = $row927['assignedName'];
    $assignedBy927 = $row927['assignedBy'];
    $upload_img927 = $row927['upload_img'];
    $description927 = $row927['description'];


    //FOR ID 928 lights
    $sql928 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 928";
    $stmt928 = $conn->prepare($sql928);
    $stmt928->execute();
    $result928 = $stmt928->get_result();
    $row928 = $result928->fetch_assoc();
    $assetId928 = $row928['assetId'];
    $category928 = $row928['category'];
    $date928 = $row928['date'];
    $building928 = $row928['building'];
    $floor928 = $row928['floor'];
    $room928 = $row928['room'];
    $status928 = $row928['status'];
    $assignedName928 = $row928['assignedName'];
    $assignedBy928 = $row928['assignedBy'];
    $upload_img928 = $row928['upload_img'];
    $description928 = $row928['description'];


    //FOR ID 929 lights
    $sql929 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 929";
    $stmt929 = $conn->prepare($sql929);
    $stmt929->execute();
    $result929 = $stmt929->get_result();
    $row929 = $result929->fetch_assoc();
    $assetId929 = $row929['assetId'];
    $category929 = $row929['category'];
    $date929 = $row929['date'];
    $building929 = $row929['building'];
    $floor929 = $row929['floor'];
    $room929 = $row929['room'];
    $status929 = $row929['status'];
    $assignedName929 = $row929['assignedName'];
    $assignedBy929 = $row929['assignedBy'];
    $upload_img929 = $row929['upload_img'];
    $description929 = $row929['description'];



    //FOR ID 929 lights
    $sql929 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 929";
    $stmt929 = $conn->prepare($sql929);
    $stmt929->execute();
    $result929 = $stmt929->get_result();
    $row929 = $result929->fetch_assoc();
    $assetId929 = $row929['assetId'];
    $category929 = $row929['category'];
    $date929 = $row929['date'];
    $building929 = $row929['building'];
    $floor929 = $row929['floor'];
    $room929 = $row929['room'];
    $status929 = $row929['status'];
    $assignedName929 = $row929['assignedName'];
    $assignedBy929 = $row929['assignedBy'];
    $upload_img929 = $row929['upload_img'];
    $description929 = $row929['description'];

    //FOR ID 930 lights
    $sql930 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 930";
    $stmt930 = $conn->prepare($sql930);
    $stmt930->execute();
    $result930 = $stmt930->get_result();
    $row930 = $result930->fetch_assoc();
    $assetId930 = $row930['assetId'];
    $category930 = $row930['category'];
    $date930 = $row930['date'];
    $building930 = $row930['building'];
    $floor930 = $row930['floor'];
    $room930 = $row930['room'];
    $status930 = $row930['status'];
    $assignedName930 = $row930['assignedName'];
    $assignedBy930 = $row930['assignedBy'];
    $upload_img930 = $row930['upload_img'];
    $description930 = $row930['description'];



    //FOR ID 931 lights
    $sql931 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 931";
    $stmt931 = $conn->prepare($sql931);
    $stmt931->execute();
    $result931 = $stmt931->get_result();
    $row931 = $result931->fetch_assoc();
    $assetId931 = $row931['assetId'];
    $category931 = $row931['category'];
    $date931 = $row931['date'];
    $building931 = $row931['building'];
    $floor931 = $row931['floor'];
    $room931 = $row931['room'];
    $status931 = $row931['status'];
    $assignedName931 = $row931['assignedName'];
    $assignedBy931 = $row931['assignedBy'];
    $upload_img931 = $row931['upload_img'];
    $description931 = $row931['description'];


    //FOR ID 932 lights
    $sql932 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 932";
    $stmt932 = $conn->prepare($sql932);
    $stmt932->execute();
    $result932 = $stmt932->get_result();
    $row932 = $result932->fetch_assoc();
    $assetId932 = $row932['assetId'];
    $category932 = $row932['category'];
    $date932 = $row932['date'];
    $building932 = $row932['building'];
    $floor932 = $row932['floor'];
    $room932 = $row932['room'];
    $status932 = $row932['status'];
    $assignedName932 = $row932['assignedName'];
    $assignedBy932 = $row932['assignedBy'];
    $upload_img932 = $row932['upload_img'];
    $description932 = $row932['description'];



    //FOR ID 933 lights
    $sql933 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 933";
    $stmt933 = $conn->prepare($sql933);
    $stmt933->execute();
    $result933 = $stmt933->get_result();
    $row933 = $result933->fetch_assoc();
    $assetId933 = $row933['assetId'];
    $category933 = $row933['category'];
    $date933 = $row933['date'];
    $building933 = $row933['building'];
    $floor933 = $row933['floor'];
    $room933 = $row933['room'];
    $status933 = $row933['status'];
    $assignedName933 = $row933['assignedName'];
    $assignedBy933 = $row933['assignedBy'];
    $upload_img933 = $row933['upload_img'];
    $description933 = $row933['description'];



    //FOR ID 934 lights
    $sql934 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 934";
    $stmt934 = $conn->prepare($sql934);
    $stmt934->execute();
    $result934 = $stmt934->get_result();
    $row934 = $result934->fetch_assoc();
    $assetId934 = $row934['assetId'];
    $category934 = $row934['category'];
    $date934 = $row934['date'];
    $building934 = $row934['building'];
    $floor934 = $row934['floor'];
    $room934 = $row934['room'];
    $status934 = $row934['status'];
    $assignedName934 = $row934['assignedName'];
    $assignedBy934 = $row934['assignedBy'];
    $upload_img934 = $row934['upload_img'];
    $description934 = $row934['description'];



    //FOR ID 935 lights
    $sql935 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 935";
    $stmt935 = $conn->prepare($sql935);
    $stmt935->execute();
    $result935 = $stmt935->get_result();
    $row935 = $result935->fetch_assoc();
    $assetId935 = $row935['assetId'];
    $category935 = $row935['category'];
    $date935 = $row935['date'];
    $building935 = $row935['building'];
    $floor935 = $row935['floor'];
    $room935 = $row935['room'];
    $status935 = $row935['status'];
    $assignedName935 = $row935['assignedName'];
    $assignedBy935 = $row935['assignedBy'];
    $upload_img935 = $row935['upload_img'];
    $description935 = $row935['description'];


    //FOR ID 936 aircon
    $sql936 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 936";
    $stmt936 = $conn->prepare($sql936);
    $stmt936->execute();
    $result936 = $stmt936->get_result();
    $row936 = $result936->fetch_assoc();
    $assetId936 = $row936['assetId'];
    $category936 = $row936['category'];
    $date936 = $row936['date'];
    $building936 = $row936['building'];
    $floor936 = $row936['floor'];
    $room936 = $row936['room'];
    $status936 = $row936['status'];
    $assignedName936 = $row936['assignedName'];
    $assignedBy936 = $row936['assignedBy'];
    $upload_img936 = $row936['upload_img'];
    $description936 = $row936['description'];

    //FOR ID 937 lights
    $sql937 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 937";
    $stmt937 = $conn->prepare($sql937);
    $stmt937->execute();
    $result937 = $stmt937->get_result();
    $row937 = $result937->fetch_assoc();
    $assetId937 = $row937['assetId'];
    $category937 = $row937['category'];
    $date937 = $row937['date'];
    $building937 = $row937['building'];
    $floor937 = $row937['floor'];
    $room937 = $row937['room'];
    $status937 = $row937['status'];
    $assignedName937 = $row937['assignedName'];
    $assignedBy937 = $row937['assignedBy'];
    $upload_img937 = $row937['upload_img'];
    $description937 = $row937['description'];

    //FOR ID 938 lights
    $sql938 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 938";
    $stmt938 = $conn->prepare($sql938);
    $stmt938->execute();
    $result938 = $stmt938->get_result();
    $row938 = $result938->fetch_assoc();
    $assetId938 = $row938['assetId'];
    $category938 = $row938['category'];
    $date938 = $row938['date'];
    $building938 = $row938['building'];
    $floor938 = $row938['floor'];
    $room938 = $row938['room'];
    $status938 = $row938['status'];
    $assignedName938 = $row938['assignedName'];
    $assignedBy938 = $row938['assignedBy'];
    $upload_img938 = $row938['upload_img'];
    $description938 = $row938['description'];


    //FOR ID 939 lights
    $sql939 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 939";
    $stmt939 = $conn->prepare($sql939);
    $stmt939->execute();
    $result939 = $stmt939->get_result();
    $row939 = $result939->fetch_assoc();
    $assetId939 = $row939['assetId'];
    $category939 = $row939['category'];
    $date939 = $row939['date'];
    $building939 = $row939['building'];
    $floor939 = $row939['floor'];
    $room939 = $row939['room'];
    $status939 = $row939['status'];
    $assignedName939 = $row939['assignedName'];
    $assignedBy939 = $row939['assignedBy'];
    $upload_img939 = $row939['upload_img'];
    $description939 = $row939['description'];



    //FOR ID 940 lights
    $sql940 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 940";
    $stmt940 = $conn->prepare($sql940);
    $stmt940->execute();
    $result940 = $stmt940->get_result();
    $row940 = $result940->fetch_assoc();
    $assetId940 = $row940['assetId'];
    $category940 = $row940['category'];
    $date940 = $row940['date'];
    $building940 = $row940['building'];
    $floor940 = $row940['floor'];
    $room940 = $row940['room'];
    $status940 = $row940['status'];
    $assignedName940 = $row940['assignedName'];
    $assignedBy940 = $row940['assignedBy'];
    $upload_img940 = $row940['upload_img'];
    $description940 = $row940['description'];


    //FOR ID 941 lights
    $sql941 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 941";
    $stmt941 = $conn->prepare($sql941);
    $stmt941->execute();
    $result941 = $stmt941->get_result();
    $row941 = $result941->fetch_assoc();
    $assetId941 = $row941['assetId'];
    $category941 = $row941['category'];
    $date941 = $row941['date'];
    $building941 = $row941['building'];
    $floor941 = $row941['floor'];
    $room941 = $row941['room'];
    $status941 = $row941['status'];
    $assignedName941 = $row941['assignedName'];
    $assignedBy941 = $row941['assignedBy'];
    $upload_img941 = $row941['upload_img'];
    $description941 = $row941['description'];


    //FOR ID 942 lights
    $sql942 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 942";
    $stmt942 = $conn->prepare($sql942);
    $stmt942->execute();
    $result942 = $stmt942->get_result();
    $row942 = $result942->fetch_assoc();
    $assetId942 = $row942['assetId'];
    $category942 = $row942['category'];
    $date942 = $row942['date'];
    $building942 = $row942['building'];
    $floor942 = $row942['floor'];
    $room942 = $row942['room'];
    $status942 = $row942['status'];
    $assignedName942 = $row942['assignedName'];
    $assignedBy942 = $row942['assignedBy'];
    $upload_img942 = $row942['upload_img'];
    $description942 = $row942['description'];

    //FOR ID 943 lights
    $sql943 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 943";
    $stmt943 = $conn->prepare($sql943);
    $stmt943->execute();
    $result943 = $stmt943->get_result();
    $row943 = $result943->fetch_assoc();
    $assetId943 = $row943['assetId'];
    $category943 = $row943['category'];
    $date943 = $row943['date'];
    $building943 = $row943['building'];
    $floor943 = $row943['floor'];
    $room943 = $row943['room'];
    $status943 = $row943['status'];
    $assignedName943 = $row943['assignedName'];
    $assignedBy943 = $row943['assignedBy'];
    $upload_img943 = $row943['upload_img'];
    $description943 = $row943['description'];



    //FOR ID 944 lights
    $sql944 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 944";
    $stmt944 = $conn->prepare($sql944);
    $stmt944->execute();
    $result944 = $stmt944->get_result();
    $row944 = $result944->fetch_assoc();
    $assetId944 = $row944['assetId'];
    $category944 = $row944['category'];
    $date944 = $row944['date'];
    $building944 = $row944['building'];
    $floor944 = $row944['floor'];
    $room944 = $row944['room'];
    $status944 = $row944['status'];
    $assignedName944 = $row944['assignedName'];
    $assignedBy944 = $row944['assignedBy'];
    $upload_img944 = $row944['upload_img'];
    $description944 = $row944['description'];


    //FOR ID 945 lights
    $sql945 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 945";
    $stmt945 = $conn->prepare($sql945);
    $stmt945->execute();
    $result945 = $stmt945->get_result();
    $row945 = $result945->fetch_assoc();
    $assetId945 = $row945['assetId'];
    $category945 = $row945['category'];
    $date945 = $row945['date'];
    $building945 = $row945['building'];
    $floor945 = $row945['floor'];
    $room945 = $row945['room'];
    $status945 = $row945['status'];
    $assignedName945 = $row945['assignedName'];
    $assignedBy945 = $row945['assignedBy'];
    $upload_img945 = $row945['upload_img'];
    $description945 = $row945['description'];


    //FOR ID 946 lights
    $sql946 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 946";
    $stmt946 = $conn->prepare($sql946);
    $stmt946->execute();
    $result946 = $stmt946->get_result();
    $row946 = $result946->fetch_assoc();
    $assetId946 = $row946['assetId'];
    $category946 = $row946['category'];
    $date946 = $row946['date'];
    $building946 = $row946['building'];
    $floor946 = $row946['floor'];
    $room946 = $row946['room'];
    $status946 = $row946['status'];
    $assignedName946 = $row946['assignedName'];
    $assignedBy946 = $row946['assignedBy'];
    $upload_img946 = $row946['upload_img'];
    $description946 = $row946['description'];


    //FOR ID 947 lights
    $sql947 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 947";
    $stmt947 = $conn->prepare($sql947);
    $stmt947->execute();
    $result947 = $stmt947->get_result();
    $row947 = $result947->fetch_assoc();
    $assetId947 = $row947['assetId'];
    $category947 = $row947['category'];
    $date947 = $row947['date'];
    $building947 = $row947['building'];
    $floor947 = $row947['floor'];
    $room947 = $row947['room'];
    $status947 = $row947['status'];
    $assignedName947 = $row947['assignedName'];
    $assignedBy947 = $row947['assignedBy'];
    $upload_img947 = $row947['upload_img'];
    $description947 = $row947['description'];


    //FOR ID 948 lights
    $sql948 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 948";
    $stmt948 = $conn->prepare($sql948);
    $stmt948->execute();
    $result948 = $stmt948->get_result();
    $row948 = $result948->fetch_assoc();
    $assetId948 = $row948['assetId'];
    $category948 = $row948['category'];
    $date948 = $row948['date'];
    $building948 = $row948['building'];
    $floor948 = $row948['floor'];
    $room948 = $row948['room'];
    $status948 = $row948['status'];
    $assignedName948 = $row948['assignedName'];
    $assignedBy948 = $row948['assignedBy'];
    $upload_img948 = $row948['upload_img'];
    $description948 = $row948['description'];

    //FOR ID 949 lights
    $sql949 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 949";
    $stmt949 = $conn->prepare($sql949);
    $stmt949->execute();
    $result949 = $stmt949->get_result();
    $row949 = $result949->fetch_assoc();
    $assetId949 = $row949['assetId'];
    $category949 = $row949['category'];
    $date949 = $row949['date'];
    $building949 = $row949['building'];
    $floor949 = $row949['floor'];
    $room949 = $row949['room'];
    $status949 = $row949['status'];
    $assignedName949 = $row949['assignedName'];
    $assignedBy949 = $row949['assignedBy'];
    $upload_img949 = $row949['upload_img'];
    $description949 = $row949['description'];

    //FOR ID 950 lights
    $sql950 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 950";
    $stmt950 = $conn->prepare($sql950);
    $stmt950->execute();
    $result950 = $stmt950->get_result();
    $row950 = $result950->fetch_assoc();
    $assetId950 = $row950['assetId'];
    $category950 = $row950['category'];
    $date950 = $row950['date'];
    $building950 = $row950['building'];
    $floor950 = $row950['floor'];
    $room950 = $row950['room'];
    $status950 = $row950['status'];
    $assignedName950 = $row950['assignedName'];
    $assignedBy950 = $row950['assignedBy'];
    $upload_img950 = $row950['upload_img'];
    $description950 = $row950['description'];

    //FOR ID 951 lights
    $sql951 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 951";
    $stmt951 = $conn->prepare($sql951);
    $stmt951->execute();
    $result951 = $stmt951->get_result();
    $row951 = $result951->fetch_assoc();
    $assetId951 = $row951['assetId'];
    $category951 = $row951['category'];
    $date951 = $row951['date'];
    $building951 = $row951['building'];
    $floor951 = $row951['floor'];
    $room951 = $row951['room'];
    $status951 = $row951['status'];
    $assignedName951 = $row951['assignedName'];
    $assignedBy951 = $row951['assignedBy'];
    $upload_img951 = $row951['upload_img'];
    $description951 = $row951['description'];

    //FOR ID 952 lights
    $sql952 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 952";
    $stmt952 = $conn->prepare($sql952);
    $stmt952->execute();
    $result952 = $stmt952->get_result();
    $row952 = $result952->fetch_assoc();
    $assetId952 = $row952['assetId'];
    $category952 = $row952['category'];
    $date952 = $row952['date'];
    $building952 = $row952['building'];
    $floor952 = $row952['floor'];
    $room952 = $row952['room'];
    $status952 = $row952['status'];
    $assignedName952 = $row952['assignedName'];
    $assignedBy952 = $row952['assignedBy'];
    $upload_img952 = $row952['upload_img'];
    $description952 = $row952['description'];

    //FOR ID 953 lights
    $sql953 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 953";
    $stmt953 = $conn->prepare($sql953);
    $stmt953->execute();
    $result953 = $stmt953->get_result();
    $row953 = $result953->fetch_assoc();
    $assetId953 = $row953['assetId'];
    $category953 = $row953['category'];
    $date953 = $row953['date'];
    $building953 = $row953['building'];
    $floor953 = $row953['floor'];
    $room953 = $row953['room'];
    $status953 = $row953['status'];
    $assignedName953 = $row953['assignedName'];
    $assignedBy953 = $row953['assignedBy'];
    $upload_img953 = $row953['upload_img'];
    $description953 = $row953['description'];


    //FOR ID 954 lights
    $sql954 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 954";
    $stmt954 = $conn->prepare($sql954);
    $stmt954->execute();
    $result954 = $stmt954->get_result();
    $row954 = $result954->fetch_assoc();
    $assetId954 = $row954['assetId'];
    $category954 = $row954['category'];
    $date954 = $row954['date'];
    $building954 = $row954['building'];
    $floor954 = $row954['floor'];
    $room954 = $row954['room'];
    $status954 = $row954['status'];
    $assignedName954 = $row954['assignedName'];
    $assignedBy954 = $row954['assignedBy'];
    $upload_img954 = $row954['upload_img'];
    $description954 = $row954['description'];



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
        $stmt4->bind_param('sssssi', $status4, $assignedName4, $assignedBy4, $description4, $room4, $assetId4);

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
        $stmt5->bind_param('sssssi', $status5, $assignedName5, $assignedBy5, $description5, $room5, $assetId5);

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
    //FOR ID 906
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit906'])) {
        // Get form data
        $assetId906 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status906 = $_POST['status']; // Get the status from the form
        $description906 = $_POST['description']; // Get the description from the form
        $room906 = $_POST['room']; // Get the room from the form
        $assignedBy906 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName906 = $status906 === 'Need Repair' ? '' : $assignedName906;

        // Prepare SQL query to update the asset
        $sql906 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt906 = $conn->prepare($sql906);
        $stmt906->bind_param('sssssi', $status906, $assignedName906, $assignedBy906, $description906, $room906, $assetId906);

        if ($stmt906->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId906 to $status906.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt906->close();
    }


    //FOR ID 907
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit907'])) {
        // Get form data
        $assetId907 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status907 = $_POST['status']; // Get the status from the form
        $description907 = $_POST['description']; // Get the description from the form
        $room907 = $_POST['room']; // Get the room from the form
        $assignedBy907 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName907 = $status907 === 'Need Repair' ? '' : $assignedName907;

        // Prepare SQL query to update the asset
        $sql907 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt907 = $conn->prepare($sql907);
        $stmt907->bind_param('sssssi', $status907, $assignedName907, $assignedBy907, $description907, $room907, $assetId907);

        if ($stmt907->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId907 to $status907.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt907->close();
    }



    //FOR ID 908
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit908'])) {
        // Get form data
        $assetId908 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status908 = $_POST['status']; // Get the status from the form
        $description908 = $_POST['description']; // Get the description from the form
        $room908 = $_POST['room']; // Get the room from the form
        $assignedBy908 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName908 = $status908 === 'Need Repair' ? '' : $assignedName908;

        // Prepare SQL query to update the asset
        $sql908 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt908 = $conn->prepare($sql908);
        $stmt908->bind_param('sssssi', $status908, $assignedName908, $assignedBy908, $description908, $room908, $assetId908);

        if ($stmt908->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId908 to $status908.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt908->close();
    }


    //FOR ID 909
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit909'])) {
        // Get form data
        $assetId909 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status909 = $_POST['status']; // Get the status from the form
        $description909 = $_POST['description']; // Get the description from the form
        $room909 = $_POST['room']; // Get the room from the form
        $assignedBy909 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName909 = $status909 === 'Need Repair' ? '' : $assignedName909;

        // Prepare SQL query to update the asset
        $sql909 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt909 = $conn->prepare($sql909);
        $stmt909->bind_param('sssssi', $status909, $assignedName909, $assignedBy909, $description909, $room909, $assetId909);

        if ($stmt909->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId909 to $status909.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt909->close();
    }



    //FOR ID 920
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit920'])) {
        // Get form data
        $assetId920 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status920 = $_POST['status']; // Get the status from the form
        $description920 = $_POST['description']; // Get the description from the form
        $room920 = $_POST['room']; // Get the room from the form
        $assignedBy920 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName920 = $status920 === 'Need Repair' ? '' : $assignedName920;

        // Prepare SQL query to update the asset
        $sql920 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt920 = $conn->prepare($sql920);
        $stmt920->bind_param('sssssi', $status920, $assignedName920, $assignedBy920, $description920, $room920, $assetId920);

        if ($stmt920->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId920 to $status920.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt920->close();
    }





    //FOR ID 921
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit921'])) {
        // Get form data
        $assetId921 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status921 = $_POST['status']; // Get the status from the form
        $description921 = $_POST['description']; // Get the description from the form
        $room921 = $_POST['room']; // Get the room from the form
        $assignedBy921 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName921 = $status921 === 'Need Repair' ? '' : $assignedName921;

        // Prepare SQL query to update the asset
        $sql921 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt921 = $conn->prepare($sql921);
        $stmt921->bind_param('sssssi', $status921, $assignedName921, $assignedBy921, $description921, $room921, $assetId921);

        if ($stmt921->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId921 to $status921.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt921->close();
    }

    //FOR ID 922
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit922'])) {
        // Get form data
        $assetId922 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status922 = $_POST['status']; // Get the status from the form
        $description922 = $_POST['description']; // Get the description from the form
        $room922 = $_POST['room']; // Get the room from the form
        $assignedBy922 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName922 = $status922 === 'Need Repair' ? '' : $assignedName922;

        // Prepare SQL query to update the asset
        $sql922 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt922 = $conn->prepare($sql922);
        $stmt922->bind_param('sssssi', $status922, $assignedName922, $assignedBy922, $description922, $room922, $assetId922);

        if ($stmt922->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId922 to $status922.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt922->close();
    }


    //FOR ID 923
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit923'])) {
        // Get form data
        $assetId923 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status923 = $_POST['status']; // Get the status from the form
        $description923 = $_POST['description']; // Get the description from the form
        $room923 = $_POST['room']; // Get the room from the form
        $assignedBy923 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName923 = $status923 === 'Need Repair' ? '' : $assignedName923;

        // Prepare SQL query to update the asset
        $sql923 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt923 = $conn->prepare($sql923);
        $stmt923->bind_param('sssssi', $status923, $assignedName923, $assignedBy923, $description923, $room923, $assetId923);

        if ($stmt923->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId923 to $status923.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt923->close();
    }


    //FOR ID 924
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit924'])) {
        // Get form data
        $assetId924 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status924 = $_POST['status']; // Get the status from the form
        $description924 = $_POST['description']; // Get the description from the form
        $room924 = $_POST['room']; // Get the room from the form
        $assignedBy924 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName924 = $status924 === 'Need Repair' ? '' : $assignedName924;

        // Prepare SQL query to update the asset
        $sql924 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt924 = $conn->prepare($sql924);
        $stmt924->bind_param('sssssi', $status924, $assignedName924, $assignedBy924, $description924, $room924, $assetId924);

        if ($stmt924->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId924 to $status924.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt924->close();
    }


    //FOR ID 925
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit925'])) {
        // Get form data
        $assetId925 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status925 = $_POST['status']; // Get the status from the form
        $description925 = $_POST['description']; // Get the description from the form
        $room925 = $_POST['room']; // Get the room from the form
        $assignedBy925 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName925 = $status925 === 'Need Repair' ? '' : $assignedName925;

        // Prepare SQL query to update the asset
        $sql925 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt925 = $conn->prepare($sql925);
        $stmt925->bind_param('sssssi', $status925, $assignedName925, $assignedBy925, $description925, $room925, $assetId925);

        if ($stmt925->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId925 to $status925.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt925->close();
    }

    //FOR ID 926
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit926'])) {
        // Get form data
        $assetId926 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status926 = $_POST['status']; // Get the status from the form
        $description926 = $_POST['description']; // Get the description from the form
        $room926 = $_POST['room']; // Get the room from the form
        $assignedBy926 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName926 = $status926 === 'Need Repair' ? '' : $assignedName926;

        // Prepare SQL query to update the asset
        $sql926 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt926 = $conn->prepare($sql926);
        $stmt926->bind_param('sssssi', $status926, $assignedName926, $assignedBy926, $description926, $room926, $assetId926);

        if ($stmt926->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId926 to $status926.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt926->close();
    }


    //FOR ID 927
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit927'])) {
        // Get form data
        $assetId927 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status927 = $_POST['status']; // Get the status from the form
        $description927 = $_POST['description']; // Get the description from the form
        $room927 = $_POST['room']; // Get the room from the form
        $assignedBy927 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName927 = $status927 === 'Need Repair' ? '' : $assignedName927;

        // Prepare SQL query to update the asset
        $sql927 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt927 = $conn->prepare($sql927);
        $stmt927->bind_param('sssssi', $status927, $assignedName927, $assignedBy927, $description927, $room927, $assetId927);

        if ($stmt927->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId927 to $status927.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt927->close();
    }


    //FOR ID 928
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit928'])) {
        // Get form data
        $assetId928 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status928 = $_POST['status']; // Get the status from the form
        $description928 = $_POST['description']; // Get the description from the form
        $room928 = $_POST['room']; // Get the room from the form
        $assignedBy928 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName928 = $status928 === 'Need Repair' ? '' : $assignedName928;

        // Prepare SQL query to update the asset
        $sql928 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt928 = $conn->prepare($sql928);
        $stmt928->bind_param('sssssi', $status928, $assignedName928, $assignedBy928, $description928, $room928, $assetId928);

        if ($stmt928->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId928 to $status928.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt928->close();
    }


    //FOR ID 929
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit929'])) {
        // Get form data
        $assetId929 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status929 = $_POST['status']; // Get the status from the form
        $description929 = $_POST['description']; // Get the description from the form
        $room929 = $_POST['room']; // Get the room from the form
        $assignedBy929 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName929 = $status929 === 'Need Repair' ? '' : $assignedName929;

        // Prepare SQL query to update the asset
        $sql929 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt929 = $conn->prepare($sql929);
        $stmt929->bind_param('sssssi', $status929, $assignedName929, $assignedBy929, $description929, $room929, $assetId929);

        if ($stmt929->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId929 to $status929.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt929->close();
    }
    //FOR ID 930
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit930'])) {
        // Get form data
        $assetId930 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status930 = $_POST['status']; // Get the status from the form
        $description930 = $_POST['description']; // Get the description from the form
        $room930 = $_POST['room']; // Get the room from the form
        $assignedBy930 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName930 = $status930 === 'Need Repair' ? '' : $assignedName930;

        // Prepare SQL query to update the asset
        $sql930 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt930 = $conn->prepare($sql930);
        $stmt930->bind_param('sssssi', $status930, $assignedName930, $assignedBy930, $description930, $room930, $assetId930);

        if ($stmt930->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId930 to $status930.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt930->close();
    }


    //FOR ID 931
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit931'])) {
        // Get form data
        $assetId931 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status931 = $_POST['status']; // Get the status from the form
        $description931 = $_POST['description']; // Get the description from the form
        $room931 = $_POST['room']; // Get the room from the form
        $assignedBy931 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName931 = $status931 === 'Need Repair' ? '' : $assignedName931;

        // Prepare SQL query to update the asset
        $sql931 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt931 = $conn->prepare($sql931);
        $stmt931->bind_param('sssssi', $status931, $assignedName931, $assignedBy931, $description931, $room931, $assetId931);

        if ($stmt931->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId931 to $status931.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt931->close();
    }



    //FOR ID 932
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit932'])) {
        // Get form data
        $assetId932 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status932 = $_POST['status']; // Get the status from the form
        $description932 = $_POST['description']; // Get the description from the form
        $room932 = $_POST['room']; // Get the room from the form
        $assignedBy932 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName932 = $status932 === 'Need Repair' ? '' : $assignedName932;

        // Prepare SQL query to update the asset
        $sql932 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt932 = $conn->prepare($sql932);
        $stmt932->bind_param('sssssi', $status932, $assignedName932, $assignedBy932, $description932, $room932, $assetId932);

        if ($stmt932->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId932 to $status932.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt932->close();
    }


    //FOR ID 933
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit933'])) {
        // Get form data
        $assetId933 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status933 = $_POST['status']; // Get the status from the form
        $description933 = $_POST['description']; // Get the description from the form
        $room933 = $_POST['room']; // Get the room from the form
        $assignedBy933 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName933 = $status933 === 'Need Repair' ? '' : $assignedName933;

        // Prepare SQL query to update the asset
        $sql933 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt933 = $conn->prepare($sql933);
        $stmt933->bind_param('sssssi', $status933, $assignedName933, $assignedBy933, $description933, $room933, $assetId933);

        if ($stmt933->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId933 to $status933.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt933->close();
    }


    //FOR ID 934
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit934'])) {
        // Get form data
        $assetId934 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status934 = $_POST['status']; // Get the status from the form
        $description934 = $_POST['description']; // Get the description from the form
        $room934 = $_POST['room']; // Get the room from the form
        $assignedBy934 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName934 = $status934 === 'Need Repair' ? '' : $assignedName934;

        // Prepare SQL query to update the asset
        $sql934 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt934 = $conn->prepare($sql934);
        $stmt934->bind_param('sssssi', $status934, $assignedName934, $assignedBy934, $description934, $room934, $assetId934);

        if ($stmt934->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId934 to $status934.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt934->close();
    }



    //FOR ID 935
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit935'])) {
        // Get form data
        $assetId935 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status935 = $_POST['status']; // Get the status from the form
        $description935 = $_POST['description']; // Get the description from the form
        $room935 = $_POST['room']; // Get the room from the form
        $assignedBy935 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName935 = $status935 === 'Need Repair' ? '' : $assignedName935;

        // Prepare SQL query to update the asset
        $sql935 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt935 = $conn->prepare($sql935);
        $stmt935->bind_param('sssssi', $status935, $assignedName935, $assignedBy935, $description935, $room935, $assetId935);

        if ($stmt935->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId935 to $status935.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt935->close();
    }


    //FOR ID 936
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit936'])) {
        // Get form data
        $assetId936 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status936 = $_POST['status']; // Get the status from the form
        $description936 = $_POST['description']; // Get the description from the form
        $room936 = $_POST['room']; // Get the room from the form
        $assignedBy936 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName936 = $status936 === 'Need Repair' ? '' : $assignedName936;

        // Prepare SQL query to update the asset
        $sql936 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt936 = $conn->prepare($sql936);
        $stmt936->bind_param('sssssi', $status936, $assignedName936, $assignedBy936, $description936, $room936, $assetId936);

        if ($stmt936->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId936 to $status936.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt936->close();
    }



    //FOR ID 937
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit937'])) {
        // Get form data
        $assetId937 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status937 = $_POST['status']; // Get the status from the form
        $description937 = $_POST['description']; // Get the description from the form
        $room937 = $_POST['room']; // Get the room from the form
        $assignedBy937 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName937 = $status937 === 'Need Repair' ? '' : $assignedName937;

        // Prepare SQL query to update the asset
        $sql937 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt937 = $conn->prepare($sql937);
        $stmt937->bind_param('sssssi', $status937, $assignedName937, $assignedBy937, $description937, $room937, $assetId937);

        if ($stmt937->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId937 to $status937.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt937->close();
    }


    //FOR ID 938
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit938'])) {
        // Get form data
        $assetId938 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status938 = $_POST['status']; // Get the status from the form
        $description938 = $_POST['description']; // Get the description from the form
        $room938 = $_POST['room']; // Get the room from the form
        $assignedBy938 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName938 = $status938 === 'Need Repair' ? '' : $assignedName938;

        // Prepare SQL query to update the asset
        $sql938 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt938 = $conn->prepare($sql938);
        $stmt938->bind_param('sssssi', $status938, $assignedName938, $assignedBy938, $description938, $room938, $assetId938);

        if ($stmt938->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId938 to $status938.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt938->close();
    }


    //FOR ID 939
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit939'])) {
        // Get form data
        $assetId939 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status939 = $_POST['status']; // Get the status from the form
        $description939 = $_POST['description']; // Get the description from the form
        $room939 = $_POST['room']; // Get the room from the form
        $assignedBy939 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName939 = $status939 === 'Need Repair' ? '' : $assignedName939;

        // Prepare SQL query to update the asset
        $sql939 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt939 = $conn->prepare($sql939);
        $stmt939->bind_param('sssssi', $status939, $assignedName939, $assignedBy939, $description939, $room939, $assetId939);

        if ($stmt939->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId939 to $status939.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt939->close();
    }


    //FOR ID 940
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit940'])) {
        // Get form data
        $assetId940 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status940 = $_POST['status']; // Get the status from the form
        $description940 = $_POST['description']; // Get the description from the form
        $room940 = $_POST['room']; // Get the room from the form
        $assignedBy940 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName940 = $status940 === 'Need Repair' ? '' : $assignedName940;

        // Prepare SQL query to update the asset
        $sql940 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt940 = $conn->prepare($sql940);
        $stmt940->bind_param('sssssi', $status940, $assignedName940, $assignedBy940, $description940, $room940, $assetId940);

        if ($stmt940->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId940 to $status940.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt940->close();
    }


    //FOR ID 941
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit941'])) {
        // Get form data
        $assetId941 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status941 = $_POST['status']; // Get the status from the form
        $description941 = $_POST['description']; // Get the description from the form
        $room941 = $_POST['room']; // Get the room from the form
        $assignedBy941 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName941 = $status941 === 'Need Repair' ? '' : $assignedName941;

        // Prepare SQL query to update the asset
        $sql941 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt941 = $conn->prepare($sql941);
        $stmt941->bind_param('sssssi', $status941, $assignedName941, $assignedBy941, $description941, $room941, $assetId941);

        if ($stmt941->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId941 to $status941.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt941->close();
    }


    //FOR ID 942
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit942'])) {
        // Get form data
        $assetId942 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status942 = $_POST['status']; // Get the status from the form
        $description942 = $_POST['description']; // Get the description from the form
        $room942 = $_POST['room']; // Get the room from the form
        $assignedBy942 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName942 = $status942 === 'Need Repair' ? '' : $assignedName942;

        // Prepare SQL query to update the asset
        $sql942 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt942 = $conn->prepare($sql942);
        $stmt942->bind_param('sssssi', $status942, $assignedName942, $assignedBy942, $description942, $room942, $assetId942);

        if ($stmt942->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId942 to $status942.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt942->close();
    }


    //FOR ID 943
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit943'])) {
        // Get form data
        $assetId943 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status943 = $_POST['status']; // Get the status from the form
        $description943 = $_POST['description']; // Get the description from the form
        $room943 = $_POST['room']; // Get the room from the form
        $assignedBy943 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName943 = $status943 === 'Need Repair' ? '' : $assignedName943;

        // Prepare SQL query to update the asset
        $sql943 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt943 = $conn->prepare($sql943);
        $stmt943->bind_param('sssssi', $status943, $assignedName943, $assignedBy943, $description943, $room943, $assetId943);

        if ($stmt943->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId943 to $status943.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt943->close();
    }


    //FOR ID 944
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit944'])) {
        // Get form data
        $assetId944 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status944 = $_POST['status']; // Get the status from the form
        $description944 = $_POST['description']; // Get the description from the form
        $room944 = $_POST['room']; // Get the room from the form
        $assignedBy944 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName944 = $status944 === 'Need Repair' ? '' : $assignedName944;

        // Prepare SQL query to update the asset
        $sql944 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt944 = $conn->prepare($sql944);
        $stmt944->bind_param('sssssi', $status944, $assignedName944, $assignedBy944, $description944, $room944, $assetId944);

        if ($stmt944->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId944 to $status944.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt944->close();
    }


    //FOR ID 945
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit945'])) {
        // Get form data
        $assetId945 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status945 = $_POST['status']; // Get the status from the form
        $description945 = $_POST['description']; // Get the description from the form
        $room945 = $_POST['room']; // Get the room from the form
        $assignedBy945 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName945 = $status945 === 'Need Repair' ? '' : $assignedName945;

        // Prepare SQL query to update the asset
        $sql945 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt945 = $conn->prepare($sql945);
        $stmt945->bind_param('sssssi', $status945, $assignedName945, $assignedBy945, $description945, $room945, $assetId945);

        if ($stmt945->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId945 to $status945.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt945->close();
    }


    //FOR ID 946
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit946'])) {
        // Get form data
        $assetId946 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status946 = $_POST['status']; // Get the status from the form
        $description946 = $_POST['description']; // Get the description from the form
        $room946 = $_POST['room']; // Get the room from the form
        $assignedBy946 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName946 = $status946 === 'Need Repair' ? '' : $assignedName946;

        // Prepare SQL query to update the asset
        $sql946 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt946 = $conn->prepare($sql946);
        $stmt946->bind_param('sssssi', $status946, $assignedName946, $assignedBy946, $description946, $room946, $assetId946);

        if ($stmt946->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId946 to $status946.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt946->close();
    }


    //FOR ID 947
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit947'])) {
        // Get form data
        $assetId947 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status947 = $_POST['status']; // Get the status from the form
        $description947 = $_POST['description']; // Get the description from the form
        $room947 = $_POST['room']; // Get the room from the form
        $assignedBy947 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName947 = $status947 === 'Need Repair' ? '' : $assignedName947;

        // Prepare SQL query to update the asset
        $sql947 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt947 = $conn->prepare($sql947);
        $stmt947->bind_param('sssssi', $status947, $assignedName947, $assignedBy947, $description947, $room947, $assetId947);

        if ($stmt947->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId947 to $status947.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt947->close();
    }




    //FOR ID 948
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit948'])) {
        // Get form data
        $assetId948 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status948 = $_POST['status']; // Get the status from the form
        $description948 = $_POST['description']; // Get the description from the form
        $room948 = $_POST['room']; // Get the room from the form
        $assignedBy948 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName948 = $status948 === 'Need Repair' ? '' : $assignedName948;

        // Prepare SQL query to update the asset
        $sql948 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt948 = $conn->prepare($sql948);
        $stmt948->bind_param('sssssi', $status948, $assignedName948, $assignedBy948, $description948, $room948, $assetId948);

        if ($stmt948->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId948 to $status948.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt948->close();
    }



    //FOR ID 949
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit949'])) {
        // Get form data
        $assetId949 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status949 = $_POST['status']; // Get the status from the form
        $description949 = $_POST['description']; // Get the description from the form
        $room949 = $_POST['room']; // Get the room from the form
        $assignedBy949 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName949 = $status949 === 'Need Repair' ? '' : $assignedName949;

        // Prepare SQL query to update the asset
        $sql949 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt949 = $conn->prepare($sql949);
        $stmt949->bind_param('sssssi', $status949, $assignedName949, $assignedBy949, $description949, $room949, $assetId949);

        if ($stmt949->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId949 to $status949.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt949->close();
    }


    //FOR ID 950
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit950'])) {
        // Get form data
        $assetId950 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status950 = $_POST['status']; // Get the status from the form
        $description950 = $_POST['description']; // Get the description from the form
        $room950 = $_POST['room']; // Get the room from the form
        $assignedBy950 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName950 = $status950 === 'Need Repair' ? '' : $assignedName950;

        // Prepare SQL query to update the asset
        $sql950 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt950 = $conn->prepare($sql950);
        $stmt950->bind_param('sssssi', $status950, $assignedName950, $assignedBy950, $description950, $room950, $assetId950);

        if ($stmt950->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId950 to $status950.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt950->close();
    }




    //FOR ID 951
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit951'])) {
        // Get form data
        $assetId951 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status951 = $_POST['status']; // Get the status from the form
        $description951 = $_POST['description']; // Get the description from the form
        $room951 = $_POST['room']; // Get the room from the form
        $assignedBy951 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName951 = $status951 === 'Need Repair' ? '' : $assignedName951;

        // Prepare SQL query to update the asset
        $sql951 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt951 = $conn->prepare($sql951);
        $stmt951->bind_param('sssssi', $status951, $assignedName951, $assignedBy951, $description951, $room951, $assetId951);

        if ($stmt951->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId951 to $status951.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt951->close();
    }




    //FOR ID 952
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit952'])) {
        // Get form data
        $assetId952 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status952 = $_POST['status']; // Get the status from the form
        $description952 = $_POST['description']; // Get the description from the form
        $room952 = $_POST['room']; // Get the room from the form
        $assignedBy952 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName952 = $status952 === 'Need Repair' ? '' : $assignedName952;

        // Prepare SQL query to update the asset
        $sql952 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt952 = $conn->prepare($sql952);
        $stmt952->bind_param('sssssi', $status952, $assignedName952, $assignedBy952, $description952, $room952, $assetId952);

        if ($stmt952->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId952 to $status952.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt952->close();
    }




    //FOR ID 953
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit953'])) {
        // Get form data
        $assetId953 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status953 = $_POST['status']; // Get the status from the form
        $description953 = $_POST['description']; // Get the description from the form
        $room953 = $_POST['room']; // Get the room from the form
        $assignedBy953 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName953 = $status953 === 'Need Repair' ? '' : $assignedName953;

        // Prepare SQL query to update the asset
        $sql953 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt953 = $conn->prepare($sql953);
        $stmt953->bind_param('sssssi', $status953, $assignedName953, $assignedBy953, $description953, $room953, $assetId953);

        if ($stmt953->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId953 to $status953.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt953->close();
    }


    //FOR ID 954
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit954'])) {
        // Get form data
        $assetId954 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status954 = $_POST['status']; // Get the status from the form
        $description954 = $_POST['description']; // Get the description from the form
        $room954 = $_POST['room']; // Get the room from the form
        $assignedBy954 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName954 = $status954 === 'Need Repair' ? '' : $assignedName954;

        // Prepare SQL query to update the asset
        $sql954 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt954 = $conn->prepare($sql954);
        $stmt954->bind_param('sssssi', $status954, $assignedName954, $assignedBy954, $description954, $room954, $assetId954);

        if ($stmt954->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId954 to $status954.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: TEBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt954->close();
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
        <title>iTrak | Map</title>
        <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/NEB/NEWBF1.css" />
        <script src="../../src/js/locationTracker.js"></script>
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
                <a href="../../manager/dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../../manager/attendance-logs.php">
                    <i class="bi bi-calendar-week"></i>
                    <span class="text">Attendance Logs</span>
                </a>
            </li>
            <li>
                <a href="../../manager/gps.php">
                    <i class="bi bi-geo-alt"></i>
                    <span class="text">GPS</span>
                </a>
            </li>
            <li class="active">
                <a href="../../manager/map.php">
                    <i class="bi bi-map"></i>
                    <span class="text">Map</span>
                </a>
            </li>
            <li>
                <a href="../../manager/reports.php">
                    <i class="bi bi-clipboard"></i>
                    <span class="text">Reports</span>
                </a>
            </li>
            <li>
                <a href="../../manager/activity-logs.php">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span class="text">Activity Logs</span>
                </a>
            </li>
        </ul>
    </section>
    <div id="map-top-nav">
        <a href="../../manager/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>

        <div class="legend-button" id="legendButton">
            <i class="bi bi-info-circle"></i>
        </div>
    </div>
    <section id="content">
        <main>
            <div class="content-container" id="content-container">
                <div id="belmonte-F1" class="content">
                    <!-- FLOOR PLAN -->
                    <img class="Floor-container-1" src="../../../src/floors/techvocB/TV1F.png" alt="">
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
                    <!-- assetss -->

                     <!-- ASSET 910 -->
                     <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId910; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:60px; left:590px;"
                        alt="Asset Image 910" data-bs-toggle="modal" data-bs-target="#imageModal910"
                        onclick="fetchAssetData(910);" data-room="<?php echo htmlspecialchars($room910); ?>"
                        data-floor="<?php echo htmlspecialchars($floor910); ?>"
                        data-image="<?php echo base64_encode($upload_img910); ?>"
                        data-category="<?php echo htmlspecialchars($category910); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName910); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status); ?>; position:absolute; top:60px; left:600px;'>
                    </div>

                    <!-- ASSET 911 -->
                    <img src="../image.php?id=911" class="asset-image" data-id="<?php echo $assetId911; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:85px; left:590px;"
                        alt="Asset Image 911" data-bs-toggle="modal" data-bs-target="#imageModal911"
                        onclick="fetchAssetData(911);" data-room="<?php echo htmlspecialchars($room911); ?>"
                        data-floor="<?php echo htmlspecialchars($floor911); ?>"
                        data-image="<?php echo base64_encode($upload_img911); ?>"
                        data-category="<?php echo htmlspecialchars($category911); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName911); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2); ?>; position:absolute; top:85px; left:600px;'>
                    </div>

                    <!-- ASSET 912 -->
                    <img src="../image.php?id=912" class="asset-image" data-id="<?php echo $assetId912; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:80px; left:700px;"
                        alt="Asset Image 912" data-bs-toggle="modal" data-bs-target="#imageModal912"
                        onclick="fetchAssetData(912);" data-room="<?php echo htmlspecialchars($room912); ?>"
                        data-floor="<?php echo htmlspecialchars($floor912); ?>"
                        data-image="<?php echo base64_encode($upload_img912); ?>"
                        data-category="<?php echo htmlspecialchars($category912); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName912); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status3); ?>; position:absolute; top:80px; left:710px;'>
                    </div>

                    <!-- ASSET 913 -->
                    <img src="../image.php?id=913" class="asset-image" data-id="<?php echo $assetId913; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:60px; left:785px;"
                        alt="Asset Image 913" data-bs-toggle="modal" data-bs-target="#imageModal913"
                        onclick="fetchAssetData(913);" data-room="<?php echo htmlspecialchars($room913); ?>"
                        data-floor="<?php echo htmlspecialchars($floor913); ?>"
                        data-image="<?php echo base64_encode($upload_img913); ?>"
                        data-category="<?php echo htmlspecialchars($category913); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName913); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status4); ?>; position:absolute; top:60px; left:795px;'>
                    </div>


                    <!-- ASSET 914 -->
                    <img src="../image.php?id=914" class="asset-image" data-id="<?php echo $assetId914; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:105px; left:785px;"
                        alt="Asset Image 914" data-bs-toggle="modal" data-bs-target="#imageModal914"
                        onclick="fetchAssetData(914);" data-room="<?php echo htmlspecialchars($room914); ?>"
                        data-floor="<?php echo htmlspecialchars($floor914); ?>"
                        data-image="<?php echo base64_encode($upload_img914); ?>"
                        data-category="<?php echo htmlspecialchars($category914); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName914); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5); ?>; position:absolute; top:105px; left:795px;'>
                    </div>

                    <!-- ASSET 915 -->
                    <img src="../image.php?id=915" class="asset-image" data-id="<?php echo $assetId915; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:130px; left:625px;"
                        alt="Asset Image 915" data-bs-toggle="modal" data-bs-target="#imageModal915"
                        onclick="fetchAssetData(915);" data-room="<?php echo htmlspecialchars($room915); ?>"
                        data-floor="<?php echo htmlspecialchars($floor915); ?>"
                        data-image="<?php echo base64_encode($upload_img915); ?>"
                        data-category="<?php echo htmlspecialchars($category915); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName915); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status6); ?>; position:absolute; top:130px; left:635px;'>
                    </div>

                    <!-- ASSET 916 -->
                    <img src="../image.php?id=916" class="asset-image" data-id="<?php echo $assetId916; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:130px; left:742px;"
                        alt="Asset Image 916" data-bs-toggle="modal" data-bs-target="#imageModal916"
                        onclick="fetchAssetData(916);" data-room="<?php echo htmlspecialchars($room916); ?>"
                        data-floor="<?php echo htmlspecialchars($floor916); ?>"
                        data-image="<?php echo base64_encode($upload_img916); ?>"
                        data-category="<?php echo htmlspecialchars($category916); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName916); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7); ?>; position:absolute; top:130px; left:752px;'>
                    </div>

                    <!-- ASSET 917 -->
                    <img src="../image.php?id=917" class="asset-image" data-id="<?php echo $assetId917; ?>"
                        style="width:35px; cursor:pointer; position:absolute; top:70px; left:630px;"
                        alt="Asset Image 917" data-bs-toggle="modal" data-bs-target="#imageModal917"
                        onclick="fetchAssetData(917);" data-room="<?php echo htmlspecialchars($room917); ?>"
                        data-floor="<?php echo htmlspecialchars($floor917); ?>"
                        data-image="<?php echo base64_encode($upload_img917); ?>"
                        data-category="<?php echo htmlspecialchars($category917); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName917); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status8); ?>; position:absolute; top:73px; left:652px;'>
                    </div>


                    <!-- ASSET 918 -->
                    <img src="../image.php?id=918" class="asset-image" data-id="<?php echo $assetId918; ?>"
                        style="width:35px; cursor:pointer; position:absolute; top:70px; left:750px;"
                        alt="Asset Image 918" data-bs-toggle="modal" data-bs-target="#imageModal918"
                        onclick="fetchAssetData(918);" data-room="<?php echo htmlspecialchars($room918); ?>"
                        data-floor="<?php echo htmlspecialchars($floor918); ?>"
                        data-image="<?php echo base64_encode($upload_img918); ?>"
                        data-category="<?php echo htmlspecialchars($category918); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName918); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9); ?>; position:absolute; top:73px; left:772px;'>
                    </div>

                    <!-- ASSET 919 -->
                    <img src="../image.php?id=919" class="asset-image" data-id="<?php echo $assetId919; ?>"
                        style="width:35px; cursor:pointer; position:absolute; top:120px; left:690px;"
                        alt="Asset Image 919" data-bs-toggle="modal" data-bs-target="#imageModal919"
                        onclick="fetchAssetData(919);" data-room="<?php echo htmlspecialchars($room919); ?>"
                        data-floor="<?php echo htmlspecialchars($floor919); ?>"
                        data-image="<?php echo base64_encode($upload_img919); ?>"
                        data-category="<?php echo htmlspecialchars($category919); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName919); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10); ?>; position:absolute; top:123px; left:712px;'>
                    </div>

                    <!-- ASSET 903 -->
                    <img src="../image.php?id=903" class="asset-image" data-id="<?php echo $assetId903; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:55px; left:840px;"
                        alt="Asset Image 903" data-bs-toggle="modal" data-bs-target="#imageModal903"
                        onclick="fetchAssetData(903);" data-room="<?php echo htmlspecialchars($room903); ?>"
                        data-floor="<?php echo htmlspecialchars($floor903); ?>"
                        data-image="<?php echo base64_encode($upload_img903); ?>"
                        data-category="<?php echo htmlspecialchars($category903); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName903); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status11); ?>; position:absolute; top:55px; left:850px;'>
                    </div>

                    <!-- ASSET 904 -->
                    <img src="../image.php?id=904" class="asset-image" data-id="<?php echo $assetId904; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:55px; left:1060px;"
                        alt="Asset Image 904" data-bs-toggle="modal" data-bs-target="#imageModal904"
                        onclick="fetchAssetData(904);" data-room="<?php echo htmlspecialchars($room904); ?>"
                        data-floor="<?php echo htmlspecialchars($floor904); ?>"
                        data-image="<?php echo base64_encode($upload_img904); ?>"
                        data-category="<?php echo htmlspecialchars($category904); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName904); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status12); ?>; position:absolute; top:55px; left:1070px;'>
                    </div>


                    <!-- ASSET 905 -->
                    <img src="../image.php?id=905" class="asset-image" data-id="<?php echo $assetId905; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:95px; left:900px;"
                        alt="Asset Image 905" data-bs-toggle="modal" data-bs-target="#imageModal905"
                        onclick="fetchAssetData(905);" data-room="<?php echo htmlspecialchars($room905); ?>"
                        data-floor="<?php echo htmlspecialchars($floor905); ?>"
                        data-image="<?php echo base64_encode($upload_img905); ?>"
                        data-category="<?php echo htmlspecialchars($category905); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName905); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status6866); ?>; position:absolute; top:95px; left:910px;'>
                    </div>

                    <!-- ASSET 906 -->
                    <img src="../image.php?id=906" class="asset-image" data-id="<?php echo $assetId906; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:95px; left:1000px;"
                        alt="Asset Image 906" data-bs-toggle="modal" data-bs-target="#imageModal906"
                        onclick="fetchAssetData(906);" data-room="<?php echo htmlspecialchars($room906); ?>"
                        data-floor="<?php echo htmlspecialchars($floor906); ?>"
                        data-image="<?php echo base64_encode($upload_img906); ?>"
                        data-category="<?php echo htmlspecialchars($category906); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName906); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status906); ?>; position:absolute; top:95px; left:1010px;'>
                    </div>

                    <!-- ASSET 907 -->
                    <img src="../image.php?id=907" class="asset-image" data-id="<?php echo $assetId907; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:135px; left:840px;"
                        alt="Asset Image 907" data-bs-toggle="modal" data-bs-target="#imageModal907"
                        onclick="fetchAssetData(907);" data-room="<?php echo htmlspecialchars($room907); ?>"
                        data-floor="<?php echo htmlspecialchars($floor907); ?>"
                        data-image="<?php echo base64_encode($upload_img907); ?>"
                        data-category="<?php echo htmlspecialchars($category907); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName907); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status907); ?>; position:absolute; top:135px; left:850px;'>
                    </div>

                    <!-- ASSET 908 -->
                    <img src="../image.php?id=908" class="asset-image" data-id="<?php echo $assetId908; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:135px; left:949px;"
                        alt="Asset Image 908" data-bs-toggle="modal" data-bs-target="#imageModal908"
                        onclick="fetchAssetData(908);" data-room="<?php echo htmlspecialchars($room908); ?>"
                        data-floor="<?php echo htmlspecialchars($floor908); ?>"
                        data-image="<?php echo base64_encode($upload_img908); ?>"
                        data-category="<?php echo htmlspecialchars($category908); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName908); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status908); ?>; position:absolute; top:135px; left:959px;'>
                    </div>




                    <!-- ASSET 909 -->
                    <img src="../image.php?id=909" class="asset-image" data-id="<?php echo $assetId909; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:135px; left:1060px;"
                        alt="Asset Image 909" data-bs-toggle="modal" data-bs-target="#imageModal909"
                        onclick="fetchAssetData(909);" data-room="<?php echo htmlspecialchars($room909); ?>"
                        data-floor="<?php echo htmlspecialchars($floor909); ?>"
                        data-image="<?php echo base64_encode($upload_img909); ?>"
                        data-category="<?php echo htmlspecialchars($category909); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName909); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status909); ?>; position:absolute; top:135px; left:1070px;'>
                    </div>

                    <!-- ASSET 920 -->
                    <img src="../image.php?id=920" class="asset-image" data-id="<?php echo $assetId920; ?>"
                        style="width:35px; cursor:pointer; position:absolute; top:70px; left:376px;"
                        alt="Asset Image 920" data-bs-toggle="modal" data-bs-target="#imageModal920"
                        onclick="fetchAssetData(920);" data-room="<?php echo htmlspecialchars($room920); ?>"
                        data-floor="<?php echo htmlspecialchars($floor920); ?>"
                        data-image="<?php echo base64_encode($upload_img920); ?>"
                        data-category="<?php echo htmlspecialchars($category920); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName920); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status920); ?>; position:absolute; top:70px; left:398px;'>
                    </div>

                    <!-- ASSET 921 -->
                    <img src="../image.php?id=921" class="asset-image" data-id="<?php echo $assetId921; ?>"
                        style="width:35px; cursor:pointer; position:absolute; top:120px; left:475px;"
                        alt="Asset Image 921" data-bs-toggle="modal" data-bs-target="#imageModal921"
                        onclick="fetchAssetData(921);" data-room="<?php echo htmlspecialchars($room921); ?>"
                        data-floor="<?php echo htmlspecialchars($floor921); ?>"
                        data-image="<?php echo base64_encode($upload_img921); ?>"
                        data-category="<?php echo htmlspecialchars($category921); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName921); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status921); ?>; position:absolute; top:120px; left:497px;'>
                    </div>

                    <!-- ASSET 922 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId922; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:340px;"
                        alt="Asset Image 922" data-bs-toggle="modal" data-bs-target="#imageModal922"
                        onclick="fetchAssetData(922);" data-room="<?php echo htmlspecialchars($room922); ?>"
                        data-floor="<?php echo htmlspecialchars($floor922); ?>"
                        data-image="<?php echo base64_encode($upload_img922); ?>"
                        data-category="<?php echo htmlspecialchars($category922); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName922); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status922); ?>; 
    position:absolute; top:63px; left:350px;'>
                    </div>

                    <!-- ASSET 923 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId923; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:420px;"
                        alt="Asset Image 923" data-bs-toggle="modal" data-bs-target="#imageModal923"
                        onclick="fetchAssetData(923);" data-room="<?php echo htmlspecialchars($room923); ?>"
                        data-floor="<?php echo htmlspecialchars($floor923); ?>"
                        data-image="<?php echo base64_encode($upload_img923); ?>"
                        data-category="<?php echo htmlspecialchars($category923); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName923); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status923); ?>; 
    position:absolute; top:63px; left:430px;'>
                    </div>

                    <!-- ASSET 924 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId924; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:100px; left:340px;"
                        alt="Asset Image 924" data-bs-toggle="modal" data-bs-target="#imageModal924"
                        onclick="fetchAssetData(924);" data-room="<?php echo htmlspecialchars($room924); ?>"
                        data-floor="<?php echo htmlspecialchars($floor924); ?>"
                        data-image="<?php echo base64_encode($upload_img924); ?>"
                        data-category="<?php echo htmlspecialchars($category924); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName924); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status924); ?>; 
    position:absolute; top:100px; left:350px;'>
                    </div>

                    <!-- ASSET 925 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId925; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:100px; left:420px;"
                        alt="Asset Image 925" data-bs-toggle="modal" data-bs-target="#imageModal925"
                        onclick="fetchAssetData(925);" data-room="<?php echo htmlspecialchars($room925); ?>"
                        data-floor="<?php echo htmlspecialchars($floor925); ?>"
                        data-image="<?php echo base64_encode($upload_img925); ?>"
                        data-category="<?php echo htmlspecialchars($category925); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName925); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status925); ?>; 
    position:absolute; top:100px; left:430px;'>
                    </div>


                    <!-- ASSET 926 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId926; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:460px;"
                        alt="Asset Image 926" data-bs-toggle="modal" data-bs-target="#imageModal926"
                        onclick="fetchAssetData(926);" data-room="<?php echo htmlspecialchars($room926); ?>"
                        data-floor="<?php echo htmlspecialchars($floor926); ?>"
                        data-image="<?php echo base64_encode($upload_img926); ?>"
                        data-category="<?php echo htmlspecialchars($category926); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName926); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status926); ?>; position:absolute; top:63px; left:470px;'>
                    </div>

                    <!-- ASSET 927 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId927; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:545px;"
                        alt="Asset Image 927" data-bs-toggle="modal" data-bs-target="#imageModal927"
                        onclick="fetchAssetData(927);" data-room="<?php echo htmlspecialchars($room927); ?>"
                        data-floor="<?php echo htmlspecialchars($floor927); ?>"
                        data-image="<?php echo base64_encode($upload_img927); ?>"
                        data-category="<?php echo htmlspecialchars($category927); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName927); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status927); ?>; position:absolute; top:63px; left:555px;'>
                    </div>

                    <!-- ASSET 928 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId928; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:107px; left:504px;"
                        alt="Asset Image 928" data-bs-toggle="modal" data-bs-target="#imageModal928"
                        onclick="fetchAssetData(928);" data-room="<?php echo htmlspecialchars($room928); ?>"
                        data-floor="<?php echo htmlspecialchars($floor928); ?>"
                        data-image="<?php echo base64_encode($upload_img928); ?>"
                        data-category="<?php echo htmlspecialchars($category928); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName928); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status928); ?>; position:absolute; top:107px; left:514px;'>
                    </div>

                    <!-- ASSET 929 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId929; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:139px; left:504px;"
                        alt="Asset Image 929" data-bs-toggle="modal" data-bs-target="#imageModal929"
                        onclick="fetchAssetData(929);" data-room="<?php echo htmlspecialchars($room929); ?>"
                        data-floor="<?php echo htmlspecialchars($floor929); ?>"
                        data-image="<?php echo base64_encode($upload_img929); ?>"
                        data-category="<?php echo htmlspecialchars($category929); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName929); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status929); ?>; position:absolute; top:139px; left:514px;'>
                    </div>



                    <!-- ASSET 930 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId930; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:200px;"
                        alt="Asset Image 930" data-bs-toggle="modal" data-bs-target="#imageModal930"
                        onclick="fetchAssetData(930);" data-room="<?php echo htmlspecialchars($room930); ?>"
                        data-floor="<?php echo htmlspecialchars($floor930); ?>"
                        data-image="<?php echo base64_encode($upload_img930); ?>"
                        data-category="<?php echo htmlspecialchars($category930); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName930); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status930); ?>; position:absolute; top:63px; left:210px;'>
                    </div>

                    <!-- ASSET 931 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId931; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:250px;"
                        alt="Asset Image 931" data-bs-toggle="modal" data-bs-target="#imageModal931"
                        onclick="fetchAssetData(931);" data-room="<?php echo htmlspecialchars($room931); ?>"
                        data-floor="<?php echo htmlspecialchars($floor931); ?>"
                        data-image="<?php echo base64_encode($upload_img931); ?>"
                        data-category="<?php echo htmlspecialchars($category931); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName931); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status931); ?>; position:absolute; top:63px; left:260px;'>
                    </div>

                    <!-- ASSET 932 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId932; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:63px; left:298px;"
                        alt="Asset Image 932" data-bs-toggle="modal" data-bs-target="#imageModal932"
                        onclick="fetchAssetData(932);" data-room="<?php echo htmlspecialchars($room932); ?>"
                        data-floor="<?php echo htmlspecialchars($floor932); ?>"
                        data-image="<?php echo base64_encode($upload_img932); ?>"
                        data-category="<?php echo htmlspecialchars($category932); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName932); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status932); ?>; position:absolute; top:63px; left:308px;'>
                    </div>

                    <!-- ASSET 933 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId933; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:105px; left:200px;"
                        alt="Asset Image 933" data-bs-toggle="modal" data-bs-target="#imageModal933"
                        onclick="fetchAssetData(933);" data-room="<?php echo htmlspecialchars($room933); ?>"
                        data-floor="<?php echo htmlspecialchars($floor933); ?>"
                        data-image="<?php echo base64_encode($upload_img933); ?>"
                        data-category="<?php echo htmlspecialchars($category933); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName933); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status933); ?>; position:absolute; top:105px; left:210px;'>
                    </div>



                    <!-- ASSET 934 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId934; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:105px; left:250px;"
                        alt="Asset Image 934" data-bs-toggle="modal" data-bs-target="#imageModal934"
                        onclick="fetchAssetData(934);" data-room="<?php echo htmlspecialchars($room934); ?>"
                        data-floor="<?php echo htmlspecialchars($floor934); ?>"
                        data-image="<?php echo base64_encode($upload_img934); ?>"
                        data-category="<?php echo htmlspecialchars($category934); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName934); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status934); ?>; position:absolute; top:105px; left:260px;'>
                    </div>

                    <!-- ASSET 935 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId935; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:105px; left:298px;"
                        alt="Asset Image 935" data-bs-toggle="modal" data-bs-target="#imageModal935"
                        onclick="fetchAssetData(935);" data-room="<?php echo htmlspecialchars($room935); ?>"
                        data-floor="<?php echo htmlspecialchars($floor935); ?>"
                        data-image="<?php echo base64_encode($upload_img935); ?>"
                        data-category="<?php echo htmlspecialchars($category935); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName935); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status935); ?>; position:absolute; top:105px; left:308px;'>
                    </div>

                    <!-- ASSET 936 -->
                    <img src="../image.php?id=936" class="asset-image" data-id="<?php echo $assetId936; ?>"
                        style="width:30px; cursor:pointer; position:absolute; top:78px; left:243px;"
                        alt="Asset Image 936" data-bs-toggle="modal" data-bs-target="#imageModal936"
                        onclick="fetchAssetData(936);" data-room="<?php echo htmlspecialchars($room936); ?>"
                        data-floor="<?php echo htmlspecialchars($floor936); ?>"
                        data-image="<?php echo base64_encode($upload_img936); ?>"
                        data-category="<?php echo htmlspecialchars($category936); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName936); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status936); ?>; position:absolute; top:78px; left:265px;'>
                    </div>

                    <!-- ASSET 937 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId937; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:132px; left:298px;"
                        alt="Asset Image 937" data-bs-toggle="modal" data-bs-target="#imageModal937"
                        onclick="fetchAssetData(937);" data-room="<?php echo htmlspecialchars($room937); ?>"
                        data-floor="<?php echo htmlspecialchars($floor937); ?>"
                        data-image="<?php echo base64_encode($upload_img937); ?>"
                        data-category="<?php echo htmlspecialchars($category937); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName937); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status937); ?>; position:absolute; top:132px; left:308px;'>
                    </div>

                    <!-- ASSET 938 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId938; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:132px; left:235px;"
                        alt="Asset Image 938" data-bs-toggle="modal" data-bs-target="#imageModal938"
                        onclick="fetchAssetData(938);" data-room="<?php echo htmlspecialchars($room938); ?>"
                        data-floor="<?php echo htmlspecialchars($floor938); ?>"
                        data-image="<?php echo base64_encode($upload_img938); ?>"
                        data-category="<?php echo htmlspecialchars($category938); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName938); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status938); ?>; position:absolute; top:132px; left:245px;'>
                    </div>

                    <!-- ASSET 939 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId939; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:132px; left:175px;"
                        alt="Asset Image 939" data-bs-toggle="modal" data-bs-target="#imageModal939"
                        onclick="fetchAssetData(939);" data-room="<?php echo htmlspecialchars($room939); ?>"
                        data-floor="<?php echo htmlspecialchars($floor939); ?>"
                        data-image="<?php echo base64_encode($upload_img939); ?>"
                        data-category="<?php echo htmlspecialchars($category939); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName939); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status939); ?>; position:absolute; top:132px; left:185px;'>
                    </div>

                    <!-- ASSET 940 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId940; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:120px; left:106px;"
                        alt="Asset Image 940" data-bs-toggle="modal" data-bs-target="#imageModal940"
                        onclick="fetchAssetData(940);" data-room="<?php echo htmlspecialchars($room940); ?>"
                        data-floor="<?php echo htmlspecialchars($floor940); ?>"
                        data-image="<?php echo base64_encode($upload_img940); ?>"
                        data-category="<?php echo htmlspecialchars($category940); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName940); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status940); ?>; position:absolute; top:120px; left:116px;'>
                    </div>

                    <!-- ASSET 941 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId941; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:165px; left:106px;"
                        alt="Asset Image 941" data-bs-toggle="modal" data-bs-target="#imageModal941"
                        onclick="fetchAssetData(941);" data-room="<?php echo htmlspecialchars($room941); ?>"
                        data-floor="<?php echo htmlspecialchars($floor941); ?>"
                        data-image="<?php echo base64_encode($upload_img941); ?>"
                        data-category="<?php echo htmlspecialchars($category941); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName941); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status941); ?>; position:absolute; top:165px; left:116px;'>
                    </div>


                    <!-- ASSET 942 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId942; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:215px; left:106px;"
                        alt="Asset Image 942" data-bs-toggle="modal" data-bs-target="#imageModal942"
                        onclick="fetchAssetData(942);" data-room="<?php echo htmlspecialchars($room942); ?>"
                        data-floor="<?php echo htmlspecialchars($floor942); ?>"
                        data-image="<?php echo base64_encode($upload_img942); ?>"
                        data-category="<?php echo htmlspecialchars($category942); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName942); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status942); ?>; position:absolute; top:215px; left:116px;'>
                    </div>

                    <!-- ASSET 943 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId943; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:255px; left:133px;"
                        alt="Asset Image 943" data-bs-toggle="modal" data-bs-target="#imageModal943"
                        onclick="fetchAssetData(943);" data-room="<?php echo htmlspecialchars($room943); ?>"
                        data-floor="<?php echo htmlspecialchars($floor943); ?>"
                        data-image="<?php echo base64_encode($upload_img943); ?>"
                        data-category="<?php echo htmlspecialchars($category943); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName943); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status943); ?>; position:absolute; top:255px; left:143px;'>
                    </div>

                    <!-- ASSET 944 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId944; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:290px; left:133px;"
                        alt="Asset Image 944" data-bs-toggle="modal" data-bs-target="#imageModal944"
                        onclick="fetchAssetData(944);" data-room="<?php echo htmlspecialchars($room944); ?>"
                        data-floor="<?php echo htmlspecialchars($floor944); ?>"
                        data-image="<?php echo base64_encode($upload_img944); ?>"
                        data-category="<?php echo htmlspecialchars($category944); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName944); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status944); ?>; position:absolute; top:290px; left:143px;'>
                    </div>

                    <!-- ASSET 945 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId945; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:322px; left:133px;"
                        alt="Asset Image 945" data-bs-toggle="modal" data-bs-target="#imageModal945"
                        onclick="fetchAssetData(945);" data-room="<?php echo htmlspecialchars($room945); ?>"
                        data-floor="<?php echo htmlspecialchars($floor945); ?>"
                        data-image="<?php echo base64_encode($upload_img945); ?>"
                        data-category="<?php echo htmlspecialchars($category945); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName945); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status945); ?>; position:absolute; top:322px; left:143px;'>
                    </div>

                    <!-- ASSET 946 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId946; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:365px; left:110px;"
                        alt="Asset Image 946" data-bs-toggle="modal" data-bs-target="#imageModal946"
                        onclick="fetchAssetData(946);" data-room="<?php echo htmlspecialchars($room946); ?>"
                        data-floor="<?php echo htmlspecialchars($floor946); ?>"
                        data-image="<?php echo base64_encode($upload_img946); ?>"
                        data-category="<?php echo htmlspecialchars($category946); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName946); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status946); ?>; position:absolute; top:365px; left:120px;'>
                    </div>

                    <!-- ASSET 947 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId947; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:415px; left:110px;"
                        alt="Asset Image 947" data-bs-toggle="modal" data-bs-target="#imageModal947"
                        onclick="fetchAssetData(947);" data-room="<?php echo htmlspecialchars($room947); ?>"
                        data-floor="<?php echo htmlspecialchars($floor947); ?>"
                        data-image="<?php echo base64_encode($upload_img947); ?>"
                        data-category="<?php echo htmlspecialchars($category947); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName947); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status947); ?>; position:absolute; top:415px; left:120px;'>
                    </div>

                    <!-- ASSET 948 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId948; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:465px; left:110px;"
                        alt="Asset Image 948" data-bs-toggle="modal" data-bs-target="#imageModal948"
                        onclick="fetchAssetData(948);" data-room="<?php echo htmlspecialchars($room948); ?>"
                        data-floor="<?php echo htmlspecialchars($floor948); ?>"
                        data-image="<?php echo base64_encode($upload_img948); ?>"
                        data-category="<?php echo htmlspecialchars($category948); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName948); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status948); ?>; position:absolute; top:465px; left:120px;'>
                    </div>

                    <!-- ASSET 949 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId949; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:165px; left:185px;"
                        alt="Asset Image 949" data-bs-toggle="modal" data-bs-target="#imageModal949"
                        onclick="fetchAssetData(949);" data-room="<?php echo htmlspecialchars($room949); ?>"
                        data-floor="<?php echo htmlspecialchars($floor949); ?>"
                        data-image="<?php echo base64_encode($upload_img949); ?>"
                        data-category="<?php echo htmlspecialchars($category949); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName949); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status949); ?>; position:absolute; top:165px; left:195px;'>
                    </div>



                    <!-- ASSET 950 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId950; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:165px; left:295px;"
                        alt="Asset Image 950" data-bs-toggle="modal" data-bs-target="#imageModal950"
                        onclick="fetchAssetData(950);" data-room="<?php echo htmlspecialchars($room950); ?>"
                        data-floor="<?php echo htmlspecialchars($floor950); ?>"
                        data-image="<?php echo base64_encode($upload_img950); ?>"
                        data-category="<?php echo htmlspecialchars($category950); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName950); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status950); ?>; position:absolute; top:165px; left:305px;'>
                    </div>

                    <!-- ASSET 951 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId951; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:240px; left:185px;"
                        alt="Asset Image 951" data-bs-toggle="modal" data-bs-target="#imageModal951"
                        onclick="fetchAssetData(951);" data-room="<?php echo htmlspecialchars($room951); ?>"
                        data-floor="<?php echo htmlspecialchars($floor951); ?>"
                        data-image="<?php echo base64_encode($upload_img951); ?>"
                        data-category="<?php echo htmlspecialchars($category951); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName951); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status951); ?>; position:absolute; top:240px; left:195px;'>
                    </div>

                    <!-- ASSET 952 -->
                    <img src="../image.php?id=910" class="asset-image" data-id="<?php echo $assetId952; ?>"
                        style="width:15px; cursor:pointer; position:absolute; top:240px; left:295px;"
                        alt="Asset Image 952" data-bs-toggle="modal" data-bs-target="#imageModal952"
                        onclick="fetchAssetData(952);" data-room="<?php echo htmlspecialchars($room952); ?>"
                        data-floor="<?php echo htmlspecialchars($floor952); ?>"
                        data-image="<?php echo base64_encode($upload_img952); ?>"
                        data-category="<?php echo htmlspecialchars($category952); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName952); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status952); ?>; position:absolute; top:240px; left:305px;'>
                    </div>

                    <!-- ASSET 953 -->
                    <img src="../image.php?id=953" class="asset-image" data-id="<?php echo $assetId953; ?>"
                        style="width:30px; cursor:pointer; position:absolute; top:205px; left:185px;"
                        alt="Asset Image 953" data-bs-toggle="modal" data-bs-target="#imageModal953"
                        onclick="fetchAssetData(953);" data-room="<?php echo htmlspecialchars($room953); ?>"
                        data-floor="<?php echo htmlspecialchars($floor953); ?>"
                        data-image="<?php echo base64_encode($upload_img953); ?>"
                        data-category="<?php echo htmlspecialchars($category953); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName953); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status953); ?>; position:absolute; top:205px; left:207px;'>
                    </div>



                    <!-- ASSET 954 -->
                    <img src="../image.php?id=953" class="asset-image" data-id="<?php echo $assetId954; ?>"
                        style="width:30px; cursor:pointer; position:absolute; top:205px; left:275px;"
                        alt="Asset Image 954" data-bs-toggle="modal" data-bs-target="#imageModal954"
                        onclick="fetchAssetData(954);" data-room="<?php echo htmlspecialchars($room954); ?>"
                        data-floor="<?php echo htmlspecialchars($floor954); ?>"
                        data-image="<?php echo base64_encode($upload_img954); ?>"
                        data-category="<?php echo htmlspecialchars($category954); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName954); ?>">
                    <div
                        style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status954); ?>; position:absolute; top:205px; left:297px;'>
                    </div>


                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>

                    <!--End of hover-->


                </div>



                <!-- Modal structure for id 910 -->
                <div class='modal fade' id='imageModal910' tabindex='-1' aria-labelledby='imageModalLabel910'
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
                                            alt="No Image">
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
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-10">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img"
                                            accept="image/*" capture="user" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop1">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 1-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            <!-- Modal structure for id 911-->
            <div class='modal fade' id='imageModal911' tabindex='-1' aria-labelledby='imageModalLabel911'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2); ?>"
                                        alt="No Image">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId2); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date2); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room2); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building2); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor2); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category2); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName2); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy2); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description2); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop2">
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
                <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal912' tabindex='-1' aria-labelledby='imageModalLabel912'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img3); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId3); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date3); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room3); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building3); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor3); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category3); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName3); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy3); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description3); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <!-- Modal footer -->
                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop3">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 3-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal913' tabindex='-1' aria-labelledby='imageModalLabel913'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img4); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId4); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date4); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room4); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building4); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor4); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category4); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName4); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy4); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description4); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <div class="button-submit-container">

                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop4">
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
                <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal914' tabindex='-1' aria-labelledby='imageModalLabel914'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId5); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date5); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room5); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building5); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor5); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category5); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName5); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy5); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description5); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop5">
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
                <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal915' tabindex='-1' aria-labelledby='imageModalLabel915'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img6); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId6); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date6); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room6); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building6); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor6); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category6); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName6); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy6); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description6); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <!-- Modal footer -->
                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop6">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 6-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop6" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal916' tabindex='-1' aria-labelledby='imageModalLabel916'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img7); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId7); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date7); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room7); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building7); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor7); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category7); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName7); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy7); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description7); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <!-- Modal footer -->
                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop7">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="map-alert">
                <!--Edit for table 7-->
                <div class="modal fade" id="staticBackdrop7" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal917' tabindex='-1' aria-labelledby='imageModalLabel917'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img8); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId8); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date8); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room8); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building8); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor8); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category8); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName8); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy8); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description8); ?>" />
                                </div>
                                <!--End of Fifth Row-->

                                <!--Sixth Row-->
                                <div class="col-2">
                                    <label for="upload_img" class="form-label">Upload:</label>
                                </div>
                                <div class="col-8">
                                    <input type="file" class="form-control" id="upload_img" name="upload_img"
                                        accept="image/*" capture="user" />
                                </div>
                                <!--End of Sixth Row-->

                                <!-- Modal footer -->
                                <div class="button-submit-container">
                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop8">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 8-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop8" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
            <div class='modal fade' id='imageModal918' tabindex='-1' aria-labelledby='imageModalLabel918'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img9); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId9); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date9); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room9); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building9); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor9); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category9); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName9); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy9); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description9); ?>" />
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
                                        data-bs-target="#staticBackdrop9">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 9-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop9" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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



            <!-- Modal structure for id 919-->
            <div class='modal fade' id='imageModal919' tabindex='-1' aria-labelledby='imageModalLabel919'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img10); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId10); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date10); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room10); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building10); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor10); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category10); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName10); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy10); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description10); ?>" />
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
                                        data-bs-target="#staticBackdrop10">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>

            <!--Edit for table 10-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop10" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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


            <!-- Modal structure for id 903-->
            <div class='modal fade' id='imageModal903' tabindex='-1' aria-labelledby='imageModalLabel903'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date11); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room11); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building11); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor11); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category11); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName11); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy11); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description11); ?>" />
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
                                        data-bs-target="#staticBackdrop11">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 11-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop11" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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

            <!-- Modal structure for id 904-->
            <div class='modal fade' id='imageModal904' tabindex='-1' aria-labelledby='imageModalLabel904'
                aria-hidden='true'>
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
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img12); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId12); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date12); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room12); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building12); ?>" readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor12); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category12); ?>" readonly />
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName12); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy12); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description12); ?>" />
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
                                        data-bs-target="#staticBackdrop12">
                                        Save
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--Edit for table 12-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop12" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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


            <!-- Modal structure for id 905-->
            <div class='modal fade' id='imageModal905' tabindex='-1' aria-labelledby='imageModalLabel905'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId6866); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img6866); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId6866); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date6866); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room6866); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building6866); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor6866); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category6866); ?>"
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
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName6866); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy6866); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description6866); ?>" />
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
                                        data-bs-target="#staticBackdrop6866">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 6866-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop6866" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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



            <!-- Modal structure for id 906-->
            <div class='modal fade' id='imageModal906' tabindex='-1' aria-labelledby='imageModalLabel906'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId906); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img906); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId906); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date906); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room906); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building906); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor906); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category906); ?>"
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
                                        <option value="Working" <?php echo ($status906 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status906 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status906 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status906 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName906); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy906); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description906); ?>" />
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
                                        data-bs-target="#staticBackdrop906">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 906-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop906" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit906">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>







            <!-- Modal structure for id 907-->
            <div class='modal fade' id='imageModal907' tabindex='-1' aria-labelledby='imageModalLabel907'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId907); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img907); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId907); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date907); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room907); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building907); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor907); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category907); ?>"
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
                                        <option value="Working" <?php echo ($status907 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status907 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status907 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status907 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName907); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy907); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description907); ?>" />
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
                                        data-bs-target="#staticBackdrop907">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 907-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop907" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit907">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>







            <!-- Modal structure for id 908-->
            <div class='modal fade' id='imageModal908' tabindex='-1' aria-labelledby='imageModalLabel908'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId908); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img908); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId908); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date908); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room908); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building908); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor908); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category908); ?>"
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
                                        <option value="Working" <?php echo ($status908 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status908 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status908 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status908 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName908); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy908); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description908); ?>" />
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
                                        data-bs-target="#staticBackdrop908">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 908-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop908" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit908">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>




            <!-- Modal structure for id 909-->
            <div class='modal fade' id='imageModal909' tabindex='-1' aria-labelledby='imageModalLabel909'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId909); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img909); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId909); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date909); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room909); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building909); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor909); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category909); ?>"
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
                                        <option value="Working" <?php echo ($status909 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status909 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status909 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status909 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName909); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy909); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description909); ?>" />
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
                                        data-bs-target="#staticBackdrop909">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 909-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop909" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit909">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>




            <!-- Modal structure for id 920-->
            <div class='modal fade' id='imageModal920' tabindex='-1' aria-labelledby='imageModalLabel920'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId920); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img920); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId920); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date920); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room920); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building920); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor920); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category920); ?>"
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
                                        <option value="Working" <?php echo ($status920 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status920 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status920 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status920 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName920); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy920); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description920); ?>" />
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
                                        data-bs-target="#staticBackdrop920">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 920-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop920" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit920">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>







            <!-- Modal structure for id 921-->
            <div class='modal fade' id='imageModal921' tabindex='-1' aria-labelledby='imageModalLabel921'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId921); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img921); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId921); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date921); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room921); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building921); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor921); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category921); ?>"
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
                                        <option value="Working" <?php echo ($status921 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status921 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status921 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status921 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName921); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy921); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description921); ?>" />
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
                                        data-bs-target="#staticBackdrop921">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 921-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop921" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit921">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            <!-- Modal structure for id 922-->
            <div class='modal fade' id='imageModal922' tabindex='-1' aria-labelledby='imageModalLabel922'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId922); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img922); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId922); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date922); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room922); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building922); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor922); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category922); ?>"
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
                                        <option value="Working" <?php echo ($status922 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status922 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status922 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status922 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName922); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy922); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description922); ?>" />
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
                                        data-bs-target="#staticBackdrop922">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 922-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop922" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit922">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            <!-- Modal structure for id 923-->
            <div class='modal fade' id='imageModal923' tabindex='-1' aria-labelledby='imageModalLabel923'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId923); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img923); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId923); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date923); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room923); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building923); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor923); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category923); ?>"
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
                                        <option value="Working" <?php echo ($status923 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status923 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status923 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status923 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName923); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy923); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description923); ?>" />
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
                                        data-bs-target="#staticBackdrop923">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 923-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop923" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit923">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            <!-- Modal structure for id 924-->
            <div class='modal fade' id='imageModal924' tabindex='-1' aria-labelledby='imageModalLabel924'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId924); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img924); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId924); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date924); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room924); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building924); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor924); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category924); ?>"
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
                                        <option value="Working" <?php echo ($status924 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status924 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status924 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status924 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName924); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy924); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description924); ?>" />
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
                                        data-bs-target="#staticBackdrop924">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 924-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop924" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit924">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 925-->
            <div class='modal fade' id='imageModal925' tabindex='-1' aria-labelledby='imageModalLabel925'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId925); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img925); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId925); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date925); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room925); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building925); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor925); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category925); ?>"
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
                                        <option value="Working" <?php echo ($status925 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status925 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status925 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status925 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName925); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy925); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description925); ?>" />
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
                                        data-bs-target="#staticBackdrop925">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 925-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop925" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit925">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            </div> <!-- Modal structure for id 926-->
            <div class='modal fade' id='imageModal926' tabindex='-1' aria-labelledby='imageModalLabel926'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId926); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img926); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId926); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date926); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room926); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building926); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor926); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category926); ?>"
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
                                        <option value="Working" <?php echo ($status926 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status926 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status926 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status926 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName926); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy926); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description926); ?>" />
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
                                        data-bs-target="#staticBackdrop926">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 926-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop926" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit926">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 927-->
            <div class='modal fade' id='imageModal927' tabindex='-1' aria-labelledby='imageModalLabel927'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId927); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img927); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId927); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date927); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room927); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building927); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor927); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category927); ?>"
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
                                        <option value="Working" <?php echo ($status927 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status927 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status927 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status927 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName927); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy927); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description927); ?>" />
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
                                        data-bs-target="#staticBackdrop927">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 927-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop927" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit927">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 928-->
            <div class='modal fade' id='imageModal928' tabindex='-1' aria-labelledby='imageModalLabel928'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId928); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img928); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId928); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date928); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room928); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building928); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor928); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category928); ?>"
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
                                        <option value="Working" <?php echo ($status928 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status928 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status928 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status928 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName928); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy928); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description928); ?>" />
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
                                        data-bs-target="#staticBackdrop928">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 928-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop928" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit928">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 929-->
            <div class='modal fade' id='imageModal929' tabindex='-1' aria-labelledby='imageModalLabel929'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId929); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img929); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId929); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date929); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room929); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building929); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor929); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category929); ?>"
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
                                        <option value="Working" <?php echo ($status929 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status929 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status929 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status929 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName929); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy929); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description929); ?>" />
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
                                        data-bs-target="#staticBackdrop929">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 929-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop929" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit929">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 930-->
            <div class='modal fade' id='imageModal930' tabindex='-1' aria-labelledby='imageModalLabel930'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId930); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img930); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId930); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date930); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room930); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building930); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor930); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category930); ?>"
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
                                        <option value="Working" <?php echo ($status930 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status930 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status930 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status930 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName930); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy930); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description930); ?>" />
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
                                        data-bs-target="#staticBackdrop930">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 930-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop930" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit930">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 931-->
            <div class='modal fade' id='imageModal931' tabindex='-1' aria-labelledby='imageModalLabel931'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId931); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img931); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId931); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date931); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room931); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building931); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor931); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category931); ?>"
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
                                        <option value="Working" <?php echo ($status931 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status931 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status931 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status931 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName931); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy931); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description931); ?>" />
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
                                        data-bs-target="#staticBackdrop931">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 931-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop931" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit931">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>




            </div> <!-- Modal structure for id 932-->
            <div class='modal fade' id='imageModal932' tabindex='-1' aria-labelledby='imageModalLabel932'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId932); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img932); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId932); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date932); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room932); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building932); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor932); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category932); ?>"
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
                                        <option value="Working" <?php echo ($status932 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status932 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status932 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status932 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName932); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy932); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description932); ?>" />
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
                                        data-bs-target="#staticBackdrop932">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 932-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop932" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit932">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 933-->
            <div class='modal fade' id='imageModal933' tabindex='-1' aria-labelledby='imageModalLabel933'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId933); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img933); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId933); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date933); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room933); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building933); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor933); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category933); ?>"
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
                                        <option value="Working" <?php echo ($status933 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status933 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status933 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status933 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName933); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy933); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description933); ?>" />
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
                                        data-bs-target="#staticBackdrop933">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 933-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop933" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit933">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 934-->
            <div class='modal fade' id='imageModal934' tabindex='-1' aria-labelledby='imageModalLabel934'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId934); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img934); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId934); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date934); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room934); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building934); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor934); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category934); ?>"
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
                                        <option value="Working" <?php echo ($status934 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status934 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status934 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status934 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName934); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy934); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description934); ?>" />
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
                                        data-bs-target="#staticBackdrop934">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 934-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop934" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit934">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>




            </div> <!-- Modal structure for id 935-->
            <div class='modal fade' id='imageModal935' tabindex='-1' aria-labelledby='imageModalLabel935'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId935); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img935); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId935); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date935); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room935); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building935); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor935); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category935); ?>"
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
                                        <option value="Working" <?php echo ($status935 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status935 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status935 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status935 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName935); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy935); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description935); ?>" />
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
                                        data-bs-target="#staticBackdrop935">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 935-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop935" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit935">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 936-->
            <div class='modal fade' id='imageModal936' tabindex='-1' aria-labelledby='imageModalLabel936'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId936); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img936); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId936); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date936); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room936); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building936); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor936); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category936); ?>"
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
                                        <option value="Working" <?php echo ($status936 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status936 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status936 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status936 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName936); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy936); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description936); ?>" />
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
                                        data-bs-target="#staticBackdrop936">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 936-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop936" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit936">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 937-->
            <div class='modal fade' id='imageModal937' tabindex='-1' aria-labelledby='imageModalLabel937'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId937); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img937); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId937); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date937); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room937); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building937); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor937); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category937); ?>"
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
                                        <option value="Working" <?php echo ($status937 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status937 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status937 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status937 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName937); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy937); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description937); ?>" />
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
                                        data-bs-target="#staticBackdrop937">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 937-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop937" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit937">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>




            </div> <!-- Modal structure for id 938-->
            <div class='modal fade' id='imageModal938' tabindex='-1' aria-labelledby='imageModalLabel938'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId938); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img938); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId938); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date938); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room938); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building938); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor938); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category938); ?>"
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
                                        <option value="Working" <?php echo ($status938 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status938 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status938 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status938 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName938); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy938); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description938); ?>" />
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
                                        data-bs-target="#staticBackdrop938">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 938-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop938" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit938">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 939-->
            <div class='modal fade' id='imageModal939' tabindex='-1' aria-labelledby='imageModalLabel939'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId939); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img939); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId939); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date939); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room939); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building939); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor939); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category939); ?>"
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
                                        <option value="Working" <?php echo ($status939 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status939 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status939 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status939 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName939); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy939); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description939); ?>" />
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
                                        data-bs-target="#staticBackdrop939">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 939-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop939" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit939">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 940-->
            <div class='modal fade' id='imageModal940' tabindex='-1' aria-labelledby='imageModalLabel940'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId940); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img940); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId940); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date940); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room940); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building940); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor940); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category940); ?>"
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
                                        <option value="Working" <?php echo ($status940 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status940 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status940 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status940 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName940); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy940); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description940); ?>" />
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
                                        data-bs-target="#staticBackdrop940">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 940-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop940" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit940">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 941-->
            <div class='modal fade' id='imageModal941' tabindex='-1' aria-labelledby='imageModalLabel941'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId941); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img941); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId941); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date941); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room941); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building941); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor941); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category941); ?>"
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
                                        <option value="Working" <?php echo ($status941 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status941 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status941 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status941 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName941); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy941); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description941); ?>" />
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
                                        data-bs-target="#staticBackdrop941">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 941-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop941" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit941">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 942-->
            <div class='modal fade' id='imageModal942' tabindex='-1' aria-labelledby='imageModalLabel942'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId942); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img942); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId942); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date942); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room942); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building942); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor942); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category942); ?>"
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
                                        <option value="Working" <?php echo ($status942 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status942 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status942 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status942 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName942); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy942); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description942); ?>" />
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
                                        data-bs-target="#staticBackdrop942">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 942-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop942" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit942">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 943-->
            <div class='modal fade' id='imageModal943' tabindex='-1' aria-labelledby='imageModalLabel943'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId943); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img943); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId943); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date943); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room943); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building943); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor943); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category943); ?>"
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
                                        <option value="Working" <?php echo ($status943 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status943 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status943 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status943 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName943); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy943); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description943); ?>" />
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
                                        data-bs-target="#staticBackdrop943">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 943-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop943" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit943">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 944-->
            <div class='modal fade' id='imageModal944' tabindex='-1' aria-labelledby='imageModalLabel944'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId944); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img944); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId944); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date944); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room944); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building944); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor944); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category944); ?>"
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
                                        <option value="Working" <?php echo ($status944 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status944 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status944 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status944 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName944); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy944); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description944); ?>" />
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
                                        data-bs-target="#staticBackdrop944">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 944-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop944" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit944">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 945-->
            <div class='modal fade' id='imageModal945' tabindex='-1' aria-labelledby='imageModalLabel945'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId945); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img945); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId945); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date945); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room945); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building945); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor945); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category945); ?>"
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
                                        <option value="Working" <?php echo ($status945 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status945 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status945 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status945 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName945); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy945); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description945); ?>" />
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
                                        data-bs-target="#staticBackdrop945">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 945-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop945" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit945">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 946-->
            <div class='modal fade' id='imageModal946' tabindex='-1' aria-labelledby='imageModalLabel946'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId946); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img946); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId946); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date946); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room946); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building946); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor946); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category946); ?>"
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
                                        <option value="Working" <?php echo ($status946 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status946 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status946 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status946 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName946); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy946); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description946); ?>" />
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
                                        data-bs-target="#staticBackdrop946">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 946-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop946" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit946">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 947-->
            <div class='modal fade' id='imageModal947' tabindex='-1' aria-labelledby='imageModalLabel947'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId947); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img947); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId947); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date947); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room947); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building947); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor947); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category947); ?>"
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
                                        <option value="Working" <?php echo ($status947 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status947 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status947 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status947 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName947); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy947); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description947); ?>" />
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
                                        data-bs-target="#staticBackdrop947">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 947-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop947" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit947">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



            </div> <!-- Modal structure for id 948-->
            <div class='modal fade' id='imageModal948' tabindex='-1' aria-labelledby='imageModalLabel948'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId948); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img948); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId948); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date948); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room948); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building948); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor948); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category948); ?>"
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
                                        <option value="Working" <?php echo ($status948 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status948 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status948 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status948 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName948); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy948); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description948); ?>" />
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
                                        data-bs-target="#staticBackdrop948">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 948-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop948" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit948">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            </div> <!-- Modal structure for id 949-->
            <div class='modal fade' id='imageModal949' tabindex='-1' aria-labelledby='imageModalLabel949'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId949); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img949); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId949); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date949); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room949); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building949); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor949); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category949); ?>"
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
                                        <option value="Working" <?php echo ($status949 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status949 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status949 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status949 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName949); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy949); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description949); ?>" />
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
                                        data-bs-target="#staticBackdrop949">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 949-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop949" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit949">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            </div> <!-- Modal structure for id 950-->
            <div class='modal fade' id='imageModal950' tabindex='-1' aria-labelledby='imageModalLabel950'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId950); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img950); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId950); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date950); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room950); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building950); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor950); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category950); ?>"
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
                                        <option value="Working" <?php echo ($status950 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status950 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status950 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status950 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName950); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy950); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description950); ?>" />
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
                                        data-bs-target="#staticBackdrop950">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 950-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop950" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit950">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 951-->
            <div class='modal fade' id='imageModal951' tabindex='-1' aria-labelledby='imageModalLabel951'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId951); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img951); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId951); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date951); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room951); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building951); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor951); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category951); ?>"
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
                                        <option value="Working" <?php echo ($status951 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status951 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status951 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status951 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName951); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy951); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description951); ?>" />
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
                                        data-bs-target="#staticBackdrop951">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 951-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop951" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit951">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            </div> <!-- Modal structure for id 952-->
            <div class='modal fade' id='imageModal952' tabindex='-1' aria-labelledby='imageModalLabel952'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId952); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img952); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId952); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date952); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room952); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building952); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor952); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category952); ?>"
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
                                        <option value="Working" <?php echo ($status952 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status952 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status952 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status952 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName952); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy952); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description952); ?>" />
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
                                        data-bs-target="#staticBackdrop952">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 952-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop952" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit952">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 953-->
            <div class='modal fade' id='imageModal953' tabindex='-1' aria-labelledby='imageModalLabel953'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId953); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img953); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId953); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date953); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room953); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building953); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor953); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category953); ?>"
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
                                        <option value="Working" <?php echo ($status953 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status953 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status953 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status953 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName953); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy953); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description953); ?>" />
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
                                        data-bs-target="#staticBackdrop953">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 953-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop953" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit953">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>


            </div> <!-- Modal structure for id 954-->
            <div class='modal fade' id='imageModal954' tabindex='-1' aria-labelledby='imageModalLabel954'
                aria-hidden='true'>
                <div class='modal-dialog modal-xl modal-dialog-centered'>
                    <div class='modal-content'>
                        <!-- Modal header -->
                        <div class='modal-header'>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>

                        <!-- Modal body -->
                        <div class='modal-body'>
                            <form method="post" class="row g-3" enctype="multipart/form-data">
                                <input type="hidden" name="assetId"
                                    value="<?php echo htmlspecialchars($assetId954); ?>">
                                <!--START DIV FOR IMAGE -->

                                <!--First Row-->
                                <!--IMAGE HERE-->
                                <div class="col-12 center-content">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img954); ?>"
                                        alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                </div>
                                <!--END DIV FOR IMAGE -->

                                <div class="col-4" style="display:none">
                                    <label for="assetId" class="form-label">Tracking #:</label>
                                    <input type="text" class="form-control" id="assetId" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId954); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="<?php echo htmlspecialchars($date954); ?>" readonly />
                                </div>

                                <!--Second Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="room" name="room"
                                        value="<?php echo htmlspecialchars($room954); ?>" readonly />
                                </div>


                                <div class="col-6" style="display:none">
                                    <input type="text" class="form-control  center-content" id="building"
                                        name="building" value="<?php echo htmlspecialchars($building954); ?>"
                                        readonly />
                                </div>

                                <!--End of Second Row-->

                                <!--Third Row-->
                                <div class="col-6">
                                    <input type="text" class="form-control" id="floor" name="floor"
                                        value="<?php echo htmlspecialchars($floor954); ?>" readonly />
                                </div>

                                <div class="col-12 center-content">
                                    <input type="text" class="form-control  center-content" id="category"
                                        name="category" value="<?php echo htmlspecialchars($category954); ?>"
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
                                        <option value="Working" <?php echo ($status954 == 'Working')
                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                        <option value="Under Maintenance" <?php echo ($status954 == 'Under Maintenance')
                                            ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                        <option value="For Replacement" <?php echo ($status954 == 'For Replacement')
                                            ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                        <option value="Need Repair" <?php echo ($status954 == 'Need Repair')
                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                    </select>
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedName" class="form-label">Assigned Name:</label>
                                    <input type="text" class="form-control" id="assignedName" name="assignedName"
                                        value="<?php echo htmlspecialchars($assignedName954); ?>" readonly />
                                </div>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                        value="<?php echo htmlspecialchars($assignedBy954); ?>" readonly />
                                </div>

                                <!--End of Fourth Row-->

                                <!--Fifth Row-->
                                <!-- <div class="col-3">
                                                        <label for="description" class="form-label">Description:</label>
                                                    </div> -->
                                <div class="col-12">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="<?php echo htmlspecialchars($description954); ?>" />
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
                                        data-bs-target="#staticBackdrop954">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Edit for table 954-->
            <div class="map-alert">
                <div class="modal fade" id="staticBackdrop954" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-footer">
                                <p>Are you sure you want to save changes?</p>
                                <div class="modal-popups">
                                    <button type="submit" class="btn add-modal-btn" name="edit954">Yes</button>
                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>



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
    <script>
        // Find all input elements with ID 'description'
        var inputElements = document.querySelectorAll('input#description');

        // Iterate through each input element
        inputElements.forEach(function (inputElement) {
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>