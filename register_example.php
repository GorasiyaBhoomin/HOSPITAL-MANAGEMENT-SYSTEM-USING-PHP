<?php
    session_start();
    include_once("Database/connection.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $email = mysqli_real_escape_string($con, $_POST['email']); 
        $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
        $dob = mysqli_real_escape_string($con, $_POST['dob']);
        $gender = mysqli_real_escape_string($con, $_POST['gender']);
        $address = mysqli_real_escape_string($con, $_POST['address']);
        $password = mysqli_real_escape_string($con, $_POST['password']);

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate activation token
        $activation_token = bin2hex(random_bytes(32));

        // Check if email already exists
        $check_email = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            echo "<script>alert('Email already exists!'); window.location.href='register.php';</script>";
        } else {
            // Insert user data with activation token and status
            $sql = "INSERT INTO users (username, email, mobile, dob, gender, address, password, activation_code, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $con->prepare($sql);
            $is_active = 0;
            $stmt->bind_param("ssssssssi", $username, $email, $mobile, $dob, $gender, $address, $hashed_password, $activation_token, $is_active);

            if($stmt->execute()) {
                // Create activation link with full path
                $activation_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/login.php?code=" . $activation_token . "&email=" . urlencode($email);

                // Create PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'gorasiyabhoomin@gmail.com'; // Your Gmail
                    $mail->Password = 'rvlf jtwu oeyx ouka'; // Your Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('gorasiyabhoomin@gmail.com', 'KK Patel Hospital');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Account Registration - KK Patel Hospital';
                    $mail->Body = "
                        <html>
                        <body>
                            <h2>Welcome to KK Patel Hospital!</h2>
                            <p>Your account has been created successfully.</p>
                            <p><strong>Username:</strong> {$username}</p>
                            <p><strong>password:</strong> {$password}</p>
                            
                            <p>To activate your account, please click on the following link:</p>
                            <p><a href='{$activation_link}'>Click here to activate your account</a></p>
                            <p>If the link doesn't work, copy and paste this URL in your browser:</p>
                            <p>{$activation_link}</p>
                            <p>Please note: You won't be able to login until you activate your account.</p>
                            <br>
                            <p>Best regards,</p>
                            <p>KK Patel Hospital Team</p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    echo "<script>
                        alert('Registration successful! Please check your email to activate your account.');
                        window.location.replace('login.php');
                    </script>";
                    exit();
                } catch (Exception $e) {
                    echo "<script>alert('Failed to send activation email. Please try again.'); window.location.href='register.php';</script>";
                }
            } else {
                echo "<script>alert('Something went wrong. Please try again.'); window.location.href='register.php';</script>";
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
        <title>KK PATEL HOSPITAL</title>

        <link rel="shortcut icon" type="image/x-icon" href="assets2/img/logo3.png">

        <link rel="stylesheet" type="text/css" href="assets2/assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets2/assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets2/assets/css/style.css">

        <!-- jQuery & jQuery Validation -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

        <script>
        $(document).ready(function () {
            // Custom validation method for allowing only letters and spaces in Username
            $.validator.addMethod("lettersonly", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
            }, "Only letters and spaces are allowed");

            $("#register").validate({
                rules: {
                    username: {
                        required: true,
                        lettersonly: true,  // Using correct validation method
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    dob: {
                    required: true,
                    date: true,
                    max: function() {
                        return new Date().toISOString().split('T')[0];
                    },
                    min: "1900-01-01"
                },
                    gender: {
                        required: true
                    },
                    address: {
                        required: true,
                        minlength: 5
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    conf_password: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    username: {
                        required: "Please enter your username",
                        lettersonly: "Only letters and spaces are allowed",
                        minlength: "Username must be at least 3 characters"
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Enter a valid email address"
                    },
                    mobile: {
                        required: "Please enter your mobile number",
                        digits: "Only numbers are allowed",
                        minlength: "Mobile number must be 10 digits",
                        maxlength: "Mobile number must be 10 digits"
                    },
                    dob: {
                        required: "Please enter your date of birth"
                    },
                    gender: {
                        required: "Please select your gender"
                    },
                    address: {
                        required: "Please enter your address",
                        minlength: "Address must be at least 5 characters"
                    },
                    password: {
                        required: "Please enter a password",
                        minlength: "Password must be at least 6 characters"
                    },
                    conf_password: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
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
        </style>
    </head>
    <body>
        <div class="main-wrapper account-wrapper">
            <div class="account-page">
                <div class="account-center">
                    <div class="account-box">
                        <form action="register.php" method="POST" id="register" class="form-signin">
                            <div class="account-logo">
                                <a href="index.php"><img src="assets2/img/logo3.png" alt=""></a>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" id="conf-password" name="conf_password" class="form-control">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Signup</button>
                            </div>
                            <div class="text-center login-link">
                                Already have an account? <a href="login.php">Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="assets2/assets/js/popper.min.js"></script>
        <script src="assets2/assets/js/bootstrap.min.js"></script>
        <script src="assets2/assets/js/app.js"></script>
    </body>
    </html>
