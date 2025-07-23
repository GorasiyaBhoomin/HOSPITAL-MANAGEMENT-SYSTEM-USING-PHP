<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php"); // Redirect if not logged in
    exit;
}
// Fetch doctor data from database
$doctor_email = $_SESSION['doctor_email'];
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
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
        // Custom validation method for allowing only letters and spaces in Username
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed");    $("#add-patient").validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                lettersonly: true  

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
            departmentname: {
                required: true
            },
            address: {
                required: true,
                minlength: 5
            },
            city: {
                required: true
            },
            postal_code: {
                required: true,
                digits: true
            },
            cv: {
                required: true,
                extension: "pdf|doc|docx"
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            avatar: {
                required: true,
                extension: "png|jpeg|jpg|gif"

            },
            password: {
                required: true,
                minlength: 6
            },
            age: {
                required: true,
                number: true,
                min: 1,
                max: 120
            },
            Confirm_password: {
                required: true,
                equalTo: "#password"
            },
            Consultancy_fees: {
                required: true,
                number: true
            },
            message: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            username: {
                required: "Please enter a username",
                minlength: "Username must be at least 3 characters long",
                lettersonly: "Only letters and spaces are allowed"

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
         
            phone: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },
           
            Consultancy_fees: {
                required: "Please enter the consultancy fees",
                number: "Please enter a valid numeric amount"
            },
           
            age: {
                required: "Please enter your age",
                number: "Please enter a valid number",
                min: "Age must be at least 1 year",
                max: "Age must be less than 120 years"
            },
            message: {
                required: "Please enter a short biography",
                minlength: "Biography must be at least 10 characters long"
            }
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
</head>

<body>
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
                <h4 class="page-title">Edit Patient</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
             
                <form action="" id="add-patient" method="POST"  enctype="multipart/form-data">
                    <div class="row">
                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="username" value="Anand" placeholder="Enter username">
                               
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="Anand@gmail.com" placeholder="Enter email">
                               
                            </div>
                            
                        </div>
                     
                        <div class="col-sm-6">
    <div class="form-group gender-select">
        <label class="gen-label">Gender:<span class="text-danger">*</span></label>
        <div id="gender-options">
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" name="gender" value="male" class="form-check-input" id="male" readonly> Male
                </label>
            </div>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" name="gender" value="female" class="form-check-input" id="female"> Female
                </label>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6">
    <div class="form-group">
        <label>Age <span class="text-danger">*</span></label>
        <input class="form-control" type="number" name="age" value="19" placeholder="Enter age">
    </div>
</div>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" value="asfjjhgh" placeholder="Enter address">
                                        
                                    </div>
                                    
                                </div>
                              
                                
                               
                                
                              


                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="phone" value="4756893478" placeholder="Enter phone number">
                               
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                    <div class="form-group">
                    <label>Problem Facing<span class="text-danger">*</span></label>
                    <textarea class="form-control" rows="3" cols="30" name="message" value="bnfjfgjfgghjfgjjfg"></textarea>
                   
                    
                </div>
                    
                </div>

                <div class="form-group">
                                <label class="display-block">Status</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="status" id="product_active" value="option1">
									<label class="form-check-label" for="product_active">
									Approved
									</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="status" id="product_inactive" value="option2">
									<label class="form-check-label" for="product_inactive">
									Delete
									</label>
								</div>
                            </div>
                        

                       

                       
                       

                    </div>
                   
                    
                   
                    <div class="m-t-20 text-center">
                    <button type="submit" class="btn btn-primary submit-btn">Edit Patient</button>
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
