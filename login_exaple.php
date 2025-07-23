<?php
session_start();
include_once("Database/connection.php");

// Check if user is already logged in
if(isset($_SESSION['user_email'])) {
    header("Location: User/index.php");
    exit();
}

// Check if logged out
if(isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success = "Successfully logged out.";
}

// Check if redirected from activation and not already logged in
if(!isset($_SESSION['user_email']) && isset($_GET['activated']) && $_GET['activated'] == 'true') {
    $success = "Your account has been successfully activated. You can now login.";
}

// Only check activation code if not already logged in
if(!isset($_SESSION['user_email']) && isset($_GET['code']) && isset($_GET['email'])) {
    $activation_code = $_GET['code'];
    $email = $_GET['email'];
    
    // Verify activation code
    $sql = "SELECT * FROM users WHERE email = ? AND activation_code = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $email, $activation_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        // Activate the account
        $update_sql = "UPDATE users SET is_active = 1, activation_code = NULL, status = 'active' WHERE email = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("s", $email);
        
        if($update_stmt->execute()) {
            $success = "Your account has been successfully activated. You can now login.";
        } else {
            $error = "Error activating account. Please try again.";
        }
    } else {
        $error = "Invalid activation link.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Check if user exists and is active
    $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // User is active, allow login
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['username'];
            
            // Update status to active
            $update_sql = "UPDATE users SET status = 'active' WHERE email = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("s", $email);
            $update_stmt->execute();
            
            header("Location: User/index.php");
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>KK PATEL HOSPITAL</title>
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
        $("#login").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                email: {
                    required: "Please enter your email",
                    email: "Enter a valid email address"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 6 characters long"
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
        .success-message {
            color: green;
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
                    <form action="login.php" id="login" method="POST" class="form-signin">
                        <div class="account-logo">
                            <a href="index-2.php"><img src="assets2/img/logo3.png" alt=""></a>
                        </div>
                        <?php if(isset($error)): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if(isset($success)): ?>
                            <div class="success-message"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Login</button>
                        </div>
                        <div class="form-group text-right">
                            <a href="forgot-password.php">Forgot your password?</a>
                        </div>
                        <div class="text-center register-link">
                            Don't have an account? <a href="register.php">Register Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
