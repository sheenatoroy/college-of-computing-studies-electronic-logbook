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
    $sql = "SELECT firstname, lastname, username, account_type, year_section FROM student WHERE username = $username";
    $result = mysqli_query($conn, $sql);

    //check if the query returned any rows
    if(mysqli_num_rows($result) > 0){

        //retrieve the first row from the result set
        $row = mysqli_fetch_assoc($result);

        $fullname = $row["firstname"] . " " . $row["lastname"];
        $username = $row["username"];
        $account_type = $row['account_type'];
        $year_section = $row['year_section'];
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

    <title>CCS: E-LOG | Dashboard</title>
</head>
<style>
    body{
        font-family: 'Poppins', sans-serif;
    }

    .timeline {
        margin: 0 auto;
        padding: 20px 0;
        max-width: 100%;
    }

    .timeline-outer {
        border-left: 2px solid #333;
        margin-left: 10px; /* Adjust spacing */
        padding-left: 10px; /* Adjust spacing */
    }

    .timeline-card {
        position: relative;
        margin: 0 0 20px 13px;
        padding: 8px;
        background: rgb(255, 255, 255);
        color: rgb(0, 0, 0);
        border-radius: 8px;
        max-width: 100%;
    }

    .timeline-info {
        display: flex;
        flex-direction: row;
        align-items: center; /* Align items horizontally */
        gap: 10px; /* Add gap between items */
    }

    .timeline-title {
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0; /* Remove default margin */
    }

    .timeline-title::before {
        content: " ";
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: white;
        border-radius: 999px;
        left: -34px;
        border: 3px solid #e78121f8;
    }
    .timeline-info h6 {
        margin-bottom: 0; /* Remove default margin */
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
        WHERE a.remarks = 'Pending' AND (s.username = '$username')
        AND (p.username IS NOT NULL) AND (p.username <> '$username')
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
                            if ($account_type === 'student') {
                                $action = 'You have received';
                                $appointmentType = 'an appointment with';
                            } else {
                                $action = 'You set an appointment with';
                                $appointmentType = '';
                            }
                        ?>
                        <div class="alert alert-info" role="alert">
                            <strong><?php echo $action; ?> <?php echo $appointmentType; ?>:</strong>
                            <a href="../student/student-pending-appointment.php">
                                <?php echo $row['prof_firstname'] . ' ' . $row['prof_lastname'] . ' on ' . date("F j, Y", strtotime($row["day"])) . ' at ' . date("h:i A", strtotime($row["time_start"])) . ' '; ?>
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
                    if ($notificationCount > 0 && strpos($username, 'student') === false) {
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
                    <a class="nav-link fs-6 p-1 m-1" href="/student/student-dashboard.php" style="color: #131313;"> 
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
                        <li><a class="dropdown-item" href="/student/student-set-appointment.php">Set Appointment</a></li>
                        <li><a class="dropdown-item" href="/student/student-history-appointment.php">History Appointments</a></li>
                    </ul>
                </div>

                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/student/student-feedback.php" style="color: #131313;">
                        <i class='bx bx-message-square-add'></i>
                        <span>Feedbacks</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1"  href="/student/student-terms-and-conditions.php"style="color: #131313;">
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
                    <?php
                        $sql = "SELECT
                                    a.appointment_id,
                                    a.student_id,
                                    a.prof_id,
                                    a.time_start,
                                    a.time_end,
                                    a.day,
                                    a.appointment_status,
                                    a.type_of_concern,
                                    a.specific_concern,
                                    a.detailed_concern,
                                    a.remarks,
                                    a.appoint_by,
                                    a.action_report_path,
                                    a.action_report_textbox,

                                    s.firstname AS student_firstname,
                                    s.lastname AS student_lastname,
                                    s.middlename AS student_middlename,
                                    s.year_section AS student_year_section,
                                    s.email AS student_email,

                                    p.firstname AS prof_firstname,
                                    p.lastname AS prof_lastname,
                                    p.middlename AS prof_middlename

                                    FROM appointments a
                                    JOIN student s ON a.student_id = s.username
                                    JOIN prof p ON a.prof_id = p.username
                                    WHERE a.remarks = 'Approved'
                                        AND (s.username = '$username')
                                    ORDER BY a.day ASC;";
                        $result = mysqli_query($conn, $sql);
                    ?>
                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                    <h4 class="pt-2 mb-3"><i class='bx bx-list-ul mx-2'></i>Approved Appointments</h4>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <form method="post" class="input-group mb-3">
                                    <input type="text" class="form-control mr-2" name="search" placeholder="Search" aria-describedby="button-addon2">
                                    <button class="btn btn-secondary" type="submit" id="search">Search</button>
                                </form>
                            </div>
                        </div>

                        <?php
                        if (isset($_POST['search'])) {

                            $searchTerm = "%{$_POST['search']}%";
                            
                        
                            $sql_appointments = "SELECT 
                                                    s.firstname AS student_firstname,
                                                    s.lastname AS student_lastname,
                                                    s.middlename AS student_middlename,
                                                    p.firstname AS professor_firstname,
                                                    p.lastname AS professor_lastname,
                                                    p.middlename AS professor_middlename,
                                                    a.appointment_id,
                                                    a.appointment_id,
                                                    a.student_id,
                                                    a.prof_id,
                                                    a.time_start,
                                                    a.time_end,
                                                    a.day,
                                                    a.appointment_status,
                                                    a.type_of_concern,
                                                    a.specific_concern,
                                                    a.detailed_concern,
                                                    a.remarks,
                                                    a.evaluation_status,
                                                    a.appoint_by,
                                                    a.action_report_path,
                                                    a.action_report_textbox

                                                FROM appointments a 
                                                JOIN student s ON a.student_id = s.username
                                                JOIN prof p ON a.prof_id = p.username
                                                WHERE a.student_id = ? AND a.remarks = 'Approved'
                                                AND (s.firstname LIKE ? OR s.lastname LIKE ? OR a.type_of_concern LIKE ? OR a.appointment_status LIKE ? OR p.firstname LIKE ? OR. p.lastname LIKE ?)";

                            if ($stmt = $conn->prepare($sql_appointments)) {
                                $stmt->bind_param("sssssss", $username, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
                                $stmt->execute();
                                $result_appointments = $stmt->get_result();
                            } else {
                                // Handle error
                                echo "Error in preparing statement: " . $conn->error;
                                exit; // Stop execution after encountering an error
                            }
                        } else {
                            // If search term is not set, fetch all appointments
                            $sql_appointments = "SELECT 
                                                    s.firstname AS student_firstname,
                                                    s.lastname AS student_lastname,
                                                    s.middlename AS student_middlename,
                                                    s.email AS email,
                                                    s.year_section AS year_section,
                                                    p.firstname AS professor_firstname,
                                                    p.lastname AS professor_lastname,
                                                    p.middlename AS professor_middlename,
                                                    
                                                    a.appointment_id,
                                                    a.student_id,
                                                    a.prof_id,
                                                    a.time_start,
                                                    a.time_end,
                                                    a.day,
                                                    a.appointment_status,
                                                    a.type_of_concern,
                                                    a.specific_concern,
                                                    a.detailed_concern,
                                                    a.remarks,
                                                    a.evaluation_status,
                                                    a.appoint_by,
                                                    a.action_report_path,
                                                    a.action_report_textbox

                                                FROM appointments a 
                                                JOIN student s ON a.student_id = s.username
                                                JOIN prof p ON a.prof_id = p.username
                                                WHERE a.student_id = '$username' AND a.remarks= 'Approved'";
                            
                            $result_appointments = mysqli_query($conn, $sql_appointments);
                        }

                        // Output HTML
                        ?>
                        <div class='text-dark shadow-sm rounded table-responsive mb-3'>
                            <?php
                            if ($result_appointments) {
                                if (mysqli_num_rows($result_appointments) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_appointments)) {
                                        // Calculate the remaining days from the appointment date
                                        $appointmentDate = strtotime($row["day"]);
                                        $currentDate = strtotime(date("Y-m-d"));
                                        $remainingDays = floor(($appointmentDate - $currentDate) / (60 * 60 * 24));
                                        $appointment_id = $row['appointment_id'];
                                        $year_section = $row['year_section'];
                                        $email = $row['email'];
                                        $student_id = $row['student_id'];
                                        $time_start = $row['time_start'];
                                        $time_end = $row['time_end'];
                                        $last_name = $row['student_lastname'];
                                        $first_name = $row['student_firstname'];
                                        $firstLetterMiddleName = substr($row['student_middlename'], 0, 1);
                                        $action_report_path = $row["action_report_path"];
                                        $textbox = $row['action_report_textbox'];
                                        $file_path = $action_report_path;
                                        $file_name = basename($file_path);

                                        $lastname_prof = $row['professor_lastname'];
                                        $firstname_prof = $row['professor_firstname'];
                                        $firstLetterMiddleNameProf = substr($row['professor_middlename'], 0, 1);

                                        $concern = $row['type_of_concern'];
                                        $appointment_status = $row['appointment_status'];
                                        $remarks = $row['remarks'];
                                        $day = $row['day'];

                                        $prof_id = $row['prof_id'];

                                        $student_fullname = $last_name . ', ' . $first_name . ' ' . $firstLetterMiddleName . '.';
                                        $prof_fullname = $lastname_prof . ', ' . $firstname_prof . ' ' . $firstLetterMiddleNameProf . '.';
                                        // Update remarks if appointment is on the same day
                                        if ($remainingDays == 0) {
                                            $appointmentId = $row["appointment_id"];
                                            $updateQuery = "UPDATE appointments SET remarks = 'Unresolved', evaluation_status = 'Done' WHERE appointment_id = '$appointmentId'";
                                            mysqli_query($conn, $updateQuery);
                                        }
                                        echo "<div class='text-dark shadow-sm rounded table-responsive mb-3'>";
                                        echo "<div class='card'>";
                                        echo "<div class='card-body'>";
                                        echo "<b class='card-title fs-5'>Appointment ID: " . $appointment_id . "</b>";
                                        echo "<p class='card-text mt-1'>";
                                        echo "<strong>Student Name: </strong>" . htmlspecialchars($student_fullname) . "<br>";
                                        echo "<strong style='display: none';>Prof ID: " . htmlspecialchars($prof_id) . "<br> </strong>";
                                        echo "<strong>Appointment with: </strong>" . htmlspecialchars($prof_fullname) ."<br>";
                                        echo "<strong>Type of Concern:</strong> " . htmlspecialchars($concern) . "<br>";
                                        echo "<strong>Appointment Status:</strong> " . htmlspecialchars($appointment_status) . "<br>";
                                        echo "<strong>Date of Appointment:</strong> " . htmlspecialchars($day) . "<br>";
                                        echo "<strong>Time of Appointment:</strong> " . date('h:i A', strtotime($row['time_start']));
                                        echo "</p>";
                                        echo "</div>";    

                                        echo "<div class='card-footer'>";
                                        echo "<div class='btn-group btn-group-sm' role='group'>
                                                    <button type='button' class='btn btn-warning text-white m-1 viewBtn' title='View'
                                                    data-appointment-id=" . $row['appointment_id'] . "
                                                    data-student-id=" . $row['student_id'] . "
                                                    data-remarks= " . $row['remarks'] . "
                                                    data-appoint-by=". $row['appoint_by']."
                                                    data-prof-id=" . $row['prof_id'] . "
                                                    data-prof-name='" . htmlspecialchars($row['professor_firstname'] . ' ' . $row['professor_lastname']) . "'
                                                    data-bs-toggle='modal' data-bs-target='#viewStatusModal'>
                                                    <i class='fa fa-eye' style='color:#ffffff'></i> View Documentation
                                                    </button>";
                                                    
                                        echo "</div>";

                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>No appointments found.</p>";
                                }
                            } else {
                                echo "Error: " . htmlspecialchars(mysqli_error($conn));
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!--Reschedule Modal-->
                <div class="modal fade" id="rescheduleModal">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Reschedule Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="rescheduleAppointment.php" method="POST">
                                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            
                                        <h5 class="mt-1">Professor Details:</h5>
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="appointment_id"  id="appointment_id" value=""  placeholder="" disabled>
                                                <input type="hidden" class="form-control" name="appointment_id" id="hidden_appointment_id" value="" >
                                                <label for="floatingInput">Appointment Id</label>
                                            </div>
                                            
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="prof_id_resched" id="prof_id_resched1" value="" id="floatingInput" placeholder="" disabled>
                                                <input type="hidden" class="form-control" name="prof_id_resched_hidden" id="prof_id_resched_hidden" value="" id="floatingInput" placeholder="">
                                                <label for="floatingInput">Employee Number</label>
                                            </div>

                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="prof_name_resched" id="prof_name_resched" value="" placeholder="" disabled>
                                                <label for="floatingInput">Professor Name</label>
                                            </div>
                                        </div>
                                
                                        <div class="col-md-6">
                                            <h5 class="mt-1">Student Details:</h5>
                                            
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="student_id" id="student_id" value="" placeholder="" disabled>
                                                <input type="hidden" class="form-control" name="student_id" id="hidden_student_id" value="">
                                                <label for="floatingInput">Student Number</label>
                                            </div>
                                            
                                            <div class="form-floating mt-2">
                                            <input type="text" class="form-control" id="student_name" placeholder="" disabled>
                                            <input type="hidden" class="form-control" id="student_name" placeholder="">
                                            <label for="floatingInput">Selected Student</label>
                                            </div>  
                                            
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="student_year_section" id="student_year_section" placeholder="" disabled>
                                                <input type="hidden" class="form-control" name="student_year_section" id="student_year_section" placeholder="">
                                                <label for="floatingInput">Year and Section</label>
                                            </div>

                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control" name="student_email" id="student_email" placeholder="" disabled>
                                                <input type="hidden" class="form-control" name="student_email" id="student_email" placeholder="">
                                                <label for="floatingInput">Email</label>
                                            </div>
                                        </div>
                                    </div>


                                    <!--Appointment Transaction-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="mt-1">Appointment Details:</h5>
                                                <div class="form-floating mt-2">
                                                    <select class="form-select" name="edit_day" id="edit_day">
                                                        <option selected>Day Availability</option>
                                                    </select>
                                                </div>
                                                <!-- Time Start -->
                                                <div class="form-floating mt-2">
                                                    <select class="form-select" name="time_start" id="time_start">
                                                        <option selected>Consultation Hours</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-floating mt-2" style="display: none;">
                                                    <select class="form-select" name="time_end" id="time_end">
                                                        <option selected>Time End</option>
                                                    </select>
                                                </div>

                                                <select id="firstChoice" onchange="updateOptions()" name="type_of_concern" class="form-select mt-4" aria-label="Default select example" style="height: 60px;">
                                                <option selected>Please select a type of concern</option>
                                                <option value="Advising">Advising</option>
                                                <option value="Consultation">Consultation</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <br>
                                                <select id="secondChoice" class="form-select mt-2" name="specific_concern" aria-label="Default select example" style="height: 60px;" >
                                                    <option selected>Specify your concern</option>
                                                </select>

                                                <select id="appointment_status" name="appointment_status" class="form-select mt-2" aria-label="Default select example" style="height: 60px;" hidden>
                                                    <option value="default">Appointment Status</option>
                                                    <option value="Priority">Priority</option>
                                                    <option value="Standard">Standard</option>
                                                </select> 

                                                <script>
                                                    function updateOptions() {
                                                        var firstChoice = document.getElementById('firstChoice');
                                                        var secondChoice = document.getElementById('secondChoice');
                                                        var appointmentStatus = document.getElementById('appointment_status');

                                                        // Clear existing options
                                                        secondChoice.innerHTML = '';

                                                        // Get the selected value from the first choice
                                                        var selectedValue = firstChoice.value;

                                                        // Add options based on the selected value
                                                        if (selectedValue === 'Advising') {
                                                            var advising = document.createElement('option');
                                                            advising.value = 'Option exploration and Goal setting';
                                                            advising.text = 'Option exploration and Goal setting';
                                                            secondChoice.add(advising);

                                                            var advising = document.createElement('option');
                                                            advising.value = 'Program Policy and Procedure Clarification';
                                                            advising.text = 'Program Policy and Procedure Clarification';
                                                            secondChoice.add(advising);

                                                            // Set appointment status to 'Priority' for advising
                                                            appointmentStatus.value = 'Priority';
                                                        } else if (selectedValue === 'Consultation') {
                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Study Techniques';
                                                            consultation.text = 'Study Techniques';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Class Participation';
                                                            consultation.text = 'Class Participation';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Tutorial and Review Sessions';
                                                            consultation.text = 'Tutorial and Review Sessions';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Addressing Learning Concerns';
                                                            consultation.text = 'Addressing Learning Concerns';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Student Academic Record';
                                                            consultation.text = 'Student Academic Record';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Academic Conferences';
                                                            consultation.text = 'Academic Conferences';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Academic Planning';
                                                            consultation.text = 'Academic Planning';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Support Plans';
                                                            consultation.text = 'Support Plans';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Progress Monitoring';
                                                            consultation.text = 'Progress Monitoring';
                                                            secondChoice.add(consultation);

                                                            var consultation = document.createElement('option');
                                                            consultation.value = 'Parent/Guardian Communication';
                                                            consultation.text = 'Parent/Guardian Communication';
                                                            secondChoice.add(consultation);

                                                            // Set appointment status to 'Standard' for consultation
                                                            appointmentStatus.value = 'Standard';
                                                        } else {
                                                            // Reset appointment status for other concerns
                                                            appointmentStatus.value = 'default';
                                                        }
                                                    }
                                                </script>


                                                <div class="form-floating mt-2">
                                                <textarea class="form-control" name="detailed_concern" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">Detailed Concern</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                <textarea class="form-control" name="resched_reason" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">Reason for Rescheduling</label>
                                                </div>

                                                <div class="form-floating mt-2" hidden>
                                                    <select name="remarks" id="remarks" class="form-select mb-2" aria-label="Default select example" style="height: 60px;" disabled>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Done">Done</option>
                                                    </select>

                                                    <select style="display:none;" name="remarks" id="remarks" class="form-select mb-2" aria-label="Default select example" style="height: 60px;">
                                                    <option value="Pending">Pending</option>
                                                    <option value="Done">Done</option>
                                                    </select>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                                            <button type="submit" name="updateAppointment" class="btn btn-primary"  onclick="generatePDF()">Update Appointment</button>
                                    </div>
                                </div>
                            </form> 
                        </div>
                    </div>
                </div><!--closing for modal-->

                
                <script>
                    $(document).ready(function () {
                        $('.reschedBtn').on('click', function (e) {
                            e.preventDefault();

                            $('#rescheduleModal').modal('show');    

                            var appointment_id = $(this).closest('.card').find('.reschedBtn').data('appointment-id');                                
                            var prof_id = "<?php echo $prof_id?>";
                            var prof_fullname = "<?php echo $prof_fullname; ?>";
                            var student_id = "<?php echo $username; ?>";
                            var student_fullname = "<?php echo $student_fullname; ?>";
                            var year_section = "<?php echo $year_section; ?>";
                            var student_email = "<?php echo $email; ?>";
                            var days = "<?php echo $day?>"

                            $('#appointment_id').val(appointment_id);
                            $('#hidden_appointment_id').val(appointment_id);
                            $('#prof_id_resched1').val(prof_id);
                            $('#prof_name_resched').val(prof_fullname);
                            $('#student_id').val(student_id);
                            $('#hidden_student_id').val(student_id);
                            $('#student_name').val(student_fullname);
                            $('#student_year_section').val(year_section);
                            $('#student_email').val(student_email);

                            var prof_id = document.getElementById('prof_id_resched1').value;
                            console.log("Prof ID: ", prof_id);

                            const appointmentId = this.getAttribute('data-appointment-id');
                            const studentId = this.getAttribute('data-student-id');
                            const profId = this.getAttribute('data-prof-id');
                            const profName = this.getAttribute('data-fullname-prof');
                            const email = this.getAttribute('data-email');
                            const studentName = this.getAttribute('data-student-name');
                            const yearSection = this.getAttribute('data-year-section');

                            document.getElementById('appointment_id').value = appointmentId;
                            document.getElementById('prof_id_resched1').value = profId;
                            document.getElementById('prof_name_resched').value = profName;
                            document.getElementById('student_id').value = studentId;
                            document.getElementById('student_name').value = studentName;
                            document.getElementById('student_year_section').value = yearSection;
                            document.getElementById('student_email').value = email;

                            console.log("Appointment ID: ", appointmentId);
                            console.log("Prof ID: ", profId);
                            console.log("Prof Name: ", profName);
                            console.log("Student ID: ", studentId);
                            console.log("Student Name: ", studentName);
                            console.log("Year Section: ", yearSection);
                            console.log("Email: ", email);
                            //console.log("Prof Name: ", prof_name);

                            document.getElementById('prof_id_resched_hidden').value = prof_id;

                            $('#edit_day').html('<option selected>Day Availability</option>');

                            $.ajax({
                                url: './functions/fetch_day_availability.php',
                                method: 'GET',
                                data: { prof_id_resched1: prof_id },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        response.day.forEach(function (day) {
                                            $('#edit_day').append($('<option>', {
                                                value: day,
                                                text: day
                                            }));
                                        });
                                    } else {
                                        console.error('Error fetching availability:', response.error);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error fetching availability:', error);
                                }
                            });

                            $('#time_start').html('<option selected>Consultation Hours</option>');
                            
                            $('#edit_day').on('change', function () {
                                var selectedDay = $(this).val(); // Get the selected day
                                var profId = $('#prof_id_resched1').val();

                                // Fetch availability based on the selected student and day
                                $.ajax({
                                    url: './functions/fetch_hourly_availability.php',
                                    method: 'GET',
                                    data: { prof_id_resched1: prof_id, edit_day: selectedDay }, // Pass both student ID and selected day
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.success) {
                                            $('#time_start').html('<option selected>Consultation Hours</option>'); // Clear previous options
                                            response.timeSlots.forEach(function (timeSlots) {
                                                var option = $('<option>', {
                                                    value: timeSlots,
                                                    text: timeSlots
                                                });
                                                $('#time_start').append(option);
                                            });
                                        } else {
                                            console.error('Error fetching availability:', response.error);
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching availability:', error);
                                    }
                                });
                            });

                            $('#time_end').html('<option selected>Time End</option>');

                            $('#edit_day').on('change', function () {
                                var selectedDay = $(this).val(); // Get the selected day
                                var profId = $('#prof_id_resched1').val(); // Get the student ID

                                // Fetch availability based on the selected student and day
                                $.ajax({
                                    url: './functions/fetch_time_end_availability.php',
                                    method: 'GET',
                                    data: { prof_id_resched1: profId, edit_day: selectedDay }, // Pass both student ID and selected day
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.success) {
                                            $('#time_end').html('<option selected>Time End</option>'); // Clear previous options
                                            response.timeEnd.forEach(function (timeEnd) {
                                                var option = $('<option>', {
                                                    value: timeEnd,
                                                    text: timeEnd
                                                });
                                                $('#time_end').append(option);
                                            });
                                        } else {
                                            console.error('Error fetching availability:', response.error);
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching availability:', error);
                                    }
                                });
                            });
                            //console.log('Professor ID:', prof_id);

                        });
                    });
                </script>


                <!--Modal for View Status-->
                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                <?php
                $statusMessage = "";
                $remarks = "";
                $disabled = true; // Initial state (adjust based on your logic)

                // Query to get appointment details and statusMessage
                if(isset($_GET['appointment_id'])) {
                    $appointment_id = $_GET['appointment_id'];

                    $sql1 = "SELECT a.remarks, a.student_id, a.prof_id, a.appointment_id, s.firstname, s.lastname
                                FROM appointments AS a
                                JOIN student AS s ON a.student_id = s.username
                                WHERE a.appointment_id = ?";
                    $stmt = $conn->prepare($sql1);
                    $stmt->bind_param("i", $appointment_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $remarks = $row['remarks'];
                        $fullname = $row['firstname'] . ' ' . $row['lastname'];
                        $prof_id = $row['prof_id'];

                        // Set the correct statusMessage based on the appointment_status
                        if ($remarks === "Pending") {
                            $statusMessage = "Wait for approval of your appointment.";
                            $disabled = true; // Use a shorter variable name
                        } elseif ($remarks === "Approved") {
                            $statusMessage = "Your appointment is approved with <span style='color: green;'>$fullname</span>.";
                            $disabled = false;
                        } else {
                            $statusMessage = "Unknown status";
                        }
                } else {
                    $statusMessage = "Appointment not found";
                }

                    $stmt->close();
                }
                ?>


                <div class="modal fade" id="setAppointment">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Appointment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="appointment_id_input" name="appointment_id_input">
                                <input type="hidden" id="student_id_input" name="student_id_input">
                                <input type="hidden" id="prof_id_input" name="prof_id_input"> 
                                <input type="hidden" id="remarks_input" name="remarks_input" value="">
                                <input type="hidden" id="appoint_by_input" name="appoint_by_input" value="">
                                <input type="hidden" id="prof_name_input" name="prof_name_input" value="">

                                <div class="timeline">
                                    <div class="timeline-outer">
                                        <div class="timeline-card">
                                            <div class="timeline-info">
                                                <h5 class="timeline-title">Step 1: <span style="font-style:italic; font-size: 15px">Check the appointment details.</span></h5>
                                            </div>
                                            <button type="button" class="btn btn-primary mt-1 detailsBtn" data-bs-toggle="modal" data-bs-toggle="modal">View Appointment Details</button>
                                            <div class="timeline-info">
                                                <h5 class="timeline-title mt-2">Step 2:
                                                <span id="statusMessage" style="font-style: italic; font-size: 15px;"><?php echo $statusMessage; ?></span>
                                                </h5>
                                            </div>

                                            <div class="timeline-info">
                                                <h5 class="timeline-title mt-2">Step 3: <span style="font-style: italic; font-size: 15px;">Here's the accomplished action taken report.</span></h5>
                                            </div>
                                                <!-- <button type="button" class="btn btn-primary mt-1 actionReportBtn" name="actionReportBtn" id="actionReportBtn" data-bs-toggle="modal" data-bs-target="#" value="<?php echo $remarks; ?>" <?php echo $disabled ? 'disabled' : ''; ?>>
                                                    Action Report
                                            </button> -->

                                            <div class="container mt-2">
                                                <p style="font-weight: bolder;">Action Report File: </p> <a href="<?php echo $action_report_path; ?>" target="_blank" alt="<?php echo $file_name; ?>"><?php echo $file_name; ?></a>
                                                <p style="font-weight: bolder; margin-top: 10px;">Action Report Summary:</p>
                                                <div><?php echo $textbox; ?></div>
                                            </div>
                                            
                                            <div class="timeline-info">
                                                <h5 class="timeline-title mt-2">Step 4:<span style="font-style:italic; font-size: 15px"> Wait for the evaluation of appointment.</span></h5>
                                            </div>
                                            <a href="../faculty/faculty-feedback.php">
                                                <button type="button" class="btn btn-success mt-1 feedbackBtn" name="feedbackBtn" id="feedbackBtn">
                                                    Done Feedback
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                    <!--View Appointment Details-->
                    <div class="modal fade" id="viewAppointment">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">View Appointment Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="./functions/approvedAppointment.php" method="POST">
                                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mt-1">Professor Details:</h5>
                                                <input type="hidden" class="form-control" name="appointment_id_view" id="hidden_appointment_id_view" value="" >
                                                <input type="hidden" class="form-control" name="hidden_student_id_view" id="hidden_student_id_view" value="" >

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="prof_id_view" id="prof_id_view" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="prof_id_view" value="" id="hidden_prof_id_view" placeholder="">
                                                    <label for="floatingInput">Employee Number</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="prof_name_view" id="prof_name_view" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="prof_name_view" id="hidden_prof_name_view" placeholder="">
                                                    <label for="floatingInput">Professor Name</label>
                                                </div>
                                                <h5 class="mt-2">Student Details:</h5>
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="student_id_view" id="student_id_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="student_id_view" value="" id="hidden_student_id_view">
                                                    <label for="floatingInput">Student Number</label>
                                                </div>
                                                
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" id="student_firstname_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" value="">
                                                    <label for="floatingInput">Selected Student</label>
                                                </div>  
                                                
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="student_year_section_view" id="student_year_section_view"  value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="student_year_section_view" value="" placeholder="">
                                                    <label for="floatingInput">Year and Section</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="student_email_view" id="student_email_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="student_email_view" value="" placeholder="">
                                                    <label for="floatingInput">Email</label>
                                                </div>
                                            </div>
                                            <!--Appointment Transaction-->
                                            <div class="col-md-6">
                                                <h5 class="mt-1">Appointment Details:</h5>
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="day_view" id="day_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="day_view" value="" placeholder="">
                                                    <label for="floatingInput">Day of Availability</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="time_start_view" id="time_start_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="time_start_view" id="time_start_view"  value="" placeholder="">
                                                    <label for="floatingInput">Consultation Hours</label>
                                                </div>

                                                <div class="form-floating mt-2" style="display: none;">
                                                    <input type="text" class="form-control" name="time_end_view" id="time_end_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="time_end_view" id="time_end_view"  value="" placeholder="">
                                                    <label for="floatingInput">Time End</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="type_of_concern_view" id="type_of_concern_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="type_of_concern_view" id="type_of_concern_view" value="" placeholder="">
                                                    <label for="floatingInput">Type of Concern</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="specific_concern_view" id="specific_concern_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="specific_concern_view" id="specific_concern_view" value="" placeholder="">
                                                    <label for="floatingInput">Specific Concern</label>
                                                </div>

                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="appointment_status_view" id="appointment_status_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="appointment_status_view" id="appointment_status_view" value="" placeholder="">
                                                    <label for="floatingInput">Appointment Status</label>
                                                </div>
                                                <div class="form-floating mt-2">
                                                    <input type="text" class="form-control" name="detailed_concern_view" id="detailed_concern_view" value="" placeholder="" disabled>
                                                    <input type="hidden" class="form-control" name="detailed_concern_view" id="detailed_concern_view" value="" placeholder="">
                                                    <label for="floatingInput">Detailed Concern</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="remarks1" name="remarks1" value="">
                                    <input type="hidden" id="hidden_appoint_by_input_view" name="hidden_appoint_by_input_view" value="">
                                    <input type="hidden" id="hidden_prof_input_view" name="hidden_prof_input_view">
                                    <div class="modal-footer">
                                        <button type="submit" name="decline" id="declineBtn" class="btn btn-danger" data-bs-dismiss="modal" onclick="generatePDF()">Decline</button>
                                        <button type="submit" name="updateRemarks" id="approveBtn" class="btn btn-primary" onclick="generatePDF()">Approved</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                        // Function to handle click on detailsBtn
                        document.querySelectorAll('.detailsBtn').forEach(button => {
                            button.addEventListener('click', function () {
                                // Extract appointment ID
                                var appointmentId = document.getElementById('appointment_id_input').value;
                                var studentId = document.getElementById('student_id_input').value;
                                var profId = document.getElementById('prof_id_input').value;
                                var appointBy = document.getElementById('appoint_by_input').value;
                                var userloggedIn = "<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>";
                                var profName = document.getElementById('prof_name_input').value;


                                console.log("Appointment ID:", appointmentId);
                                console.log("Student ID:", studentId);
                                console.log("Prof ID:", profId);
                                console.log("Appointed by:", appointBy);
                                console.log("Prof Name: ", profName);

                                document.getElementById('hidden_appointment_id_view').value = appointmentId;
                                document.getElementById('hidden_student_id_view').value = studentId;
                                document.getElementById('prof_id_view').value = profId;
                                document.getElementById('hidden_appoint_by_input_view').value = appointBy;
                                document.getElementById('prof_name_view').value = profName;


                                var declineBtn = document.getElementById("declineBtn");
                                var approveBtn = document.getElementById("approveBtn");

                                if(userloggedIn === appointBy){
                                    declineBtn.disabled = true;
                                    approveBtn.disabled = true;
                                }else{
                                    declineBtn.disabled = false;
                                    approveBtn.disabled = false;
                                }
                                if (remarks === "Approved") {
                                    declineBtn.disabled = true;
                                    approveBtn.disabled = true;
                                } else if (remarks === "Unresolved") {
                                    declineBtn.disabled = false;
                                    approveBtn.disabled = false;
                                }

                                // Show viewAppointment modal
                                var viewAppointmentModal = new bootstrap.Modal(document.getElementById('viewAppointment'));
                                viewAppointmentModal.show();

                                // Fetch data from server
                                fetch('./functions/fetch_details.php', {
                                    method: 'POST',
                                    body: new URLSearchParams({
                                        'appointment_id_view': appointmentId,
                                        'student_id_view': studentId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    // Update modal with fetched data
                                    document.getElementById('student_id_view').value = data.student_id;
                                    document.getElementById('student_firstname_view').value = data.student_firstname + ' ' + data.student_lastname;
                                    document.getElementById('student_year_section_view').value = data.student_year_section;
                                    document.getElementById('student_email_view').value = data.student_email;
                                    document.getElementById('day_view').value = data.day;
                                    // Assuming data.time_start and data.time_end are in the format "HH:mm"
                                    var time_start = data.time_start.split(':');
                                    var time_end = data.time_end.split(':');

                                    // Function to format time in 12-hour format with AM/PM
                                    function formatTime(hours, minutes) {
                                        var ampm = hours >= 12 ? 'PM' : 'AM';
                                        hours = hours % 12;
                                        hours = hours ? hours : 12; // 12-hour clock, so 0 should be 12
                                        minutes = minutes < 10 ? '0' + minutes : minutes;
                                        return hours + ':' + minutes + ' ' + ampm;
                                    }

                                    document.getElementById('time_start_view').value = formatTime(parseInt(time_start[0]), parseInt(time_start[1]));
                                    document.getElementById('time_end_view').value = formatTime(parseInt(time_end[0]), parseInt(time_end[1]));
                                    document.getElementById('type_of_concern_view').value = data.type_of_concern;
                                    document.getElementById('specific_concern_view').value = data.specific_concern;
                                    document.getElementById('appointment_status_view').value = data.appointment_status;
                                    document.getElementById('detailed_concern_view').value = data.detailed_concern;
                                })
                                .catch(error => console.error('Error:', error));
                            });
                        });
                    });
                    </script>


                    <!-- Include jQuery library -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                        const viewButtons = document.querySelectorAll('.viewBtn');
                        const modal = document.getElementById('setAppointment');
                        const modalObj = new bootstrap.Modal(modal);

                        viewButtons.forEach(button => {
                            button.addEventListener('click', function () {
                                const appointmentId = this.getAttribute('data-appointment-id');
                                const studentId = this.getAttribute('data-student-id');
                                const remarks = this.getAttribute('data-remarks');
                                const appointBy = this.getAttribute('data-appoint-by');
                                const profId = this.getAttribute('data-prof-id');
                                const profName = this.getAttribute('data-prof-name');
                                document.getElementById('appointment_id_input').value = appointmentId;
                                document.getElementById('student_id_input').value = studentId;
                                document.getElementById('remarks_input').value = remarks;
                                document.getElementById('appoint_by_input').value = appointBy;
                                document.getElementById('prof_id_input').value = profId;
                                document.getElementById('prof_name_input').value = profName;

                                // AJAX request to fetch updated statusMessage
                                fetchStatusMessage(appointmentId, remarks);
                            });
                        });

                        function fetchStatusMessage(appointmentId, remarks) {
                            fetch('fetch_status_message.php?appointment_id=' + appointmentId)
                                .then(response => response.text())
                                .then(data => {
                                    document.getElementById('statusMessage').innerHTML = data;
                                    modalObj.show(); // Show the modal after fetching data
                                })
                                .catch(error => {
                                    console.error('Error fetching status message:', error);
                                });

                                fetch('fetch_status_message.php?remarks=' + remarks)
                                    .then(response => response.text())
                                    .then(data => {
                                        console.log('Remarks received:', remarks); // Debugging line
                                        
                                        // Enable/disable the button based on remarks
                                        const actionReportButton = document.querySelector('.actionReportBtn');
                                        const feedbackBtn = document.querySelector('.feedbackBtn');
                                        if (remarks === 'Pending') {
                                            feedbackBtn.disabled = false;

                                        }else {
                                            feedbackBtn.disabled = true;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching remarks:', error);
                                    });
                        }
                    });

                    </script>

            </div>
        </div>
    </div>
