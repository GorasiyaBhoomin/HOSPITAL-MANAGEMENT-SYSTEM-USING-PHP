<?php
session_start();
include_once("Database/connection.php");

// Check if user is already logged in
if(isset($_SESSION['user_email'])) {
    header("Location: User/index.php");
    exit();
}

// Check if logged out
if(isset($_SESSION['logout_success'])) {
    $success = $_SESSION['logout_success'];
    unset($_SESSION['logout_success']); // Clear the session message after displaying
}

// Check if redirected from activation link
if(isset($_GET['code']) && isset($_GET['email'])) {
    $activation_code = $_GET['code'];
    $email = $_GET['email'];
    
    // Verify activation code
    $sql = "SELECT u.*, a.activation_code, a.expires_at 
            FROM users u 
            LEFT JOIN activation a ON u.id = a.user_id 
            WHERE u.email = ? AND a.activation_code = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $email, $activation_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if activation code is expired
        if(strtotime($user['expires_at']) > time()) {
            // Activate the account
            $update_sql = "UPDATE users SET status = 'active' WHERE email = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("s", $email);
            
            if($update_stmt->execute()) {
                // Remove activation code
                $delete_sql = "DELETE FROM activation WHERE user_id = ?";
                $delete_stmt = $con->prepare($delete_sql);
                $delete_stmt->bind_param("i", $user['id']);
                $delete_stmt->execute();
                
                $success = "Your account has been successfully activated. You can now login.";
            } else {
                $error = "Error activating account. Please try again.";
            }
        } else {
            $error = "Activation link has expired. Please contact support.";
        }
    } else {
        $error = "Invalid activation link.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Check if user exists
    $sql = "SELECT u.*, a.activation_code, a.expires_at 
            FROM users u 
            LEFT JOIN activation a ON u.id = a.user_id 
            WHERE u.email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Check if account is activated
            if ($user['status'] === 'inactive') {
                if ($user['activation_code'] && strtotime($user['expires_at']) > time()) {
                    $error = "Please activate your account first. Check your email for activation link.";
                } else {
                    $error = "Your account is not activated. Please contact support.";
                }
            } else {
                // User is active, allow login
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                
                header("Location: User/index.php");
                exit;
            }
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
