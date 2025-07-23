<?php

session_start();
include_once("../Database/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

// Handle admin logout only
if(isset($_GET['logout'])) {
    // Store success message before unsetting admin session
    $_SESSION['logout_success'] = "Logged out successfully!";
    
    // Only unset admin-specific session variables
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']); // Changed from admin_username to match login
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    header("Location: ../login.php");
    exit();
}

// Fetch admin data from database
$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email = ?"; // Changed to query by email instead of id
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email); // Changed parameter type to string for email
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// If admin data not found, redirect to login
if (!$user_data) {
    header("Location: ../login.php");
    exit;
}


// Fetch user data
$email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    // Update user data
    $update_sql = "UPDATE users SET username=?, email=?, dob=?, gender=?, address=?, mobile=? WHERE email=?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("sssssss", $username, $email, $dob, $gender, $address, $phone, $_SESSION['user_email']);
    
    if ($update_stmt->execute()) {
        // Update session email if email was changed
        $_SESSION['user_email'] = $email;
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: myprofile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile!";
        header("Location: myprofile.php");
        exit();
    }
}


?>


<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="css/nice-select.css">
    <link rel="stylesheet" type="text/css" href="css/flaticon.css">
    <link rel="stylesheet" type="text/css" href="css/gijgo.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/slicknav.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- jQuery first, then other JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize form validation
    $("#edit-profile-user").validate({
        // Validation rules
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
                minlength: 5,
                maxlength: 200
            },
            phone: {
                required: true,
                minlength: 10,
                maxlength: 15,
                pattern: /^[0-9+\-\s()]{10,15}$/
            }
        },
        // Custom error messages
        messages: {
            username: {
                required: "Username is required",
                minlength: "Username must be at least 3 characters",
                pattern: "Only letters, numbers and underscore allowed"
            },
            email: {
                required: "Email is required",
                email: "Please enter a valid email"
            },
            dob: {
                required: "Date of birth is required",
                date: "Please enter a valid date",
                max: "Date cannot be in the future",
                min: "Date must be after 1900"
            },
            gender: {
                required: "Please select a gender"
            },
            address: {
                required: "Address is required",
                minlength: "Address must be at least 5 characters",
                maxlength: "Address cannot exceed 200 characters"
            },
            phone: {
                required: "Phone number is required", 
                minlength: "Phone must be at least 10 digits",
                maxlength: "Phone cannot exceed 15 digits",
                pattern: "Please enter a valid phone number"
            }
        },
        // Error placement
        errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            error.insertAfter(element);
        },
        // Highlight invalid fields
        highlight: function(element) {
            $(element)
                .addClass("is-invalid")
                .removeClass("is-valid");
        },
        // Unhighlight valid fields
        unhighlight: function(element) {
            $(element)
                .removeClass("is-invalid")
                .addClass("is-valid");
        },
        // Submit handler
        submitHandler: function(form) {
            // Prevent double submission
            if($(form).valid()) {
                form.submit();
            }
        }
    });

    // Real-time validation on input
    $("#edit-profile-user input, #edit-profile-user select").on("input blur", function() {
        $(this).valid();
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
.alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: fadeOut 5s forwards;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: fadeOut 5s forwards;
        }

        @keyframes fadeOut {
            0% {opacity: 1;}
            70% {opacity: 1;}
            100% {opacity: 0; visibility: hidden;}
        }

        .btn-close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            opacity: .5;
            background: none;
            border: 0;
            padding: 0;
            margin-left: 15px;
            cursor: pointer;
        }
</style>

    <!-- <link rel="stylesheet" href="css/responsive.css"> -->
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 90,
                            behavior: 'smooth'
                        });
                    }
                });
            });
         });
    </script>
