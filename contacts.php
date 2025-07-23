<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer autoload
include_once("Database/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $contactname = $_POST['contactname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Prepare SQL statement
    $sql = "INSERT INTO contacts (contactname, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $contactname, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        // Send email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username = 'gorasiyabhoomin@gmail.com';
            $mail->Password = 'rvlf jtwu oeyx ouka';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('your-email@gmail.com', 'KKP Hospital');
            $mail->addAddress($email, $contactname); // To user

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Thank you for contacting KKP Hospital';
            $mail->Body    = "
                <h3>Hello $contactname,</h3>
                <p>Thank you for reaching out to us. We have received your message:</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong><br>$message</p>
                <p>We will get back to you soon.</p>
                <br><p>Regards,<br>KKP Hospital Team</p>
            ";

            $mail->send();
            $_SESSION['success'] = "Contact details submitted and email sent successfully!";
        } catch (Exception $e) {
            $_SESSION['success'] = "Contact saved, but email failed to send.";
        }

        header("Location: contacts.php");
        exit();
    } else {
        $_SESSION['error'] = "Error submitting contact: " . $con->error;
    }
}
?>


<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Contacts</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets2/img/logo3.png">
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

    <!-- jQuery and Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

    <script>
    $(document).ready(function() {
        // Custom validation method for letters only
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed");

        // Custom validation method for 10-digit phone number
        $.validator.addMethod("phoneformat", function(value, element) {
            return this.optional(element) || /^[0-9]{10}$/.test(value);
        }, "Please enter a valid 10-digit phone number");

        // Form validation
        $("#contactForm").validate({
            rules: {
                contactname: {
                    required: true,
                    lettersonly: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    phoneformat: true
                },
                subject: {
                    required: true,
                    minlength: 5
                },
                message: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                contactname: {
                    required: "Please enter your name",
                    lettersonly: "Only letters and spaces are allowed",
                    minlength: "Name must be at least 3 characters long"
                },
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                phone: {
                    required: "Please enter your phone number",
                    phoneformat: "Please enter a valid 10-digit phone number"
                },
                subject: {
                    required: "Please enter a subject",
                    minlength: "Subject must be at least 5 characters long"
                },
                message: {
                    required: "Please enter your message",
                    minlength: "Message must be at least 10 characters long"
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
            },
            submitHandler: function(form) {
                $("#contactss").prop("disabled", true).text("Sending...");
                form.submit();
            }
        });

        // Real-time validation
        $("input, textarea").on("keyup blur", function() {
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
</head>
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
            <img src="assets2/img/logo3.png" alt="Loading...">
        </div>
    </div>

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
<!-- bradcam_area_start  -->
    <div class="bradcam_area breadcam_bg_2 bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>contact</h3>
                        <p><a href="index">Home /</a> contact</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- bradcam_area_end  -->
<!-- ================ contact section start ================= -->
    <section class="contact-section">
            <div class="container">
                <div class="d-none d-sm-block mb-5 pb-4">
               </div>
               <div class="row">
                    <div class="col-12">
                        <h2 class="contact-title">Get in Touch</h2>
                    </div>
                    <div class="col-lg-8">
                    <form class="form-contact contact_form" id="contactForm" action="contacts.php" method="post">
                    <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" name="contactname" id="name" type="text" placeholder="Enter your name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" name="email" id="email" type="email" placeholder="Enter email address">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input class="form-control" name="phone" id="phone" type="text" placeholder="Enter phone number">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input class="form-control" name="subject" id="subject" type="text" placeholder="Enter Subject">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="message" id="message" cols="30" rows="9" placeholder="Enter Message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="button button-contactForm boxed-btn" id="contactss" name="contactss">Send Message</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-3 offset-lg-1">
                        <div class="media contact-info">
                            <span class="contact-info__icon"><i class="ti-home"></i></span>
                            <div class="media-body">
                                <h3>Bhuj-Kachchh, Gujarat.</h3>
                                <p>Near Kutch University,
                                    Mundra Road,
                                    Bhuj, Kachchh,
                                    Gujarat - 370001.</p>
                            </div>
                        </div>
                        <div class="media contact-info">
                            <span class="contact-info__icon"><i class="ti-tablet"></i></span>
                            <div class="media-body">
                                <a href="tel:tel:02832236000"><h3>tel:02832236000</h3></a>  
                            </div>
                        </div>
                        <div class="media contact-info">
                            <span class="contact-info__icon"><i class="ti-email"></i></span>
                            <div class="media-body">
                                <h3>info@kkphospital.org</h3>
                                <p>Send us your query anytime!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- ================ contact section end ================= -->
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

<!-- JS files -->
<script src="assets2/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets2/js/wow.min.js"></script>
    <script src="assets2/js/jquery.slicknav.min.js"></script>
    <script src="assets2/js/main.js"></script>

    <script>
    // Preloader
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            document.getElementById("preloader").style.display = "none";
        }, 1000);
    });
    </script>

</body>
</html>