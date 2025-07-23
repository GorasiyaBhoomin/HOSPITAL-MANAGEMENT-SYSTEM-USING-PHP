
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>About</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="assets2/img/logo3.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" type="text/css" href="assets2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/nice-select.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/flaticon.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/gijgo.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/animate.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/slicknav.css">
    <link rel="stylesheet" type="text/css" href="assets2/css/style.css">
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
            <img src="assets2/img/logo3.png" alt="Loading...">

        </div>
      </div>
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <!-- header-start -->
    
    <!-- header-start -->
    <header>
        <div class="header-area ">
           
            <div id="sticky-header" class="main-header-area">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-2">
                            <div class="logo">
                                <a href="about">
                                    <img src="assets2/img/logo2.png" alt="" height="60px" width="250px">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-7">
                            <div class="main-menu  d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                    <li><a class="active" href="index.php">home</a></li>
                                        <li><a href="about.php">about</a></li>

                                        <li><a href="Doctors.php">Doctors</a></li>
                                        
                                        <li><a href="Department.php">Department</a></li>

                                        <li><a href="#">pages <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="Pressdesk.php">Press Desk</a></li>
                                                <li><a href="gallery.php">Gallery</a></li>
                                                <li><a href="appointment.php">Book An Appointment</a></li>

                                            </ul>
                                        </li>
                                        <li><a href="#">Contact Us <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="contacts.php">Contact</a></li>
                                                <li><a href="feedbackreview.php">Feedback & Review</a></li>

                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                            <div class="Appointment">
                                <div class="book_btn d-none d-lg-block">
                                    <a class="popup-with-form" href="login.php">Have An Account?</a>
                                </div>
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
    <div class="bradcam_area breadcam_bg_2 bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>Doctors</h3>
                        <p><a href="index">Home /</a> Doctors</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="expert_doctors_area doctor_page">
        <div class="container">
            <div class="row">
                <?php
                include_once("Database/connection.php");
                $sql = "SELECT * FROM doctors";
                $result = $con->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-6 col-lg-4">
                                <div class="single_expert mb-40">
                                    <div class="expert_thumb">
                                        <img src="admin/' . (!empty($row['avatar']) ? $row['avatar'] : 'assets/img/user.jpg') . '" alt="">
                                    </div>
                                    <div class="experts_name text-center">
                                        <a href="profile.php?id=' . $row['id'] . '"><h3>' . $row['username'] . '</h3></a>
                                        <span>' . $row['departmentname'] . '</span>
                                    </div>
                                </div>
                            </div>';
                    }
                } else {
                    echo '<p class="text-center">No doctors found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
 
     <!-- expert_doctors_area_start -->
   
    </div>
    <!-- expert_doctors_area_end -->

    <!-- testmonial_area_start -->
    

    <!-- Emergency_contact start -->
    <div class="Emergency_contact">
        <div class="conatiner-fluid p-0">
            <div class="row no-gutters">
                <div class="col-xl-6">
                    <div class="single_emergency d-flex align-items-center justify-content-center emergency_bg_1 overlay_skyblue">
                        <div class="info">
                            <h3>For Any Emergency Contact</h3>
                            <p>Esteem spirit temper too say adieus.</p>
                        </div>
                        <div class="info_button">
                            <a href="#" class="boxed-btn3-white">+91 73590 07000</a>
                                                </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="single_emergency d-flex align-items-center justify-content-center emergency_bg_2 overlay_skyblue">
                        <div class="info">
                            <h3>Make an Online Appointment</h3>
                            <p>Esteem spirit temper too say adieus.</p>
                        </div>
                        <div class="info_button">
                            <a href="login.php" class="boxed-btn3-white">Make an Appointment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Emergency_contact end -->
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
                                <img src="assets2/img/logo2.png" alt="" height="70px" width="250px">
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

    <!-- form itself end-->
    
            </div>
        </div>
    </form>
    <!-- form itself end -->
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
    <!-- JS here -->
    <script src="assets2/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="assets2/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets2/js/wow.min.js"></script>
    <script src="assets2/js/jquery.slicknav.min.js"></script>
    

    <script src="assets2/js/main.js"></script>

    </body>
</html>