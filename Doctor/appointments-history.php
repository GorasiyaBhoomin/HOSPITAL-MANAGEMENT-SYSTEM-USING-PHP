<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php");
    exit;
}

// Fetch doctor data from database first
$doctor_email = $_SESSION['doctor_email'];
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$doctor_result = $stmt->get_result();
$doctor = $doctor_result->fetch_assoc();

// Get doctor's ID and department
$doctor_id = $doctor['id'];
$doctor_department = $doctor['departmentname'];

// Prepare and execute query to get appointments for the doctor based on department
$sql = "SELECT * FROM appointments WHERE departmentname = ? ORDER BY appointment_date DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_department);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Handle logout
if(isset($_GET['logout'])) {
    $_SESSION['logout_success'] = "Logged out successfully!";
    unset($_SESSION['doctor_email']);
    unset($_SESSION['doctor_username']);
    session_regenerate_id(true);
    header("Location: doctorlogin.php");
    exit();
}

// Handle appointment deletion
if(isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM appointments WHERE id = ?";
    $delete_stmt = $con->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    
    if($delete_stmt->execute()) {
        $_SESSION['delete_success'] = "Appointment deleted successfully!";
        echo json_encode(['success' => true]);
    } else {
        $_SESSION['delete_error'] = "Error deleting appointment: " . $con->error;
        echo json_encode(['success' => false, 'error' => $con->error]);
    }
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
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
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
                    <span class="user-img">
                        <img class="rounded-circle" src="../admin/<?php echo htmlspecialchars($doctor['avatar']); ?>" width="40" alt="Doctor">
                        <span class="status online"></span>
                    </span>
                    <span><?php echo htmlspecialchars($doctor['username']); ?></span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="Change-password2.php">Change Password</a>
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
            <?php if(isset($_SESSION['delete_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['delete_success'];
                        unset($_SESSION['delete_success']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['delete_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['delete_error'];
                        unset($_SESSION['delete_error']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-sm-4 col-3">
                    <h4 class="page-title">Appointments</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Department</th>
                                    <th>Consultancy Fee</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Current Status</th>
                                    <!-- <th class="text-right">Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $appointments_result->fetch_assoc()) { ?>
                                    <tr>
                                        <td>
                                            <img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt="">
                                            <?php echo htmlspecialchars($row['appointmentname']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['departmentname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['consultancy_fees']); ?></td>
                                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                        <td>
                                            <span class="custom-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <!-- <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item delete-appointment" href="#" data-toggle="modal" data-target="#delete_appointment" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td> -->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="delete_appointment" class="modal fade delete-modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="assets/img/sent.png" alt="" width="50" height="46">
                    <h3>Are you sure want to delete this Appointment?</h3>
                    <div class="m-t-20">
                        <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-danger" id="deleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/app.js"></script>
    

<script>
        $(document).ready(function() {
            $('.table').DataTable();
            
            $('.delete-appointment').click(function(e) {
                e.preventDefault();
                var appointmentId = $(this).data('id');
                var patientName = $(this).data('name');
                
                $('#appointment-name').text(appointmentName);
                $('#confirm-delete').attr('href', 'appointments-history.php?id=' + appointmentId);
                $('#delete_appointment').modal('show');
            });
        });
    </script>
</body>
</html>