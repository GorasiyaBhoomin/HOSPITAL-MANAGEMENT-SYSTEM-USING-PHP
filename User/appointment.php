<?php
session_start();
include_once("../Database/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $appointmentname = trim($_POST['appointmentname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $departmentname = trim($_POST['departmentname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $consultancy_fees = trim($_POST['consultancy_fees'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time_slot = trim($_POST['time_slot'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $status = 'approved';

    // Validation
    if (
        empty($appointmentname) || empty($email) || empty($phone) || empty($gender) ||
        empty($departmentname) || empty($username) || empty($consultancy_fees) ||
        empty($date) || empty($time_slot) || empty($message)
    ) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: appointment.php");
        exit();
    }

    // Check if the time slot is already booked
    $check_slot_sql = "SELECT * FROM appointments WHERE appointment_date = ? AND time_slot = ? AND status != 'Cancelled'";
    $check_stmt = $con->prepare($check_slot_sql);
    $check_stmt->bind_param("ss", $date, $time_slot);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "This time slot is already booked. Please select another.";
        header("Location: appointment.php");
        exit();
    }

    // Get available time slots for the selected doctor and date
    $available_slots_sql = "SELECT time_slot FROM doctor_time_slots WHERE doctor_id = ? AND appointment_date = ? AND status = 'Available'";
    $available_stmt = $con->prepare($available_slots_sql);
    $available_stmt->bind_param("is", $username, $date);
    $available_stmt->execute();
    $available_result = $available_stmt->get_result();
    
    $available_slots = [];
    while ($row = $available_result->fetch_assoc()) {
        $available_slots[] = $row['time_slot'];
    }

    // Check if selected time slot is available
    if (!in_array($time_slot, $available_slots)) {
        $_SESSION['error'] = "This time slot is not available. Please select another.";
        header("Location: appointment.php");
        exit();
    }

    // Insert appointment
    $insert_sql = "INSERT INTO appointments (appointmentname, email, phone, gender, departmentname, username, consultancy_fees, appointment_date, time_slot, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_sql);
    $stmt->bind_param("sssssiissss", $appointmentname, $email, $phone, $gender, $departmentname, $username, $consultancy_fees, $date, $time_slot, $message, $status);

    if ($stmt->execute()) {
        // Update slot status
        $update_slot_sql = "UPDATE doctor_time_slots SET status = 'Booked' WHERE doctor_id = ? AND time_slot = ? AND appointment_date = ?";
        $update_stmt = $con->prepare($update_slot_sql);
        $update_stmt->bind_param("iss", $username, $time_slot, $date);
        $update_stmt->execute();

        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gorasiyabhoomin@gmail.com';
            $mail->Password = 'rvlf jtwu oeyx ouka';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('gorasiyabhoomin@gmail.com', 'Hospital Appointment System');
            $mail->addAddress($email, $appointmentname);

            $mail->isHTML(true);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body = "
                <h2>Appointment Confirmation</h2>
                <p>Dear <strong>$appointmentname</strong>,</p>
                <p>Your appointment has been booked successfully!</p>
                <ul>
                    <li><strong>Department:</strong> $departmentname</li>
                    <li><strong>Doctor ID:</strong> $username</li>
                    <li><strong>Date:</strong> $date</li>
                    <li><strong>Time Slot:</strong> $time_slot</li>
                    <li><strong>Consultancy Fees:</strong> ₹$consultancy_fees</li>
                    <li><strong>Message:</strong> $message</li>
                    <li><strong>Status:</strong> $status</li>
                </ul>
                <p>Thank you for trusting us!</p>
            ";

            $mail->send();
            $_SESSION['success'] = "Appointment booked! Confirmation email sent.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Appointment booked, but email failed: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Error booking appointment: " . $stmt->error;
    }

    header("Location: appointment.php");
    exit();
}

// Fetch user info
$userEmail = $_SESSION['user_email'];
$user = [];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "User not found.";
    header("Location: ../login.php");
    exit();
}



// Handle admin logout only
if(isset($_GET['logout'])) {
    // Store success message before unsetting admin session
    $_SESSION['logout_success'] = "Logged out successfully!";
    
    // Only unset admin-specific session variables
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']); // Changed from admin_username to match login
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    header("Location: ../login.php");
    exit();
}

