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
        font-size: 14px;

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
                        <?php echo "Student Number:" . " " . $username ?>
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
                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                        <h4 class="pt-2 mb-3"><i class='bx bx-card mx-2'></i>History of Appointments</h4>
                        <div class="row row-cols-1 row-cols-md-4 g-5">
                            <div class="col">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded" style="max-width: 25rem;">
                                    <div class="card-header text-center"><span class="fw-bold fs-5">Pending Appointments</span></div>
                                    <div class="card-body mb-5">
                                        <p class="card-text">These are appointments that have been requested or scheduled but have not yet occurred.  </p>
                                    </div>
                                    <a href="../student/student-pending-appointment.php" class="btn text-light mt-3" style="background-color: #F56904;">Select</a>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded" style="max-width: 25rem; ">
                                    <div class="card-header text-center"><span class="fw-bold fs-5">Approved Appointments</span></div>
                                    <div class="card-body mb-5">
                                        <p class="card-text">These are appointments that have been confirmed and scheduled to take place.</p>
                                    </div>
                                    <a href="../student/student-approved-appointment.php" class="btn text-light mt-3" style="background-color: #F56904;">Select</a>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded" style="max-width: 25rem; ">
                                    <div class="card-header text-center"><span class="fw-bold fs-5">Accomplished Appointments</span></div>
                                    <div class="card-body mb-3">
                                        <p class="card-text">These are appointments that have been successfully completed or fulfilled. </p>
                                    </div>
                                    <a href="../student/student-accomplished-appointment.php" class="btn text-light mt-3" style="background-color: #F56904;">Select</a>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card text-dark bg-light mb-3 shadow-sm rounded" style="max-width: 25rem; ">
                                    <div class="card-header text-center"><span class="fw-bold fs-5">Unresolved Appointments</span></div>
                                    <div class="card-body mb-3">
                                        <p class="card-text">These are appointments for which there may be outstanding issues, conflicts, or uncertainties. </p>
                                    </div>
                                    <a href="../student/student-unresolved-appointment.php" class="btn text-light mt-3" style="background-color: #F56904;">Select</a>
                                </div>
                            </div>
                        </div>
                    </div>
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