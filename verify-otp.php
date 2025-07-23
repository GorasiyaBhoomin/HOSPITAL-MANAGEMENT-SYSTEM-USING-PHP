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

<title>KK PATEL HOSPITAL | Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
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
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .btn-custom {
            border-radius: 20px;
            background-color: #6a11cb;
            color: white;
        }

        .btn-custom:hover {
            background-color: #2575fc;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
<div class="container">

                    <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
                    
                    <form class="form-signin" action="" id="verify-otp" method="POST">
                    <div class="form-group">
                    <a href="index-2.php"><img src="assets/img/logo3.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Enter OTP sent to <?php echo isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : ''; ?></label>
                            <input type="text" name="otp" id="otp" class="form-control" maxlength="6">
                        </div>
                        <div class="form-group">
                        <button class="btn btn-primary account-btn" type="submit">Verify OTP</button>
                        </div>
                        <div class="form-group">
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