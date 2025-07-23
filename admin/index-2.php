<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["admin_email"])) {
    header("Location: ../admin/kkadminlogin.php"); // Redirect if not logged in
    exit;
}

// Add logout handling
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy all session data
    header("Location: ../admin/kkadminlogin.php");
    exit;
}

// Handle admin logout only
if(isset($_GET['logout'])) {
    // Store success message before unsetting admin session
    $_SESSION['logout_success'] = "Logged out successfully!";
    
    // Only unset admin-specific session variables
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_name']); // Changed from admin_username to match login
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    header("Location: kkadminlogin.php");
    exit();
}

// Fetch admin data from database
$admin_email = $_SESSION['admin_email'];
$sql = "SELECT * FROM admin_data WHERE email = ?"; // Changed to query by email instead of id
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_email); // Changed parameter type to string for email
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

// If admin data not found, redirect to login
if (!$admin_data) {
    header("Location: kkadminlogin.php");
    exit;
}

// Get counts from database with error handling
$doctor_count = 0;
$patient_count = 0;
$appointment_count = 0;
$feedback_count = 0;

try {
    $doctor_count = $con->query("SELECT COUNT(*) as count FROM doctors")->fetch_assoc()['count'];
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist or other error
}

try {
    $patient_count = $con->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist or other error
}

try {
    $appointment_count = $con->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist or other error
}

try {
    $feedback_count = $con->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist or other error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">
    <title>KK PATEL HOSPITAL</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<body>
<div class="main-wrapper">
    <div class="header">
        <div class="header-left">
            <a href="index-2.php" class="logo">
                <img src="assets/img/<?php echo $admin_data['logo']; ?>" width="35" height="35" alt=""> 
                <span>KK PATEL HOSPITAL</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
        
        <ul class="nav user-menu float-right">
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="assets/img/<?php echo $admin_data['logo']; ?>" width="40" alt="Admin">
                        <span class="status online"></span>
                    </span>
                    <span><?php echo htmlspecialchars($admin_data['username']); ?></span>
                    </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="change-password2.php">Change Password</a>
                    <a class="dropdown-item" href="?logout=1">Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li><a href="index-2.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="doctors.php"><i class="fa fa-user-md"></i> <span>Doctors</span></a></li>
                    <li><a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
                    <li>
                            <a href="premise.php"><i class="fa fa-building"></i> <span>Premise</span></a>
                        </li>
                        <li>
                            <a href="expenses.php"><i class="fa fa-money"></i> <span>Expenses</span></a>
                        </li>
                    <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li><a href="departments.php"><i class="fa fa-hospital-o"></i> <span>Departments</span></a></li>
                    <li><a href="feedback.php"><i class="fa fa-comment-o"></i> <span>Feedback</span></a></li>
                    <li><a href="press-desk.php"><i class="fa fa-newspaper-o"></i> <span>Press-desk</span></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="content">
            
            <!-- Success Message -->
            <?php if (isset($_SESSION["login_success"])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION["login_success"]; 
                        unset($_SESSION["login_success"]); // Remove message after showing it once
                    ?>
                </div>
            <?php endif; ?>

            <h2>Welcome, <?php echo $_SESSION["admin_name"]; ?>!</h2>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg1"><i class="fa fa-stethoscope" aria-hidden="true"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?php echo $doctor_count; ?></h3>
                            <span class="widget-title1">Doctors <i class="fa fa-check" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg2"><i class="fa fa-user-o"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?php echo $patient_count; ?></h3>
                            <span class="widget-title2">Patients <i class="fa fa-check" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg3"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?php echo $appointment_count; ?></h3>
                            <span class="widget-title3">Appointment Booked <i class="fa fa-check" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg4"><i class="fa fa-heartbeat" aria-hidden="true"></i></span>
                        <div class="dash-widget-info text-right">
                            <h3><?php echo $feedback_count; ?></h3>
                            <span class="widget-title4">Feedbacks <i class="fa fa-check" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
					
					<div class="col-12 col-md-6 col-lg-6 col-xl-6">
						<div class="card">
							<div class="card-body">
								<div class="chart-title">
									<h4>Doctors by Department </h4>
									<div class="float-right">
										<ul class="chat-user-total">
											<li><i class="fa fa-circle current-users" aria-hidden="true"></i>Male Doctors</li>
											<li><i class="fa fa-circle old-users" aria-hidden="true"></i> Female Doctors</li>
										</ul>
									</div>
								</div>	
								<canvas id="bargraph"></canvas>
							</div>
						</div>
					</div>
				</div>
	
		
            </div>
       
        </div>
    </div>

<div class="sidebar-overlay" data-reff=""></div>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/Chart.bundle.js"></script>
<script src="assets/js/chart.js"></script>
<script src="assets/js/app.js"></script>

</body>
</html>
