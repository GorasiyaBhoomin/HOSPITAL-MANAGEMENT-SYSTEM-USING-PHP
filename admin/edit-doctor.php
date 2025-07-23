<?php
session_start();
include_once("../Database/connection.php");
if(!isset($_SESSION['admin_email'])) {
    header("Location: kkadminlogin.php");
    exit();
}
// Check if ID is provided in URL or POST
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    $_SESSION['error'] = "Doctor ID not provided.";
    header("Location: doctors.php");
    exit();
}

$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']); // Get ID from either GET or POST

// Fetch doctor details
$query = "SELECT * FROM doctors WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch existing time slots
$time_slots_query = "SELECT time_slot FROM doctor_time_slots WHERE doctor_id = ?";
$stmt = $con->prepare($time_slots_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$time_slots_result = $stmt->get_result();
$existing_time_slots = [];
while($row = $time_slots_result->fetch_assoc()) {
    $existing_time_slots[] = $row['time_slot'];
}

// Display available time slots
// echo "<h3>Available Time Slots:</h3>";
// if(count($existing_time_slots) > 0) {
//     echo "<ul>";
//     foreach($existing_time_slots as $slot) {
//         echo "<li>" . date('h:i A', strtotime($slot)) . "</li>";
//     }
//     echo "</ul>";
// } else {
//     echo "<p>No time slots available</p>";
// }

if (!$doctor) {
    $_SESSION['error'] = "Doctor not found.";
    header("Location: doctors.php");
    exit();
}

// Handle form submission
if (isset($_POST['update_doctor'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $departmentname = $_POST['departmentname'];
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);
    $Consultancy_fees = trim($_POST['Consultancy_fees']);
    $Bio = trim($_POST['Bio']);

    // Handle avatar upload
    $avatar = $doctor['avatar']; // Default to existing avatar
    if (!empty($_FILES['avatar']['name'])) {
        $avatar_path = "uploads/Doctors/" . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path);
        $avatar = $avatar_path;
    }

    // Handle CV upload
    $cv = $doctor['cv']; // Default to existing CV
    if (!empty($_FILES['cv']['name'])) {
        $cv_path = "uploads/cv/" . basename($_FILES['cv']['name']);
        move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);
        $cv = $cv_path;
    }

    // Correct database connection variable
    $update_query = "UPDATE doctors SET username=?, email=?, dob=?, gender=?, departmentname=?, address=?, city=?, postal_code=?, phone=?, consultancy_fees=?, bio=?, avatar=?, cv=? WHERE id=?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("sssssssssssssi", $username, $email, $dob, $gender, $departmentname, $address, $city, $postal_code, $phone, $Consultancy_fees, $Bio, $avatar, $cv, $doctor_id);
    
    if ($stmt->execute()) {
        // Delete existing time slots
        $delete_slots = "DELETE FROM doctor_time_slots WHERE doctor_id = ?";
        $stmt = $con->prepare($delete_slots);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();

        // Insert new time slots
        if(isset($_POST['time_slots']) && is_array($_POST['time_slots'])) {
            foreach($_POST['time_slots'] as $time_slot) {
                if(!empty($time_slot)) {
                    $insert_slot = "INSERT INTO doctor_time_slots (doctor_id, time_slot) VALUES (?, ?)";
                    $stmt = $con->prepare($insert_slot);
                    $stmt->bind_param("is", $doctor_id, $time_slot);
                    $stmt->execute();
                }
            }
        }

        $_SESSION['success'] = "Doctor updated successfully!";
        header("Location: edit-doctor.php?id=" . $doctor_id);
        exit();
    } else {
        $_SESSION['error'] = "Error updating doctor.";
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
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            avatar: {
                required: true,
                imagefile: "png|jpeg|jpg"

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
            phone: {
                required: "Please enter your phone number",
                digits: "Only numeric values are allowed",
                minlength: "Phone number must be at least 10 digits long",
                maxlength: "Phone number can't exceed 15 digits"
            },
            avatar: {
                required: "Please upload an image",
                imagefile: "Only PNG, JPEG, JPG files are allowed"
            },
            password: {
                required: "Please enter a password",
                minlength: "Password must be at least 6 characters long"
            },
            Confirm_password: {
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
                            <a href="premise.php"><i class="fa fa-building"></i> <span>Premise</span></a>
                        </li>
                        <li>
                            <a href="expenses.php"><i class="fa fa-money"></i> <span>Expenses</span></a>
                        </li>
                        <li>
                            <a href="expenses.php"><i class="fa fa-wheelchair"></i> <span>Expenses</span></a>
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
                <h4 class="page-title">Edit Doctor</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
             
            <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form action="edit-doctor.php?id=<?php echo $doctor_id; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>">

        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" value="<?php echo $doctor['username']; ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control"  value="<?php echo $doctor['email']; ?>" required>
        </div>

        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" name="dob" class="form-control" value="<?php echo $doctor['dob']; ?>" required>
        </div>

        <div class="form-group">
            <label>Gender:</label><br>
            <input type="radio" name="gender" value="male" <?php echo ($doctor['gender'] == 'male') ? 'checked' : ''; ?>> Male
            <input type="radio" name="gender" value="female" <?php echo ($doctor['gender'] == 'female') ? 'checked' : ''; ?>> Female
        </div>

        <div class="form-group">
            <label>Department:</label>
            <select id="departmentname" name="departmentname" class="form-control">
                <option value="">Select a Department</option>
                <?php
                $sql = "SELECT * FROM departments";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $selected = ($doctor['departmentname'] === $row['departmentname']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['departmentname']) . "' " . $selected . ">" . htmlspecialchars($row['departmentname']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" class="form-control" value="<?php echo $doctor['address']; ?>" required>
        </div>

        <div class="form-group">
            <label>City:</label>
            <input type="text" name="city" class="form-control" value="<?php echo $doctor['city']; ?>" required>
        </div>

        <div class="form-group">
            <label>Postal Code:</label>
            <input type="text" name="postal_code" class="form-control" value="<?php echo $doctor['postal_code']; ?>" required>
        </div>

        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="phone" class="form-control" value="<?php echo $doctor['phone']; ?>" required>
        </div>

        <div class="form-group">
            <label>Consultancy Fees:</label>
            <input type="text" name="Consultancy_fees" class="form-control" value="<?php echo $doctor['consultancy_fees']; ?>" required>
        </div>

        <div class="form-group">
            <label>Short Biography:</label>
            <textarea name="Bio" class="form-control" required><?php echo $doctor['bio']; ?></textarea>
        </div>

        <div class="form-group">
            <label>Upload New Avatar:</label>
            <input type="file" name="avatar" class="form-control">
            <img src="<?php echo $doctor['avatar']; ?>" width="100" class="mt-2">
        </div>

        <div class="form-group">
            <label>Upload New CV:</label>
            <input type="file" name="cv" class="form-control">
            <a href="<?php echo $doctor['cv']; ?>" target="_blank">View CV</a>
        </div>

        <div class="form-group">
            <label>Time Slots:</label>
            <div id="time-slots-container">
                <?php foreach($existing_time_slots as $time_slot): ?>
                <div class="time-slot-row d-flex mt-2">
                    <input type="time" class="form-control mr-2" name="time_slots[]" value="<?php echo $time_slot; ?>" required>
                    <button type="button" class="btn btn-danger remove-time-slot">-</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-primary mt-2 add-time-slot">Add Time Slot</button>
        </div>

        <button type="submit" name="update_doctor" class="btn btn-primary">Update Doctor</button>
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
