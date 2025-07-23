<?php
session_start();
include_once("../Database/connection.php");

// Check if admin is logged in, if not redirect to login page
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}

// Fetch expenses data from database
$sql = "SELECT * FROM expenses ORDER BY expense_date DESC";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$expenses = $result->fetch_all(MYSQLI_ASSOC);

// Handle expense deletion
if(isset($_GET['id'])) {
    $expense_id = $_GET['id'];
    
    $delete_expense = "DELETE FROM expenses WHERE id = ?";
    $stmt = $con->prepare($delete_expense);
    $stmt->bind_param("i", $expense_id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = "Expense deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting expense: " . $con->error;
    }
    
    header("Location: expenses.php");
    exit();
}

// Handle admin logout only
if(isset($_GET['logout'])) {
    $_SESSION['logout_success'] = "Logged out successfully!";
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_name']);
    session_regenerate_id(true);
    header("Location: kkadminlogin.php");
    exit();
}

// Fetch admin data from database
$admin_email = $_SESSION['admin_email'];
$sql = "SELECT * FROM admin_data WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();
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
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="main-wrapper">
<div class="header">
        <div class="header-left">
            <a href="index-2.html" class="logo">
            <img src="assets/img/<?php echo isset($admin_data['logo']) ? htmlspecialchars($admin_data['logo']) : ''; ?>" width="35" height="35" alt=""> 
            <span>KK PATEL HOSPITAL</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
        <ul class="nav user-menu float-right">
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="assets/img/<?php echo isset($admin_data['logo']) ? htmlspecialchars($admin_data['logo']) : ''; ?>" width="40" alt="Admin">
                        <span class="status online"></span>
                    </span>
                    <span><?php echo isset($admin_data['username']) ? htmlspecialchars($admin_data['username']) : ''; ?></span>
                    </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="change-password2.php">Change Password</a>
                    <a class="dropdown-item" href="?logout=1">Logout</a>
                </div>
            </li>
        </ul>
        <div class="dropdown mobile-user-menu float-right">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="profile.php">My Profile</a>
                <a class="dropdown-item" href="change-password2.php">Change Password</a>
                <a class="dropdown-item" href="?logout=1">Logout</a>
            </div>
        </div>
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
            <div class="row">
                <div class="col-sm-4 col-3">
                    <h4 class="page-title">Expenses</h4>
                </div>
                <div class="col-sm-7 col-7 text-right m-b-30">
                    <a href="add_expense.php" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add Expense</a>
                </div>
            </div>

            <!-- Display Success or Error Message -->
            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php } ?>
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Amount</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exp['id']) ?></td>
                                    <td><?= htmlspecialchars($exp['title']) ?></td>
                                    <td><?= number_format($exp['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($exp['category']) ?></td>
                                    <td><?= htmlspecialchars($exp['expense_date']) ?></td>
                                    <td><?= htmlspecialchars($exp['description']) ?></td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="edit_expense.php?id=<?= $exp['id'] ?>"><i class="fa fa-pencil m-r-5"></i>Edit</a>
                                                <a class="dropdown-item" href="expenses.php?id=<?= $exp['id'] ?>" onclick="return confirm('Are you sure you want to delete this expense?');"><i class="fa fa-trash-o m-r-5"></i>Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<script src="assets/js/app.js"></script></body>
</html>