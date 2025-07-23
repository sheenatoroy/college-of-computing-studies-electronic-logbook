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

    <title>CCS: E-LOG | Subject Management</title>
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
                    <div class="row mt-4">
                    <h4 class="pt-2 mb-3"><i class="bx bx-list-ul mx-2"></i>All Subjects</h4>
                    <!-- Search bar -->
                    <div class="col-md-5">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search by subject code or name">
                    </div>
                    <div class="col-md-7 d-flex justify-content-end align-items-center"> <!-- Right side column for buttons -->
                        <button id="addLaboratorySubjectBtn" class="btn btn-primary mx-2"><i class='bx bx-plus'></i> Laboratory Subjects</button>
                        <button id="addLectureSubjectBtn" class="btn btn-primary mx-2"><i class='bx bx-plus'></i> Lecture Subjects</button>
                    </div>
                </div>

                    <!-- Table for displaying subject -->
                    <div class="container-fluid mt-4">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered" id="subjectTable">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <!-- <th>Subject ID</th> -->
                                            <th>Subject Type</th>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Units</th>
                                            <th>Day</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="subjectTableBody">
                                        <!-- Data will be inserted here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Laboratory Subject Modal -->
                    <div class="modal fade" id="addLaboratorySubjectModal" tabindex="-1" aria-labelledby="addLaboratorySubjectModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addLaboratorySubjectModalLabel">Adding new Laboratory Subject</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="subjectType" class="form-label">Subject Type:</label>
                                        <input type="text" class="form-control" id="subjectType" value="Laboratory" readonly>
                                    </div>
                                    <!-- Input fields for adding Laboratory Subject -->
                                    <div class="mb-3">
                                        <label for="subjectCode" class="form-label">Subject Code:</label>
                                        <input type="text" class="form-control" id="subjectCode">
                                    </div>
                                    <div class="mb-3">
                                        <label for="subjectName" class="form-label">Subject Name:</label>
                                        <input type="text" class="form-control" id="subjectName">
                                    </div>
                                    <div class="mb-3">
                                        <label for="units" class="form-label">Units:</label>
                                        <input type="number" class="form-control" id="units" min="3">
                                        <small class="form-text text-muted">Note: Units should be 3 or above for Laboratory Subjects.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="startTime" class="form-label">Start Time:</label>
                                        <input type="time" class="form-control" id="startTime" onchange="updateEndTime()">
                                    </div>
                                    <div class="mb-3">
                                        <label for="endTime" class="form-label">End Time:</label>
                                        <input type="time" class="form-control" id="endTime" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="day" class="form-label">Day:</label>
                                        <select class="form-select" id="day">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="saveLaboratorySubjectChanges" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lecture Subject Modal -->
                    <div class="modal fade" id="addLectureSubjectModal" tabindex="-1" aria-labelledby="addLectureSubjectModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addLectureSubjectModalLabel">Adding new Lecture Subject</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="subjectTypeLecture" class="form-label">Subject Type:</label>
                                        <input type="text" class="form-control" id="subjectTypeLecture" value="Lecture" readonly>
                                    </div>

                                    <!-- Input fields for adding Lecture Subject -->
                                    <div class="mb-3">
                                        <label for="lectureSubjectCode" class="form-label">Subject Code:</label>
                                        <input type="text" class="form-control" id="lectureSubjectCode">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lectureSubjectName" class="form-label">Subject Name:</label>
                                        <input type="text" class="form-control" id="lectureSubjectName">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lectureUnits" class="form-label">Units:</label>
                                        <input type="number" class="form-control" id="lectureUnits" min="2" max="3" onchange="updateLectureEndTime()">
                                        <small class="form-text text-muted">Note: Units should be between 2 and 3 for Lecture Subjects.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lectureStartTime" class="form-label">Start Time:</label>
                                        <input type="time" class="form-control" id="lectureStartTime" onchange="updateLectureEndTime()">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lectureEndTime" class="form-label">End Time:</label>
                                        <input type="time" class="form-control" id="lectureEndTime" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lectureDay" class="form-label">Day:</label>
                                        <select class="form-select" id="lectureDay">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="saveLectureSubjectChanges" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Edit Subject Modal -->
                    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editSubjectModalLabel">Edit Subject Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="editSubjectType" class="form-label">Subject Type:</label>
                                        <input type="text" class="form-control" id="editSubjectType">
                                    </div>
                                    <!-- Input fields for editing Subject -->
                                    <div class="mb-3">
                                        <label for="editSubjectCode" class="form-label">Subject Code:</label>
                                        <input type="text" class="form-control" id="editSubjectCode">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editSubjectName" class="form-label">Subject Name:</label>
                                        <input type="text" class="form-control" id="editSubjectName">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editUnits" class="form-label">Units:</label>
                                        <input type="number" class="form-control" id="editUnits" min="2" max="3" onchange="updateEditEndTime()">
                                        <small class="form-text text-muted">Note: Units should be between 2 and 3 for Lecture Subjects.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editStartTime" class="form-label">Start Time:</label>
                                        <input type="time" class="form-control" id="editStartTime" onchange="updateEditEndTime()">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEndTime" class="form-label">End Time:</label>
                                        <input type="time" class="form-control" id="editEndTime" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editDay" class="form-label">Day:</label>
                                        <select class="form-select" id="editDay">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="saveEditSubjectChanges" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- View Details Modal -->
                    <div class="modal fade" id="viewSubjectModal" tabindex="-1" aria-labelledby="viewSubjectModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewSubjectModalLabel">Subject Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="viewSubjectType" class="form-label">Subject Type:</label>
                                        <input type="text" class="form-control" id="viewSubjectType" readonly>
                                    </div>
                                    <!-- Read only details for subjects -->
                                    <div class="mb-3">
                                        <label for="viewSubjectCode" class="form-label">Subject Code:</label>
                                        <input type="text" class="form-control" id="viewSubjectCode" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewSubjectName" class="form-label">Subject Name:</label>
                                        <input type="text" class="form-control" id="viewSubjectName" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewUnits" class="form-label">Units:</label>
                                        <input type="number" class="form-control" id="viewUnits" min="2" max="3" onchange="updateEditEndTime()" readonly>
                                        <small class="form-text text-muted">Note: Units should be between 2 and 3 for Lecture Subjects.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewStartTime" class="form-label">Start Time:</label>
                                        <input type="time" class="form-control" id="viewStartTime" onchange="updateEditEndTime()" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewEndTime" class="form-label">End Time:</label>
                                        <input type="time" class="form-control" id="viewEndTime" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewDay" class="form-label">Day:</label>
                                        <select class="form-select" id="viewDay" readonly>
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                        </select>
                                    </div>
                                        <div class="mb-3">
                                            <label for="assignedProf" class="form-label">Assigned Prof:</label>
                                            <input type="text" class="form-control" id="assignedProf" readonly>
                                        </div>
                                    <div class="mb-3">
                                        <label for="yearAndSection" class="form-label">Year and Section:</label>
                                        <input type="text" class="form-control" id="yearAndSection" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Assign Subject to Prof Modal -->
                    <div class="modal fade" id="assignSubjectModal" tabindex="-1" aria-labelledby="assignSubjectModalLabel" aria-hidden="true" data-subject-id="" data-row-id="">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="assignSubjectModalLabel">Subject Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="mb-3">
                                        <label for="assignProf" class="form-label">Assign Faculty:</label>
                                        <select class="form-select" id="assignProf">
                                            <option value="">Select Faculty</option>
                                        </select>
                                    </div>
                                    <!-- COURSE, YEAR AND SECTION ADDITION -->
                                    <div class="mb-3">
                                        <label for="assignCourse" class="form-label">Assign Course:</label>
                                        <select class="form-select" id="course">
                                            <option value="">Select Course</option>
                                            <option value="IT">Information Technology</option>
                                            <option value="CS">Computer Science</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="assignYear" class="form-label">Assign Year Level:</label>
                                            <select class="form-select" id="year">
                                                <option value="">Select Year Level</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="assignSection" class="form-label">Assign Section:</label>
                                            <select class="form-select float-md-end" id="section">
                                                <option value="">Select Section</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                                <option value="E">E</option>
                                                <option value="F">F</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="saveAssignSubjectChanges" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

