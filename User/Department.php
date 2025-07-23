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
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Doctors</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    
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
                            top: targetElement.offsetTop - 90, // Adjusted for header height
                            behavior: 'smooth'
                        });
                    }
                });
            });
         });
    </script>
</head>

<body>
    <div class="preloader" id="preloader">
        <div class="spinner">
        <img src="assets/img/logo3.png" alt="Loading...">

        </div>
      </div>
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <!-- header-start -->
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
                        <h3>Department</h3>
                        <p><a href="index">Home /</a> Department</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- bradcam_area_end  -->


<div class="our_department_area">
    <div class="container">
       
    <div class="row">
            <?php
            // Fetch departments from database
            $sql = "SELECT * FROM departments";
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Check if image path exists and is not empty
                    $imagePath = !empty($row['departmentimage']) ? "../admin/" . $row['departmentimage'] : "assets/img/placeholder.jpg";
                    ?>
                    <div class="col-xl-4 col-md-6 col-lg-4">
                        <div class="single_department">
                            <div class="department_thumb">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['departmentname']; ?>" height="200px" width="200px">
                            </div>
                            <div class="department_content">
                                <h3><a href="#"><?php echo $row['departmentname']; ?></a></h3>
                                <p><?php echo $row['description']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No departments found</p>";
            }
            ?>
        </div>
    </div>
</div>

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
<!-- footer end  -->
<!-- link that opens popup -->
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
<!-- form itself end-->
<script src="js/vendor/jquery-1.12.4.min.js"></script>
<script src="js/imagesloaded.pkgd.min.js"></script>
<script src="js/wow.min.js"></script>
<script src="js/jquery.slicknav.min.js"></script>


<script src="js/main.js"></script>
<!-- form itself end -->

	
</body>
</html>