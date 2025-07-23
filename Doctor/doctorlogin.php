<?php
session_start();

// Check for logout success message
if(isset($_SESSION['logout_success'])) {
    $success = "Logged out successfully!";
    unset($_SESSION['logout_success']);
}

include_once("../Database/connection.php");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM doctors WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $doctor = $result->fetch_assoc();

            // Direct password comparison since password is not hashed in database
            if ($password === $doctor['password']) {
                $_SESSION["doctor_email"] = $doctor["email"];
                $_SESSION["doctor_name"] = $doctor["username"];
                
                // Set success message
                $_SESSION["login_success"] = "Doctor successfully logged in!";
                
                header("Location: index-2.php");
                exit;
            } else {
                $error = "Incorrect email or password!";
            }
        } else {
            $error = "Incorrect email or password!";
        }
        $stmt->close();
    } else {
        $error = "All fields are required!";
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
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">

    <title>KK PATEL HOSPITAL - Doctor Login</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

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
            font-size: 14px;
            margin-top: 5px;
            padding: 10px;
            background-color: #fff3f3;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            font-size: 14px;
            margin-top: 5px;
            padding: 10px;
            background-color: #f0fff0;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .login-hint {
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">
                    <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
                    <?php if (!empty($success)) echo "<p class='success-message'>$success</p>"; ?>

                    <form action="doctorlogin.php" id="login" method="POST" class="form-signin">
                        <div class="account-logo">
                            <a href="index.php"><img src="assets/img/logo3.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