<!-- **********************************FOR CALCULATION OF TIME PURPOSES********************************* -->
<!-- Function to update the Edit end time when the units field changes -->
<script>
function updateEditEndTime() {
    console.log('updateEditEndTime function triggered');

    // Get the selected start time and units
    const startTime = document.getElementById('editStartTime').value;
    const units = parseInt($('#editUnits').val(), 10);

    // Parse hours and minutes
    const [startHour, startMinute] = startTime.split(':').map(Number);

    // Calculate end time based on the selected units
    let endHour = startHour + units;
    let endMinute = startMinute;

    // Adjust end hour and minutes if necessary
    while (endHour >= 24) {
        endHour -= 24;
    }

    // Format the end time
    const formattedEndHour = (endHour < 10 ? '0' : '') + endHour;
    const formattedEndMinute = (endMinute < 10 ? '0' : '') + endMinute;
    const endTime = `${formattedEndHour}:${formattedEndMinute}`;

    // Set the calculated end time
    document.getElementById('editEndTime').value = endTime;
}

// Function to update the Edit end time when the units field changes
$('#editUnits').on('change', function () {
        updateEditEndTime();
});

// Calculation of time per subject
function updateEndTime() {
    console.log('updateEndTime function triggered');
    // Get the selected start time
    const startTime = document.getElementById('startTime').value;

    // Parse hours and minutes
    const [startHour, startMinute] = startTime.split(':').map(Number);

    // Calculate end time based on a 3-hour duration
    let endHour = startHour + 3;
    let endMinute = startMinute;

    // Adjust end hour and minutes if necessary
    if (endHour >= 24) {
        endHour -= 24;
    }

    // Format the end time
    const formattedEndHour = (endHour < 10 ? '0' : '') + endHour;
    const formattedEndMinute = (endMinute < 10 ? '0' : '') + endMinute;
    const endTime = `${formattedEndHour}:${formattedEndMinute}`;

    // Set the calculated end time
    document.getElementById('endTime').value = endTime;
}

