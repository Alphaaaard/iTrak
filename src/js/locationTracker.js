function showPosition(position) {
  var latitude = position.coords.latitude.toFixed(5); // Limit to 5 decimal places
  var longitude = position.coords.longitude.toFixed(5); // Limit to 5 decimal places
  var timestamp = new Date().toLocaleString(); // Get current timestamp

  // Reverse geocoding using OpenCage API
  fetch(
    `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=9809a46a8d374d89a59c71c1b61f9396`
  )
    .then((response) => response.json())
    .then((data) => {
      // Extract the location name from the OpenCage response
      var locationName = data.results[0].formatted;

      // Update the map and marker
      if (!map) {
        initMap(latitude, longitude);
      } else {
        map.setView([latitude, longitude], 19);
        marker.setLatLng([latitude, longitude]);
      }

      // Log the location name, latitude, longitude, and timestamp to the console
      // console.log("Location:", locationName);
      // console.log("Latitude:", latitude);
      // console.log("Longitude:", longitude);
      // console.log("Timestamp:", timestamp);

      // Send the coordinates, location name, and timestamp to update_location.php
      var updateRequest = new XMLHttpRequest();
      updateRequest.open(
        "GET",
        "../../users/update_location.php?lat=" +
          latitude +
          "&lng=" +
          longitude +
          "&location=" +
          encodeURIComponent(locationName) +
          "&timestamp=" +
          encodeURIComponent(timestamp),
        true
      );
      updateRequest.send();

      // Send the coordinates, location name, and timestamp to insert_location.php
      var insertRequest = new XMLHttpRequest();
      insertRequest.open(
        "GET",
        "../../users/insert_location.php?lat=" +
          latitude +
          "&lng=" +
          longitude +
          "&location=" +
          encodeURIComponent(locationName) +
          "&timestamp=" +
          encodeURIComponent(timestamp),
        true
      );
      insertRequest.send();
    })
    .catch((error) => {
      console.error("Error fetching location data:", error);
    });
}
