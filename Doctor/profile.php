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
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
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
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">My Profile</h4>
                    </div>
                    <div class="col-sm-5 col-6 text-right m-b-30">
                        <a href="edit-profile.php" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Edit Profile</a>
                    </div>
                </div>
                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                    <span class="user-img"><img class="rounded-circle" src="../admin/<?php echo htmlspecialchars($doctor['avatar']); ?>" width="40" alt="Doctor">
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0">Dr. <?php echo htmlspecialchars($doctor['username']); ?></h3>
                                                <small class="text-muted"><?php echo htmlspecialchars($doctor['departmentname']); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">Phone:</span>
                                                    <span class="text"><a href="#"><?php echo htmlspecialchars($doctor['phone']); ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Email:</span>
                                                    <span class="text"><a href="#"><?php echo htmlspecialchars($doctor['email']); ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Address:</span>
                                                    <span class="text"><?php echo htmlspecialchars($doctor['address'] . ', ' . $doctor['city'] . ' - ' . $doctor['postal_code']); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Date of Birth:</span>
                                                    <span class="text"><?php echo htmlspecialchars($doctor['dob']); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Gender:</span>
                                                    <span class="text"><?php echo htmlspecialchars(ucfirst($doctor['gender'])); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Consultancy Fees:</span>
                                                    <span class="text">₹<?php echo htmlspecialchars($doctor['consultancy_fees']); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>
            </div>
            <iframe
    width="100%"
    height="500"
    frameborder="0"
    scrolling="no"
    marginheight="0"
    marginwidth="0"
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1dxxxxx.xxxxxx!2d69.6437834!3d23.2084746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x96b620a818e86f16!2sK.K.Patel%20Super%20Speciality%20Hospital!5e0!3m2!1sen!2sus!4v163113493xxx!5m2!1sen!2sus"
    allowfullscreen>
</iframe>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>