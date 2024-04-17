<?php
// Include your database connection configuration
include_once("../../config/connection.php");
$conn = connection(); // Assuming you have a function called connection that sets up your DB connection

// Function to check if a location is within a certain radius of a central point
function isWithinBoundary($userLat, $userLong, $centerLat, $centerLong, $radius)
{
    $earthRadius = 6371000; // in meters
    $dLat = deg2rad($centerLat - $userLat);
    $dLong = deg2rad($centerLong - $userLong);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($userLat)) * cos(deg2rad($centerLat)) *
        sin($dLong / 2) * sin($dLong / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    return $distance <= $radius;
}


$buildings = [
    'Belmonte Building' => [
        ['lat' => 14.70088, 'long' => 121.03298, 'radius' => 12],
        ['lat' => 14.70084, 'long' => 121.03307, 'radius' => 12],
        ['lat' => 14.70079, 'long' => 121.03317, 'radius' => 12],
    ],
    'Admin Building' => [
        ['lat' => 14.70043, 'long' => 121.03287, 'radius' => 17],
    ],
    'TechVoc Building' => [
        ['lat' => 14.70019, 'long' => 121.03364, 'radius' => 27],
    ],
    'Old Academic Building' => [
        ['lat' => 14.70035, 'long' => 121.03309, 'radius' => 10.46],
        ['lat' => 14.70031, 'long' => 121.03318, 'radius' => 10.68],
        ['lat' => 14.70043, 'long' => 121.03326, 'radius' => 7.67],
        ['lat' => 14.70052, 'long' => 121.03331, 'radius' => 7.81],
        ['lat' => 14.70062, 'long' => 121.03338, 'radius' => 8.79],
        ['lat' => 14.70070, 'long' => 121.03343, 'radius' => 8.54],
        ['lat' => 14.70076, 'long' => 121.03333, 'radius' => 8.86],
    ],
    'Bautista Building' => [
        ['lat' => 14.70056, 'long' => 121.03241, 'radius' => 22]

    ],
    'KorPhil Building' => [
        ['lat' => 14.69974, 'long' => 121.03169, 'radius' =>  38.45]
    ],

    'Multipurpose Building' => [
        ['lat' => 14.70046, 'long' => 121.03401, 'radius' =>  16.68],

        ['lat' => 14.66368, 'long' => 121.04499, 'radius' =>  16.68],
    ],

    'New Academic Building' => [
        ['lat' => 14.70108, 'long' => 121.03276, 'radius' =>  22.03]
    ],

    'Urban Farming' => [
        ['lat' => 14.70078, 'long' => 121.03203, 'radius' => 24.77],
        ['lat' => 14.70108, 'long' => 121.03223, 'radius' => 26],
        ['lat' => 14.70095, 'long' => 121.03181, 'radius' => 27.10],
        ['lat' => 14.69406, 'long' => 121.02914, 'radius' => 36],

    ],

    'Ballroom Building' => [
        ['lat' => 14.70055, 'long' => 121.03382, 'radius' =>  9.92],
        ['lat' => 14.74151, 'long' => 121.06723, 'radius' =>  100],

    ],

    'Open Ground' => [
        ['lat' => 14.70091, 'long' => 121.03250, 'radius' =>  16.44],
        ['lat' => 14.70080, 'long' => 121.03271, 'radius' =>  14.69],
        ['lat' => 14.70072, 'long' => 121.03289, 'radius' =>  10.91],
        ['lat' => 14.70064, 'long' => 121.03302, 'radius' =>  10],
        ['lat' => 14.70056, 'long' => 121.03313, 'radius' =>  11.56],
        ['lat' => 14.70066, 'long' => 121.03323, 'radius' =>  8],
    ],

    'University Park' => [
        ['lat' => 14.70024, 'long' => 121.03336, 'radius' =>  8.25],
        ['lat' => 14.70033, 'long' => 121.03341, 'radius' =>  8.25],
        ['lat' => 14.70051, 'long' => 121.03350, 'radius' =>  8.67],
        ['lat' => 14.70061, 'long' => 121.03360, 'radius' =>  13.30],
        ['lat' => 14.70056, 'long' => 121.03313, 'radius' =>  11.56],
        ['lat' => 14.70066, 'long' => 121.03323, 'radius' =>  8],
    ],

    // DAPAT HULI LAGI TO
    'Inside at QCU' => [
        ['lat' => 14.70044, 'long' => 121.03273, 'radius' =>  171.21]
    ],
];


if (isset($_POST['accountId'])) {
    $accountId = $_POST['accountId'];

    // Fetch the user's current coordinates from the database
    $query = "SELECT latitude, longitude FROM account WHERE accountId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();

    // ... (rest of your code)

    if ($row = $result->fetch_assoc()) {
        $userLat = floatval($row['latitude']);
        $userLong = floatval($row['longitude']);

        $buildingFound = false; // Flag to check if building is found
        foreach ($buildings as $buildingName => $areas) {
            foreach ($areas as $area) {
                if (isWithinBoundary($userLat, $userLong, $area['lat'], $area['long'], $area['radius'])) {
                    $buildingFound = true; // Building found
                    // The user is within this building's boundary
                    $locationUpdateQuery = "UPDATE account SET qculocation = ? WHERE accountId = ?";
                    $locationUpdateStmt = $conn->prepare($locationUpdateQuery);
                    $locationUpdateStmt->bind_param('si', $buildingName, $accountId);
                    $locationUpdateStmt->execute();
                    $locationUpdateStmt->close();

                    echo json_encode(["status" => "inside", "building" => $buildingName, "latitude" => $userLat, "longitude" => $userLong]);
                    break 2; // Stop checking once we find the building they're in
                }
            }
        }

        if (!$buildingFound) {
            // If no building contains the user, they're outside
            $locationUpdateQuery = "UPDATE account SET qculocation = 'Outside of QCU' WHERE accountId = ?";
            $locationUpdateStmt = $conn->prepare($locationUpdateQuery);
            $locationUpdateStmt->bind_param('i', $accountId);
            $locationUpdateStmt->execute();
            $locationUpdateStmt->close();

            echo json_encode(["status" => "outside", "latitude" => $userLat, "longitude" => $userLong]);
        } else {
            echo json_encode(["error" => "Location not found for the given personnel."]);
        }
    }
}
