<?php
session_start();
include_once("../Database/connection.php");
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}
// Fetch patient details if 'id' is provided
$patient_id = isset($_GET['id']) ? $_GET['id'] : null;
$patient = null;

if ($patient_id) {
    $query = "SELECT * FROM patients WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating patient details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_patient'])) {
    if (!isset($_POST['patient_id'])) {
        $_SESSION['error'] = "Patient ID is missing.";
        header("Location: edit-patient.php?id=" . $patient_id);
        exit();
    }

    $patient_id = $_POST['patient_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];

    // Update query
    $update = "UPDATE patients SET 
        username = ?, email = ?, dob = ?, gender = ?, address = ?, phone = ?, status = ? 
        WHERE id = ?";
    
    $stmt = $con->prepare($update);
    if (!$stmt) {
        $_SESSION['error'] = "Error preparing statement: " . $con->error;
        header("Location: edit-patient.php?id=" . $patient_id);
        exit();
    }

    $stmt->bind_param("sssssssi", $username, $email, $dob, $gender, $address, $phone, $status, $patient_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Patient details updated successfully.";
        header("Location: patients.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating patient details: " . $stmt->error;
        header("Location: edit-patient.php?id=" . $patient_id);
        exit();
    }
    
    $stmt->close();
}
// Fetch admin data from database
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
        return this.optional(element) || (element.files[0] && element.files[0].type.match(/^image\/(jpeg|png|jpg)$/));
    }, "Only JPG, JPEG, and PNG files are allowed");

            $("#edit-patient").validate({
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
            dob: {
                required: true,
                date: true
            },
            gender: {
                required: true
            },
            
            address: {
                required: true,
                minlength: 5
            },
          
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            avatar: {
                required: true,
                extension: "png|jpeg|jpg"

            },
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
            required: true,
            equalTo: "[name='password']"
            }

            
        },
        messages: {
            username: {
                required: "Please enter a username",
                lettersonly: "Only letters and spaces are allowed",

                minlength: "Username must be at least 3 characters long"
            },
            email: {
                required: "Please enter an email",
                email: "Enter a valid email address"
            },
            dob: {
                required: "Please enter your date of birth",
                date: "Please enter a valid date"
            },
            gender: {
                required: "Please select a gender"
            },
            departmentname: {
                required: "Please select a department"
            },
            address: {
                required: "Please enter your address",
                minlength: "Address must be at least 5 characters long"
            },
            city: {
                required: "Please enter your city"
            },
            postal_code: {
                required: "Please enter a postal code",
                digits: "Only numeric values are allowed"
            },
            cv: {
                required: "Please upload your CV",
                extension: "Only PDF, DOC, or DOCX files are allowed"
            },
            phone: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },
            avatar: {
                required: "Please upload an image",
                extension: "Only PNG, JPEG, JPG files are allowed"
            },
            password: {
                required: "Please enter a password",
                minlength: "Password must be at least 6 characters long"
            },
            confirm_password: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            },
            Consultancy_fees: {
                required: "Please enter the consultancy fees",
                number: "Please enter a valid numeric amount"
            },
            Bio: {
                required: "Please enter a short biography",
                minlength: "Biography must be at least 10 characters long"
            }
        },
     
        errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "gender") {
                        error.addClass("text-danger d-block").insertAfter("#gender-options");
                    } else {
                        error.addClass("text-danger").insertAfter(element);
                    }
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
                <h4 class="page-title">Edit Patient</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
            <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
                <form action="" id="edit-patient" method="POST"  enctype="multipart/form-data">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

                    <div class="row">
                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="username" value="<?php echo $patient['username']; ?>" placeholder="Enter username">
                               
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="<?php echo $patient['email']; ?>" placeholder="Enter email">
                               
                            </div>
                            
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                    <input type="date" class="form-control datetimepicker" name="dob" value="<?php echo $patient['dob']; ?>" placeholder="Enter Date of Birth">
                                  
                            </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                    <label>Gender <span class="text-danger">*</span></label><br>
                    <input type="radio" name="gender" value="male" <?php echo ($patient['gender'] == 'male') ? 'checked' : ''; ?>> Male
                    <input type="radio" name="gender" value="female" <?php echo ($patient['gender'] == 'female') ? 'checked' : ''; ?>> Female
                </div>
</div>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" value="<?php echo $patient['address']; ?>" placeholder="Enter address">
                                        
                                    </div>
                                    
                                </div>
                              
                                
                               
                                
                              


                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="phone" value="<?php echo $patient['phone']; ?>" placeholder="Enter phone number">
                               
                            </div>
                            
                        </div>
                       

  
                       

                    </div>
                    <div class="form-group">
                    <label>Status</label><br>
                    <input type="radio" name="status" value="active" <?php echo ($patient['status'] == 'active') ? 'checked' : ''; ?>> Active
                    <input type="radio" name="status" value="inactive" <?php echo ($patient['status'] == 'inactive') ? 'checked' : ''; ?>> Inactive
                </div>
                    
                   
                    <div class="m-t-20 text-center">
                    <button type="submit" name="update_patient" class="btn btn-primary">Edit Patient</button>
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



<!-- add-doctor24:06-->
</html>