function updateLectureEndTime() {
    console.log('updateLectureEndTime function triggered');
    // Get the selected start time and units
    const startTime = document.getElementById('lectureStartTime').value;
    const units = parseInt($('#lectureUnits').val(), 10);

    // Parse hours and minutes
    const [startHour, startMinute] = startTime.split(':').map(Number);

    // Calculate end time based on the selected units
    let endHour = startHour + units;
    let endMinute = startMinute;

    // Adjust end hour and minutes if necessary
    while (endHour >= 24) {
        endHour -= 24;
    }

    // Format the end time
    const formattedEndHour = (endHour < 10 ? '0' : '') + endHour;
    const formattedEndMinute = (endMinute < 10 ? '0' : '') + endMinute;
    const endTime = `${formattedEndHour}:${formattedEndMinute}`;

    // Set the calculated end time
    document.getElementById('lectureEndTime').value = endTime;
}

// Function to update the Lecture end time when the units field changes
$('#lectureUnits').on('change', function() {
    updateLectureEndTime();
});


// Attach updateEndTime function to the onchange event of the startTime input
$('#startTime').on('change', function() {
    updateEndTime();
});

// Attach updateLectureEndTime function to the onchange event of the lectureStartTime input
$('#lectureStartTime').on('change', function() {
    updateLectureEndTime();
});
</script>


<!-- ***************************FOR FETCHING SUBJECTS PURPOSES (CREATING LABORATORY|LECTURE SUBJECTS)*************** -->
<!-- Fetch subjects function -->
<script>
// Function to fetch and display subjects
$('#addLaboratorySubjectBtn').click(function(){           
    // Show the Laboratory Subject modal
    $('#addLaboratorySubjectModal').modal('show');
});

