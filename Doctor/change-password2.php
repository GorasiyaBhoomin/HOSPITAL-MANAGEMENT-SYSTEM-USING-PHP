<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php"); // Redirect if not logged in
    exit;
}

$doctor_email = $_SESSION["doctor_email"];

// Fetch doctor info
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_pass = $_POST["old_pass"];
    $newpassword = $_POST["newpassword"];
    $confirm_password = $_POST["confirm_password"];

    if (!password_verify($old_pass, $doctor["password"])) {
        $error = "Old password is incorrect!";
    } elseif ($newpassword !== $confirm_password) {
        $error = "New password and confirm password do not match!";
    } elseif (strlen($newpassword) < 6) {
        $error = "New password must be at least 6 characters long!";
    } else {
        $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);
        $update_sql = "UPDATE doctors SET password = ? WHERE email = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $doctor_email);

        if ($update_stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Error updating password. Please try again.";
        }
    }
}


// Add logout handling
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy all session data
    header("Location: ../Doctor/doctorlogin.php");
    exit;
}

// Handle admin logout only
if(isset($_GET['logout'])) {
    // Store success message before unsetting admin session
    $_SESSION['logout_success'] = "Logged out successfully!";
    
    // Only unset admin-specific session variables
    unset($_SESSION['doctor_email']);
    unset($_SESSION['doctor_name']); // Changed from admin_username to match login
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    header("Location: doctorlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">
    <title>KK PATEL HOSPITAL</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery (Ensure it's included before Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="js/additional-methods.js"></script>

    <script>
    $(document).ready(function () {
    $("#changepass").validate({
        rules: {
            old_pass:{
                required: true
            },
            newpassword: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                equalTo: "[name='newpassword']"
            }
        },
        messages: {
            old_pass:{
                required: "Please enter a old password",

            },
            newpassword: {
                required: "Please enter a password",
                minlength: "Password must be at least 6 characters long"
            },
            confirm_password: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            },
            
        },
        errorElement: "span",
        errorPlacement: function (error, element) {
            if (element.attr("name") === "gender") {
                error.addClass("text-danger d-block").insertAfter("#gender-options");
            } else {
                error.addClass("text-danger").insertAfter(element);
            }
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

<body>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="main-wrapper">
<div class="header">
			<div class="header-left">
				<a href="index-2.php" class="logo">
                <img src="assets/img/logo3.png" width="35" height="35" alt=""> <span>KK PATEL HOSPITAL</span>
				</a>
			</div>
			<a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                
                <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                <span class="user-img"><img class="rounded-circle" src="../admin/<?php echo htmlspecialchars($doctor['avatar']); ?>" width="40" alt="Doctor">
                <span class="status online"></span></span>
                        <span><?php echo htmlspecialchars($doctor['username']); ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">My Profile</a>
                        <a class="dropdown-item" href="Change-password2.php">Change Password</a>
                        <a class="dropdown-item" href="?logout=1">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="Change-password2.php">Change Password</a>
                    <a class="dropdown-item" href="?logout=1">Logout</a>
                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="index-2.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
						
                        <li>
                        <a href="#"><i class="fa fa-columns"></i> <span>Patients</span> <span class="menu-arrow"></span></a>
                        <ul style="display: none;">
                        <li><a href="patient_visit.php"> Patient visit </a></li>
                        <li><a href="manage_visit.php"> Manage visit  </a></li>

                            </ul>
                        </li>
                        <li>
                            <a href="appointments-history.php"><i class="fa fa-calendar"></i> <span>Appointment History</span></a>
                        </li>
                        <!-- <li>
                            <a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedule</span></a>
                        </li> -->
                        
						
						
                       
						
                        
                     

                    </ul>
                </div>
            </div>
        </div>

<div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h4 class="page-title">Change Password</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                   

                    <form action="" id="changepass" method="POST">
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="old_pass" class="form-control" placeholder="Old Password">
                           
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="newpassword" class="form-control" placeholder="New Password">
                          
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
<!-- Update the name attribute for the password confirmation field -->
<input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                            
                        </div>
                        <div class="m-t-20 text-center">
                            <button class="btn btn-primary submit-btn">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/app.js"></script>

</body>


<!-- appointments23:20-->
</html>