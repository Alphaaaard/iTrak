<?php
// reset_password.php
session_start();
include_once("./config/connection.php");

$conn = connection();

if (isset($_GET['token'])) {
  $token = $_GET['token'];

  // Prepare a SQL statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT * FROM account WHERE reset_token = ? AND token_expiry > NOW()");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Token is valid
    $user = $result->fetch_assoc();
    // Show form to enter new password
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Forgot Password - UpKeep</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <link rel="stylesheet" href="./src/css/reset-password.css">
      <link rel="stylesheet" href="./src/css/main.css">
    </head>

    <body>
      <div class="container-of-box">
        <div class="box1">
          <main>
            <div class="login-header">
              <h1 style="font-weight: bold;">Reset your password</h1>
              <h5>Create a new password for your account.</h5>
            </div>
            <form action="update_password.php" method="post" enctype="multipart/form-data" onsubmit="return validatePassword()"> <!-- Your existing input fields -->
              <input type="hidden" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
              <div class="mb-4">
                <input type="password" id="password" name="password" class="form-textbox" placeholder="New password" required>
                <i id="togglepassword" class="bi bi-eye-slash" onclick="togglePassword('password')"></i>
              </div>
              <div class="mb-4">
                <input type="password" id="confirm_password" name="confirm_password" class="form-textbox" placeholder="Confirm new password" required>
                <i id="toggleconfirm_password" class="bi bi-eye-slash" onclick="togglePassword('confirm_password')"></i>
              </div>
              <div class="mb-3 d-flex justify-content-end">
                <button type="submit" id="reset-button" class="custom-button btn">Reset Password</button>
              </div>
            </form>
          </main>
        </div>
        <div class="box2">
          <!-- The rest of your existing box2 content -->
          <a href="#" class="brand" title="UpKeep">
            <img src="src/img/UpKeep.png" alt="UpKeep Logo" class="logo" />
          </a>
        </div>
      </div>
      <script>
        function togglePassword(fieldId) {
          var passwordField = document.getElementById(fieldId);
          var toggleIcon = document.getElementById("toggle" + fieldId);

          if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
          } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
          }
        }

        function validatePassword() {
          var password = document.getElementById("password").value;
          var confirmPassword = document.getElementById("confirm_password").value;

          if (password !== confirmPassword) {
            Swal.fire({
              icon: 'error',
              text: 'Passwords do not match. Please try again.',
              showConfirmButton: false,
              timer: 1500 // The alert will close after 1.5 seconds
            });
            return false; // Prevent form submission
          }
          return true; // Allow form submission
        }
      </script>

      <script>
        function validatePassword() {
          var password = document.getElementById("password");
          var confirmPassword = document.getElementById("confirm_password");

          if (password.value.length < 8) {
            password.setCustomValidity('Password must be at least 8 characters long.');
            password.reportValidity();
            return false;
          } else {
            password.setCustomValidity('');
          }

          if (confirmPassword.value.length < 8) {
            confirmPassword.setCustomValidity('Confirm Password must be at least 8 characters long.');
            confirmPassword.reportValidity();
            return false;
          } else {
            confirmPassword.setCustomValidity('');
          }

          if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match.');
            confirmPassword.reportValidity();
            return false;
          } else {
            confirmPassword.setCustomValidity('');
          }

          return true; // Allow form submission
        }

        // Event listeners to clear custom validity when user starts typing
        document.getElementById("password").addEventListener('input', function() {
          this.setCustomValidity('');
        });

        document.getElementById("confirm_password").addEventListener('input', function() {
          this.setCustomValidity('');
        });
      </script>
    </body>

    </html>
  <?php
  } else {
    // Token is invalid or expired, only display the SweetAlert
  ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="./src/css/reset-password.css">
      <link rel="stylesheet" href="./src/css/main.css">
    </head>

    <body>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        Swal.fire({
          text: 'The password reset link is invalid or has expired.',
          icon: 'error',
          timer: 3000, // Time in milliseconds before the dialog closes
          showConfirmButton: false, // This hides the confirm button
          willClose: () => {
            window.location.href = 'index.php'; // Redirect to your login page
          }
        });
      </script>
    </body>

    </html>
<?php
  }
  $stmt->close();
}
$conn->close();
?>