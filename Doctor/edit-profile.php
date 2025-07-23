<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php");
    exit;
}

$doctor_email = $_SESSION['doctor_email'];
$error = "";
$success = "";

// Fetch current doctor data
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor_data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctorName = trim($_POST['doctorName']);
    $clinicAddress = trim($_POST['clinicAddress']);
    $consultancyFees = trim($_POST['consultancyFees']);
    $contactNo = trim($_POST['contactNo']);

    // Handle file upload
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['avatar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $temp = $_FILES['avatar']['tmp_name'];
            $upload_path = "../admin/uploads/Doctors/" . $filename; // Changed path to match admin directory
            
            if(move_uploaded_file($temp, $upload_path)) {
                $sql = "UPDATE doctors SET username = ?, address = ?, consultancy_fees = ?, phone = ?, avatar = ? WHERE email = ?";
                $stmt = $con->prepare($sql);
                if($stmt === false) {
                    error_log("Prepare failed: " . $con->error);
                    $error = "Database error occurred";
                } else {
                    $stmt->bind_param("ssssss", $doctorName, $clinicAddress, $consultancyFees, $contactNo, $filename, $doctor_email);
                }
            } else {
                $error = "Failed to upload image. Please try again.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG and PNG allowed.";
        }
    } else {
        $sql = "UPDATE doctors SET username = ?, address = ?, consultancy_fees = ?, phone = ? WHERE email = ?";
        $stmt = $con->prepare($sql);
        if($stmt === false) {
            error_log("Prepare failed: " . $con->error);
            $error = "Database error occurred";
        } else {
            $stmt->bind_param("sssss", $doctorName, $clinicAddress, $consultancyFees, $contactNo, $doctor_email);
        }
    }

    if(empty($error) && isset($stmt)) {
        if($stmt->execute()) {
            $success = "Profile updated successfully!";
            
            $sql = "SELECT * FROM doctors WHERE email = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $doctor_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor_data = $result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
    }
}

// Fetch doctor data from database
$doctor_email = $_SESSION['doctor_email'];
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if(isset($_GET['logout'])) {
    $_SESSION['logout_success'] = "Logged out successfully!";
    unset($_SESSION['doctor_email']);
    unset($_SESSION['doctor_name']);
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
        }, "Only letters and spaces are allowed");   
        
        
         $("#editdoctor-profile").validate({
        rules: {
            doctorName: {
                required: true,
                minlength: 3,
                lettersonly: true,  // Using correct validation method

            },
            email: {
                required: true,
                email: true
            },
            
            specialization: {
                required: true
            },
            clinicAddress: {
                required: true,
                minlength: 5
            },
            contactNo: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            
            Consultancy_fees: {
                required: true,
                number: true
            }
        },
        messages: {
            doctorName: {
                required: "Please enter a username",
                minlength: "Username must be at least 3 characters long",
                lettersonly: "Only letters and spaces are allowed"

            },
            email: {
                required: "Please enter an email",
                email: "Enter a valid email address"
            },
            specialization: {
                required: "Please select a department"
            },
            clinicAddress: {
                required: "Please enter your address",
                minlength: "Address must be at least 5 characters long"
            },
         
            contactNo: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },
           
            Consultancy_fees: {
                required: "Please enter the consultancy fees",
                number: "Please enter a valid numeric amount"
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
                       

                    </ul>
                </div>
            </div>
        </div>


        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

                    <div class="col-sm-12">
                        <h4 class="page-title">Edit Profile</h4>
                    </div>
                </div>
                <form action="" id="editdoctor-profile" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="specialization">Doctor Specialization</label>
        <input type="text" id="specialization" name="specialization" class="form-control" value="<?php echo htmlspecialchars($doctor_data['departmentname'] ?? ''); ?>" readonly>
    </div>
    <div class="form-group">
        <label for="email">Doctor Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($doctor_data['email'] ?? ''); ?>" readonly>
    </div>

    <div class="form-group">
        <label for="doctorName">Doctor Name</label>
        <input type="text" id="doctorName" name="doctorName" class="form-control" value="<?php echo htmlspecialchars($doctor_data['username'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="clinicAddress">Doctor Clinic Address</label>
        <textarea id="clinicAddress" name="clinicAddress" class="form-control"><?php echo htmlspecialchars($doctor_data['address'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
        <label for="consultancyFees">Doctor Consultancy Fees</label>
        <input type="number" id="consultancyFees" name="consultancyFees" class="form-control" value="<?php echo htmlspecialchars($doctor_data['consultancy_fees'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="contactNo">Doctor Contact No</label>
        <input type="tel" id="contactNo" name="contactNo" class="form-control" value="<?php echo htmlspecialchars($doctor_data['phone'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="avatar">Doctor Avatar</label>
        <input type="file" id="avatar" name="avatar" class="form-control">
    <?php if(isset($doctor_data['avatar']) && !empty($doctor_data['avatar'])): ?>
        <img src="../admin/<?php echo htmlspecialchars($doctor_data['avatar']); ?>" alt="Current Avatar" style="max-width: 100px; margin-top: 10px;">
    <?php endif; ?>
    </div>

  
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>


            </div>
             </div>
    </div>
    <script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/app.js"></script>

    
</body>


<!-- edit-profile23:05-->
</html>