// Handle click event for "Lecture Subjects" button
$('#addLectureSubjectBtn').click(function(){
    // Show the Lecture Subject modal
    $('#addLectureSubjectModal').modal('show');
});

// Function to filter table rows based on search input
$('#searchInput').on('keyup', function() {
    const searchText = $(this).val().toLowerCase();
    $('#subjectTableBody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
    });
});

function fetchSubjects() {
    // Send an AJAX request to fetch data
    $.ajax({
        type: 'GET',
        url: 'fetch_subjects.php', // Endpoint for fetching data
        dataType: 'json',
        success: function(response){
            // Sort the subjects based on Subject Code in ascending order
            response.sort((a, b) => (a.subjectCode > b.subjectCode) ? 1 : -1);
            
            // Update the table with fetched data
            updateTable(response);
        },
        error: function(error){
            // Handle error (if needed)
            console.error(error);
        }
    });
}

// Call the fetchSubjects function when the page loads
$(document).ready(function () {
    fetchSubjects();
});


//ETO INAYOS KO LAST THURSDAY
// Handle click event for "Save changes" button in Laboratory Subject modal
$('#saveLaboratorySubjectChanges').click(function(){
    // Get data from modal fields
    const subjectType = 'Laboratory'; // Hardcoded value for Laboratory
    const subjectCode = $('#subjectCode').val();
    const subjectName = $('#subjectName').val();
    const units = $('#units').val();
    const startTime = $('#startTime').val();
    const endTime = $('#endTime').val();
    const day = $('#day').val();

    // Send data to insert_subject.php using Ajax
    $.ajax({
        type: 'POST',
        url: 'insert_subject.php',
        data: {
            subjectType: subjectType,
            subjectCode: subjectCode,
            subjectName: subjectName,
            units: units,
            startTime: startTime,
            endTime: endTime,
            day: day
        },
        success: function(response){
            // Handle success
            console.log(response);
            // Close the modal
            $('#addLaboratorySubjectModal').modal('hide');
            // Show success message
            if (confirm('Successfully created a new subject! Do you want to refresh the page to see the changes?')) {
                // Reload the page
                location.reload();
            }
        },
        error: function(error){
            // Handle error (if needed)
            console.error(error);
        }
    });
});


//ETO INAYOS KO LAST THURSDAY
// Handle click event for "Save changes" button in Lecture Subject modal
$('#saveLectureSubjectChanges').click(function(){
            // Retrieve the values from the modal inputs
            const subjectType = 'Lecture'; // Hardcoded value for Laboratory
            const subjectCode = $('#lectureSubjectCode').val();
            const subjectName = $('#lectureSubjectName').val();
            const units = $('#lectureUnits').val();
            const startTime = $('#lectureStartTime').val();
            const endTime = $('#lectureEndTime').val();
            const day = $('#lectureDay').val();

            // Send data to insert_subject.php using Ajax
    $.ajax({
        type: 'POST',
        url: 'insert_subject.php',
        data: {
            subjectType: subjectType,
            subjectCode: subjectCode,
            subjectName: subjectName,
            units: units,
            startTime: startTime,
            endTime: endTime,
            day: day
        },
        success: function(response){
            // Handle success
            console.log(response);
            // Close the modal
            $('#addLectureSubjectModal').modal('hide');
            // Show success message
            if (confirm('Successfully created a new subject! Do you want to refresh the page to see the changes?')) {
                // Reload the page
                location.reload();
            }
        },
        error: function(error){
            // Handle error (if needed)
            console.error(error);
        }
    });
});
</script>


<!-- ***************************FOR EDIT SUBJECT DETAILS PER ROW PURPOSES*************** -->
<script>

