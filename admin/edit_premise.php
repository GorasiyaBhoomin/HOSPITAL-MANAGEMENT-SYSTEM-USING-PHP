<?php
session_start();
include_once("../Database/connection.php");
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}

// Get premise ID from URL parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: premise.php");
    exit();
}

// Fetch existing premise data
$sql = "SELECT * FROM premises WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$premise = $result->fetch_assoc();

if(!$premise) {
    header("Location: premise.php");
    exit();
}

if(isset($_POST['update_premise'])){
    $name = $_POST['name'];
    $location = $_POST['location'];
    $capacity = $_POST['capacity'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $update = "UPDATE premises SET name=?, location=?, capacity=?, status=?, description=? WHERE id=?";
    $stmt = $con->prepare($update);
    $stmt->bind_param("ssissi", $name, $location, $capacity, $status, $description, $id);
    
    try {
        if($stmt->execute()){
            $_SESSION['success'] = 'Premise updated successfully';
            header("Location: premise.php");
            exit();
        } else {
            $_SESSION['error'] = 'Error updating premise: ' . $con->error;
            header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
            exit();
        }
    } catch (Exception $e){
        $_SESSION['error'] = 'Error updating premise: ' . $e->getMessage();
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
        exit();
    }
}

// Rest of the admin session handling code remains the same...
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
            $("#edit-premise").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    location: {
                        required: true
                    },
                    capacity: {
                        required: true,
                        min: 1
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter premise name",
                        minlength: "Name must be at least 3 characters long"
                    },
                    location: {
                        required: "Please enter location"
                    },
                    capacity: {
                        required: "Please enter capacity",
                        min: "Capacity must be at least 1"
                    },
                    status: {
                        required: "Please select status"
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
                    <h4 class="page-title">Edit Premise #<?php echo htmlspecialchars($premise['id']); ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <form action="" id="edit-premise" method="POST">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($premise['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Location</label>
                            <textarea name="location" class="form-control" required><?php echo htmlspecialchars($premise['location']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control" min="1" value="<?php echo (int)$premise['capacity']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <?php foreach (['Available','Occupied','Maintenance'] as $st): ?>
                                    <option value="<?php echo $st; ?>" <?php echo $st===$premise['status']?'selected':''; ?>><?php echo $st; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"><?php echo htmlspecialchars($premise['description']); ?></textarea>
                        </div>
                        
                        <div class="m-t-20 text-center">
                            <button type="submit" name="update_premise" class="btn btn-primary submit-btn">Update Premise</button>
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