</div>
<!--Main Content-->
</div>


<!-- Modal for Profile Icon-->
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!--Account Details-->
                <form action="../student/functions/insert-availability.php" method="POST">
                <h5 class="fw-bold">Student Information:</h5>
                <div class="card mb-4" style="max-width: 1024px;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="/assets/img/profile-icon.png" class="img-fluid rounded-start p-3" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="mb-1">
                                    <label for="exampleFormControlInput1" class="form-label">Student Number:</label>
                                    <input type="text" name="student_id" id="student_id" value="<?php echo $username?>" class="form-control"  placeholder="" disabled>
                                    <input type="hidden" name="student_id" id="student_id" value="<?php echo $username?>" class="form-control" placeholder="">
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
                                
                                $query = "SELECT * FROM student_availability WHERE student_id = $username ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
                                $result = mysqli_query($conn, $query);

                                $currentDay = null; 
                                echo '<table class="table table-sm">';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    if (strtotime($row['day']) >= strtotime(date('Y-m-d', strtotime('today')))) {

                                        if ($row['day'] != $currentDay) {

                                            if ($currentDay !== null) {
                                                echo '</td></tr>';
                                            }

                                            echo '<tr><th scope="row">' . date('F j, Y (l)', strtotime($row['day'])) . '</th><td>';
                                            $currentDay = $row['day'];
                                        }

                                        if (isset($row['time_start_availability']) && isset($row['time_end_availability'])) {
                                            $startTime = date('h:ia', strtotime($row['time_start_availability']));
                                            $endTime = date('h:ia', strtotime($row['time_end_availability']));

                                            $startTimeFormatted = date('h:i A', strtotime($startTime));
                                            $endTimeFormatted = date('h:i A', strtotime($endTime));

                                            $timeRange = $startTimeFormatted . ' - ' . $endTimeFormatted;

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

                <div class="card mb-2 m-2">
                    <div class="card-header fw-bold">
                    Set your availability:
                        <!-- <button class="btn btn-primary btn-sm float-end" data-bs-target="#viewProfileModal" data-bs-toggle="modal" data-bs-dismiss="modal">Edit Schedule</button> -->
                    </div>
                    <div class="card mb-3 p-2 table-responsive">
                        <table class="table overflow-x: auto">
                            <thead>
                                <tr>
                                    <th scope="col">Day</th>
                                    <th scope="col">Set Time</th>
                                </tr>
                            </thead>

                            <tbody>
                                <th scope="row">
                                    <div class="col-sm mb-1">
                                            <b>Select Date of Availability
                                                <select name="day" id="day" class="form-control">
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>
                                                </select>
                                            </b>
                                    </div>
                                </th>

                                <td>
                                    <div class="row-sm">
                                        <div class="col-sm mb-1">
                                            <b>Start Time
                                                <input type="time" name="time_start" id="time_start" class="form-control" placeholder="Start time">
                                            </b>
                                        </div>
                                        <div class="col-sm">
                                            <b>End Time
                                                <input type="time" name="time_end" id="time_end" class="form-control"  placeholder="End time">
                                            </b>
                                        </div>
                                        <div class="col-sm">
                                            <button type="submit" class="btn btn-dark mt-1" name="addBtn">Add</button>
                                        </div>
                                    </div>
                                </td>
                            </tbody>
                        </table>
                    </div>
                </div>
                </form>
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
<!--Chatbot if necessary-->

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>