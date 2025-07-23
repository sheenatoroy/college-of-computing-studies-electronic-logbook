<?php

session_start();

include "../connection.php";

//check if username exist
if(isset($_SESSION["username"])){
    $username = $_SESSION["username"];
}else{

    echo "<script>alert('User not found')</script>";
    exit;
}

    //query to fetch the data
    $sql = "SELECT firstname, lastname, username, account_type FROM prof WHERE username = $username";
    $result = mysqli_query($conn, $sql);

    //check if the query returned any rows
    if(mysqli_num_rows($result) > 0){

        //retrieve the first row from the result set
        $row = mysqli_fetch_assoc($result);

        $fullname = $row["firstname"] . " " . $row["lastname"];
        $username = $row["username"];
        $account_type = $row['account_type'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <!--Bootstrap 5 CSS CDN-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!--Fontawesome-->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <!--Website Logo-->
    <link rel="icon" href="/assets/img/ccs-logo.png">

    <!--Boxicons-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/chat-bot-style.css">

    <!--Calendar-->
    <link rel="stylesheet" href="../fullcalendar/lib/main.min.css">
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../fullcalendar/lib/main.min.js"></script>

    <title>CCS: E-LOG | Account Management</title>
</head>
<style>
    body{
        font-family: 'Poppins', sans-serif;
    }
</style>
<body>

<!--TopNavbar and SideNavbar-->
<div class="wrapper">
<?php
    // Counter variable
    $notificationCount = 0;

    $sql = "SELECT
        a.appointment_id,
        a.day,
        a.time_start,
        a.time_end,
        a.appointment_status,
        a.type_of_concern,
        a.specific_concern,
        a.detailed_concern,
        s.username AS student_username,
        s.firstname AS student_firstname,
        s.lastname AS student_lastname,
        s.middlename AS student_middlename,
        s.year_section AS student_year_section,
        p.username AS prof_username,
        p.firstname AS prof_firstname,
        p.lastname AS prof_lastname,
        p.middlename AS prof_middlename
        FROM appointments a
        JOIN student s ON a.student_id = s.username
        JOIN prof p ON a.prof_id = p.username
        WHERE a.remarks = 'Pending' AND (p.username = '$username')
        AND (s.username IS NOT NULL) AND (s.username <> '$username')
        ORDER BY a.day ASC;";

    $result = mysqli_query($conn, $sql);

    // Check if any appointments are received by the professor from students
    if(mysqli_num_rows($result) > 0) {
        // Count the number of pending appointments
        $notificationCount = mysqli_num_rows($result);
    }
?>

<!-- Modal for Notification icon -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($notificationCount > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <?php
                            // Check if the user is a student or faculty/admin
                            if ($account_type === 'admin') {
                                $action = 'You have received';
                                $appointmentType = 'an appointment with';
                            } else {
                                $action = 'You set an appointment with';
                                $appointmentType = '';
                            }
                        ?>
                        <div class="alert alert-info" role="alert">
                            <strong><?php echo $action; ?> <?php echo $appointmentType; ?>:</strong>
                            <a href="../admin/admin-pending-appointment.php">
                                <?php echo $row['student_firstname'] . ' ' . $row['student_lastname'] . ' on ' . date("F j, Y", strtotime($row["day"])) . ' at ' . date("h:i A", strtotime($row["time_start"])) . ' '; ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p class="text-muted text-center">Nothing to see here right now...</p>
                <?php endif; ?>
            </div>

            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>



<!--TopNavbar-->
<header class="navbar sticky-top navbar-expand-sm header-top" header-theme="orange" style="background-color: #F56904;">
    <div class="container-fluid">
        <div class="row-md-8 sm-8">
            <button class="btn fs-2 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                <i class='bx bx-menu'></i>
            </button>
        </div>
        <div class="row-md-4 sm-4">
            <form class="d-flex">
                <!-- Update the button to include the notification count -->
                <button type="button" class="btn fs-5 text-white" data-bs-toggle="modal" data-bs-target="#notificationModal">
                    <i class='bx bx-bell'></i>
                    <?php
                    // Check if the sender is not on the admin side
                    if ($notificationCount > 0 && strpos($username, 'admin') === false) {
                        echo '<span class="badge bg-danger">' . $notificationCount . '</span>';
                    }
                    ?>
                </button>
                <button type="button" class="btn fs-5 text-white" data-bs-toggle="modal" data-bs-target="#accountModal">
                    <i class='bx bx-user-circle'></i>
                </button>

                <button type="button" class="btn fs-5 text-white" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class='bx bx-log-out-circle'></i>
                </button>
            </form>
        </div>
    </div>
</header>

<!--SideNavbar header-->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
    <div class="offcanvas-header">
        <div class="row">
            <div class="col">
                <div class="logo-details">
                        <button type="button" class="btn-close text-reset float-end" data-bs-dismiss="offcanvas" aria-label="Close" style="margin: 5px;"></button>
                        <img src="/assets/img/ccs-sidenavbar-logo.png"  img class="img-fluid mx-auto d-block justify-content-center" width="100%" alt="logo" style="margin-top: -50px;">
                </div>
            </div>
        </div>
    </div>

    <!--SideNavbar Links-->
    <!--Sidenavbar w/ name of user-->          
    <div class="offcanvas-body" style="margin-top: -30px;">
        <div class="nav-lavel bg-secondary text-white pl-2 py-2 mb-4">
            <span style="text-align:center; margin-left: 70px;">First Semester</span>
                <h6 style="margin-left: 65px;" class="mb-0">A.Y. 2023-2024</h6>
        </div>
            <p class="text-start fw-bold fs-5 m-0" style="font-family: 'Poppins', sans-serif;">
                        <!--display the fullname according to the user-->
                        <?php echo strtoupper($fullname); ?>
                    </p>
                    
                    <p class="text-start fw-normal fst-italic fs-6 m-0">
                        <!--display the id according to the user-->
                        <?php echo "Employee Number:" . " " . $username ?>
                    </p>
                    
                    <br>
            <!--Sidenavbar vertical links-->
            <div class="row">
                <div class="nav-item float-start">
                    <a class="nav-link fs-6 p-1 m-1" href="/admin/admin-dashboard.php" style="color: #131313;"> 
                    <i class='bx bx-layout'></i>
                    <span style="margin-left: 3px;">My Dashboard</span>
                    </a>
                </div>

                <div class="dropdown">
                    <a class="nav-link fs-6 p-1 m-1 dropdown-toggle" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false" style="color: #131313;">
                        <i class='bx bx-note'></i>
                        <span>Appointment Management</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                        <li><a class="dropdown-item" href="/admin/admin-set-appointment.php">Set Appointment</a></li>
                        <li><a class="dropdown-item" href="/admin/admin-history-appointment.php">History Appointments</a></li>
                        <li><a class="dropdown-item" href="/admin/admin-summary-appointment.php">Summary Appointments</a></li>
                    </ul>
                </div>

                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/admin/admin-announcement.php" style="color: #131313;">
                        <i class='bx bx-volume-full'></i>
                        <span>Announcement</span>
                    </a>
                </div>
                
                    
                <!-- <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/admin/admin-feedback.php" style="color: #131313;">
                        <i class='bx bx-message-square-add'></i>
                        <span>Feedbacks</span>
                    </a>
                </div> -->

                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/admin/admin-report.php" style="color: #131313;">
                        <i class='bx bx-edit-alt'></i>
                        <span>Reports and Analytics</span>
                    </a>
                </div>

                <div class="dropdown">
                    <a class="nav-link fs-6 p-1 m-1 dropdown-toggle" href="" role="button" id="dropdownMenuLink2" data-bs-toggle="dropdown" aria-expanded="false" style="color: #131313;">
                        <i class='bx bx-note'></i>
                        <span>Administration Tools</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink2">
                        <li><a class="dropdown-item" href="/admin/admin-account-management.php">Account Management</a></li>
                        <li><a class="dropdown-item" href="/admin/admin-subject-management.php">Subject Management</a></li>
                    </ul>
                </div>

                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1"  href="/admin/admin-terms-and-conditions.php"style="color: #131313;">
                        <i class='bx bx-notepad'></i>
                        <span>Terms and Conditions</span>
                    </a>
                </div>
            </div>
        </div>
<!--Closing tag of SideNavbar Header-->
</div>

<!--My Dashboard Content-->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl">
                <div class="row justify-content-center">
                    <!--CCS Banner-->
                    <div class="col-sm-5 col-md-5 col-lg-5 w-75 h-75">
                        <img class="img-fluid mx-auto d-block" src="/assets/img/ccs-header.png">
                    </div>
                    <!--CCS Main Content Header-->
                    <div class="alert alert-secondary p-4">
                        <h2 class="display-4 text-center mb-0">
                            <small class="fw-bold">CCS: E-LOG</small>
                        </h2>
                        <h6 class="text-center text-dark fs-6">College of Computing Studies: Electronic Logbook System</h6>
                    </div>
                    <br>
                    
                    <!--Content Start-->
                    <!--Account Management-->
                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                        <h4 class="pt-2 mb-3"><i class='bx bx-card mx-2'></i>Select Account to Create</h4>
                        <div class="row justify-content-center">
                            <div class="col-md-4 col-sm-6">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded">
                                    <div class="card-header"><span class="fw-bold fs-5">Faculty Member</span></div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm text-light" style="background-color: #F56904;" data-bs-toggle="modal" data-bs-target="#facultyCategoryModal">Create Account</button>
                                            <a href="./admin-faculty-account.php" class="btn btn-sm text-light" style="background-color: #F56904;">View Accounts</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded">
                                    <div class="card-header"><span class="fw-bold fs-5">Student</span></div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm text-light" style="background-color: #F56904;" data-bs-toggle="modal" data-bs-target="#studentCategoryModal">Create Account</button>
                                            <a href="./admin-student-account.php" class="btn btn-sm text-light" style="background-color: #F56904;">View Accounts</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Student Modal -->
                    <!--Modal for Student-->
                    <div class="modal fade" id="studentCategoryModal">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalToggleLabel2">Create Student Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="/admin/functions/insertAccountStudent.php" method="POST">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Please fill out the following fields</h6>
                                                <div class="form-floating mt-2">
                                                    <input type="text" name="account_type" id="account_type" value="student" class="form-control" id="floatingInput" placeholder="" disabled>
                                                    <input type="hidden" name="account_type" id="account_type" value="student" class="form-control" id="floatingInput" placeholder="">
                                                    <label for="floatingInput">Account Type</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="username" id="username" placeholder="" required pattern="[0-9]+">
                                                    <label for="floatingInput">Student Id</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="firstname" id="firstname" placeholder="">
                                                    <label for="floatingInput">First Name</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="middlename" id="middlename" placeholder="">
                                                    <label for="floatingInput">Middle Name</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="lastname" id="lastname" placeholder="">
                                                    <label for="floatingInput">Last Name</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                            <br>
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="year_section" id="year_section" placeholder="">
                                                    <label for="floatingInput">Year and Section</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="email" id="email" placeholder="">
                                                    <label for="floatingInput">Email Address</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="">
                                                    <label for="floatingInput">Contact Number</label>
                                                </div>

                                                <select name="gender" id="gender" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                                    <option selected>Please select your gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="address" id="address" placeholder="">
                                                    <label for="floatingInput">Address</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="password" id="password"  placeholder="">
                                                    <label for="floatingInput">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                                        <button type="submit" name="save_account" id="save_account" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!--Modal for Faculty/Employee-->
                    <div class="modal fade" id="facultyCategoryModal">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Faculty Information</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                        
                                    <div class="modal-body">
                                        <form action="/admin/functions/insertAccountFaculty.php" method="POST" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Please fill out the following fields</h6>
                                                    <select name="account_type" id="account_type" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                                    <option selected>Please select account type</option>
                                                        <option value="faculty">Faculty</option>
                                                        <option value="admin">Admin</option>
                                                    </select>
                                                
                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="username" id="username" class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Employee Id</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="firstname" id="firstname"class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">First Name</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="middlename" id="middlename"class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Middle Name</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="lastname" id="lastname"class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Last Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <br>
                                                    <select name="gender" id="gender" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                                        <option selected>Please select your gender</option>
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="email" id="email"class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Email Address</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="contact_number" id="contact_number" class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Contact Number</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="address" id="address" class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Address</label>
                                                    </div>

                                                    <div class="form-floating mt-2">
                                                        <input type="text" name="password" id="password"class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Password</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <br> 
                                            <h6 class="mt-3">Set day for Consultation and Advising Availability</h6>
                                            <select name="day" id="day_availability" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                                <option selected>Please select a day of availability</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                            </select>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mt-2">
                                                        <input type="time" name="time_start_availability" id="time_start_availability" class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">Start Time</label>
                                                    </div>  
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating mt-2">
                                                        <input type="time" name="time_end_availability" id="time_end_availability" class="form-control" id="floatingInput" placeholder="">
                                                        <label for="floatingInput">End Time</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="row">
                                                <h6 class="mt-1">Upload Photo
                                                    <br>
                                                    <small class="text-muted">Note: 1x1 photo should have be in a plain white background.</small>
                                                </h6>

                                                <div class="input-group">
                                                    <input type="file" name="image" class="form-control" accept=".jpeg,.jpg,.png" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                                                <button type="submit" name="save_changes" id="save_changes" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="accountModal">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Account Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <!--Account Details-->
                                    <h5 class="fw-bold">Employee Information:</h5>
                                    <div class="card mb-4" style="max-width: 1024px;">
                                        <div class="row g-0">
                                            <div class="col-md-4">
                                                <img src="/assets/img/profile-icon.png" class="img-fluid rounded-start p-3" alt="...">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <div class="mb-1">
                                                        <label for="exampleFormControlInput1" class="form-label">Employee Number:</label>
                                                        <input type="text" name="prof_id" id="prof_id" value="<?php echo $username?>" class="form-control"  placeholder="" disabled>
                                                        <input type="hidden" name="prof_id" id="prof_id" value="<?php echo $username?>" class="form-control" placeholder="">
                                                    </div>
                                                    <div class="mb-1">
                                                        <label for="exampleFormControlInput1" class="form-label">Full Name:</label>
                                                        <input type="email" value="<?php echo $fullname?>" class="form-control" id="exampleFormControlInput1" placeholder="" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-2 m-2">
                                        <div class="card-header fw-bold">
                                            Availability schedule:
                                        </div>
                                            <div class="card-body">
                                                <?php
                                                    
                                                    // Fetch availability data from the database
                                                    $query = "SELECT * FROM prof_availability WHERE prof_id = $username ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
                                                    $result = mysqli_query($conn, $query);

                                                    $currentDay = null; // Variable to store the current day

                                                    echo '<table class="table table-sm">';

                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        // Check if the day is equal to or later than the current day
                                                        if (strtotime($row['day']) >= strtotime(date('Y-m-d', strtotime('today')))) {

                                                            // Display the day label only if it's different from the current day
                                                            if ($row['day'] != $currentDay) {

                                                                // If it's not the first day, close the previous day's row
                                                                if ($currentDay !== null) {
                                                                    echo '</td></tr>';
                                                                }

                                                                // Start a new row for the current day
                                                                echo '<tr><th scope="row">' . date('F j, Y (l)', strtotime($row['day'])) . '</th><td>';
                                                                $currentDay = $row['day'];
                                                            }

                                                            // Check if the keys exist in the row before accessing them
                                                            if (isset($row['time_start_availability']) && isset($row['time_end_availability'])) {
                                                                // Convert military time to 12-hour format with uppercase AM/PM
                                                                $startTime = date('h:ia', strtotime($row['time_start_availability']));
                                                                $endTime = date('h:ia', strtotime($row['time_end_availability']));

                                                                // Format time in 12-hour format with uppercase AM/PM
                                                                $startTimeFormatted = date('h:i A', strtotime($startTime));
                                                                $endTimeFormatted = date('h:i A', strtotime($endTime));

                                                                // Combine the time range into a single string
                                                                $timeRange = $startTimeFormatted . ' - ' . $endTimeFormatted;

                                                                // Display the clickable time range for the day
                                                                echo '' . $timeRange . '<br>';
                                                            }
                                                        }
                                                    }

                                                    // Close the last day's row
                                                    if ($currentDay !== null) {
                                                        echo '</td></tr>';
                                                    }

                                                    echo '</table>';
                                                ?>

                                            </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!--For logout-->
                    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <h4>Are you sure you want to logout?</h4>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                    <button type="submit" id="logoutBtn" class="btn btn-primary">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                        <!-- Your JavaScript code -->
                        <script>
                            $(document).ready(function () {
                                $('#logoutBtn').on('click', function () {
                                    // Redirect to logout.php when the button is clicked
                                    window.location.href = '../../index.php';
                                });
                            });
                    </script>


                </div>
            </div>
        </div>
    </div>
<!--Main Content-->
</div>

<!--Chatbot if necessary-->

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>