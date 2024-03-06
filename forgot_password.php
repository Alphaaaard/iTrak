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
    <link rel="stylesheet" href="./src/css/forgot.css">
    <link rel="stylesheet" href="./src/css/main.css">
</head>

<body>
    <div class="container-of-box">
        <div class="box1">
            <main>
                <div class="login-header">
                    <h1 style="font-weight: bold;">Forgot your password?</h1>
                    <h5>Enter the email address associated with your account and we'll send you a link to reset your password.</h5>
                </div>
                <form id="password-reset-form" action="send_reset_link.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="email" id="email" name="email" class="form-textbox" placeholder="Email" required>
                    </div>

                </form>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="button" id="continue-button" class="custom-button">Continue</button>
                </div>
                <div class="back-to-login">
                    <a href="index.php">Back To Login</a>
                </div>
            </main>
        </div>
        <div class="box2">
            <a href="#" class="brand" title="UpKeep">
                <img src="src/img/UpKeep.png" alt="UpKeep Logo" class="logo" />
            </a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const continueButton = document.getElementById('continue-button');
            const form = document.getElementById('password-reset-form');
            const emailInput = document.getElementById('email');

            function validateEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }

            continueButton.addEventListener('click', function() {
                if (!validateEmail(emailInput.value)) {
                    Swal.fire('', 'Please enter a valid email address.', 'error');
                    return false;
                }

                Swal.fire({
                    text: 'A password reset message will be sent to your email address. Do you want to continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(form);
                        fetch('send_reset_link.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('', data.message, 'success').then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'index.php'; // Redirect to login page
                                        }
                                    });
                                } else {
                                    Swal.fire('', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('', 'An error occurred. Please try again.', 'error');
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>