function updateTable(data) {
    const tbody = $('#subjectTableBody');

    const rowsHtml = data.map(subject => {
        return `<tr style="text-align: center;">
                    <td>${subject.subjectType}</td>
                    <td>${subject.subjectCode}</td>
                    <td>${subject.subjectName}</td>
                    <td>${subject.units}</td>
                    <td>${subject.day}</td>
                    <td>${formatTime(subject.startTime)}</td>
                    <td>${formatTime(subject.endTime)}</td>
                    <td class="status" data-subject-id="${subject.subject_id}">${subject.status}</td> <!-- Add data-subject-id attribute -->
                    <td>
                        <div class='btn-group btn-group-sm' role='group'>
                            <button type="button" class="btn btn-primary m-1 btn-sm assignSubjectBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Assign Professor" onclick="handleAssignAction(${subject.subject_id})"><i class="fas fa-plus"></i> Assign Professor</button>
                            <button type="button" class="btn btn-success m-1 btn-sm assignSubjectBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Assigned Professsor " onclick="handleAssignAction(${subject.subject_id})"><i class="fas fa-pencil"></i> Update Assigned Professor</button>
                        </div>
                    </td>
                </tr>`;
    }).join('');

    tbody.html(rowsHtml);
}

function formatTime(timeString) {
        // Parse the time string into hours and minutes
        var time = new Date("2024-05-12T" + timeString);
        var hours = time.getHours();
        var minutes = time.getMinutes();
        
        // Determine AM/PM
        var ampm = hours >= 12 ? 'PM' : 'AM';
        
        // Convert hours to 12-hour format
        hours = hours % 12;
        hours = hours ? hours : 12; // 12 should be displayed as 12, not 0
        
        // Pad single digit hours and minutes with leading zeros
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        
        // Construct formatted time string
        var formattedTime = hours + ':' + minutes + ' ' + ampm;
        return formattedTime;
    }

    $(document).ready(function(){

        // Handle click event for "Edit Subjects" button
        $('#subjectTable').on('click', '.editSubjectBtn', function(){
            // Get the subject ID from the clicked row
            const subjectId = $(this).data('subject-id');

            // Fetch subject details using AJAX
            $.ajax({
                type: 'GET',
                url: `get_subject_details.php?subjectId=${subjectId}`,
                dataType: 'json',
                success: function(response){
                    // Populate and display the edit Subject modal
                    populateEditModal(response);
                },
                error: function(error){
                    // Handle error (if needed)
                    console.error(error);
                }
            });
        });

        // Function to fetch and display subjects
        function fetchSubjects() {
            // Send an AJAX request to fetch data
            $.ajax({
                type: 'GET',
                url: 'fetch_subjects.php', // Create a new PHP file for fetching data
                dataType: 'json',
                success: function(response){
                    // Update the table with fetched data
                    updateTable(response);
                },
                error: function(error){
                    // Handle error (if needed)
                    console.error(error);
                }
            });
        }

        // Call the fetchSubjects function when the page loads
        fetchSubjects();

        // Handle click event for "Save Edit changes" button in Lecture Subject modal
$('#saveEditSubjectChanges').click(function () {
    // Get the edited values from the modal
    const editedSubject = {
        subjectId: subjectDetails.subject_id,
        subjectType: $('#editSubjectType').val(),
        subjectCode: $('#editSubjectCode').val(),
        subjectName: $('#editSubjectName').val(),
        units: $('#editUnits').val(),
        startTime: $('#editStartTime').val(),
        endTime: $('#editEndTime').val(),
        day: $('#editDay').val(),
        // Additional fields...
    };

    // Send an AJAX request to update the subject
    $.ajax({
        type: 'POST',
        url: 'update_subject.php',
        data: editedSubject,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message); // Display success message
                // Additional actions if needed
            } else {
                alert('Update failed'); // Display error message
            }
        },

        success: function(response){
            // Handle success
            console.log(response);
            // Close the modal
            $('#editSubjectModal').modal('hide');
            // Show success message
            if (confirm('Changes were done successfully! Do you want to refresh the page to see the changes?')) {
                // Reload the page
                location.reload();
            }
        },

        
        error: function (error) {
            // Handle error
            console.error('Update error:', error);
            alert('Error updating details. Please try again.'); // Display error message
        }
    });
});


    });

    // Declare subjectDetails at a higher scope
    let subjectDetails;

    // Function to populate the edit modal with subject details
    function populateEditModal(details) {
        // Assign details to subjectDetails
        subjectDetails = details;

        // Update the modal title
        $('#editSubjectModalLabel').text('Editing Subject');

        // Update the modal fields with the subject details
        $('#editSubjectType').val(subjectDetails.subject_type);
        $('#editSubjectCode').val(subjectDetails.subject_code);
        $('#editSubjectName').val(subjectDetails.subject_name);
        $('#editUnits').val(subjectDetails.units);
        $('#editStartTime').val(subjectDetails.start_time);
        $('#editEndTime').val(subjectDetails.end_time);
        $('#editDay').val(subjectDetails.day);

        // Force the modal to open
        $('#editSubjectModal').modal('show');
    }

    // Function to update the table row with the edited data
    function updateTableRow(editedSubject) {
        // Select the table row using data attribute
        const editedRow = $(`#subjectTableBody tr[data-subject-id="${editedSubject.subjectId}"]`);

        // Update each cell in the row with the edited data
        editedRow.find('td:eq(1)').text(editedSubject.subjectType);
        editedRow.find('td:eq(2)').text(editedSubject.subjectCode);
        editedRow.find('td:eq(3)').text(editedSubject.subjectName);
        editedRow.find('td:eq(4)').text(editedSubject.units);
        editedRow.find('td:eq(5)').text(editedSubject.day);
        editedRow.find('td:eq(6)').text(editedSubject.startTime);
        editedRow.find('td:eq(7)').text(editedSubject.endTime);
        editedRow.find('td:eq(8)').text(editedSubject.status); // Add this line to update the status column
    }
