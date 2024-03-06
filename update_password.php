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
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>

        <body>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Your password has been updated successfully.',
                    icon: 'success',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the login page or home page
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