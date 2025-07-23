<?php
session_start();
include_once("../Database/connection.php");
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}
if(isset($_POST['create_doctor'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dob =$_POST['dob'];
    $gender = $_POST['gender'];
    $departmentname = $_POST['departmentname'];
    $address =$_POST['address'];
    $city = $_POST['city'];
    $postal_code =$_POST['postal_code'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $Consultancy_fees = $_POST['Consultancy_fees'];
    $Bio = $_POST['Bio'];

    $avatar = '';
    if (!empty($_FILES["avatar"]["name"])) {
        $avatar = "uploads/Doctors/" . basename($_FILES["avatar"]["name"]);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar);
    }

    $cv = '';
    if (!empty($_FILES["cv"]["name"])) {
        $cv = "uploads/cv/" . basename($_FILES["cv"]["name"]);
        move_uploaded_file($_FILES["cv"]["tmp_name"], $cv);
    }

    $insert = "INSERT INTO doctors (username, email, dob, gender, departmentname, address, city, postal_code, phone, password, consultancy_fees, bio, avatar, cv)
    VALUES ('$username', '$email', '$dob', '$gender', '$departmentname', '$address', '$city', '$postal_code','$phone','$password','$Consultancy_fees','$Bio','$avatar','$cv')";
    
    try{
        if($con->query($insert)){
            $doctor_id = $con->insert_id;
            
            // Insert time slots
            if(isset($_POST['time_slots']) && is_array($_POST['time_slots'])) {
                foreach($_POST['time_slots'] as $time_slot) {
                    $insert_slot = "INSERT INTO doctor_time_slots (doctor_id, time_slot) VALUES ('$doctor_id', '$time_slot')";
                    $con->query($insert_slot);
                }
            }

            $_SESSION['success'] = 'Data inserted successfully';
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
    } catch (Exception $e){
        $_SESSION['error'] = 'Error inserting data';
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
 $(document).ready(function () {
        // Custom validation method for allowing only letters and spaces in Username
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed");  
        
        $.validator.addMethod("imagefile", function(value, element) {
        return this.optional(element) || (element.files[0] && element.files[0].type.match(/^image\/(jpeg|png|jpg)$/));
    }, "Only JPG, JPEG, and PNG files are allowed");

        $("#add-doctor").validate({
        rules: {
            username: {
                required: true,
                lettersonly: true,  // Using correct validation method

                minlength: 3
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

            avatar: {
                required: true,
                imagefile: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
           
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
            required: true,
            equalTo: "[name='password']"
            },
            Consultancy_fees: {
                required: true,
                number: true
            },
            Bio: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            username: {
                required: "Please enter a username",
                lettersonly: "Only letters and spaces are allowed",

                minlength: "Username must be at least 3 characters long"
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
            city: {
                required: "Please enter your city"
            },
            postal_code: {
                required: "Please enter a postal code",
                digits: "Only numeric values are allowed"
            },
            cv: {
                required: "Please upload your CV",
                extension: "Only PDF, DOC, or DOCX files are allowed"
            },
            avatar: {
                required: "Please upload a profile image",
                imagefile: "Only JPG, JPEG, and PNG formats are allowed"
            },
            phone: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },

            password: {
                required: "Please enter a password",
                minlength: "Password must be at least 6 characters long"
            },
            confirm_password: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            },
            Consultancy_fees: {
                required: "Please enter the consultancy fees",
                number: "Please enter a valid numeric amount"
            },
            Bio: {
                required: "Please enter a short biography",
                minlength: "Biography must be at least 10 characters long"
            }
        },
        submitHandler: function (form) {
            alert("Form submitted successfully!");
            form.submit(); // Remove this if using AJAX
        },
        
        errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "gender") {
                        error.addClass("text-danger d-block").insertAfter("#gender-options");
                    } else {
                        error.addClass("text-danger").insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function(element) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                }
    });
});

$(document).ready(function () {
    $(document).on("click", ".add-time-slot", function () {
        let newSlot = `<div class="time-slot-row d-flex mt-2">
                            <input type="time" class="form-control mr-2" name="time_slots[]" required>
                            <button type="button" class="btn btn-danger remove-time-slot">-</button>
                        </div>`;
        $("#time-slots-container").append(newSlot);
    });
    
    $(document).on("click", ".remove-time-slot", function () {
        $(this).parent(".time-slot-row").remove();
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
                <img src="assets/img/<?php echo $admin_data['logo']; ?>" width="35" height="35" alt=""> <span>KK PATEL HOSPITAL</span>
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
                <h4 class="page-title">Add Doctor</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
             
                <form action="add-doctor.php" id="add-doctor" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        
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
                            <div class="form-group">
                                <label>Date of Birth</label>
                                    <input type="date" class="form-control datetimepicker" name="dob" value="" placeholder="Enter Date of Birth">
                                  
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

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" value="" placeholder="Enter address">
                                        
                                    </div>
                                    
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Department<span class="text-danger">*</span></label>
                                        <select id="departmentname" name="departmentname" class="form-control">
                                            <option value="" disabled selected>Select a Department</option>
                                            <?php
                                            $sql = "SELECT * FROM departments";
                                            $result = $con->query($sql);
                                            if ($result->num_rows > 0) {
                                                while($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['departmentname'] . "'>" . $row['departmentname'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>City<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="city" value="" placeholder="Enter city">
                                       
                                    </div>
                                   
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Postal Code<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="postal_code" value="" placeholder="Enter postal code" >
                                        
                                    </div>
                                    
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">

                                <div class="form-group">
                                    <label>Upload your CV<span class="text-danger">*</span></label>
                                    <div class="profile-upload">
                                        
                                        <div class="upload-input">
                                        <input type="file" class="form-control" name="cv">
                                        </div>
                                    </div>
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
                                <label>Upload your Image<span class="text-danger">*</span></label>
                                <div class="profile-upload">
                                    <div class="upload-img">
                                        <img alt="" src="assets/img/user.jpg">
                                    </div>
                                    <div class="upload-input">
                                    <input type="file" class="form-control" name="avatar" accept="image/*">
                                  
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="password" value="" placeholder="Enter Password">
                               
                            </div>
                            
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="confirm_password" value="" placeholder="Enter Confirm Password">
                              
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Doctor Consultancy Fees <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="Consultancy_fees" value="" placeholder="Enter Doctor Consultancy Fees">
                               
                            </div>
                            
                        </div>


                        <div class="col-sm-12">
    <div class="form-group">
        <label>Available Time Slots <span class="text-danger">*</span></label>
        <div id="time-slots-container">
            <div class="time-slot-row d-flex">
                <input type="time" class="form-control mr-2" name="time_slots[]" required>
                <button type="button" class="btn btn-success add-time-slot">+</button>
            </div>
        </div>
    </div>
</div>
                    </div>
                    <div class="form-group">
                        <label>Short Biography<span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="3" cols="30" name="Bio" value="" placeholder="Enter Short Bio"></textarea>
                       
                    </div>
                    
                   
                    <div class="m-t-20 text-center">
                    <button type="submit" class="btn btn-primary submit-btn" id="create_doctor" name="create_doctor">Create Doctor</button>
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



<!-- add-doctor24:06-->
</html>