</script>


<!-- ***************************FOR VIEW SUBJECT DETAILS PER ROW PURPOSES*************** -->
<script>
    // Function to handle view action
function handleViewAction(subjectId) {
    // Send an AJAX request to fetch subject details
    $.ajax({
        type: 'GET',
        url: `view_subject_details.php?subjectId=${subjectId}`,
        dataType: 'json',
        success: function(response){
            // Populate and display the View Subject modal
            populateViewModal(response);
        },
        error: function(error){
            // Handle error (if needed)
            console.error(error);
        }
    });
}

// Function to populate the View modal with subject details
function populateViewModal(subjectDetails) {
    // Update the modal title
    $('#viewSubjectModalLabel').text('Viewing Subject Details');

    // Update the modal fields with the subject details
    $('#viewSubjectType').val(subjectDetails.subjectType);
    $('#viewSubjectCode').val(subjectDetails.subjectCode);
    $('#viewSubjectName').val(subjectDetails.subjectName);
    $('#viewUnits').val(subjectDetails.units);
    $('#viewStartTime').val(subjectDetails.startTime);
    $('#viewEndTime').val(subjectDetails.endTime);
    $('#viewDay').val(subjectDetails.day);
    $('#assignedProf').val(subjectDetails.profName);
    $('#yearAndSection').val(subjectDetails.yearandsection);

    // Show the modal with id "viewSubjectModal"
    $('#viewSubjectModal').modal('show');
}

</script>


<!-- ***************************FOR ASSIGN SUBJECT DETAILS PER ROW PURPOSES*************** -->
<script>
// Function to populate the Assign modal with faculty names
function populateFacultyNames() {
    // Send an AJAX request to fetch faculty names from database
    $.ajax({
        type: 'POST',
        url: 'fetch_faculty_names.php', // Endpoint for fetching faculty names from database
        success: function(response){
            try {
                // Parse the JSON response
                var facultyData = JSON.parse(response);

                // Clear the current options in the select element
                $('#assignProf').empty();

                // Add the placeholder option
                $('#assignProf').append($('<option>', {
                    value: '',
                    text: 'Not Assigned'
                }));

                // Loop through the faculty data and add them as options
                facultyData.forEach(function(data) {
                    $('#assignProf').append($('<option>', {
                        value: data.username, // prof_id
                        text: data.full_name // full name
                    }));
                });
            } catch (error) {
                // Handle JSON parsing error
                console.error('Error parsing JSON:', error);
            }
        },
        error: function(xhr, status, error){
            // Handle other AJAX errors
            console.error('Error fetching faculty names:', error);
            console.log(xhr.responseText); // Log the response text for debugging
        }
    });
}

