<?php
session_start();
include_once("Database/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!empty($email)) {
        // Check if email exists in users table
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Generate 6 digit OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in session
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_email'] = $email;
            $_SESSION['otp_timestamp'] = time();

            // Create PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'gorasiyabhoomin@gmail.com';
                $mail->Password = 'rvlf jtwu oeyx ouka';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('gorasiyabhoomin@gmail.com', 'KK Patel Hospital');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "
                    <html>
                    <body>
                        <h2>Password Reset Request</h2>
                        <p>Your OTP for password reset is: <strong>{$otp}</strong></p>
                        <p>This OTP will expire in 10 minutes.</p>
                        <p>If you did not request this password reset, please ignore this email.</p>
                        <br>
                        <p>Best regards,</p>
                        <p>KK Patel Hospital Team</p>
                    </body>
                    </html>
                ";

                $mail->send();
                header("Location: verify-otp.php");
                exit;
            } catch (Exception $e) {
                $error = "Failed to send OTP. Please try again. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Email not found in our records!";
        }
        $stmt->close();
    } else {
        $error = "Email is required!";
    }
}

if ($con) {
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>KK PATEL HOSPITAL - Forgot Password</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets2/img/logo3.png">

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets2/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets2/assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets2/assets/css/style.css">

    <!-- jQuery & Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
    $(document).ready(function () {
        $("#forgot-pass").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "Please enter your email",
                    email: "Enter a valid email address"
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
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="forgot-pass" method="POST" class="form-signin">
                        <div class="account-logo">
                            <a href="login.php"><img src="assets2/img/logo3.png" alt=""></a>
                        </div>
                        <?php if(isset($error)): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Request Password Reset</button>
                        </div>
                        <div class="text-center register-link">
                            Remember your password? <a href="login.php">Login Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
