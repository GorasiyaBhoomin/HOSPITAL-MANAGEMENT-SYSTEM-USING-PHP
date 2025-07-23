<?php
session_start();
include_once("../Database/connection.php");
require '../vendor/autoload.php';

if (!isset($_SESSION["doctor_email"])) {
    header("Location: ../Doctor/doctorlogin.php");
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $problem = $_POST['message'];
    $doctor_id = $doctor['id'];
    $doctor_name = $doctor['username'];

    // Insert into patient_visits table
    $sql = "INSERT INTO patient_visits (doctor_id, doctor_name, username, email, gender, phone, problem) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("issssss", $doctor_id, $doctor_name, $username, $email, $gender, $phone, $problem);
    
    if ($stmt->execute()) {
        // Send email to patient
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gorasiyabhoomin@gmail.com'; // Replace with your Gmail
            $mail->Password = 'rvlf jtwu oeyx ouka'; // Replace with your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('gorasiyabhoomin@gmail.com', 'KK Patel Hospital');
            $mail->addAddress($email, $username);
            
            $mail->isHTML(true);
            $mail->Subject = 'Patient Visit Confirmation - KK Patel Hospital';
            $mail->Body = "
                <h2>Dear $username,</h2>
                <p>Your visit has been successfully recorded with Dr. $doctor_name.</p>
                <p><strong>Visit Details:</strong></p>
                <ul>
                    <li>Doctor: $doctor_name</li>
                    <li>Problem: $problem</li>
                    <li>Date: " . date('Y-m-d') . "</li>
                </ul>
                <p>Thank you for choosing KK Patel Hospital.</p>
                <p>Best regards,<br>KK Patel Hospital Team</p>
            ";

            $mail->send();
            $_SESSION['success'] = "Patient visit recorded successfully and confirmation email sent!";
        } catch (Exception $e) {
            $_SESSION['success'] = "Patient visit recorded successfully but email could not be sent: " . $mail->ErrorInfo;
        }
        header("Location: patient_visit.php");
        exit();
    } else {
        $_SESSION['error'] = "Error recording patient visit. Please try again.";
        header("Location: patient_visit.php");
        exit();
    }
}

// Handle logout
if(isset($_GET['logout'])) {
    $_SESSION['logout_success'] = "Logged out successfully!";
    unset($_SESSION['doctor_email']);
    unset($_SESSION['doctor_name']);
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
    $(document).ready(function () {
        // Custom validation method for allowing only letters and spaces in Username
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed");
            $("#add-patient").validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                lettersonly: true  

            },
            email: {
                required: true,
                email: true
            },
            dob: {
                required: true,
                date: true
            },
            gender: {
                required: true
            },
            departmentname: {
                required: true
            },
            address: {
                required: true,
                minlength: 5
            },
            city: {
                required: true
            },
            postal_code: {
                required: true,
                digits: true
            },
            cv: {
                required: true,
                extension: "pdf|doc|docx"
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            avatar: {
                required: true,
                extension: "png|jpeg|jpg|gif"

            },
            password: {
                required: true,
                minlength: 6
            },
            age: {
                required: true,
                number: true,
                min: 1,
                max: 120
            },
            Confirm_password: {
                required: true,
                equalTo: "#password"
            },
            Consultancy_fees: {
                required: true,
                number: true
            },
            message: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            username: {
                required: "Please enter a username",
                minlength: "Username must be at least 3 characters long",
                lettersonly: "Only letters and spaces are allowed"

            },
            email: {
                required: "Please enter an email",
                email: "Enter a valid email address"
            },
            dob: {
                required: "Please enter your date of birth",
                date: "Please enter a valid date"
            },
            gender: {
                required: "Please select a gender"
            },
            departmentname: {
                required: "Please select a department"
            },
            address: {
                required: "Please enter your address",
                minlength: "Address must be at least 5 characters long"
            },
         
            phone: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },
           
            Consultancy_fees: {
                required: "Please enter the consultancy fees",
                number: "Please enter a valid numeric amount"
            },
           
            age: {
                required: "Please enter your age",
                number: "Please enter a valid number",
                min: "Age must be at least 1 year",
                max: "Age must be less than 120 years"
            },
            message: {
                required: "Please enter a short biography",
                minlength: "Biography must be at least 10 characters long"
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

<body>
<div class="main-wrapper">
<?php
    if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php
        unset($_SESSION['error']);
    }
    ?>
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
                        <!-- <li>
                            <a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedule</span></a>
                        </li> -->
                        
                        
                        
                       
                        
                        
                     

                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">

    <div class="content">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h4 class="page-title">Patient Visit</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
             





<!-- Rest of the HTML remains the same until the form -->
                <form action="patient_visit.php" id="add-patient" method="POST">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Doctor Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="doctor_name" value="<?php echo htmlspecialchars($doctor['username']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="username" value="" placeholder="Enter username">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group gender-select">
                                <label class="gen-label">Gender:<span class="text-danger">*</span></label>
                                <div id="gender-options">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="gender" value="male" class="form-check-input" id="male"> Male
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="gender" value="female" class="form-check-input" id="female"> Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="phone" value="" placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Problem Facing<span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="3" cols="30" name="message" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Add Visit</button>
                    </div>
                </form>
<!-- Rest of the HTML remains the same -->
</div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/app.js"></script>

    
</body>



<!-- add-doctor24:06-->
</html>// Rest of the code remains the same...// Rest of the code remains the same...