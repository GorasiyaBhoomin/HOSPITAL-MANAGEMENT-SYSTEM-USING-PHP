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

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['user_email'];
    $old_pass = $_POST['old_pass'];
    $newpassword = $_POST['newpassword'];
    $confirm_password = $_POST['confirm_password'];
    
    // Fetch the current hashed password from the database
    $stmt = $con->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user || !password_verify($old_pass, $user['password'])) {
        $error = "Old password is incorrect.";
    } elseif ($newpassword !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($newpassword) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($newpassword, PASSWORD_BCRYPT);
        $stmt = $con->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
            $success = "Password changed successfully.";
        } else {
            $error = "Error updating password. Please try again.";
        }
    }
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
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

    <!-- jQuery (Single Version to Avoid Conflicts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#change-pass-form").validate({
                rules: {
                    old_pass: { required: true },
                    newpassword: { required: true, minlength: 6 },
                    confirm_password: { required: true, equalTo: "#newpassword" }
                },
                messages: {
                    old_pass: { required: "Please enter your old password" },
                    newpassword: {
                        required: "Please enter a new password",
                        minlength: "Password must be at least 6 characters long"
                    },
                    confirm_password: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    }
                },
                errorElement: "span",
                errorPlacement: function (error, element) {
                    error.addClass("text-danger").insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                }
            });

            $("#change-pass-form").on("submit", function (e) {
                if (!$(this).valid()) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <style>
        .is-invalid { border: 2px solid red; }
        .is-valid { border: 2px solid green; }
    </style>
</head>

<body>
<div class="preloader" id="preloader">
        <div class="spinner">
            <img src="assets/img/logo3.png" alt="Loading...">

        </div>
      </div>
    <!-- Header Start -->
    <header>
        <div class="header-area">
            <div id="sticky-header" class="main-header-area">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-2">
                            <div class="logo">
                                <a href="index.html"><img src="assets/img/logo2.png" alt="Logo" height="60px" width="250px"></a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-7">
                            <div class="main-menu d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                        <li><a href="Doctors.php">Doctors</a></li>
                                        <li><a href="Department.php">Department</a></li>
                                        <li><a href="#">Book Appointment <i class="fa fa-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="appointment.php">Book An Appointment</a></li>
                                                <li><a href="appointment_history.php">Appointment History</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">My Profile <i class="fa fa-angle-down"></i></a>
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
    <!-- Header End -->

    <!-- Page Title -->
    <div class="bradcam_area breadcam_bg bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>Change Password</h3>
                        <p><a href="index">Home</a> / Change Password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Form -->
    <section class="h-100 bg-white">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col">
                    <div class="card card-registration my-4">
                        <div class="row g-0">
                            <div class="col-xl-6 d-none d-xl-block">
                                <img src="assets/img/banner/6374585-removebg-preview.png" height="1000px" width="1200px"
                                    alt="Sample photo" class="img-fluid" />
                            </div>
                            <div class="col-xl-6">
                                <div class="card-body p-md-5 text-black">
                                    <form id="change-pass-form" method="post" novalidate="novalidate">
                                        <h3 class="mb-5 text-uppercase">Change Password</h3>
                                        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
                                        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
                                        <div class="form-outline mb-4">
                                            <input type="text" name="old_pass" placeholder="Old Password"
                                                class="form-control form-control-lg" required />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="newpassword" name="newpassword"
                                                placeholder="New Password" class="form-control form-control-lg"
                                                required minlength="6" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" name="confirm_password"
                                                placeholder="Confirm Password" class="form-control form-control-lg"
                                                required />
                                        </div>

                                        <div class="form-group mt-3">
                                            <button type="submit"
                                                class="button button-contactForm boxed-btn">Change Password</button>
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

    <!-- Footer Start -->
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

    <!-- Footer End -->
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

<!-- form itself end -->

	
</body>
</html>