// Call the function to populate faculty names when the page is ready
$(document).ready(function() {
    populateFacultyNames();
});

// Function to handle assign action
function handleAssignAction(subjectId) {
    // Send an AJAX request to fetch subject details
    $.ajax({
        type: 'GET',
        url: `assign_to_prof.php?subjectId=${subjectId}`,
        dataType: 'json',
        success: function(response){
            // Populate and display the Assign Subject modal
            populateAssignModal(response, subjectId); // Pass subjectId to populateAssignModal
        },
        error: function(error){
            // Handle error (if needed)
            console.error(error);
        }
    });
}

// Function to populate the Assign modal with subject details
function populateAssignModal(subjectDetails, subjectId) { // Receive subjectId as an argument
    // Update the modal title
    $('#assignSubjectModalLabel').text('Assigning Subject to a Faculty');

    // Update the modal fields with the subject details
    $('#assignSubjectTypeLabel').text(subjectDetails.subjectType);
    $('#assignSubjectCodeLabel').text(subjectDetails.subjectCode);
    $('#assignSubjectNameLabel').text(subjectDetails.subjectName);
    $('#assignUnitsLabel').text(subjectDetails.units);
    $('#assignStartTimeLabel').text(subjectDetails.startTime);
    $('#assignEndTimeLabel').text(subjectDetails.endTime);
    $('#assignDayLabel').text(subjectDetails.day);
    $('#assignStatusLabel').text(subjectDetails.status);

    // Set the subjectId and rowId data attributes
    $('#assignSubjectModal').data('subject-id', subjectId); // Set subjectId
    $('#assignSubjectModal').data('row-id', ''); // Set rowId to empty initially

    // Show the modal with id "assignSubjectModal"
    $('#assignSubjectModal').modal('show');
}

// Function to handle save changes action
$('#saveAssignSubjectChanges').click(function() {
    var selectedProf = $('#assignProf').val();
    var selectedYear = $('#year').val();
    var selectedCourse = $('#course').val();
    var selectedSection = $('#section').val();
    var subjectId = $('#assignSubjectModal').data('subject-id');
    var rowId = $('#assignSubjectModal').data('row-id');

    // Log the values for debugging
    console.log('subjectId:', subjectId);
    console.log('selectedProf:', selectedProf);
    console.log('selectedYear:', selectedYear);
    console.log('selectedCourse:', selectedCourse);
    console.log('selectedSection:', selectedSection);

    // Check if subjectId, selectedProf, selectedYear, selectedCourse and selectedSection are not empty
    if (subjectId && selectedProf && selectedYear && selectedCourse && selectedSection) {
        // Update the status column in the table with the selected value for the corresponding subject ID
        $.ajax({
            type: 'POST', 
            url: 'update_status.php',
            data: {
                subjectId: subjectId,
                selectedProf: selectedProf,
                selectedYear: selectedYear,
                selectedCourse: selectedCourse,
                selectedSection: selectedSection
            },
            success: function(response) {
                // Check if the update was successful
                if (response === 'success') {
                    // Find the status cell using data-subject-id attribute
                    var statusCell = $('td[data-subject-id="' + subjectId + '"]');
                    if (statusCell.length) {
                        statusCell.text(selectedProf);
                        $('#assignSubjectModal').modal('hide');
                        location.reload();
                    } else {
                        console.error('Error: Unable to find status cell');
                    }
                } else {
                    console.error('Error updating status:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    } else {
        console.error('Error: subjectId or selectedProf is empty');
    }
});

</script>
                    
                </div>
            </div>
        </div>
    </div>
<!--Main Content-->
</div>
<!-- Modals -->
<?php include 'functions/header-modals.php'; ?>

<!--Chatbot if necessary-->

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>