<?php
session_start();
include_once("../Database/connection.php");

if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];
$error = "";
$success = "";

// Fetch current admin data
$sql = "SELECT * FROM admin_data WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']); 
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);

    // Debug output
    error_log("Form Data:");
    error_log("Username: " . $username);
    error_log("Address: " . $address);
    error_log("City: " . $city);
    error_log("Postal Code: " . $postal_code);
    error_log("Phone: " . $phone);

    // Handle file upload
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['logo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $temp = $_FILES['logo']['tmp_name'];
            $new_filename = "logo_" . time() . "." . $filetype;
            $upload_path = "assets/img/" . $new_filename;
            
            if(move_uploaded_file($temp, $upload_path)) {
                // Update with new logo
                $sql = "UPDATE admin_data SET username = ?, address = ?, city = ?, postal_code = ?, phone = ?, logo = ? WHERE email = ?";
                $stmt = $con->prepare($sql);
                if($stmt === false) {
                    error_log("Prepare failed: " . $con->error);
                    $error = "Database error occurred";
                } else {
                    $stmt->bind_param("sssssss", $username, $address, $city, $postal_code, $phone, $new_filename, $admin_email);
                }
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG and PNG allowed.";
        }
    } else {
        // Update without logo
        $sql = "UPDATE admin_data SET username = ?, address = ?, city = ?, postal_code = ?, phone = ? WHERE email = ?";
        $stmt = $con->prepare($sql);
        if($stmt === false) {
            error_log("Prepare failed: " . $con->error);
            $error = "Database error occurred";
        } else {
            $stmt->bind_param("ssssss", $username, $address, $city, $postal_code, $phone, $admin_email);
        }
    }

    if(empty($error) && isset($stmt)) {
        if($stmt->execute()) {
            error_log("Update successful");
            $success = "Profile updated successfully!";
            
            // Refresh admin data after update
            $sql = "SELECT * FROM admin_data WHERE email = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $admin_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin_data = $result->fetch_assoc();
            
            // Debug output
            error_log("Updated Data:");
            error_log(print_r($admin_data, true));
        } else {
            error_log("Execute failed: " . $stmt->error);
            $error = "Error updating profile: " . $stmt->error;
        }
    }
}

// Handle admin logout only
if(isset($_GET['logout'])) {
    // Store success message before unsetting admin session
    $_SESSION['logout_success'] = "Logged out successfully!";
    
    // Only unset admin-specific session variables
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_name']); // Changed from admin_username to match login
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    header("Location: kkadminlogin.php");
    exit();
}

// Fetch admin data from database
$admin_email = $_SESSION['admin_email'];
$sql = "SELECT * FROM admin_data WHERE email = ?"; // Changed to query by email instead of id
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_email); // Changed parameter type to string for email
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

// If admin data not found, redirect to login
if (!$admin_data) {
    header("Location: kkadminlogin.php");
    exit;
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
        // Custom validation method for allowing only letters and spaces in Username
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed"); 

        $.validator.addMethod("imagefile", function(value, element) {
            if(element.files.length > 0) {
                return element.files[0].type.match(/^image\/(jpeg|png|jpg)$/);
            }
            return true;
        }, "Only JPG, JPEG, and PNG files are allowed");

        $("#admin-edit").validate({
            rules: {
                name: {
                    required: true,
                    lettersonly: true,
                    minlength: 3
                },
                city: {
                    required: true,
                    lettersonly: true
                },
                postal_code: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                logo: {
                    imagefile: true
                }
            },
            messages: {
                name: {
                    required: "Please enter your username",
                    lettersonly: "Only letters and spaces are allowed",
                    minlength: "Username must be at least 3 characters"
                },
                city: {
                    required: "Please enter your city",
                    lettersonly: "City name can only contain letters"
                },
                postal_code: {
                    required: "Please enter postal code",
                    digits: "Only numbers are allowed",
                    minlength: "Postal code must be 6 digits",
                    maxlength: "Postal code must be 6 digits"
                },
                phone: {
                    required: "Please enter your phone number",
                    digits: "Only numbers are allowed",
                    minlength: "Phone number must be 10 digits",
                    maxlength: "Phone number must be 10 digits"
                }
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass("text-danger").insertAfter(element);
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
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
    .success-message {
        color: green;
        font-size: 14px;
        margin-top: 5px;
    }
    .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }
</style>
</head>

<body>
<div class="main-wrapper">
<div class="header">
			<div class="header-left">
				<a href="index-2.php" class="logo">
                <img src="assets/img/<?php echo $admin_data['logo']; ?>" width="35" height="35" alt=""> <span>KK PATEL HOSPITAL</span>
				</a>
			</div>
			<a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/<?php echo $admin_data['logo']; ?>" width="40" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span><?php echo htmlspecialchars($admin_data['username']); ?></span>
                    </a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="profile.php">My Profile</a>
						<a class="dropdown-item" href="change-password2.php">Change Password</a>
                        <a class="dropdown-item" href="?logout=1">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="change-password2.php">Change Password</a>
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
                            <a href="doctors.php"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
                        </li>
                        <li>
                            <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a>
                        </li>
                        <li>
                            <a href="premise.php"><i class="fa fa-building"></i> <span>Premise</span></a>
                        </li>
                        <li>
                            <a href="expenses.php"><i class="fa fa-money"></i> <span>Expenses</span></a>
                        </li>
                        <li>
                            <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                        </li>
                        <li>
                            <a href="departments.php"><i class="fa fa-hospital-o"></i> <span>Departments</span></a>
                        </li>
                        <li>
                            <a href="feedback.php"><i class="fa fa-comment-o"></i> <span>Feedback</span></a>
                        </li>
                        <li>
                            <a href="press-desk.php"><i class="fa fa-newspaper-o"></i> <span>Press-desk</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Edit Profile</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <?php if (!empty($success)) echo "<p class='success-message'>$success</p>"; ?>
                        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
                        
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="admin-edit" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($admin_data['username']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($admin_data['address']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-3">
                                            <div class="form-group">
                                                <label>City<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($admin_data['city']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-3">
                                            <div class="form-group">
                                                <label>Postal Code<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="postal_code" value="<?php echo htmlspecialchars($admin_data['postal_code']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($admin_data['phone']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Change your Logo</label>
                                        <div class="profile-upload">
                                            <div class="upload-img">
                                                <img alt="" src="assets/img/<?php echo $admin_data['logo']; ?>">
                                            </div>
                                            <div class="upload-input">
                                                <input type="file" class="form-control" name="logo">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>