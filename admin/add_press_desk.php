<?php
session_start();
include_once("../Database/connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) { // Changed from admin_id to admin_email since that's what's set in login
    header("Location: kkadminlogin.php");
    exit;
}
// Fetch admin data from database
$admin_email = $_SESSION['admin_email'];
$sql = "SELECT * FROM admin_data WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

if(isset($_POST['create_press'])){
    $description = $_POST['description'];

    $pressimage = '';
    if (!empty($_FILES["pressimage"]["name"])) {
        // Create Pressdesk directory if it doesn't exist
        if (!file_exists("uploads/Pressdesk")) {
            mkdir("uploads/Pressdesk", 0777, true);
        }
        $pressimage = "uploads/Pressdesk/" . basename($_FILES["pressimage"]["name"]);
        move_uploaded_file($_FILES["pressimage"]["tmp_name"], $pressimage);
    }

    $insert = "INSERT INTO press_desk (pressimage, description) VALUES ('$pressimage', '$description')";
    
    try {
        if($con->query($insert)){
            $_SESSION['success'] = 'Press desk added successfully';
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['error'] = 'Error adding press desk: ' . $con->error;
            header("Location: ".$_SERVER['PHP_SELF']); 
            exit();
        }
    } catch (Exception $e){
        $_SESSION['error'] = 'Error adding press desk: ' . $e->getMessage();
        header("Location: ".$_SERVER['PHP_SELF']); 
        exit();
    }
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
            $.validator.addMethod("imagefile", function(value, element) {
        return this.optional(element) || (element.files[0] && element.files[0].type.match(/^image\/(jpeg|png|jpg)$/));
    }, "Only JPG, JPEG, and PNG files are allowed");

    $("#add-press").validate({
        rules: {
            pressimage: {
                required: true,
                imagefile: "jpg|jpeg|png"
            },
            description: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            pressimage: {
                required: "Please upload a press image.",
                imagefile: "Only JPG, JPEG, PNG files are allowed."
            },
            description: {
                required: "Please enter a description.",
                minlength: "Description must be at least 10 characters long."
            }
        },
        errorElement: "span",
        errorPlacement: function (error, element) {
            if (element.attr("name") === "gender") {
                error.addClass("text-danger d-block").insertAfter("#gender-options");
            } else {
                error.addClass("text-danger").insertAfter(element);
            }
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
                    <h4 class="page-title">Add Press Desk</h4>
                </div>
            </div>
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
        
            <form action="" id="add-press" method="POST" enctype="multipart/form-data">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Press image</label>
                        <div class="profile-upload">
                            <div class="upload-img">
                                <img alt="" src="assets/img/user.jpg">
                            </div>
                            <div class="upload-input">
                                <input type="file" name="pressimage" class="form-control" accept="image/*">
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea cols="30" rows="4" name="description" class="form-control"></textarea>
                   
                </div>
                
                <div class="m-t-20 text-center">
                    <button class="btn btn-primary submit-btn" name="create_press">Create Press</button>
                </div>
            </form>
        </div>
    </div>
</div>
    

<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/app.js"></script>

    
</body>


<!-- index22:59-->
</html>