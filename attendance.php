<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="./src/img/tab-logo.png">
  <link rel="stylesheet" href="./src/css/attendance.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">


  <title>Time In/Time Out</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js" integrity="sha512-b94Z6431JyXY14iSXwgzeZurHHRNkLt9d6bAHt7BZT38eqV+GyngIi/tVye4jBKPYQ2lBdRs0glww4fmpuLRwA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="path/to/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body>

  <div class="content">
    <img src="src/img/UpKeep.png" alt="" class="Logo" />
    <img src="src/img/Scan.png" alt="" class="Scan" />
  </div>

  <form action="" id="rfid_form">
    <input type="text" id="rfid" name="rfid">
  </form>

</body>

<script>
  $(document).ready(function() {
    $('body').mousemove(function() {
      $('#rfid').focus();
    });
    $('#rfid').keyup(function() {});
  });

  const formId = document.getElementById('rfid_form');

  formId.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevents the default form submission behavior

    const rfidValue = formId.elements['rfid'].value;
    console.log('RFID Value:', rfidValue);

    axios.post('rfid.php', {
        // Manually input for testing but no need sana???
        rfid: rfidValue
      })
      .then(response => {
        // Handle the response from the server
        const message = response.data.success;
        const isSuccess = response.data.message;
        console.log('Server Response:', response.data);

        if (!isSuccess) {

          if (message === "You already timed out!") {
            return Swal.fire({
              title: message,
              icon: "warning",
              showConfirmButton: false,
              timer: 1200,
            });
          } else {
            return Swal.fire({
              title: message,
              icon: "error", // For unauthorized access
              showConfirmButton: false,
              timer: 1200,
            });
          }
        } else {
          return Swal.fire({
            title: message,
            icon: "success", // For successful time in and out
            showConfirmButton: false,
            timer: 1200,
          });
        }
      })
      .catch(error => {
        // Handle errors
        console.error('Error:', error);
      })
      .finally(() => {
        // Clear the RFID input field value regardless of success or error
        formId.elements['rfid'].value = '';
      });
  });
</script>

</html>