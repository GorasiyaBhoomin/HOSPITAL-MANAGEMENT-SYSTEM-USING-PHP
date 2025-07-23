<?php
session_start();
include_once("../Database/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

$user_email = $_SESSION['user_email']; // get logged-in user's email

$sql = "SELECT * FROM appointments WHERE email = ? ORDER BY appointment_date DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$serial = 1;

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

// Handle download request
if(isset($_GET['download']) && isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $download_sql = "SELECT * FROM appointments WHERE id = ? AND email = ?";
    $download_stmt = $con->prepare($download_sql);
    $download_stmt->bind_param("is", $appointment_id, $user_email);
    $download_stmt->execute();
    $appointment = $download_stmt->get_result()->fetch_assoc();
    
    if($appointment) {
        // Generate HTML content for PDF with enhanced design
        $html = "
            <html>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif;
                        margin: 40px;
                        color: #333;
                    }
                    .header { 
                        text-align: center;
                        margin-bottom: 30px;
                        border-bottom: 2px solid #0066cc;
                        padding-bottom: 20px;
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .logo img {
                        max-width: 200px;
                        height: auto;
                    }
                    .details-container {
                        background: #f9f9f9;
                        padding: 20px;
                        border-radius: 8px;
                        margin-bottom: 30px;
                    }
                    .detail-row {
                        display: flex;
                        margin-bottom: 15px;
                        padding: 10px;
                        background: white;
                        border-radius: 4px;
                    }
                    .detail-label {
                        font-weight: bold;
                        width: 200px;
                        color: #0066cc;
                    }
                    .detail-value {
                        flex: 1;
                    }
                    .total-amount {
                        text-align: right;
                        font-size: 18px;
                        font-weight: bold;
                        margin-top: 20px;
                        padding-top: 20px;
                        border-top: 2px solid #0066cc;
                    }
                    .footer {
                        margin-top: 50px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <div class='logo'>
                    <img src='../assets/img/logo2.png' alt='Hospital Logo'>
                </div>
                <div class='header'>
                    <h1>Appointment Details</h1>
                    <p>Appointment Receipt</p>
                </div>
                
                <div class='details-container'>
                    <div class='detail-row'>
                        <div class='detail-label'>Patient Name:</div>
                        <div class='detail-value'>{$appointment['appointmentname']}</div>
                    </div>
                    <div class='detail-row'>
                        <div class='detail-label'>Doctor:</div>
                        <div class='detail-value'>{$appointment['username']}</div>
                    </div>
                    <div class='detail-row'>
                        <div class='detail-label'>Department:</div>
                        <div class='detail-value'>{$appointment['departmentname']}</div>
                    </div>
                    <div class='detail-row'>
                        <div class='detail-label'>Date:</div>
                        <div class='detail-value'>{$appointment['appointment_date']}</div>
                    </div>
                    <div class='detail-row'>
                        <div class='detail-label'>Time:</div>
                        <div class='detail-value'>{$appointment['time_slot']}</div>
                    </div>
                    <div class='detail-row'>
                        <div class='detail-label'>Status:</div>
                        <div class='detail-value'>{$appointment['status']}</div>
                    </div>
                </div>
                
                <div class='total-amount'>
                    Total Amount: ₹{$appointment['consultancy_fees']}
                </div>
                
                <div class='footer'>
                    <p>Thank you for choosing our hospital. For any queries, please contact our help desk.</p>
                    <p>© " . date('Y') . " Hospital Name. All rights reserved.</p>
                </div>
            </body>
            </html>
        ";
        
        // Set headers for download
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="appointment_receipt.html"');
        
        echo $html;
        exit();
    }
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
<style>
        .download-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .download-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .download-btn i {
            font-size: 14px;
        }
    </style>
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
                        <h3>Appointment history</h3>
                        <p><a href="index.php">Home /</a> appointment history</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- bradcam_area_end  -->

<!-- Add this inside the <div class="loaded" id="content"> section -->
<div class="container mt-5">
    <h2 class="text-center">Appointment History</h2>
    <table class="table table-bordered mt-3">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Doctor Name</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Consultancy_fees</th>
                <th>Status</th>
                <th>Dowload Bill</th>
            </tr>
        </thead>
        <tbody>
        <tbody>

<!-- Rest of the HTML remains the same until the table row -->

<?php while ($row = $result->fetch_assoc()):
    $status = $row['status'] ?? 'pending'; // default to pending
    $badgeClass = match(strtolower($status)) {
        'approved' => 'badge-success',
        'cancelled' => 'badge-danger',
        'pending' => 'badge-warning',
        default => 'badge-secondary',
    };
?>
<tr>
    <td><?= $serial++ ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= htmlspecialchars($row['departmentname']) ?></td>
    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
    <td><?= htmlspecialchars($row['time_slot']) ?></td>
    <td><?= htmlspecialchars($row['consultancy_fees']) ?></td>
    <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span></td>
    <td>
        <a href="?download=1&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-download"></i> Download
        </a>
    </td>
</tr>
<?php endwhile; ?>

</tbody>

        </tbody>
    </table>
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