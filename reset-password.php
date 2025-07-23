<?php
session_start();
include_once("Database/connection.php");

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["password"]);
    $email = $_SESSION['reset_email'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password=? WHERE email=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Clear all session variables
            session_destroy();
            $success = "Password successfully updated! Redirecting to login page...";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                  </script>";
        } else {
            $error = "Something went wrong!";
        }
        $stmt->close();
    } else {
        $error = "Password cannot be empty!";
    }
}

if ($con) {
    $con->close();
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>KK PATEL HOSPITAL | Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        
        .success {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Reset Password</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

        <form method="post">
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="New Password" required>
            </div>
            <button type="submit" class="btn btn-custom btn-block">Update Password</button>
        </form>
    </div>

</body>

</html>