// Fetch admin data from database
$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email = ?"; // Changed to query by email instead of id
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email); // Changed parameter type to string for email
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// If admin data not found, redirect to login
if (!$user_data) {
    header("Location: ../login.php");
    exit;
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>appointment</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo3.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="css/nice-select.css">
    <link rel="stylesheet" type="text/css" href="css/flaticon.css">
    <link rel="stylesheet" type="text/css" href="css/gijgo.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/slicknav.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- <link rel="stylesheet" href="css/responsive.css"> -->

    <!-- jQuery (Only one version) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="assets/js/additional-methods.js"></script>
    <script>
    $(document).ready(function () {
        // Custom validation method for allowing only letters and spaces in Username
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters and spaces are allowed");
            $("#appointment").validate({
                rules: {
                    appointmentname: { required: true, minlength: 3 ,
                        lettersonly: true  
                    },
                    email: { required: true, email: true },
                    phone: { required: true, digits: true, minlength: 10, maxlength: 15 },
                    gender: { required: true },
                    departmentname: { required: true },
                    username: { required: true },
                    date: { required: true, date: true },
                    time: { required: true },
                    message: { required: true, minlength: 5 }
                },
                messages: {
                    appointmentname: { required: "Please enter your name", minlength: "At least 3 characters", 
                        lettersonly: "Only letters and spaces are allowed"
                    },
                    email: { required: "Please enter your email", email: "Enter a valid email" },
                    phone: { required: "Please enter your phone", digits: "Only numbers", minlength: "At least 10 digits", maxlength: "Max 15 digits" },
                    gender: { required: "Please select your gender" },
                    departmentname: { required: "Please select a department" },
                    username: { required: "Please select a doctor" },
                    date: { required: "Please select a date" },
                    time: { required: "Please select a time" },
                    message: { required: "Describe your problem", minlength: "At least 5 characters" }
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
            $(document).ready(function () {
    $('#department').on('change', function () {
        const department = $(this).val();

        $.ajax({
            url: 'get_doctors.php',
            type: 'POST',
            data: { department: department },
            dataType: 'json',
            success: function (response) {
                console.log("Doctors received:", response);
                
                let options = "<option value='' disabled selected>Select Doctor</option>";

                if (response.length > 0) {
                    response.forEach(function (doctor) {
                        options += `<option value="${doctor.id}" data-fees="${doctor.fees}">${doctor.name}</option>`; 
                    });
                } else {
                    options += "<option value='' disabled>No doctors available</option>";
                }

                $('#doctors').html(options).prop('disabled', response.length === 0);
            },
            error: function (xhr) {
                console.error("Error fetching doctors:", xhr.responseText);
            }
        });
    });

    // Fetch time slots and update consultancy fees when doctor is selected
    $('#doctors').on('change', function () {
        var doctor_id = $(this).val(); 
        var fees = $(this).find(':selected').data('fees'); 

        $("#consultancy_fees").val(fees); // Update consultancy fees field

        $.ajax({
            url: "get_time_slots.php",
            type: "POST",
            data: { doctor_id: doctor_id }, 
            success: function (data) {
                console.log("Time slots received:", data);
                $("#time_slots").html(data);
            },
            error: function (xhr) {
                console.error("Error fetching time slots:", xhr.responseText);
            }
        });
    });
});
    });
    </script>
    
    <style>
        .is-invalid { border: 2px solid red; }
        .is-valid { border: 2px solid green; }

        

        
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 90,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="preloader" id="preloader">
        <div class="spinner">
            <img src="assets/img/logo3.png" alt="Loading...">
        </div>
    </div>
    <header>
        <div class="header-area">
            <div id="sticky-header" class="main-header-area">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-2">
                            <div class="logo">
                                <a href="index.html">
                                    <img src="assets/img/logo2.png" alt="" height="60px" width="250px">
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-xl-6 col-lg-7">
                            <div class="main-menu d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                        <li><a class="active" href="Doctors.php">Doctors</a></li>
                                        <li><a href="Department.php">Department</a></li>
                                        <li><a href="#">Book Appointment <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="appointment.php">Book An Appointment</a></li>
                                                <li><a href="appointment_history.php">Appointment History</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">My Profile<i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="myprofile.php">My Profile</a></li>
                                                <li><a href="changepassword.php">Change Password</a></li>
                                                <li><a href="?logout=1">Log Out</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>



    <div class="bradcam_area breadcam_bg bradcam_overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="bradcam_text">
                        <h3>Appointment</h3>
                        <p><a href="index">Home /</a> Appointment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-form">
        <div class="container1">
            <div class="main">
                <div class="content">
                    <h2>Book An Appointment</h2>
                  <header>
    <!-- Your Header Menu code here -->
</header>

<?php
// 📢 Improved Alerts Section (place it immediately after header)
if (isset($_SESSION['error']) || isset($_SESSION['success'])):
?>
    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
<?php
endif;
?>


                    <form id="appointment" method="post">
                        <input type="text" name="appointmentname" placeholder="Enter Your Name" value="">
                      
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                     
                        <input type="number" name="phone" placeholder="Enter Your Phone number" value="">
                      
                   
                        <select id="gender" name="gender">
                            <option value="" disabled selected>Select a Gender</option>
                            <option value="Male" >Male</option>
                            <option value="Female">Female</option>
                            <!-- Add more options as needed -->
                          </select>
                        


                        <select id="department" name="departmentname">
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
                       
                        

                        
                        <select id="doctors" name="username">
                            <option value="" disabled selected>Select doctors</option>
                           


                        </select>
                      
                        <input type="text" id="consultancy_fees" name="consultancy_fees" placeholder=" Consultancy Fees" readonly>

                          <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="">
                          
<select id="time_slots" name="time_slot">
    <option value="" disabled selected>Select Time Slot</option>
</select>
                         
    
                        <input type="text" name="message" placeholder="Problem Facing!!"> 
                     
                       <button type="submit" class="btn">Book Appointment <i class="fa fa-paper-plane-o"></i></button>
                    </form>
                </div>
                <div class="form-img">
                    <img src="assets/img/doctors/appointment.jpg" alt="">
                </div>
            </div>
            
        </div>
    </div> 
    
    <div class="whole-wrap">
        <div class="container box_1170">
        </div>
    </div>
    <!-- End Align Area -->

<!-- footer start -->
<footer class="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-md-6 col-lg-4">
                    <div class="footer_widget">
                        <div class="footer_logo">
                            <a href="#">
                                <img src="assets/img/logo2.png" alt="" height="70px" width="250px">
                            </a>
                        </div>
                        <p>
                            The trust has vision to provide best healthcare services in terms of accessibility, patient safety, effectiveness, affordable and efficiency.
                        </p>
                        <div class="socail_links">
                            <ul>
                                <li>
                                    <a href="https://www.facebook.com/kkpatelhospital/">
                                        <i class="ti-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/kkpatelhospital/">
                                        <i class="ti-twitter-alt"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/kkpatelhospital/">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 offset-xl-1 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Departments
                        </h3>
                        <ul>
                            <li><a href="Department">Cardiology </a></li>
                            <li><a href="Department"> oncology</a></li>
                            <li><a href="Department"> orthopaedics</a></li>
                            <li><a href="Department">nephrology </a></li>
                            <li><a href="Department">neuro sciences  </a></li>
                        </ul>

                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-lg-2">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Useful Links
                        </h3>
                        <ul>
                            <li><a href="about">About</a></li>
                            <li><a href="Pressdesk">Press Desk</a></li>
                            <li><a href="Doctors">Doctors</a></li>
                            <li><a href="contact"> Contact</a></li>
                            <li><a href="appointment"> Appointment</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                                Address
                        </h3>
                       
                            <ul>
                                <p>Near Kutch University,
                                    Mundra Road,
                                    Bhuj, Kachchh,
                                    Gujarat - 370001.</p>
                                <li><a href="tel:tel:02832236000"> tel:02832236000</a></li>
                                <li><a href="">Mail:  info@kkphospital.org</a></li>
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</footer><!-- footer end  -->
<script>
    // Simulate content loading
    document.addEventListener("DOMContentLoaded", function() {
      // Hide the preloader after 2 seconds (you can adjust this time)
      setTimeout(function() {
        document.getElementById("preloader").style.display = "none";
        document.getElementById("content").style.display = "block";
      }, 1000);
     
    });
    
    
  </script>
 
  
<!-- form itself end -->

    
</body>
</html>