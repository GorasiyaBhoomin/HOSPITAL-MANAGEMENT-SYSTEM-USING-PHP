<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Appointment</title>
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
    <div class="bradcam_area breadcam_bg bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>Appointment</h3>
                        <p><a href="index">Home /</a> Appointment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- bradcam_area_end  -->
    <div class="contact-form">
        <div class="container1">
            <div class="main">
                <div class="content">
                    <h2>Book An Appointment </h2>
                  
                    <form action="" method="post">
                        <input type="text" name="appointmentname" placeholder="Enter Your Name" value="">
                      
                        <input type="email" name="email" placeholder="Enter Your Email" value="">
                     
                        <input type="number" name="phone" placeholder="Enter Your Phone number" value="">
                      
                   
                        <select id="gender" name="gender">
                            <option value="" disabled selected>Select a Gender</option>
                            <option value="Male" >Male</option>
                            <option value="Female">Female</option>
                            <!-- Add more options as needed -->
                          </select>
                        


                        <select id="department" name="departmentname">
                            <option value="" disabled selected>Select a Department</option>
                                <option value="Cardiology">Cardiology</option>
                                <option value="Physio">Physio</option>

                        </select>
                       
                        

                        
                        <select id="doctors" name="username">
                            <option value="" disabled selected>Select doctors</option>
                            <option value="Dr.anand">Dr.anand</option>
                            <option value="Dr.diya">Dr.diya</option>


                        </select>
                      
                        
                          <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="">
                          
                          <input type="time" id="time" name="time" value="">
                         
    
                        <input type="text" name="message" placeholder="Problem Facing!!"> 
                     
                       <button type="submit" class="btn" > <a href="login.php"> Appointment <i class="fa fa-paper-plane-o"></a></i></button>
                    </form>
                </div>
                <div class="form-img">
                    <img src="assets2/img/doctors/appointment.jpg" alt="">
                </div>
            </div>
            
        </div>
    </div> 
	
	<div class="whole-wrap">
		<div class="container box_1170">
        </div>
    </div>
	<!-- End Align Area -->

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
   
</footer><!-- footer end  -->
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
  
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
<script>
    jQuery(document).ready(function(){
        jQuery('#department').change(function(){
            let cid=jQuery(this).val();
            jQuery('#time').html('<option value="">Select time</option>')
            jQuery.ajax({
                url:'/getdoctor',
                type:'post',
                data:'cid='+cid+'&_token={{csrf_token()}}',
                success:function(result){
                    jQuery('#doctor').html(result)
                }
            });
        });
        jQuery('#department').change(function(){
    let cid = jQuery(this).val(); // Get the selected department ID
    jQuery('#doctors').html('<option value="">Select doctors</option>'); // Clear the doctors dropdown
    jQuery.ajax({
        url: '/getdoctor',
        type: 'post',
        data: { cid: cid, _token: '{{ csrf_token() }}' }, // Send department ID and CSRF token
        success: function(result){
            jQuery('#doctors').html(result); // Populate doctors dropdown with received data
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});




        
        jQuery('#doctors').change(function(){
            let sid=jQuery(this).val();
            jQuery.ajax({
                url:'/gettime',
                type:'post',
                data:'sid='+sid+'&_token={{csrf_token()}}',
                success:function(result){
                    jQuery('#time').html(result)
                }
            });
        });
        
    });
        
    </script> 
<!-- form itself end-->
<script src="assets2/js/vendor/jquery-1.12.4.min.js"></script>
<script src="assets2/js/imagesloaded.pkgd.min.js"></script>
<script src="assets2/js/wow.min.js"></script>
<script src="assets2/js/jquery.slicknav.min.js"></script>


<script src="assets2/js/main.js"></script>
<!-- form itself end -->

	
</body>
</html>