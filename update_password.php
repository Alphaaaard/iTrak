<?php
// update_password.php
session_start();
include_once("./config/connection.php");

$conn = connection();

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['password']);

    // Update the user's password in the database without hashing
    $stmt = $conn->prepare("UPDATE account SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $newPassword, $email);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows === 1) {
        // The password was updated successfully
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <!-- Include SweetAlert2 -->
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <link rel="stylesheet" href="./src/css/main.css">
            <link rel="stylesheet" href="./src/css/update-pass.css">
        </head>

        <body>
            <script>
                Swal.fire({
                    text: 'Your password has been updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false, // This hides the confirm button
                    willClose: () => {
                        window.location.href = 'index.php';
                    }
                });
            </script>
        </body>

        </html>
    <?php
    } else {
        // The password was not updated, handle the error as needed
    ?>
        <!-- Include your error handling SweetAlert here -->
<?php
    }
    $stmt->close();
}
$conn->close();
?>