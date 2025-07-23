<?php
session_start();

if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email']) || !isset($_SESSION['otp_timestamp'])) {
    header("Location: forgot-password.php");
    exit;
}

$error = "";

// Check if OTP is expired (10 minutes)
if (time() - $_SESSION['otp_timestamp'] > 600) {
    unset($_SESSION['reset_otp']);
    unset($_SESSION['reset_email']); 
    unset($_SESSION['otp_timestamp']);
    header("Location: forgot-password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_entered = trim($_POST["otp"]);

    if ($otp_entered == $_SESSION['reset_otp']) {
        header("Location: reset-password.php");
        exit;
    } else {
        $error = "Invalid OTP! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">

    <title>KK PATEL HOSPITAL - Verify OTP</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <!-- jQuery & Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
    $(document).ready(function () {
        $("#verify-otp").validate({
            rules: {
                otp: {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                    digits: true
                }
            },
            messages: {
                otp: {
                    required: "Please enter the OTP",
                    minlength: "OTP must be 6 digits",
                    maxlength: "OTP must be 6 digits",
                    digits: "OTP must contain only numbers"
                }
            },
            errorElement: "span",
            errorClass: "text-danger",
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            }
        });
    });
    </script>

    <style>
        .is-invalid {
            border: 2px solid red;
        }
        .is-valid {
            border: 2px solid green;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">
                    <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
                    
                    <form class="form-signin" action="" id="verify-otp" method="POST">
                        <div class="account-logo">
                            <a href="index-2.php"><img src="assets/img/logo3.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Enter OTP sent to <?php echo isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : ''; ?></label>
                            <input type="text" name="otp" id="otp" class="form-control" maxlength="6">
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary account-btn" type="submit">Verify OTP</button>
                        </div>
                        <div class="text-center register-link">
                            <a href="forgot-password.php">Back to Forgot Password</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>