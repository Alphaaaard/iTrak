<?php
session_start();
date_default_timezone_set('Asia/Manila');

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    // Store the submitted email in the session to retain it in the email field
    $_SESSION['login_email'] = $_POST['email'];
}

$email = isset($_SESSION['login_email']) ? $_SESSION['login_email'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTrak Login Portal</title>
    <link rel="icon" type="image/x-icon" href="./src/img/tab-logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./src/css/login.css">
</head>

<body>
    <div class="container-of-box">
        <div class="box1">
            <main>
                <div class="login-header">
                    <h1 style="font-weight: bold;">Welcome!</h1>
                    <h5>Login your account</h5>
                </div>
                <br>
                <form action="login.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12">
                                <b><label for="email" class="text-left">Email</label></b>
                                <input type="email" id="email" name="email" class="form-textbox" style="width: 100%; height: 40px;" placeholder="Email" required value="<?php echo htmlspecialchars(isset($_SESSION['login_email']) ? $_SESSION['login_email'] : ''); ?>">
                                <br>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <b><label for="password" class="text-left">Password</label></b>
                                <div class="password-container">
                                    <input type="password" name="password" id="password" class="form-textbox" style="width: 100%; height: 40px;" placeholder="Password" required>
                                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                                    <div class="forgot">
                                        </p>
                                        <p><a href="forgot_password.php">Forgot Password?</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="text-center">
                        <input type="submit" name="submit" value="LOGIN" class="custom-button">
                    </div>
                    </br>
                    <?php
                    if (isset($_SESSION['login_error']) && $_SESSION['login_error']) {
                        echo '<script>
                                Swal.fire({
                                    icon: "error",
                                    text: "You already timed out",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            </script>';
                        unset($_SESSION['login_error']);
                    }
                    if (isset($_SESSION['loginwrong_error']) && $_SESSION['loginwrong_error']) {
                        echo '<script>
                                Swal.fire({
                                    icon: "error",
                                    text: "Incorrect email or password",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            </script>';
                        unset($_SESSION['loginwrong_error']);
                    }
                    if (isset($_SESSION['loginTap_error']) && $_SESSION['loginTap_error']) {
                        echo '<script>
                                Swal.fire({
                                    icon: "warning",
                                    text: "It seems that you did not time in before logging in.",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            </script>';
                        unset($_SESSION['loginTap_error']);
                    }
                    ?>
                </form>
            </main>
        </div>
        <div class="box2">
            <a href="#" class="brand" title="logo">
                <i><img src="src/img/UpKeep.png" alt="" class="logo" /></i>
            </a>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // Toggle the eye icon class properly
            this.classList.toggle("bi-eye");
        });
    </script>
</body>

</html>