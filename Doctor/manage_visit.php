<?php
session_start();
include_once("../Database/connection.php");

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php");
    exit;
}

// Fetch doctor data
$doctor_email = $_SESSION['doctor_email'];
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle patient visit deletion
if(isset($_GET['id'])) {
    $visit_id = $_GET['id'];
    
    $delete_visit = "DELETE FROM patient_visits WHERE id = ? AND doctor_id = ?";
    $stmt_visit = $con->prepare($delete_visit);
    $stmt_visit->bind_param("ii", $visit_id, $doctor['id']);
    
    if($stmt_visit->execute()) {
        $_SESSION['success'] = "Patient visit deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting patient visit: " . $con->error;
    }
    
    header("Location: manage_visit.php");
    exit();
}

// Fetch patient visits data for the logged-in doctor
$visits_sql = "SELECT * FROM patient_visits WHERE doctor_id = ? ORDER BY created_at DESC";
$stmt_visits = $con->prepare($visits_sql);
$stmt_visits->bind_param("i", $doctor['id']);
$stmt_visits->execute();
$visits_result = $stmt_visits->get_result();

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../Doctor/doctorlogin.php");
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
        <!-- Header and Sidebar code remains the same -->
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
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Patient Visits</h4>
                    </div>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>Visit ID</th>
                                        <th>Patient Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Problem</th>
                                        <th>Visit Date</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($visit = $visits_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($visit['id']); ?></td>
                                            <td><?php echo htmlspecialchars($visit['username']); ?></td>
                                            <td><?php echo htmlspecialchars($visit['email']); ?></td>
                                            <td><?php echo ucfirst(htmlspecialchars($visit['gender'])); ?></td>
                                            <td><?php echo htmlspecialchars($visit['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($visit['problem']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($visit['created_at'])); ?></td>
                                            <td class="text-right">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item delete-btn" href="#" 
                                                           data-id="<?php echo $visit['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($visit['username']); ?>">
                                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete_patient" class="modal fade delete-modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="assets/img/sent.png" alt="" width="50" height="46">
                        <h3>Are you sure you want to delete visit of <span id="patient-name"></span>?</h3>
                        <div class="m-t-20">
                            <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                            <a href="#" class="btn btn-danger" id="confirm-delete">Delete</a>
                        </div>
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
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            $('.table').DataTable();
            
            $('.delete-btn').click(function(e) {
                e.preventDefault();
                var visitId = $(this).data('id');
                var patientName = $(this).data('name');
                
                $('#patient-name').text(patientName);
                $('#confirm-delete').attr('href', 'manage_visit.php?id=' + visitId);
                $('#delete_patient').modal('show');
            });
        });
    </script>
</body>
</html>