</head>
<?php
if (isset($_SESSION['success'])) { ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
    </div>
<?php
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
    </div>
<?php
    unset($_SESSION['error']);
}
?>
<body>
    <div class="preloader" id="preloader">
        <div class="spinner">
            <img src="assets/img/logo3.png" alt="Loading...">
        </div>
    </div>
 
    <header>
    <div class="header-area">
        <div id="sticky-header" class="main-header-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-2">
                        <div class="logo">
                            <a href="index.html">
                                <img src="assets/img/logo2.png" alt="" height="60px" width="250px">
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-xl-6 col-lg-7">
                            <div class="main-menu  d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                    <li><a class="active" href="Doctors.php">Doctors</a></li>
                                        
                                        <li><a href="Department.php">Department</a></li>
                                        <li><a href="#">Book Appointment <i class="ti-angle-down"></i></a>
                                        <ul class="submenu">
                                        <li><a href="appointment.php">Book An Appointment</a></li>
                                        <li><a href="appointment_history.php">Appointment History</a></li>
                                        </ul>
                                    </li>
                                       
                                        <li><a href="#">My Profile<i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="myprofile.php">My Profile</a></li>
                                                <li><a href="changepassword.php">Change Password</a></li>
                                                <li><a href="?logout=1">Log Out</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- header-end -->
    <div class="loaded" id="content">

    <!-- bradcam_area_start  -->
    <div class="bradcam_area breadcam_bg bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>Edit Profile</h3>
                        <p><a href="index.php">Home /</a> Edit Profile</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="h-100 bg-white">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col">
                    <div class="card card-registration my-4">
                        <div class="row g-0">
                            <div class="col-xl-6 d-none d-xl-block">
                                <img src="assets/img/2859735-removebg-preview.png" height="1000px" width="1200px" class="img-fluid" />
                            </div>
                            <div class="col-xl-6">
                                <div class="card-body p-md-5 text-black">
                            <form id="edit-profile-user" method="POST">
                                <h3 class="mb-5 text-uppercase">Edit Profile</h3>

                                        <div class="form-outline mb-4">
                                            <input type="text" name="username"  placeholder="Username" value="<?php echo $user['username']; ?>" class="form-control form-control-lg" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="email" name="email"  placeholder="Email ID" value="<?php echo $user['email']; ?>" class="form-control form-control-lg" readonly />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="date" name="dob" placeholder="Date of Birth" class="form-control form-control-lg" value="<?php echo $user['dob']; ?>" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <select name="gender" class="form-control form-control-lg">
                                                <option value="">Select Gender</option>
                                                 <option value="male" <?php echo (strtolower($user['gender']) == 'male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo (strtolower($user['gender']) == 'female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="other" <?php echo (strtolower($user['gender']) == 'other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" name="address" placeholder="Address" value="<?php echo $user['address']; ?>" class="form-control form-control-lg" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="tel" name="phone"  placeholder="Phone Number" value="<?php echo $user['mobile']; ?>" class="form-control form-control-lg" />
                                        </div>

                                        <div class="form-group mt-3">
                                            <button type="submit" class="button button-contactForm boxed-btn">Edit Profile</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

	<!-- Start Align Area -->
            
  
<!-- End Align Area -->

	<!-- End Align Area -->
</div>
<!-- footer start -->
<footer class="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-md-6 col-lg-4">
                    <div class="footer_widget">
                        <div class="footer_logo">
                            <a href="#">
                                <img src="assets/img/logo2.png" alt="" height="70px" width="250px">
                            </a>
                        </div>
                        <p>
                            The trust has vision to provide best healthcare services in terms of accessibility, patient safety, effectiveness, affordable and efficiency.
                        </p>
                        <div class="socail_links">
                            <ul>
                                <li>
                                    <a href="https://www.facebook.com/kkpatelhospital/">
                                        <i class="ti-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/kkpatelhospital/">
                                        <i class="ti-twitter-alt"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/kkpatelhospital/">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 offset-xl-1 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Departments
                        </h3>
                       <ul>
                        <li><a href="Department">Cardiology </a></li>
                        <li><a href="Department"> oncology</a></li>
                        <li><a href="Department"> orthopaedics</a></li>
                        <li><a href="Department">nephrology </a></li>
                        <li><a href="Department">neuro sciences  </a></li>
                        </ul>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-lg-2">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Useful Links
                        </h3>
                        <ul>
                            <li><a href="about">About</a></li>
                            <li><a href="Pressdesk">Press Desk</a></li>
                            <li><a href="Doctors">Doctors</a></li>
                            <li><a href="contact"> Contact</a></li>
                            <li><a href="appointment"> Appointment</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Address
                        </h3>
                       
                            <ul>
                                <p>Near Kutch University,
                                    Mundra Road,
                                    Bhuj, Kachchh,
                                    Gujarat - 370001.</p>
                                <li><a href="tel:tel:02832236000"> tel:02832236000</a></li>

                            <li><a href="">Mail:  info@kkphospital.org</a></li>
                            </ul>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</footer>
<script>
    // Simulate content loading
    document.addEventListener("DOMContentLoaded", function() {
      // Hide the preloader after 2 seconds (you can adjust this time)
      setTimeout(function() {
        document.getElementById("preloader").style.display = "none";
        document.getElementById("content").style.display = "block";
      }, 1000);
    });
</script>


  
</body>
</html> 