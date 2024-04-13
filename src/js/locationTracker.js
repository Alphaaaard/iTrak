var map;
var marker;

function initMap(latitude, longitude) {
    map = L.map("map").setView([latitude, longitude], 19);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        minZoom: 1,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    marker = L.marker([latitude, longitude]).addTo(map);
}

function getLocation() {
    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    navigator.geolocation.getCurrentPosition(showPosition, showError, options);
}

function showPosition(position) {
    var latitude = position.coords.latitude.toFixed(5); // Limit to 5 decimal places
    var longitude = position.coords.longitude.toFixed(5); // Limit to 5 decimal places
    var timestamp = new Date().toLocaleString(); // Get current timestamp

    // Reverse geocoding using OpenCage API
    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=9809a46a8d374d89a59c71c1b61f9396`)
        .then(response => response.json())
        .then(data => {
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
            console.log("Location:", locationName);
            console.log("Latitude:", latitude);
            console.log("Longitude:", longitude);
            console.log("Timestamp:", timestamp);

            // Send the coordinates, location name, and timestamp to a PHP script
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open(
                "GET",
                "../../users/update_location.php?lat=" + latitude + "&lng=" + longitude + "&location=" + encodeURIComponent(locationName) + "&timestamp=" + encodeURIComponent(timestamp),
                true
            );
            xmlhttp.send();
        })
        .catch(error => {
            console.error('Error fetching location data:', error);
        });
}

function showError(error) {
    console.log('Error getting location:', error.message);
}

// Initialize the map when the page loads
window.onload = function() {
    getLocation();
    setInterval(getLocation, 1000); // 30,000 milliseconds = 30 seconds
};
