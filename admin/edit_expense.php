<?php
session_start();
include_once("../Database/connection.php");
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}

// Get expense ID from URL parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: expenses.php");
    exit();
}

// Fetch existing expense data
$sql = "SELECT * FROM expenses WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$expense = $result->fetch_assoc();

if(!$expense) {
    header("Location: expenses.php");
    exit();
}

if(isset($_POST['update_expense'])){
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['expense_date'];
    $description = $_POST['description'];

    $update = "UPDATE expenses SET title=?, amount=?, category=?, expense_date=?, description=? WHERE id=?";
    $stmt = $con->prepare($update);
    $stmt->bind_param("sdsssi", $title, $amount, $category, $date, $description, $id);
    
    try {
        if($stmt->execute()){
            $_SESSION['success'] = 'Expense updated successfully';
            header("Location: expenses.php");
            exit();
        } else {
            $_SESSION['error'] = 'Error updating expense: ' . $con->error;
            header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
            exit();
        }
    } catch (Exception $e){
        $_SESSION['error'] = 'Error updating expense: ' . $e->getMessage();
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">
    <title>KK PATEL HOSPITAL</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery (Ensure it's included before Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="js/additional-methods.js"></script>
    <script>
        $(document).ready(function() {
            $("#edit-expense").validate({
                rules: {
                    title: {
                        required: true,
                        minlength: 3
                    },
                    amount: {
                        required: true,
                        min: 0.01
                    },
                    category: {
                        required: true
                    },
                    expense_date: {
                        required: true
                    }
                },
                messages: {
                    title: {
                        required: "Please enter expense title",
                        minlength: "Title must be at least 3 characters long"
                    },
                    amount: {
                        required: "Please enter amount",
                        min: "Amount must be greater than 0"
                    },
                    category: {
                        required: "Please enter category"
                    },
                    expense_date: {
                        required: "Please select date"
                    }
                },
                errorElement: "span",
                errorPlacement: function (error, element) {
                    error.addClass("text-danger").insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                }
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
    </style>
</head>

<body>
<div class="main-wrapper">
<div class="header">
            <div class="header-left">
                <a href="index-2.php" class="logo">
                <img src="assets/img/<?php echo isset($admin_data['logo']) ? htmlspecialchars($admin_data['logo']) : ''; ?>" width="35" height="35" alt=""> <span>KK PATEL HOSPITAL</span>
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
                        <li>
                            <a href="index-2.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="doctors.php"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
                        </li>
                        <li>
                            <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a>
                        </li>
                        <li>
                            <a href="premise.php"><i class="fa fa-building"></i> <span>Premise</span></a>
                        </li>
                        <li>
                            <a href="expenses.php"><i class="fa fa-money"></i> <span>Expenses</span></a>
                        </li>
                        <li>
                            <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                        </li>
                        
                        <li>
                            <a href="departments.php"><i class="fa fa-hospital-o"></i> <span>Departments</span></a>
                        </li>
                        <li>
                            <a href="feedback.php"><i class="fa fa-comment-o"></i> <span>Feedback</span></a>
                        </li>
                        <li>
                            <a href="press-desk.php"><i class="fa fa-newspaper-o"></i> <span>Press-desk</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>    
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h4 class="page-title">Edit Expense #<?php echo htmlspecialchars($expense['id']); ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <form action="" id="edit-expense" method="POST">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($expense['title']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo htmlspecialchars($expense['amount']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($expense['category']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="expense_date" class="form-control" value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"><?php echo htmlspecialchars($expense['description']); ?></textarea>
                        </div>
                        
                        <div class="m-t-20 text-center">
                            <button type="submit" name="update_expense" class="btn btn-primary submit-btn">Update Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>