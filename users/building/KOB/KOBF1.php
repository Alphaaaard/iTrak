<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {

    //FOR ID 2249
    $sql2249 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2249";
    $stmt2249 = $conn->prepare($sql2249);
    $stmt2249->execute();
    $result2249 = $stmt2249->get_result();
    $row2249 = $result2249->fetch_assoc();
    $assetId2249 = $row2249['assetId'];
    $category2249 = $row2249['category'];
    $date2249 = $row2249['date'];
    $building2249 = $row2249['building'];
    $floor2249 = $row2249['floor'];
    $room2249 = $row2249['room'];
    $status2249 = $row2249['status'];
    $assignedName2249 = $row2249['assignedName'];
    $assignedBy2249 = $row2249['assignedBy'];
    $upload_img2249 = $row2249['upload_img'];
    $description2249 = $row2249['description'];

    //FOR ID 2250
    $sql2250 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2250";
    $stmt2250 = $conn->prepare($sql2250);
    $stmt2250->execute();
    $result2250 = $stmt2250->get_result();
    $row2250 = $result2250->fetch_assoc();
    $assetId2250 = $row2250['assetId'];
    $category2250 = $row2250['category'];
    $date2250 = $row2250['date'];
    $building2250 = $row2250['building'];
    $floor2250 = $row2250['floor'];
    $room2250 = $row2250['room'];
    $status2250 = $row2250['status'];
    $assignedName2250 = $row2250['assignedName'];
    $assignedBy2250 = $row2250['assignedBy'];
    $upload_img2250 = $row2250['upload_img'];
    $description2250 = $row2250['description'];

    //FOR ID 2251
    $sql2251 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2251";
    $stmt2251 = $conn->prepare($sql2251);
    $stmt2251->execute();
    $result2251 = $stmt2251->get_result();
    $row2251 = $result2251->fetch_assoc();
    $assetId2251 = $row2251['assetId'];
    $category2251 = $row2251['category'];
    $date2251 = $row2251['date'];
    $building2251 = $row2251['building'];
    $floor2251 = $row2251['floor'];
    $room2251 = $row2251['room'];
    $status2251 = $row2251['status'];
    $assignedName2251 = $row2251['assignedName'];
    $assignedBy2251 = $row2251['assignedBy'];
    $upload_img2251 = $row2251['upload_img'];
    $description2251 = $row2251['description'];

    //FOR ID 2252
    $sql2252 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2252";
    $stmt2252 = $conn->prepare($sql2252);
    $stmt2252->execute();
    $result2252 = $stmt2252->get_result();
    $row2252 = $result2252->fetch_assoc();
    $assetId2252 = $row2252['assetId'];
    $category2252 = $row2252['category'];
    $date2252 = $row2252['date'];
    $building2252 = $row2252['building'];
    $floor2252 = $row2252['floor'];
    $room2252 = $row2252['room'];
    $status2252 = $row2252['status'];
    $assignedName2252 = $row2252['assignedName'];
    $assignedBy2252 = $row2252['assignedBy'];
    $upload_img2252 = $row2252['upload_img'];
    $description2252 = $row2252['description'];

    //FOR ID 2253
    $sql2253 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2253";
    $stmt2253 = $conn->prepare($sql2253);
    $stmt2253->execute();
    $result2253 = $stmt2253->get_result();
    $row2253 = $result2253->fetch_assoc();
    $assetId2253 = $row2253['assetId'];
    $category2253 = $row2253['category'];
    $date2253 = $row2253['date'];
    $building2253 = $row2253['building'];
    $floor2253 = $row2253['floor'];
    $room2253 = $row2253['room'];
    $status2253 = $row2253['status'];
    $assignedName2253 = $row2253['assignedName'];
    $assignedBy2253 = $row2253['assignedBy'];
    $upload_img2253 = $row2253['upload_img'];
    $description2253 = $row2253['description'];

    //FOR ID 2254
    $sql2254 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2254";
    $stmt2254 = $conn->prepare($sql2254);
    $stmt2254->execute();
    $result2254 = $stmt2254->get_result();
    $row2254 = $result2254->fetch_assoc();
    $assetId2254 = $row2254['assetId'];
    $category2254 = $row2254['category'];
    $date2254 = $row2254['date'];
    $building2254 = $row2254['building'];
    $floor2254 = $row2254['floor'];
    $room2254 = $row2254['room'];
    $status2254 = $row2254['status'];
    $assignedName2254 = $row2254['assignedName'];
    $assignedBy2254 = $row2254['assignedBy'];
    $upload_img2254 = $row2254['upload_img'];
    $description2254 = $row2254['description'];

    //FOR ID 2255
    $sql2255 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2255";
    $stmt2255 = $conn->prepare($sql2255);
    $stmt2255->execute();
    $result2255 = $stmt2255->get_result();
    $row2255 = $result2255->fetch_assoc();
    $assetId2255 = $row2255['assetId'];
    $category2255 = $row2255['category'];
    $date2255 = $row2255['date'];
    $building2255 = $row2255['building'];
    $floor2255 = $row2255['floor'];
    $room2255 = $row2255['room'];
    $status2255 = $row2255['status'];
    $assignedName2255 = $row2255['assignedName'];
    $assignedBy2255 = $row2255['assignedBy'];
    $upload_img2255 = $row2255['upload_img'];
    $description2255 = $row2255['description'];

    //FOR ID 2256
    $sql2256 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2256";
    $stmt2256 = $conn->prepare($sql2256);
    $stmt2256->execute();
    $result2256 = $stmt2256->get_result();
    $row2256 = $result2256->fetch_assoc();
    $assetId2256 = $row2256['assetId'];
    $category2256 = $row2256['category'];
    $date2256 = $row2256['date'];
    $building2256 = $row2256['building'];
    $floor2256 = $row2256['floor'];
    $room2256 = $row2256['room'];
    $status2256 = $row2256['status'];
    $assignedName2256 = $row2256['assignedName'];
    $assignedBy2256 = $row2256['assignedBy'];
    $upload_img2256 = $row2256['upload_img'];
    $description2256 = $row2256['description'];

    //FOR ID 2257
    $sql2257 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2257";
    $stmt2257 = $conn->prepare($sql2257);
    $stmt2257->execute();
    $result2257 = $stmt2257->get_result();
    $row2257 = $result2257->fetch_assoc();
    $assetId2257 = $row2257['assetId'];
    $category2257 = $row2257['category'];
    $date2257 = $row2257['date'];
    $building2257 = $row2257['building'];
    $floor2257 = $row2257['floor'];
    $room2257 = $row2257['room'];
    $status2257 = $row2257['status'];
    $assignedName2257 = $row2257['assignedName'];
    $assignedBy2257 = $row2257['assignedBy'];
    $upload_img2257 = $row2257['upload_img'];
    $description2257 = $row2257['description'];

    //FOR ID 2258
    $sql2258 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2258";
    $stmt2258 = $conn->prepare($sql2258);
    $stmt2258->execute();
    $result2258 = $stmt2258->get_result();
    $row2258 = $result2258->fetch_assoc();
    $assetId2258 = $row2258['assetId'];
    $category2258 = $row2258['category'];
    $date2258 = $row2258['date'];
    $building2258 = $row2258['building'];
    $floor2258 = $row2258['floor'];
    $room2258 = $row2258['room'];
    $status2258 = $row2258['status'];
    $assignedName2258 = $row2258['assignedName'];
    $assignedBy2258 = $row2258['assignedBy'];
    $upload_img2258 = $row2258['upload_img'];
    $description2258 = $row2258['description'];

    //FOR ID 2259
    $sql2259 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2259";
    $stmt2259 = $conn->prepare($sql2259);
    $stmt2259->execute();
    $result2259 = $stmt2259->get_result();
    $row2259 = $result2259->fetch_assoc();
    $assetId2259 = $row2259['assetId'];
    $category2259 = $row2259['category'];
    $date2259 = $row2259['date'];
    $building2259 = $row2259['building'];
    $floor2259 = $row2259['floor'];
    $room2259 = $row2259['room'];
    $status2259 = $row2259['status'];
    $assignedName2259 = $row2259['assignedName'];
    $assignedBy2259 = $row2259['assignedBy'];
    $upload_img2259 = $row2259['upload_img'];
    $description2259 = $row2259['description'];

    //FOR ID 2260
    $sql2260 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2260";
    $stmt2260 = $conn->prepare($sql2260);
    $stmt2260->execute();
    $result2260 = $stmt2260->get_result();
    $row2260 = $result2260->fetch_assoc();
    $assetId2260 = $row2260['assetId'];
    $category2260 = $row2260['category'];
    $date2260 = $row2260['date'];
    $building2260 = $row2260['building'];
    $floor2260 = $row2260['floor'];
    $room2260 = $row2260['room'];
    $status2260 = $row2260['status'];
    $assignedName2260 = $row2260['assignedName'];
    $assignedBy2260 = $row2260['assignedBy'];
    $upload_img2260 = $row2260['upload_img'];
    $description2260 = $row2260['description'];

    //FOR ID 2261
    $sql2261 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2261";
    $stmt2261 = $conn->prepare($sql2261);
    $stmt2261->execute();
    $result2261 = $stmt2261->get_result();
    $row2261 = $result2261->fetch_assoc();
    $assetId2261 = $row2261['assetId'];
    $category2261 = $row2261['category'];
    $date2261 = $row2261['date'];
    $building2261 = $row2261['building'];
    $floor2261 = $row2261['floor'];
    $room2261 = $row2261['room'];
    $status2261 = $row2261['status'];
    $assignedName2261 = $row2261['assignedName'];
    $assignedBy2261 = $row2261['assignedBy'];
    $upload_img2261 = $row2261['upload_img'];
    $description2261 = $row2261['description'];

    //FOR ID 2262
    $sql2262 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2262";
    $stmt2262 = $conn->prepare($sql2262);
    $stmt2262->execute();
    $result2262 = $stmt2262->get_result();
    $row2262 = $result2262->fetch_assoc();
    $assetId2262 = $row2262['assetId'];
    $category2262 = $row2262['category'];
    $date2262 = $row2262['date'];
    $building2262 = $row2262['building'];
    $floor2262 = $row2262['floor'];
    $room2262 = $row2262['room'];
    $status2262 = $row2262['status'];
    $assignedName2262 = $row2262['assignedName'];
    $assignedBy2262 = $row2262['assignedBy'];
    $upload_img2262 = $row2262['upload_img'];
    $description2262 = $row2262['description'];

    //FOR ID 2263
    $sql2263 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2263";
    $stmt2263 = $conn->prepare($sql2263);
    $stmt2263->execute();
    $result2263 = $stmt2263->get_result();
    $row2263 = $result2263->fetch_assoc();
    $assetId2263 = $row2263['assetId'];
    $category2263 = $row2263['category'];
    $date2263 = $row2263['date'];
    $building2263 = $row2263['building'];
    $floor2263 = $row2263['floor'];
    $room2263 = $row2263['room'];
    $status2263 = $row2263['status'];
    $assignedName2263 = $row2263['assignedName'];
    $assignedBy2263 = $row2263['assignedBy'];
    $upload_img2263 = $row2263['upload_img'];
    $description2263 = $row2263['description'];

    //FOR ID 2264
    $sql2264 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2264";
    $stmt2264 = $conn->prepare($sql2264);
    $stmt2264->execute();
    $result2264 = $stmt2264->get_result();
    $row2264 = $result2264->fetch_assoc();
    $assetId2264 = $row2264['assetId'];
    $category2264 = $row2264['category'];
    $date2264 = $row2264['date'];
    $building2264 = $row2264['building'];
    $floor2264 = $row2264['floor'];
    $room2264 = $row2264['room'];
    $status2264 = $row2264['status'];
    $assignedName2264 = $row2264['assignedName'];
    $assignedBy2264 = $row2264['assignedBy'];
    $upload_img2264 = $row2264['upload_img'];
    $description2264 = $row2264['description'];

    //FOR ID 2265
    $sql2265 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2265";
    $stmt2265 = $conn->prepare($sql2265);
    $stmt2265->execute();
    $result2265 = $stmt2265->get_result();
    $row2265 = $result2265->fetch_assoc();
    $assetId2265 = $row2265['assetId'];
    $category2265 = $row2265['category'];
    $date2265 = $row2265['date'];
    $building2265 = $row2265['building'];
    $floor2265 = $row2265['floor'];
    $room2265 = $row2265['room'];
    $status2265 = $row2265['status'];
    $assignedName2265 = $row2265['assignedName'];
    $assignedBy2265 = $row2265['assignedBy'];
    $upload_img2265 = $row2265['upload_img'];
    $description2265 = $row2265['description'];

    //FOR ID 2266
    $sql2266 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2266";
    $stmt2266 = $conn->prepare($sql2266);
    $stmt2266->execute();
    $result2266 = $stmt2266->get_result();
    $row2266 = $result2266->fetch_assoc();
    $assetId2266 = $row2266['assetId'];
    $category2266 = $row2266['category'];
    $date2266 = $row2266['date'];
    $building2266 = $row2266['building'];
    $floor2266 = $row2266['floor'];
    $room2266 = $row2266['room'];
    $status2266 = $row2266['status'];
    $assignedName2266 = $row2266['assignedName'];
    $assignedBy2266 = $row2266['assignedBy'];
    $upload_img2266 = $row2266['upload_img'];
    $description2266 = $row2266['description'];

    //FOR ID 2267
    $sql2267 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2267";
    $stmt2267 = $conn->prepare($sql2267);
    $stmt2267->execute();
    $result2267 = $stmt2267->get_result();
    $row2267 = $result2267->fetch_assoc();
    $assetId2267 = $row2267['assetId'];
    $category2267 = $row2267['category'];
    $date2267 = $row2267['date'];
    $building2267 = $row2267['building'];
    $floor2267 = $row2267['floor'];
    $room2267 = $row2267['room'];
    $status2267 = $row2267['status'];
    $assignedName2267 = $row2267['assignedName'];
    $assignedBy2267 = $row2267['assignedBy'];
    $upload_img2267 = $row2267['upload_img'];
    $description2267 = $row2267['description'];

    //FOR ID 2268
    $sql2268 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2268";
    $stmt2268 = $conn->prepare($sql2268);
    $stmt2268->execute();
    $result2268 = $stmt2268->get_result();
    $row2268 = $result2268->fetch_assoc();
    $assetId2268 = $row2268['assetId'];
    $category2268 = $row2268['category'];
    $date2268 = $row2268['date'];
    $building2268 = $row2268['building'];
    $floor2268 = $row2268['floor'];
    $room2268 = $row2268['room'];
    $status2268 = $row2268['status'];
    $assignedName2268 = $row2268['assignedName'];
    $assignedBy2268 = $row2268['assignedBy'];
    $upload_img2268 = $row2268['upload_img'];
    $description2268 = $row2268['description'];

    //FOR ID 2269
    $sql2269 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2269";
    $stmt2269 = $conn->prepare($sql2269);
    $stmt2269->execute();
    $result2269 = $stmt2269->get_result();
    $row2269 = $result2269->fetch_assoc();
    $assetId2269 = $row2269['assetId'];
    $category2269 = $row2269['category'];
    $date2269 = $row2269['date'];
    $building2269 = $row2269['building'];
    $floor2269 = $row2269['floor'];
    $room2269 = $row2269['room'];
    $status2269 = $row2269['status'];
    $assignedName2269 = $row2269['assignedName'];
    $assignedBy2269 = $row2269['assignedBy'];
    $upload_img2269 = $row2269['upload_img'];
    $description2269 = $row2269['description'];

    //FOR ID 2270
    $sql2270 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2270";
    $stmt2270 = $conn->prepare($sql2270);
    $stmt2270->execute();
    $result2270 = $stmt2270->get_result();
    $row2270 = $result2270->fetch_assoc();
    $assetId2270 = $row2270['assetId'];
    $category2270 = $row2270['category'];
    $date2270 = $row2270['date'];
    $building2270 = $row2270['building'];
    $floor2270 = $row2270['floor'];
    $room2270 = $row2270['room'];
    $status2270 = $row2270['status'];
    $assignedName2270 = $row2270['assignedName'];
    $assignedBy2270 = $row2270['assignedBy'];
    $upload_img2270 = $row2270['upload_img'];
    $description2270 = $row2270['description'];

    //FOR ID 2271
    $sql2271 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2271";
    $stmt2271 = $conn->prepare($sql2271);
    $stmt2271->execute();
    $result2271 = $stmt2271->get_result();
    $row2271 = $result2271->fetch_assoc();
    $assetId2271 = $row2271['assetId'];
    $category2271 = $row2271['category'];
    $date2271 = $row2271['date'];
    $building2271 = $row2271['building'];
    $floor2271 = $row2271['floor'];
    $room2271 = $row2271['room'];
    $status2271 = $row2271['status'];
    $assignedName2271 = $row2271['assignedName'];
    $assignedBy2271 = $row2271['assignedBy'];
    $upload_img2271 = $row2271['upload_img'];
    $description2271 = $row2271['description'];

    //FOR ID 2272
    $sql2272 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2272";
    $stmt2272 = $conn->prepare($sql2272);
    $stmt2272->execute();
    $result2272 = $stmt2272->get_result();
    $row2272 = $result2272->fetch_assoc();
    $assetId2272 = $row2272['assetId'];
    $category2272 = $row2272['category'];
    $date2272 = $row2272['date'];
    $building2272 = $row2272['building'];
    $floor2272 = $row2272['floor'];
    $room2272 = $row2272['room'];
    $status2272 = $row2272['status'];
    $assignedName2272 = $row2272['assignedName'];
    $assignedBy2272 = $row2272['assignedBy'];
    $upload_img2272 = $row2272['upload_img'];
    $description2272 = $row2272['description'];

    //FOR ID 2273
    $sql2273 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2273";
    $stmt2273 = $conn->prepare($sql2273);
    $stmt2273->execute();
    $result2273 = $stmt2273->get_result();
    $row2273 = $result2273->fetch_assoc();
    $assetId2273 = $row2273['assetId'];
    $category2273 = $row2273['category'];
    $date2273 = $row2273['date'];
    $building2273 = $row2273['building'];
    $floor2273 = $row2273['floor'];
    $room2273 = $row2273['room'];
    $status2273 = $row2273['status'];
    $assignedName2273 = $row2273['assignedName'];
    $assignedBy2273 = $row2273['assignedBy'];
    $upload_img2273 = $row2273['upload_img'];
    $description2273 = $row2273['description'];

    //FOR ID 2274
    $sql2274 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2274";
    $stmt2274 = $conn->prepare($sql2274);
    $stmt2274->execute();
    $result2274 = $stmt2274->get_result();
    $row2274 = $result2274->fetch_assoc();
    $assetId2274 = $row2274['assetId'];
    $category2274 = $row2274['category'];
    $date2274 = $row2274['date'];
    $building2274 = $row2274['building'];
    $floor2274 = $row2274['floor'];
    $room2274 = $row2274['room'];
    $status2274 = $row2274['status'];
    $assignedName2274 = $row2274['assignedName'];
    $assignedBy2274 = $row2274['assignedBy'];
    $upload_img2274 = $row2274['upload_img'];
    $description2274 = $row2274['description'];

    //FOR ID 2275
    $sql2275 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2275";
    $stmt2275 = $conn->prepare($sql2275);
    $stmt2275->execute();
    $result2275 = $stmt2275->get_result();
    $row2275 = $result2275->fetch_assoc();
    $assetId2275 = $row2275['assetId'];
    $category2275 = $row2275['category'];
    $date2275 = $row2275['date'];
    $building2275 = $row2275['building'];
    $floor2275 = $row2275['floor'];
    $room2275 = $row2275['room'];
    $status2275 = $row2275['status'];
    $assignedName2275 = $row2275['assignedName'];
    $assignedBy2275 = $row2275['assignedBy'];
    $upload_img2275 = $row2275['upload_img'];
    $description2275 = $row2275['description'];

    //FOR ID 2276
    $sql2276 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2276";
    $stmt2276 = $conn->prepare($sql2276);
    $stmt2276->execute();
    $result2276 = $stmt2276->get_result();
    $row2276 = $result2276->fetch_assoc();
    $assetId2276 = $row2276['assetId'];
    $category2276 = $row2276['category'];
    $date2276 = $row2276['date'];
    $building2276 = $row2276['building'];
    $floor2276 = $row2276['floor'];
    $room2276 = $row2276['room'];
    $status2276 = $row2276['status'];
    $assignedName2276 = $row2276['assignedName'];
    $assignedBy2276 = $row2276['assignedBy'];
    $upload_img2276 = $row2276['upload_img'];
    $description2276 = $row2276['description'];

    //FOR ID 2277
    $sql2277 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2277";
    $stmt2277 = $conn->prepare($sql2277);
    $stmt2277->execute();
    $result2277 = $stmt2277->get_result();
    $row2277 = $result2277->fetch_assoc();
    $assetId2277 = $row2277['assetId'];
    $category2277 = $row2277['category'];
    $date2277 = $row2277['date'];
    $building2277 = $row2277['building'];
    $floor2277 = $row2277['floor'];
    $room2277 = $row2277['room'];
    $status2277 = $row2277['status'];
    $assignedName2277 = $row2277['assignedName'];
    $assignedBy2277 = $row2277['assignedBy'];
    $upload_img2277 = $row2277['upload_img'];
    $description2277 = $row2277['description'];

    //FOR ID 2278
    $sql2278 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2278";
    $stmt2278 = $conn->prepare($sql2278);
    $stmt2278->execute();
    $result2278 = $stmt2278->get_result();
    $row2278 = $result2278->fetch_assoc();
    $assetId2278 = $row2278['assetId'];
    $category2278 = $row2278['category'];
    $date2278 = $row2278['date'];
    $building2278 = $row2278['building'];
    $floor2278 = $row2278['floor'];
    $room2278 = $row2278['room'];
    $status2278 = $row2278['status'];
    $assignedName2278 = $row2278['assignedName'];
    $assignedBy2278 = $row2278['assignedBy'];
    $upload_img2278 = $row2278['upload_img'];
    $description2278 = $row2278['description'];

    //FOR ID 2279
    $sql2279 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2279";
    $stmt2279 = $conn->prepare($sql2279);
    $stmt2279->execute();
    $result2279 = $stmt2279->get_result();
    $row2279 = $result2279->fetch_assoc();
    $assetId2279 = $row2279['assetId'];
    $category2279 = $row2279['category'];
    $date2279 = $row2279['date'];
    $building2279 = $row2279['building'];
    $floor2279 = $row2279['floor'];
    $room2279 = $row2279['room'];
    $status2279 = $row2279['status'];
    $assignedName2279 = $row2279['assignedName'];
    $assignedBy2279 = $row2279['assignedBy'];
    $upload_img2279 = $row2279['upload_img'];
    $description2279 = $row2279['description'];

    //FOR ID 2280
    $sql2280 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2280";
    $stmt2280 = $conn->prepare($sql2280);
    $stmt2280->execute();
    $result2280 = $stmt2280->get_result();
    $row2280 = $result2280->fetch_assoc();
    $assetId2280 = $row2280['assetId'];
    $category2280 = $row2280['category'];
    $date2280 = $row2280['date'];
    $building2280 = $row2280['building'];
    $floor2280 = $row2280['floor'];
    $room2280 = $row2280['room'];
    $status2280 = $row2280['status'];
    $assignedName2280 = $row2280['assignedName'];
    $assignedBy2280 = $row2280['assignedBy'];
    $upload_img2280 = $row2280['upload_img'];
    $description2280 = $row2280['description'];

    //FOR ID 2281
    $sql2281 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2281";
    $stmt2281 = $conn->prepare($sql2281);
    $stmt2281->execute();
    $result2281 = $stmt2281->get_result();
    $row2281 = $result2281->fetch_assoc();
    $assetId2281 = $row2281['assetId'];
    $category2281 = $row2281['category'];
    $date2281 = $row2281['date'];
    $building2281 = $row2281['building'];
    $floor2281 = $row2281['floor'];
    $room2281 = $row2281['room'];
    $status2281 = $row2281['status'];
    $assignedName2281 = $row2281['assignedName'];
    $assignedBy2281 = $row2281['assignedBy'];
    $upload_img2281 = $row2281['upload_img'];
    $description2281 = $row2281['description'];

    //FOR ID 2282
    $sql2282 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2282";
    $stmt2282 = $conn->prepare($sql2282);
    $stmt2282->execute();
    $result2282 = $stmt2282->get_result();
    $row2282 = $result2282->fetch_assoc();
    $assetId2282 = $row2282['assetId'];
    $category2282 = $row2282['category'];
    $date2282 = $row2282['date'];
    $building2282 = $row2282['building'];
    $floor2282 = $row2282['floor'];
    $room2282 = $row2282['room'];
    $status2282 = $row2282['status'];
    $assignedName2282 = $row2282['assignedName'];
    $assignedBy2282 = $row2282['assignedBy'];
    $upload_img2282 = $row2282['upload_img'];
    $description2282 = $row2282['description'];

    //FOR ID 2283
    $sql2283 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2283";
    $stmt2283 = $conn->prepare($sql2283);
    $stmt2283->execute();
    $result2283 = $stmt2283->get_result();
    $row2283 = $result2283->fetch_assoc();
    $assetId2283 = $row2283['assetId'];
    $category2283 = $row2283['category'];
    $date2283 = $row2283['date'];
    $building2283 = $row2283['building'];
    $floor2283 = $row2283['floor'];
    $room2283 = $row2283['room'];
    $status2283 = $row2283['status'];
    $assignedName2283 = $row2283['assignedName'];
    $assignedBy2283 = $row2283['assignedBy'];
    $upload_img2283 = $row2283['upload_img'];
    $description2283 = $row2283['description'];

    //FOR ID 2284
    $sql2284 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2284";
    $stmt2284 = $conn->prepare($sql2284);
    $stmt2284->execute();
    $result2284 = $stmt2284->get_result();
    $row2284 = $result2284->fetch_assoc();
    $assetId2284 = $row2284['assetId'];
    $category2284 = $row2284['category'];
    $date2284 = $row2284['date'];
    $building2284 = $row2284['building'];
    $floor2284 = $row2284['floor'];
    $room2284 = $row2284['room'];
    $status2284 = $row2284['status'];
    $assignedName2284 = $row2284['assignedName'];
    $assignedBy2284 = $row2284['assignedBy'];
    $upload_img2284 = $row2284['upload_img'];
    $description2284 = $row2284['description'];

    //FOR ID 2285
    $sql2285 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2285";
    $stmt2285 = $conn->prepare($sql2285);
    $stmt2285->execute();
    $result2285 = $stmt2285->get_result();
    $row2285 = $result2285->fetch_assoc();
    $assetId2285 = $row2285['assetId'];
    $category2285 = $row2285['category'];
    $date2285 = $row2285['date'];
    $building2285 = $row2285['building'];
    $floor2285 = $row2285['floor'];
    $room2285 = $row2285['room'];
    $status2285 = $row2285['status'];
    $assignedName2285 = $row2285['assignedName'];
    $assignedBy2285 = $row2285['assignedBy'];
    $upload_img2285 = $row2285['upload_img'];
    $description2285 = $row2285['description'];

    //FOR ID 2286
    $sql2286 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2286";
    $stmt2286 = $conn->prepare($sql2286);
    $stmt2286->execute();
    $result2286 = $stmt2286->get_result();
    $row2286 = $result2286->fetch_assoc();
    $assetId2286 = $row2286['assetId'];
    $category2286 = $row2286['category'];
    $date2286 = $row2286['date'];
    $building2286 = $row2286['building'];
    $floor2286 = $row2286['floor'];
    $room2286 = $row2286['room'];
    $status2286 = $row2286['status'];
    $assignedName2286 = $row2286['assignedName'];
    $assignedBy2286 = $row2286['assignedBy'];
    $upload_img2286 = $row2286['upload_img'];
    $description2286 = $row2286['description'];

    //FOR ID 2287
    $sql2287 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2287";
    $stmt2287 = $conn->prepare($sql2287);
    $stmt2287->execute();
    $result2287 = $stmt2287->get_result();
    $row2287 = $result2287->fetch_assoc();
    $assetId2287 = $row2287['assetId'];
    $category2287 = $row2287['category'];
    $date2287 = $row2287['date'];
    $building2287 = $row2287['building'];
    $floor2287 = $row2287['floor'];
    $room2287 = $row2287['room'];
    $status2287 = $row2287['status'];
    $assignedName2287 = $row2287['assignedName'];
    $assignedBy2287 = $row2287['assignedBy'];
    $upload_img2287 = $row2287['upload_img'];
    $description2287 = $row2287['description'];

    //FOR ID 2288
    $sql2288 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2288";
    $stmt2288 = $conn->prepare($sql2288);
    $stmt2288->execute();
    $result2288 = $stmt2288->get_result();
    $row2288 = $result2288->fetch_assoc();
    $assetId2288 = $row2288['assetId'];
    $category2288 = $row2288['category'];
    $date2288 = $row2288['date'];
    $building2288 = $row2288['building'];
    $floor2288 = $row2288['floor'];
    $room2288 = $row2288['room'];
    $status2288 = $row2288['status'];
    $assignedName2288 = $row2288['assignedName'];
    $assignedBy2288 = $row2288['assignedBy'];
    $upload_img2288 = $row2288['upload_img'];
    $description2288 = $row2288['description'];

    //FOR ID 2289
    $sql2289 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2289";
    $stmt2289 = $conn->prepare($sql2289);
    $stmt2289->execute();
    $result2289 = $stmt2289->get_result();
    $row2289 = $result2289->fetch_assoc();
    $assetId2289 = $row2289['assetId'];
    $category2289 = $row2289['category'];
    $date2289 = $row2289['date'];
    $building2289 = $row2289['building'];
    $floor2289 = $row2289['floor'];
    $room2289 = $row2289['room'];
    $status2289 = $row2289['status'];
    $assignedName2289 = $row2289['assignedName'];
    $assignedBy2289 = $row2289['assignedBy'];
    $upload_img2289 = $row2289['upload_img'];
    $description2289 = $row2289['description'];

    //FOR ID 2290
    $sql2290 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 2290";
    $stmt2290 = $conn->prepare($sql2290);
    $stmt2290->execute();
    $result2290 = $stmt2290->get_result();
    $row2290 = $result2290->fetch_assoc();
    $assetId2290 = $row2290['assetId'];
    $category2290 = $row2290['category'];
    $date2290 = $row2290['date'];
    $building2290 = $row2290['building'];
    $floor2290 = $row2290['floor'];
    $room2290 = $row2290['room'];
    $status2290 = $row2290['status'];
    $assignedName2290 = $row2290['assignedName'];
    $assignedBy2290 = $row2290['assignedBy'];
    $upload_img2290 = $row2290['upload_img'];
    $description2290 = $row2290['description'];


    //FOR ID 2249
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2249'])) {
        // Get form data
        $assetId2249 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2249 = $_POST['status']; // Get the status from the form
        $description2249 = $_POST['description']; // Get the description from the form
        $room2249 = $_POST['room']; // Get the room from the form
        $assignedBy2249 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2249 = $status2249 === 'Need Repair' ? '' : $assignedName2249;

        // Prepare SQL query to update the asset
        $sql2249 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2249 = $conn->prepare($sql2249);
        $stmt2249->bind_param('sssssi', $status2249, $assignedName2249, $assignedBy2249, $description2249, $room2249, $assetId2249);

        if ($stmt2249->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2249 to $status2249.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2249->close();
    }

    //FOR ID 2250
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2250'])) {
        // Get form data
        $assetId2250 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2250 = $_POST['status']; // Get the status from the form
        $description2250 = $_POST['description']; // Get the description from the form
        $room2250 = $_POST['room']; // Get the room from the form
        $assignedBy2250 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2250 = $status2250 === 'Need Repair' ? '' : $assignedName2250;

        // Prepare SQL query to update the asset
        $sql2250 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2250 = $conn->prepare($sql2250);
        $stmt2250->bind_param('sssssi', $status2250, $assignedName2250, $assignedBy2250, $description2250, $room2250, $assetId2250);

        if ($stmt2250->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2250 to $status2250.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2250->close();
    }

    //FOR ID 2251
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2251'])) {
        // Get form data
        $assetId2251 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2251 = $_POST['status']; // Get the status from the form
        $description2251 = $_POST['description']; // Get the description from the form
        $room2251 = $_POST['room']; // Get the room from the form
        $assignedBy2251 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2251 = $status2251 === 'Need Repair' ? '' : $assignedName2251;

        // Prepare SQL query to update the asset
        $sql2251 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2251 = $conn->prepare($sql2251);
        $stmt2251->bind_param('sssssi', $status2251, $assignedName2251, $assignedBy2251, $description2251, $room2251, $assetId2251);

        if ($stmt2251->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2251 to $status2251.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2251->close();
    }

    //FOR ID 2252
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2252'])) {
        // Get form data
        $assetId2252 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2252 = $_POST['status']; // Get the status from the form
        $description2252 = $_POST['description']; // Get the description from the form
        $room2252 = $_POST['room']; // Get the room from the form
        $assignedBy2252 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2252 = $status2252 === 'Need Repair' ? '' : $assignedName2252;

        // Prepare SQL query to update the asset
        $sql2252 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2252 = $conn->prepare($sql2252);
        $stmt2252->bind_param('sssssi', $status2252, $assignedName2252, $assignedBy2252, $description2252, $room2252, $assetId2252);

        if ($stmt2252->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2252 to $status2252.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2252->close();
    }

    //FOR ID 2253
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2253'])) {
        // Get form data
        $assetId2253 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2253 = $_POST['status']; // Get the status from the form
        $description2253 = $_POST['description']; // Get the description from the form
        $room2253 = $_POST['room']; // Get the room from the form
        $assignedBy2253 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2253 = $status2253 === 'Need Repair' ? '' : $assignedName2253;

        // Prepare SQL query to update the asset
        $sql2253 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2253 = $conn->prepare($sql2253);
        $stmt2253->bind_param('sssssi', $status2253, $assignedName2253, $assignedBy2253, $description2253, $room2253, $assetId2253);

        if ($stmt2253->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2253 to $status2253.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2253->close();
    }

    //FOR ID 2254
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2254'])) {
        // Get form data
        $assetId2254 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2254 = $_POST['status']; // Get the status from the form
        $description2254 = $_POST['description']; // Get the description from the form
        $room2254 = $_POST['room']; // Get the room from the form
        $assignedBy2254 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2254 = $status2254 === 'Need Repair' ? '' : $assignedName2254;

        // Prepare SQL query to update the asset
        $sql2254 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2254 = $conn->prepare($sql2254);
        $stmt2254->bind_param('sssssi', $status2254, $assignedName2254, $assignedBy2254, $description2254, $room2254, $assetId2254);

        if ($stmt2254->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2254 to $status2254.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2254->close();
    }

    //FOR ID 2255
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2255'])) {
        // Get form data
        $assetId2255 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2255 = $_POST['status']; // Get the status from the form
        $description2255 = $_POST['description']; // Get the description from the form
        $room2255 = $_POST['room']; // Get the room from the form
        $assignedBy2255 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2255 = $status2255 === 'Need Repair' ? '' : $assignedName2255;

        // Prepare SQL query to update the asset
        $sql2255 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2255 = $conn->prepare($sql2255);
        $stmt2255->bind_param('sssssi', $status2255, $assignedName2255, $assignedBy2255, $description2255, $room2255, $assetId2255);

        if ($stmt2255->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2255 to $status2255.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2255->close();
    }

    //FOR ID 2256
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2256'])) {
        // Get form data
        $assetId2256 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2256 = $_POST['status']; // Get the status from the form
        $description2256 = $_POST['description']; // Get the description from the form
        $room2256 = $_POST['room']; // Get the room from the form
        $assignedBy2256 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2256 = $status2256 === 'Need Repair' ? '' : $assignedName2256;

        // Prepare SQL query to update the asset
        $sql2256 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2256 = $conn->prepare($sql2256);
        $stmt2256->bind_param('sssssi', $status2256, $assignedName2256, $assignedBy2256, $description2256, $room2256, $assetId2256);

        if ($stmt2256->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2256 to $status2256.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2256->close();
    }

    //FOR ID 2257
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2257'])) {
        // Get form data
        $assetId2257 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2257 = $_POST['status']; // Get the status from the form
        $description2257 = $_POST['description']; // Get the description from the form
        $room2257 = $_POST['room']; // Get the room from the form
        $assignedBy2257 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2257 = $status2257 === 'Need Repair' ? '' : $assignedName2257;

        // Prepare SQL query to update the asset
        $sql2257 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2257 = $conn->prepare($sql2257);
        $stmt2257->bind_param('sssssi', $status2257, $assignedName2257, $assignedBy2257, $description2257, $room2257, $assetId2257);

        if ($stmt2257->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2257 to $status2257.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2257->close();
    }

    //FOR ID 2258
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2258'])) {
        // Get form data
        $assetId2258 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2258 = $_POST['status']; // Get the status from the form
        $description2258 = $_POST['description']; // Get the description from the form
        $room2258 = $_POST['room']; // Get the room from the form
        $assignedBy2258 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2258 = $status2258 === 'Need Repair' ? '' : $assignedName2258;

        // Prepare SQL query to update the asset
        $sql2258 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2258 = $conn->prepare($sql2258);
        $stmt2258->bind_param('sssssi', $status2258, $assignedName2258, $assignedBy2258, $description2258, $room2258, $assetId2258);

        if ($stmt2258->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2258 to $status2258.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2258->close();
    }

    //FOR ID 2259
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2259'])) {
        // Get form data
        $assetId2259 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2259 = $_POST['status']; // Get the status from the form
        $description2259 = $_POST['description']; // Get the description from the form
        $room2259 = $_POST['room']; // Get the room from the form
        $assignedBy2259 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2259 = $status2259 === 'Need Repair' ? '' : $assignedName2259;

        // Prepare SQL query to update the asset
        $sql2259 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2259 = $conn->prepare($sql2259);
        $stmt2259->bind_param('sssssi', $status2259, $assignedName2259, $assignedBy2259, $description2259, $room2259, $assetId2259);

        if ($stmt2259->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2259 to $status2259.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2259->close();
    }

    //FOR ID 2260
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2260'])) {
        // Get form data
        $assetId2260 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2260 = $_POST['status']; // Get the status from the form
        $description2260 = $_POST['description']; // Get the description from the form
        $room2260 = $_POST['room']; // Get the room from the form
        $assignedBy2260 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2260 = $status2260 === 'Need Repair' ? '' : $assignedName2260;

        // Prepare SQL query to update the asset
        $sql2260 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2260 = $conn->prepare($sql2260);
        $stmt2260->bind_param('sssssi', $status2260, $assignedName2260, $assignedBy2260, $description2260, $room2260, $assetId2260);

        if ($stmt2260->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2260 to $status2260.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2260->close();
    }

    //FOR ID 2261
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2261'])) {
        // Get form data
        $assetId2261 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2261 = $_POST['status']; // Get the status from the form
        $description2261 = $_POST['description']; // Get the description from the form
        $room2261 = $_POST['room']; // Get the room from the form
        $assignedBy2261 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2261 = $status2261 === 'Need Repair' ? '' : $assignedName2261;

        // Prepare SQL query to update the asset
        $sql2261 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2261 = $conn->prepare($sql2261);
        $stmt2261->bind_param('sssssi', $status2261, $assignedName2261, $assignedBy2261, $description2261, $room2261, $assetId2261);

        if ($stmt2261->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2261 to $status2261.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2261->close();
    }

    //FOR ID 2262
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2262'])) {
        // Get form data
        $assetId2262 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2262 = $_POST['status']; // Get the status from the form
        $description2262 = $_POST['description']; // Get the description from the form
        $room2262 = $_POST['room']; // Get the room from the form
        $assignedBy2262 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2262 = $status2262 === 'Need Repair' ? '' : $assignedName2262;

        // Prepare SQL query to update the asset
        $sql2262 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2262 = $conn->prepare($sql2262);
        $stmt2262->bind_param('sssssi', $status2262, $assignedName2262, $assignedBy2262, $description2262, $room2262, $assetId2262);

        if ($stmt2262->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2262 to $status2262.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2262->close();
    }

    //FOR ID 2263
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2263'])) {
        // Get form data
        $assetId2263 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2263 = $_POST['status']; // Get the status from the form
        $description2263 = $_POST['description']; // Get the description from the form
        $room2263 = $_POST['room']; // Get the room from the form
        $assignedBy2263 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2263 = $status2263 === 'Need Repair' ? '' : $assignedName2263;

        // Prepare SQL query to update the asset
        $sql2263 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2263 = $conn->prepare($sql2263);
        $stmt2263->bind_param('sssssi', $status2263, $assignedName2263, $assignedBy2263, $description2263, $room2263, $assetId2263);

        if ($stmt2263->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2263 to $status2263.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2263->close();
    }

    //FOR ID 2264
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2264'])) {
        // Get form data
        $assetId2264 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2264 = $_POST['status']; // Get the status from the form
        $description2264 = $_POST['description']; // Get the description from the form
        $room2264 = $_POST['room']; // Get the room from the form
        $assignedBy2264 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2264 = $status2264 === 'Need Repair' ? '' : $assignedName2264;

        // Prepare SQL query to update the asset
        $sql2264 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2264 = $conn->prepare($sql2264);
        $stmt2264->bind_param('sssssi', $status2264, $assignedName2264, $assignedBy2264, $description2264, $room2264, $assetId2264);

        if ($stmt2264->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2264 to $status2264.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2264->close();
    }

    //FOR ID 2265
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2265'])) {
        // Get form data
        $assetId2265 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2265 = $_POST['status']; // Get the status from the form
        $description2265 = $_POST['description']; // Get the description from the form
        $room2265 = $_POST['room']; // Get the room from the form
        $assignedBy2265 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2265 = $status2265 === 'Need Repair' ? '' : $assignedName2265;

        // Prepare SQL query to update the asset
        $sql2265 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2265 = $conn->prepare($sql2265);
        $stmt2265->bind_param('sssssi', $status2265, $assignedName2265, $assignedBy2265, $description2265, $room2265, $assetId2265);

        if ($stmt2265->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2265 to $status2265.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2265->close();
    }

    //FOR ID 2266
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2266'])) {
        // Get form data
        $assetId2266 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2266 = $_POST['status']; // Get the status from the form
        $description2266 = $_POST['description']; // Get the description from the form
        $room2266 = $_POST['room']; // Get the room from the form
        $assignedBy2266 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2266 = $status2266 === 'Need Repair' ? '' : $assignedName2266;

        // Prepare SQL query to update the asset
        $sql2266 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2266 = $conn->prepare($sql2266);
        $stmt2266->bind_param('sssssi', $status2266, $assignedName2266, $assignedBy2266, $description2266, $room2266, $assetId2266);

        if ($stmt2266->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2266 to $status2266.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2266->close();
    }

    //FOR ID 2267
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2267'])) {
        // Get form data
        $assetId2267 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2267 = $_POST['status']; // Get the status from the form
        $description2267 = $_POST['description']; // Get the description from the form
        $room2267 = $_POST['room']; // Get the room from the form
        $assignedBy2267 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2267 = $status2267 === 'Need Repair' ? '' : $assignedName2267;

        // Prepare SQL query to update the asset
        $sql2267 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2267 = $conn->prepare($sql2267);
        $stmt2267->bind_param('sssssi', $status2267, $assignedName2267, $assignedBy2267, $description2267, $room2267, $assetId2267);

        if ($stmt2267->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2267 to $status2267.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2267->close();
    }

    //FOR ID 2268
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2268'])) {
        // Get form data
        $assetId2268 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2268 = $_POST['status']; // Get the status from the form
        $description2268 = $_POST['description']; // Get the description from the form
        $room2268 = $_POST['room']; // Get the room from the form
        $assignedBy2268 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2268 = $status2268 === 'Need Repair' ? '' : $assignedName2268;

        // Prepare SQL query to update the asset
        $sql2268 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2268 = $conn->prepare($sql2268);
        $stmt2268->bind_param('sssssi', $status2268, $assignedName2268, $assignedBy2268, $description2268, $room2268, $assetId2268);

        if ($stmt2268->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2268 to $status2268.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2268->close();
    }

    //FOR ID 2269
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2269'])) {
        // Get form data
        $assetId2269 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2269 = $_POST['status']; // Get the status from the form
        $description2269 = $_POST['description']; // Get the description from the form
        $room2269 = $_POST['room']; // Get the room from the form
        $assignedBy2269 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2269 = $status2269 === 'Need Repair' ? '' : $assignedName2269;

        // Prepare SQL query to update the asset
        $sql2269 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2269 = $conn->prepare($sql2269);
        $stmt2269->bind_param('sssssi', $status2269, $assignedName2269, $assignedBy2269, $description2269, $room2269, $assetId2269);

        if ($stmt2269->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2269 to $status2269.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2269->close();
    }

    //FOR ID 2270
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2270'])) {
        // Get form data
        $assetId2270 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2270 = $_POST['status']; // Get the status from the form
        $description2270 = $_POST['description']; // Get the description from the form
        $room2270 = $_POST['room']; // Get the room from the form
        $assignedBy2270 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2270 = $status2270 === 'Need Repair' ? '' : $assignedName2270;

        // Prepare SQL query to update the asset
        $sql2270 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2270 = $conn->prepare($sql2270);
        $stmt2270->bind_param('sssssi', $status2270, $assignedName2270, $assignedBy2270, $description2270, $room2270, $assetId2270);

        if ($stmt2270->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2270 to $status2270.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2270->close();
    }

    //FOR ID 2271
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2271'])) {
        // Get form data
        $assetId2271 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2271 = $_POST['status']; // Get the status from the form
        $description2271 = $_POST['description']; // Get the description from the form
        $room2271 = $_POST['room']; // Get the room from the form
        $assignedBy2271 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2271 = $status2271 === 'Need Repair' ? '' : $assignedName2271;

        // Prepare SQL query to update the asset
        $sql2271 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2271 = $conn->prepare($sql2271);
        $stmt2271->bind_param('sssssi', $status2271, $assignedName2271, $assignedBy2271, $description2271, $room2271, $assetId2271);

        if ($stmt2271->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2271 to $status2271.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2271->close();
    }

    //FOR ID 2272
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2272'])) {
        // Get form data
        $assetId2272 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2272 = $_POST['status']; // Get the status from the form
        $description2272 = $_POST['description']; // Get the description from the form
        $room2272 = $_POST['room']; // Get the room from the form
        $assignedBy2272 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2272 = $status2272 === 'Need Repair' ? '' : $assignedName2272;

        // Prepare SQL query to update the asset
        $sql2272 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2272 = $conn->prepare($sql2272);
        $stmt2272->bind_param('sssssi', $status2272, $assignedName2272, $assignedBy2272, $description2272, $room2272, $assetId2272);

        if ($stmt2272->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2272 to $status2272.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2272->close();
    }

    //FOR ID 2273
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2273'])) {
        // Get form data
        $assetId2273 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2273 = $_POST['status']; // Get the status from the form
        $description2273 = $_POST['description']; // Get the description from the form
        $room2273 = $_POST['room']; // Get the room from the form
        $assignedBy2273 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2273 = $status2273 === 'Need Repair' ? '' : $assignedName2273;

        // Prepare SQL query to update the asset
        $sql2273 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2273 = $conn->prepare($sql2273);
        $stmt2273->bind_param('sssssi', $status2273, $assignedName2273, $assignedBy2273, $description2273, $room2273, $assetId2273);

        if ($stmt2273->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2273 to $status2273.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2273->close();
    }

    //FOR ID 2274
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2274'])) {
        // Get form data
        $assetId2274 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2274 = $_POST['status']; // Get the status from the form
        $description2274 = $_POST['description']; // Get the description from the form
        $room2274 = $_POST['room']; // Get the room from the form
        $assignedBy2274 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2274 = $status2274 === 'Need Repair' ? '' : $assignedName2274;

        // Prepare SQL query to update the asset
        $sql2274 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2274 = $conn->prepare($sql2274);
        $stmt2274->bind_param('sssssi', $status2274, $assignedName2274, $assignedBy2274, $description2274, $room2274, $assetId2274);

        if ($stmt2274->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2274 to $status2274.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2274->close();
    }

    //FOR ID 2275
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2275'])) {
        // Get form data
        $assetId2275 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2275 = $_POST['status']; // Get the status from the form
        $description2275 = $_POST['description']; // Get the description from the form
        $room2275 = $_POST['room']; // Get the room from the form
        $assignedBy2275 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2275 = $status2275 === 'Need Repair' ? '' : $assignedName2275;

        // Prepare SQL query to update the asset
        $sql2275 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2275 = $conn->prepare($sql2275);
        $stmt2275->bind_param('sssssi', $status2275, $assignedName2275, $assignedBy2275, $description2275, $room2275, $assetId2275);

        if ($stmt2275->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2275 to $status2275.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2275->close();
    }

    //FOR ID 2276
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2276'])) {
        // Get form data
        $assetId2276 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2276 = $_POST['status']; // Get the status from the form
        $description2276 = $_POST['description']; // Get the description from the form
        $room2276 = $_POST['room']; // Get the room from the form
        $assignedBy2276 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2276 = $status2276 === 'Need Repair' ? '' : $assignedName2276;

        // Prepare SQL query to update the asset
        $sql2276 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2276 = $conn->prepare($sql2276);
        $stmt2276->bind_param('sssssi', $status2276, $assignedName2276, $assignedBy2276, $description2276, $room2276, $assetId2276);

        if ($stmt2276->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2276 to $status2276.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2276->close();
    }

    //FOR ID 2277
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2277'])) {
        // Get form data
        $assetId2277 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2277 = $_POST['status']; // Get the status from the form
        $description2277 = $_POST['description']; // Get the description from the form
        $room2277 = $_POST['room']; // Get the room from the form
        $assignedBy2277 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2277 = $status2277 === 'Need Repair' ? '' : $assignedName2277;

        // Prepare SQL query to update the asset
        $sql2277 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2277 = $conn->prepare($sql2277);
        $stmt2277->bind_param('sssssi', $status2277, $assignedName2277, $assignedBy2277, $description2277, $room2277, $assetId2277);

        if ($stmt2277->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2277 to $status2277.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2277->close();
    }

    //FOR ID 2278
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2278'])) {
        // Get form data
        $assetId2278 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2278 = $_POST['status']; // Get the status from the form
        $description2278 = $_POST['description']; // Get the description from the form
        $room2278 = $_POST['room']; // Get the room from the form
        $assignedBy2278 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2278 = $status2278 === 'Need Repair' ? '' : $assignedName2278;

        // Prepare SQL query to update the asset
        $sql2278 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2278 = $conn->prepare($sql2278);
        $stmt2278->bind_param('sssssi', $status2278, $assignedName2278, $assignedBy2278, $description2278, $room2278, $assetId2278);

        if ($stmt2278->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2278 to $status2278.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2278->close();
    }

    //FOR ID 2279
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2279'])) {
        // Get form data
        $assetId2279 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2279 = $_POST['status']; // Get the status from the form
        $description2279 = $_POST['description']; // Get the description from the form
        $room2279 = $_POST['room']; // Get the room from the form
        $assignedBy2279 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2279 = $status2279 === 'Need Repair' ? '' : $assignedName2279;

        // Prepare SQL query to update the asset
        $sql2279 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2279 = $conn->prepare($sql2279);
        $stmt2279->bind_param('sssssi', $status2279, $assignedName2279, $assignedBy2279, $description2279, $room2279, $assetId2279);

        if ($stmt2279->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2279 to $status2279.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2279->close();
    }

    //FOR ID 2280
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2280'])) {
        // Get form data
        $assetId2280 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2280 = $_POST['status']; // Get the status from the form
        $description2280 = $_POST['description']; // Get the description from the form
        $room2280 = $_POST['room']; // Get the room from the form
        $assignedBy2280 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2280 = $status2280 === 'Need Repair' ? '' : $assignedName2280;

        // Prepare SQL query to update the asset
        $sql2280 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2280 = $conn->prepare($sql2280);
        $stmt2280->bind_param('sssssi', $status2280, $assignedName2280, $assignedBy2280, $description2280, $room2280, $assetId2280);

        if ($stmt2280->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2280 to $status2280.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2280->close();
    }

    //FOR ID 2281
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2281'])) {
        // Get form data
        $assetId2281 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2281 = $_POST['status']; // Get the status from the form
        $description2281 = $_POST['description']; // Get the description from the form
        $room2281 = $_POST['room']; // Get the room from the form
        $assignedBy2281 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2281 = $status2281 === 'Need Repair' ? '' : $assignedName2281;

        // Prepare SQL query to update the asset
        $sql2281 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2281 = $conn->prepare($sql2281);
        $stmt2281->bind_param('sssssi', $status2281, $assignedName2281, $assignedBy2281, $description2281, $room2281, $assetId2281);

        if ($stmt2281->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2281 to $status2281.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2281->close();
    }

    //FOR ID 2282
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2282'])) {
        // Get form data
        $assetId2282 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2282 = $_POST['status']; // Get the status from the form
        $description2282 = $_POST['description']; // Get the description from the form
        $room2282 = $_POST['room']; // Get the room from the form
        $assignedBy2282 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2282 = $status2282 === 'Need Repair' ? '' : $assignedName2282;

        // Prepare SQL query to update the asset
        $sql2282 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2282 = $conn->prepare($sql2282);
        $stmt2282->bind_param('sssssi', $status2282, $assignedName2282, $assignedBy2282, $description2282, $room2282, $assetId2282);

        if ($stmt2282->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2282 to $status2282.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2282->close();
    }

    //FOR ID 2283
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2283'])) {
        // Get form data
        $assetId2283 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2283 = $_POST['status']; // Get the status from the form
        $description2283 = $_POST['description']; // Get the description from the form
        $room2283 = $_POST['room']; // Get the room from the form
        $assignedBy2283 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2283 = $status2283 === 'Need Repair' ? '' : $assignedName2283;

        // Prepare SQL query to update the asset
        $sql2283 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2283 = $conn->prepare($sql2283);
        $stmt2283->bind_param('sssssi', $status2283, $assignedName2283, $assignedBy2283, $description2283, $room2283, $assetId2283);

        if ($stmt2283->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2283 to $status2283.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2283->close();
    }

    //FOR ID 2284
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2284'])) {
        // Get form data
        $assetId2284 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2284 = $_POST['status']; // Get the status from the form
        $description2284 = $_POST['description']; // Get the description from the form
        $room2284 = $_POST['room']; // Get the room from the form
        $assignedBy2284 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2284 = $status2284 === 'Need Repair' ? '' : $assignedName2284;

        // Prepare SQL query to update the asset
        $sql2284 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2284 = $conn->prepare($sql2284);
        $stmt2284->bind_param('sssssi', $status2284, $assignedName2284, $assignedBy2284, $description2284, $room2284, $assetId2284);

        if ($stmt2284->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2284 to $status2284.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2284->close();
    }

    //FOR ID 2285
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2285'])) {
        // Get form data
        $assetId2285 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2285 = $_POST['status']; // Get the status from the form
        $description2285 = $_POST['description']; // Get the description from the form
        $room2285 = $_POST['room']; // Get the room from the form
        $assignedBy2285 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2285 = $status2285 === 'Need Repair' ? '' : $assignedName2285;

        // Prepare SQL query to update the asset
        $sql2285 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2285 = $conn->prepare($sql2285);
        $stmt2285->bind_param('sssssi', $status2285, $assignedName2285, $assignedBy2285, $description2285, $room2285, $assetId2285);

        if ($stmt2285->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2285 to $status2285.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2285->close();
    }

    //FOR ID 2286
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2286'])) {
        // Get form data
        $assetId2286 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2286 = $_POST['status']; // Get the status from the form
        $description2286 = $_POST['description']; // Get the description from the form
        $room2286 = $_POST['room']; // Get the room from the form
        $assignedBy2286 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2286 = $status2286 === 'Need Repair' ? '' : $assignedName2286;

        // Prepare SQL query to update the asset
        $sql2286 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2286 = $conn->prepare($sql2286);
        $stmt2286->bind_param('sssssi', $status2286, $assignedName2286, $assignedBy2286, $description2286, $room2286, $assetId2286);

        if ($stmt2286->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2286 to $status2286.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2286->close();
    }

    //FOR ID 2287
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2287'])) {
        // Get form data
        $assetId2287 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2287 = $_POST['status']; // Get the status from the form
        $description2287 = $_POST['description']; // Get the description from the form
        $room2287 = $_POST['room']; // Get the room from the form
        $assignedBy2287 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2287 = $status2287 === 'Need Repair' ? '' : $assignedName2287;

        // Prepare SQL query to update the asset
        $sql2287 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2287 = $conn->prepare($sql2287);
        $stmt2287->bind_param('sssssi', $status2287, $assignedName2287, $assignedBy2287, $description2287, $room2287, $assetId2287);

        if ($stmt2287->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2287 to $status2287.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2287->close();
    }

    //FOR ID 2288
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2288'])) {
        // Get form data
        $assetId2288 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2288 = $_POST['status']; // Get the status from the form
        $description2288 = $_POST['description']; // Get the description from the form
        $room2288 = $_POST['room']; // Get the room from the form
        $assignedBy2288 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2288 = $status2288 === 'Need Repair' ? '' : $assignedName2288;

        // Prepare SQL query to update the asset
        $sql2288 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2288 = $conn->prepare($sql2288);
        $stmt2288->bind_param('sssssi', $status2288, $assignedName2288, $assignedBy2288, $description2288, $room2288, $assetId2288);

        if ($stmt2288->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2288 to $status2288.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2288->close();
    }

    //FOR ID 2289
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2289'])) {
        // Get form data
        $assetId2289 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2289 = $_POST['status']; // Get the status from the form
        $description2289 = $_POST['description']; // Get the description from the form
        $room2289 = $_POST['room']; // Get the room from the form
        $assignedBy2289 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2289 = $status2289 === 'Need Repair' ? '' : $assignedName2289;

        // Prepare SQL query to update the asset
        $sql2289 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2289 = $conn->prepare($sql2289);
        $stmt2289->bind_param('sssssi', $status2289, $assignedName2289, $assignedBy2289, $description2289, $room2289, $assetId2289);

        if ($stmt2289->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2289 to $status2289.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2289->close();
    }

    //FOR ID 2290
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit2290'])) {
        // Get form data
        $assetId2290 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status2290 = $_POST['status']; // Get the status from the form
        $description2290 = $_POST['description']; // Get the description from the form
        $room2290 = $_POST['room']; // Get the room from the form
        $assignedBy2290 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName2290 = $status2290 === 'Need Repair' ? '' : $assignedName2290;

        // Prepare SQL query to update the asset
        $sql2290 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt2290 = $conn->prepare($sql2290);
        $stmt2290->bind_param('sssssi', $status2290, $assignedName2290, $assignedBy2290, $description2290, $room2290, $assetId2290);

        if ($stmt2290->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId2290 to $status2290.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt2290->close();
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
                header("Location: KOBF1.php");
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/KOB/KOBF1.css" />
        <link rel="stylesheet" href="../../../src/css/map.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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

                        <!-- FLOOR PLAN -->
                        <img class="Floor-container-1 .NEWBF1" src="../../../src/floors/korPhil/Korphil1F.png" alt="">
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
                        <!-- ASSET 2249 -->
                        <img src='../image.php?id=2249' style='width:25px; cursor:pointer; position:absolute; top:140px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2249' onclick='fetchAssetData(2249);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2249); ?>; 
                        position:absolute; top:140px; left:120px;'>
                        </div>

                        <!-- ASSET 2250 -->
                        <img src='../image.php?id=2250' style='width:25px; cursor:pointer; position:absolute; top:180px; left:80px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2250' onclick='fetchAssetData(2250);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2250); ?>; 
                        position:absolute; top:180px; left:80px;'>
                        </div>

                        <!-- ASSET 2251 -->
                        <img src='../image.php?id=2251' style='width:25px; cursor:pointer; position:absolute; top:180px; left:160px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2251' onclick='fetchAssetData(2251);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2251); ?>; 
                        position:absolute; top:180px; left:160px;'>
                        </div>

                        <!-- ASSET 2252 -->
                        <img src='../image.php?id=2252' style='width:25px; cursor:pointer; position:absolute; top:220px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2252' onclick='fetchAssetData(2252);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2252); ?>; 
                        position:absolute; top:220px; left:120px;'>
                        </div>

                        <!-- ASSET 2253 -->
                        <img src='../image.php?id=2253' style='width:25px; cursor:pointer; position:absolute; top:225px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2253' onclick='fetchAssetData(2253);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2253); ?>; 
                        position:absolute; top:225px; left:200px;'>
                        </div>

                        <!-- ASSET 2254 -->
                        <img src='../image.php?id=2254' style='width:25px; cursor:pointer; position:absolute; top:115px; left:180px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2254' onclick='fetchAssetData(2254);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2254); ?>; 
                        position:absolute; top:115px; left:180px;'>
                        </div>

                        <!-- ASSET 2255 -->
                        <img src='../image.php?id=2255' style='width:25px; cursor:pointer; position:absolute; top:110px; left:260px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2255' onclick='fetchAssetData(2255);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2255); ?>; 
                        position:absolute; top:110px; left:260px;'>
                        </div>

                        <!-- ASSET 2256 -->
                        <img src='../image.php?id=2256' style='width:25px; cursor:pointer; position:absolute; top:150px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2256' onclick='fetchAssetData(2256);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2256); ?>; 
                        position:absolute; top:150px; left:220px;'>
                        </div>

                        <!-- ASSET 2257 -->
                        <img src='../image.php?id=2257' style='width:25px; cursor:pointer; position:absolute; top:150px; left:300px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2257' onclick='fetchAssetData(2257);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2257); ?>; 
                        position:absolute; top:150px; left:300px;'>
                        </div>

                        <!-- ASSET 2258 -->
                        <img src='../image.php?id=2258' style='width:25px; cursor:pointer; position:absolute; top:200px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2258' onclick='fetchAssetData(2258);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2258); ?>; 
                        position:absolute; top:200px; left:255px;'>
                        </div>

                        <!-- ASSET 2259 -->
                        <img src='../image.php?id=2259' style='width:25px; cursor:pointer; position:absolute; top:385px; left:115px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2259' onclick='fetchAssetData(2259);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2259); ?>; 
                        position:absolute; top:385px; left:115px;'>
                        </div>

                        <!-- ASSET 2260 -->
                        <img src='../image.php?id=2260' style='width:25px; cursor:pointer; position:absolute; top:385px; left:185px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2260' onclick='fetchAssetData(2260);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2260); ?>; 
                        position:absolute; top:385px; left:185px;'>
                        </div>

                        <!-- ASSET 2261 -->
                        <img src='../image.php?id=2261' style='width:25px; cursor:pointer; position:absolute; top:415px; left:115px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2261' onclick='fetchAssetData(2261);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2261); ?>; 
                        position:absolute; top:415px; left:115px;'>
                        </div>

                        <!-- ASSET 2262 -->
                        <img src='../image.php?id=2262' style='width:25px; cursor:pointer; position:absolute; top:415px; left:185px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2262' onclick='fetchAssetData(2262);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2262); ?>; 
                        position:absolute; top:415px; left:185px;'>
                        </div>

                        <!-- ASSET 2263 -->
                        <img src='../image.php?id=2263' style='width:25px; cursor:pointer; position:absolute; top:410px; left:250px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2263' onclick='fetchAssetData(2263);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2263); ?>; 
                        position:absolute; top:410px; left:250px;'>
                        </div>

                        <!-- ASSET 2264 -->
                        <img src='../image.php?id=2264' style='width:25px; cursor:pointer; position:absolute; top:410px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2264' onclick='fetchAssetData(2264);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2264); ?>; 
                        position:absolute; top:410px; left:365px;'>
                        </div>

                        <!-- ASSET 2265 -->
                        <img src='../image.php?id=2265' style='width:25px; cursor:pointer; position:absolute; top:425px; left:300px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2265' onclick='fetchAssetData(2265);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2265); ?>; 
                        position:absolute; top:425px; left:300px;'>
                        </div>

                        <!-- ASSET 2266 -->
                        <img src='../image.php?id=2266' style='width:25px; cursor:pointer; position:absolute; top:450px; left:300px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2266' onclick='fetchAssetData(2266);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2266); ?>; 
                        position:absolute; top:450px; left:300px;'>
                        </div>

                        <!-- ASSET 2267 -->
                        <img src='../image.php?id=2267' style='width:25px; cursor:pointer; position:absolute; top:470px; left:250px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2267' onclick='fetchAssetData(2267);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2267); ?>; 
                        position:absolute; top:470px; left:250px;'>
                        </div>

                        <!-- ASSET 2268 -->
                        <img src='../image.php?id=2268' style='width:25px; cursor:pointer; position:absolute; top:470px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2268' onclick='fetchAssetData(2268);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2268); ?>; 
                        position:absolute; top:470px; left:365px;'>
                        </div>

                        <!-- ASSET 2269 -->
                        <img src='../image.php?id=2269' style='width:25px; cursor:pointer; position:absolute; top:440px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2269' onclick='fetchAssetData(2269);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2269); ?>; 
                        position:absolute; top:440px; left:435px;'>
                        </div>

                        <!-- ASSET 2270 -->
                        <img src='../image.php?id=2270' style='width:25px; cursor:pointer; position:absolute; top:470px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2270' onclick='fetchAssetData(2270);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2270); ?>; 
                        position:absolute; top:470px; left:435px;'>
                        </div>

                        <!-- ASSET 2271 -->
                        <img src='../image.php?id=2271' style='width:25px; cursor:pointer; position:absolute; top:410px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2271' onclick='fetchAssetData(2271);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2271); ?>; 
                        position:absolute; top:410px; left:435px;'>
                        </div>

                        <!-- ASSET 2272 -->
                        <img src='../image.php?id=2272' style='width:25px; cursor:pointer; position:absolute; top:410px; left:485px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2272' onclick='fetchAssetData(2272);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2272); ?>; 
                        position:absolute; top:410px; left:485px;'>
                        </div>

                        <!-- ASSET 2273 -->
                        <img src='../image.php?id=2273' style='width:25px; cursor:pointer; position:absolute; top:410px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2273' onclick='fetchAssetData(2273);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2273); ?>; 
                        position:absolute; top:410px; left:560px;'>
                        </div>

                        <!-- ASSET 2274 -->
                        <img src='../image.php?id=2274' style='width:25px; cursor:pointer; position:absolute; top:440px; left:525px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2274' onclick='fetchAssetData(2274);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2274); ?>; 
                        position:absolute; top:440px; left:525px;'>
                        </div>

                        <!-- ASSET 2275 -->
                        <img src='../image.php?id=2275' style='width:25px; cursor:pointer; position:absolute; top:470px; left:485px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2275' onclick='fetchAssetData(2275);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2275); ?>; 
                        position:absolute; top:470px; left:485px;'>
                        </div>

                        <!-- ASSET 2276 -->
                        <img src='../image.php?id=2276' style='width:25px; cursor:pointer; position:absolute; top:470px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2276' onclick='fetchAssetData(2276);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2276); ?>; 
                        position:absolute; top:470px; left:560px;'>
                        </div>

                        <!-- ASSET 2277 -->
                        <img src='../image.php?id=2277' style='width:25px; cursor:pointer; position:absolute; top:405px; left:645px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2277' onclick='fetchAssetData(2277);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2277); ?>; 
                        position:absolute; top:405px; left:645px;'>
                        </div>

                        <!-- ASSET 2278 -->
                        <img src='../image.php?id=2278' style='width:25px; cursor:pointer; position:absolute; top:440px; left:610px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2278' onclick='fetchAssetData(2278);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2278); ?>; 
                        position:absolute; top:440px; left:610px;'>
                        </div>

                        <!-- ASSET 2279 -->
                        <img src='../image.php?id=2279' style='width:25px; cursor:pointer; position:absolute; top:440px; left:645px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2279' onclick='fetchAssetData(2279);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2279); ?>; 
                        position:absolute; top:440px; left:645px;'>
                        </div>

                        <!-- ASSET 2280 -->
                        <img src='../image.php?id=2280' style='width:25px; cursor:pointer; position:absolute; top:440px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2280' onclick='fetchAssetData(2280);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2280); ?>; 
                        position:absolute; top:440px; left:680px;'>
                        </div>

                        <!-- ASSET 2281 -->
                        <img src='../image.php?id=2281' style='width:25px; cursor:pointer; position:absolute; top:410px; left:725px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2281' onclick='fetchAssetData(2281);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2281); ?>; 
                        position:absolute; top:410px; left:725px;'>
                        </div>

                        <!-- ASSET 2282 -->
                        <img src='../image.php?id=2282' style='width:25px; cursor:pointer; position:absolute; top:410px; left:805px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2282' onclick='fetchAssetData(2282);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2282); ?>; 
                        position:absolute; top:410px; left:805px;'>
                        </div>

                        <!-- ASSET 2283 -->
                        <img src='../image.php?id=2283' style='width:25px; cursor:pointer; position:absolute; top:440px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2283' onclick='fetchAssetData(2283);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2283); ?>; 
                        position:absolute; top:440px; left:760px;'>
                        </div>

                        <!-- ASSET 2284 -->
                        <img src='../image.php?id=2284' style='width:25px; cursor:pointer; position:absolute; top:470px; left:725px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2284' onclick='fetchAssetData(2284);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2284); ?>; 
                        position:absolute; top:470px; left:725px;'>
                        </div>

                        <!-- ASSET 2285 -->
                        <img src='../image.php?id=2285' style='width:25px; cursor:pointer; position:absolute; top:470px; left:805px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2285' onclick='fetchAssetData(2285);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2285); ?>; 
                        position:absolute; top:470px; left:805px;'>
                        </div>

                        <!-- ASSET 2286 -->
                        <img src='../image.php?id=2286' style='width:25px; cursor:pointer; position:absolute; top:410px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2286' onclick='fetchAssetData(2286);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2286); ?>; 
                        position:absolute; top:410px; left:850px;'>
                        </div>

                        <!-- ASSET 2287 -->
                        <img src='../image.php?id=2287' style='width:25px; cursor:pointer; position:absolute; top:410px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2287' onclick='fetchAssetData(2287);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2287); ?>; 
                        position:absolute; top:410px; left:930px;'>
                        </div>

                        <!-- ASSET 2288 -->
                        <img src='../image.php?id=2288' style='width:25px; cursor:pointer; position:absolute; top:440px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2288' onclick='fetchAssetData(2288);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2288); ?>; 
                        position:absolute; top:440px; left:890px;'>
                        </div>

                        <!-- ASSET 2289 -->
                        <img src='../image.php?id=2289' style='width:25px; cursor:pointer; position:absolute; top:470px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2289' onclick='fetchAssetData(2289);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2289); ?>; 
                        position:absolute; top:470px; left:850px;'>
                        </div>

                        <!-- ASSET 2290 -->
                        <img src='../image.php?id=2290' style='width:25px; cursor:pointer; position:absolute; top:470px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal2290' onclick='fetchAssetData(2290);'>
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status2290); ?>; 
                        position:absolute; top:470px; left:930px;'>
                        </div>
                    </div>

                    <!-- Modal structure for id 2249-->
                    <div class='modal fade' id='imageModal2249' tabindex='-1' aria-labelledby='imageModalLabel2249' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2249); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2249); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2249); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2249); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2249); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2249); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2249); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2249); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2249 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2249 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2249 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2249 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2249); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2249); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2249); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2249">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2249-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2249" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2249">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2250-->
                    <div class='modal fade' id='imageModal2250' tabindex='-1' aria-labelledby='imageModalLabel2250' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2250); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2250); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2250); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2250); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2250); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2250); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2250); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2250); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2250 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2250 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2250 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2250 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2250); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2250); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2250); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2250">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2250-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2250" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2250">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2251-->
                    <div class='modal fade' id='imageModal2251' tabindex='-1' aria-labelledby='imageModalLabel2251' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2251); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2251); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2251); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2251); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2251); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2251); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2251); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2251); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2251 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2251 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2251 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2251 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2251); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2251); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2251); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2251">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2251-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2251" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2251">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2252-->
                    <div class='modal fade' id='imageModal2252' tabindex='-1' aria-labelledby='imageModalLabel2252' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2252); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2252); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2252); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2252); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2252); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2252); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2252); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2252); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2252 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2252 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2252 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2252 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2252); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2252); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2252); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2252">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2252-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2252" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2252">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2253-->
                    <div class='modal fade' id='imageModal2253' tabindex='-1' aria-labelledby='imageModalLabel2253' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2253); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2253); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2253); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2253); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2253); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2253); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2253); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2253); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2253 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2253 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2253 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2253 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2253); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2253); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2253); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2253">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2253-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2253" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2253">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2254-->
                    <div class='modal fade' id='imageModal2254' tabindex='-1' aria-labelledby='imageModalLabel2254' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2254); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2254); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2254); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2254); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2254); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2254); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2254); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2254); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2254 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2254 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2254 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2254 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2254); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2254); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2254); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2254">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2254-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2254" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2254">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2255-->
                    <div class='modal fade' id='imageModal2255' tabindex='-1' aria-labelledby='imageModalLabel2255' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2255); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2255); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2255); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2255); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2255); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2255); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2255); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2255); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2255 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2255 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2255 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2255 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2255); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2255); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2255); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2255">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2255-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2255" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2255">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2256-->
                    <div class='modal fade' id='imageModal2256' tabindex='-1' aria-labelledby='imageModalLabel2256' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2256); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2256); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2256); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2256); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2256); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2256); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2256); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2256); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2256 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2256 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2256 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2256 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2256); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2256); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2256); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2256">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2256-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2256" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2256">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2257-->
                    <div class='modal fade' id='imageModal2257' tabindex='-1' aria-labelledby='imageModalLabel2257' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2257); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2257); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2257); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2257); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2257); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2257); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2257); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2257); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2257 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2257 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2257 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2257 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2257); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2257); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2257); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2257">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2257-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2257" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2257">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2258-->
                    <div class='modal fade' id='imageModal2258' tabindex='-1' aria-labelledby='imageModalLabel2258' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2258); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2258); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2258); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2258); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2258); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2258); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2258); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2258); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2258 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2258 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2258 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2258 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2258); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2258); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2258); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2258">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2258-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2258" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2258">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2259-->
                    <div class='modal fade' id='imageModal2259' tabindex='-1' aria-labelledby='imageModalLabel2259' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2259); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2259); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2259); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2259); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2259); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2259); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2259); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2259); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2259 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2259 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2259 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2259 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2259); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2259); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2259); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2259">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2259-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2259" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2259">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2260-->
                    <div class='modal fade' id='imageModal2260' tabindex='-1' aria-labelledby='imageModalLabel2260' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2260); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2260); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2260); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2260); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2260); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2260); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2260); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2260); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2260 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2260 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2260 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2260 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2260); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2260); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2260); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2260">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2260-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2260" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2260">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2261-->
                    <div class='modal fade' id='imageModal2261' tabindex='-1' aria-labelledby='imageModalLabel2261' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2261); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2261); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2261); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2261); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2261); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2261); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2261); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2261); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2261 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2261 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2261 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2261 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2261); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2261); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2261); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2261">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2261-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2261" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2261">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2262-->
                    <div class='modal fade' id='imageModal2262' tabindex='-1' aria-labelledby='imageModalLabel2262' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2262); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2262); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2262); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2262); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2262); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2262); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2262); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2262); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2262 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2262 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2262 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2262 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2262); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2262); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2262); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2262">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2262-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2262" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2262">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2263-->
                    <div class='modal fade' id='imageModal2263' tabindex='-1' aria-labelledby='imageModalLabel2263' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2263); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2263); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2263); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2263); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2263); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2263); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2263); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2263); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2263 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2263 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2263 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2263 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2263); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2263); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2263); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2263">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2263-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2263" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2263">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2264-->
                    <div class='modal fade' id='imageModal2264' tabindex='-1' aria-labelledby='imageModalLabel2264' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2264); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2264); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2264); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2264); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2264); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2264); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2264); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2264); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2264 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2264 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2264 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2264 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2264); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2264); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2264); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2264">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2264-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2264" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2264">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2265-->
                    <div class='modal fade' id='imageModal2265' tabindex='-1' aria-labelledby='imageModalLabel2265' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2265); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2265); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2265); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2265); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2265); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2265); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2265); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2265); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2265 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2265 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2265 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2265 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2265); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2265); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2265); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2265">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2265-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2265" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2265">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2266-->
                    <div class='modal fade' id='imageModal2266' tabindex='-1' aria-labelledby='imageModalLabel2266' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2266); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2266); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2266); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2266); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2266); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2266); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2266); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2266); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2266 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2266 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2266 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2266 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2266); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2266); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2266); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2266">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2266-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2266" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2266">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2267-->
                    <div class='modal fade' id='imageModal2267' tabindex='-1' aria-labelledby='imageModalLabel2267' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2267); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2267); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2267); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2267); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2267); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2267); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2267); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2267); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2267 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2267 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2267 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2267 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2267); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2267); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2267); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2267">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2267-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2267" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2267">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2268-->
                    <div class='modal fade' id='imageModal2268' tabindex='-1' aria-labelledby='imageModalLabel2268' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2268); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2268); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2268); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2268); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2268); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2268); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2268); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2268); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2268 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2268 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2268 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2268 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2268); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2268); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2268); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2268">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2268-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2268" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2268">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2269-->
                    <div class='modal fade' id='imageModal2269' tabindex='-1' aria-labelledby='imageModalLabel2269' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2269); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2269); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2269); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2269); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2269); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2269); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2269); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2269); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2269 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2269 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2269 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2269 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2269); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2269); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2269); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2269">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2269-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2269" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2269">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2270-->
                    <div class='modal fade' id='imageModal2270' tabindex='-1' aria-labelledby='imageModalLabel2270' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2270); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2270); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2270); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2270); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2270); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2270); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2270); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2270); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2270 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2270 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2270 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2270 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2270); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2270); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2270); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2270">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2270-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2270" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2270">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2271-->
                    <div class='modal fade' id='imageModal2271' tabindex='-1' aria-labelledby='imageModalLabel2271' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2271); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2271); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2271); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2271); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2271); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2271); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2271); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2271); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2271 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2271 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2271 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2271 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2271); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2271); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2271); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2271">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2271-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2271" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2271">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2272-->
                    <div class='modal fade' id='imageModal2272' tabindex='-1' aria-labelledby='imageModalLabel2272' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2272); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2272); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2272); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2272); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2272); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2272); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2272); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2272); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2272 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2272 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2272 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2272 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2272); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2272); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2272); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2272">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2272-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2272" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2272">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2273-->
                    <div class='modal fade' id='imageModal2273' tabindex='-1' aria-labelledby='imageModalLabel2273' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2273); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2273); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2273); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2273); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2273); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2273); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2273); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2273); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2273 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2273 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2273 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2273 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2273); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2273); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2273); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2273">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2273-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2273" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2273">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2274-->
                    <div class='modal fade' id='imageModal2274' tabindex='-1' aria-labelledby='imageModalLabel2274' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2274); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2274); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2274); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2274); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2274); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2274); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2274); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2274); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2274 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2274 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2274 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2274 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2274); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2274); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2274); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2274">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2274-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2274" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2274">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2275-->
                    <div class='modal fade' id='imageModal2275' tabindex='-1' aria-labelledby='imageModalLabel2275' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2275); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2275); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2275); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2275); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2275); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2275); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2275); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2275); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2275 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2275 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2275 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2275 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2275); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2275); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2275); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2275">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2275-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2275" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2275">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2276-->
                    <div class='modal fade' id='imageModal2276' tabindex='-1' aria-labelledby='imageModalLabel2276' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2276); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2276); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2276); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2276); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2276); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2276); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2276); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2276); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2276 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2276 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2276 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2276 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2276); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2276); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2276); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2276">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2276-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2276" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2276">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2277-->
                    <div class='modal fade' id='imageModal2277' tabindex='-1' aria-labelledby='imageModalLabel2277' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2277); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2277); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2277); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2277); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2277); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2277); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2277); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2277); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2277 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2277 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2277 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2277 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2277); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2277); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2277); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2277">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2277-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2277" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2277">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2278-->
                    <div class='modal fade' id='imageModal2278' tabindex='-1' aria-labelledby='imageModalLabel2278' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2278); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2278); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2278); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2278); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2278); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2278); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2278); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2278); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2278 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2278 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2278 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2278 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2278); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2278); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2278); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2278">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2278-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2278" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2278">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2279-->
                    <div class='modal fade' id='imageModal2279' tabindex='-1' aria-labelledby='imageModalLabel2279' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2279); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2279); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2279); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2279); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2279); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2279); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2279); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2279); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2279 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2279 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2279 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2279 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2279); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2279); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2279); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2279">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2279-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2279" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2279">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2280-->
                    <div class='modal fade' id='imageModal2280' tabindex='-1' aria-labelledby='imageModalLabel2280' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2280); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2280); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2280); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2280); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2280); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2280); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2280); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2280); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2280 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2280 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2280 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2280 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2280); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2280); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2280); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2280">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2280-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2280" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2280">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2281-->
                    <div class='modal fade' id='imageModal2281' tabindex='-1' aria-labelledby='imageModalLabel2281' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2281); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2281); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2281); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2281); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2281); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2281); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2281); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2281); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2281 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2281 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2281 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2281 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2281); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2281); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2281); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2281">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2281-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2281" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2281">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2282-->
                    <div class='modal fade' id='imageModal2282' tabindex='-1' aria-labelledby='imageModalLabel2282' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2282); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2282); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2282); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2282); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2282); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2282); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2282); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2282); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2282 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2282 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2282 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2282 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2282); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2282); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2282); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2282">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2282-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2282" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2282">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2283-->
                    <div class='modal fade' id='imageModal2283' tabindex='-1' aria-labelledby='imageModalLabel2283' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2283); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2283); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2283); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2283); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2283); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2283); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2283); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2283); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2283 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2283 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2283 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2283 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2283); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2283); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2283); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2283">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2283-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2283" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2283">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2284-->
                    <div class='modal fade' id='imageModal2284' tabindex='-1' aria-labelledby='imageModalLabel2284' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2284); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2284); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2284); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2284); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2284); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2284); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2284); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2284); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2284 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2284 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2284 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2284 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2284); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2284); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2284); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2284">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2284-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2284" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2284">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2285-->
                    <div class='modal fade' id='imageModal2285' tabindex='-1' aria-labelledby='imageModalLabel2285' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2285); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2285); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2285); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2285); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2285); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2285); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2285); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2285); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2285 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2285 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2285 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2285 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2285); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2285); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2285); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2285">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2285-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2285" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2285">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2286-->
                    <div class='modal fade' id='imageModal2286' tabindex='-1' aria-labelledby='imageModalLabel2286' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2286); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2286); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2286); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2286); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2286); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2286); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2286); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2286); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2286 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2286 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2286 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2286 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2286); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2286); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2286); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2286">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2286-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2286" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2286">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2287-->
                    <div class='modal fade' id='imageModal2287' tabindex='-1' aria-labelledby='imageModalLabel2287' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2287); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2287); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2287); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2287); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2287); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2287); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2287); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2287); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2287 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2287 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2287 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2287 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2287); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2287); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2287); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2287">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2287-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2287" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2287">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2288-->
                    <div class='modal fade' id='imageModal2288' tabindex='-1' aria-labelledby='imageModalLabel2288' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2288); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2288); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2288); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2288); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2288); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2288); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2288); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2288); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2288 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2288 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2288 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2288 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2288); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2288); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2288); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2288">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2288-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2288" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2288">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2289-->
                    <div class='modal fade' id='imageModal2289' tabindex='-1' aria-labelledby='imageModalLabel2289' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2289); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2289); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2289); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2289); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2289); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2289); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2289); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2289); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2289 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2289 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2289 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2289 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2289); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2289); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2289); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2289">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2289-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2289" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2289">Yes</button>
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!-- Modal structure for id 2290-->
                    <div class='modal fade' id='imageModal2290' tabindex='-1' aria-labelledby='imageModalLabel2290' aria-hidden='true'>
                        <div class='modal-dialog modal-xl modal-dialog-centered'>
                            <div class='modal-content'>
                                <!-- Modal header -->
                                <div class='modal-header'>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>

                                <!-- Modal body -->
                                <div class='modal-body'>
                                    <form method="post" class="row g-3" enctype="multipart/form-data">
                                        <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId2290); ?>">
                                        <!--START DIV FOR IMAGE -->

                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img2290); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->

                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId2290); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="date" class="form-label">Date:</label>
                                            <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date2290); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room2290); ?>" readonly />
                                        </div>


                                        <div class="col-6" style="display:none">
                                            <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building2290); ?>" readonly />
                                        </div>

                                        <!--End of Second Row-->

                                        <!--Third Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor2290); ?>" readonly />
                                        </div>

                                        <div class="col-12 center-content">
                                            <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category2290); ?>" readonly />
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
                                                <option value="Working" <?php echo ($status2290 == 'Working')
                                                                            ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status2290 == 'Under Maintenance')
                                                                                        ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status2290 == 'For Replacement')
                                                                                    ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status2290 == 'Need Repair')
                                                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName2290); ?>" readonly />
                                        </div>

                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy2290); ?>" readonly />
                                        </div>

                                        <!--End of Fourth Row-->

                                        <!--Fifth Row-->
                                        <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description2290); ?>" />
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
                                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2290">
                                                Save
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Edit for table 2290-->
                    <div class="map-alert">
                        <div class="modal fade" id="staticBackdrop2290" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        <p>Are you sure you want to save changes?</p>
                                        <div class="modal-popups">
                                            <button type="submit" class="btn add-modal-btn" name="edit2290">Yes</button>
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