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
                    <?php
                        $sql = "SELECT * FROM student WHERE username = $username";
                        $result = mysqli_query($conn, $sql);
                    ?>

                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                        <h4 class="pt-2 mb-3"><i class='bx bx-list-ul mx-2'></i>My Appointments List</h4>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <form method="post" class="input-group mb-3">
                                        <input type="text" class="form-control mr-2" name="search" placeholder="Search" aria-describedby="button-addon2">
                                        <button class="btn btn-secondary" type="submit" id="search">Search</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#setAppointment">Set Appointment</button>
                            </div>

                            <!-- Modal for Set Appointment -->
                            <div class="modal fade" id="setAppointment" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Set an Appointment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <form action="./functions/insert-appointment.php" method="POST" >
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!--First Column-->
                                                    <div class="col-md-6">
                                                        <h5 class="mt-1">Student Details:</h5>
                                                        <div class="form-floating mt-2">
                                                            <input type="text" class="form-control" name="student_id" id="student_id" value="<?php echo $username?>" placeholder="" readonly>
                                                            <input type="hidden" class="form-control" name="appoint_by" value="<?php echo $username?>" id="floatingInput" placeholder="">
                                                            <label for="floatingInput">Student Number</label>
                                                        </div>

                                                        <div class="form-floating mt-2">
                                                            <input type="text" value="<?php echo $fullname?>" class="form-control" id="floatingInput" placeholder="" disabled>
                                                            <label for="floatingInput">Fullname</label>
                                                        </div>

                                                        <div class="form-floating mt-2">
                                                        <input type="text" name="year_section" id="year_section" value="<?php echo $year_section?>" class="form-control" id="floatingInput" placeholder="" readonly>
                                                            <label for="floatingInput">Year and Section</label>
                                                        </div>
                                                    </div>

                                                    <!--Second Column-->
                                                    <div class="col-md-6">
                                                    <h5 class="mt-1">Appointment Details:</h5>
                                                    <div class="form-floating mt-1">
                                                        <select class="form-select" name="prof_id" id="prof_id">
                                                            <option selected>Select Professor</option>
                                                            <?php
                                                                $sql_prof = "SELECT p.*
                                                                            FROM prof AS p
                                                                            WHERE p.account_type IN ('admin', 'faculty')
                                                                            AND p.username NOT IN (
                                                                                SELECT a.prof_id
                                                                                FROM appointments AS a
                                                                                WHERE a.remarks = 'Pending' AND a.remarks = 'Approved'
                                                                            )";
                                                                $result_prof = $conn->query($sql_prof);

                                                                if ($result_prof->num_rows > 0) {
                                                                    while ($row = $result_prof->fetch_assoc()) {
                                                                        $prof_username = $row['username'];
                                                                        $prof_fullname = $row['firstname'] . ' ' . $row['lastname'];
                                                                        $prof_email = $row['email'];

                                                                        echo "<option value='$prof_username'> $prof_fullname</option>";
                                                                    }
                                                                } else {
                                                                    echo "Error: " . $conn->error;
                                                                }
                                                            ?>
                                                        </select>
                                                        <label for="floatingInput">Name of Professor</label>
                                                    </div>


                                                        <div class="form-floating mt-2">
                                                            <select class="form-select" name="day" id="day">
                                                                <option selected> Day of Availability</option>
                                                            </select>
                                                            <label for="floatingInput">Day Availability</label>
                                                        </div>

                
                                                        <div class="form-floating mt-2">
                                                            <select class="form-select" name="time_start" id="time_start">
                                                                <option selected>Consultation Hours</option>
                                                            </select>
                                                            <label for="floatingInput">Consultation Hours</label>
                                                        </div>

                                                        <div class="form-floating mt-2" style="display: none;">
                                                            <select class="form-select" name="time_end" id="time_end">
                                                                <option selected>Time End</option>
                                                            </select>
                                                            <label for="floatingInput">Time End</label>
                                                        </div>

                                                        <script>
                                                            $(document).ready(function () {
                                                                $('#prof_id').on('change', function () {
                                                                    var professorId = $(this).val();

                                                                    $('#day').html('<option selected>Day of Availability</option>');

                                                                    // Fetch availability based on the selected professor
                                                                    $.ajax({
                                                                        url: './functions/fetch_day_availability.php',
                                                                        method: 'GET',
                                                                        data: { prof_id_resched1: professorId },
                                                                        dataType: 'json',
                                                                        success: function (response) {
                                                                            if (response.success) {
                                                                                response.day.forEach(function (day) {
                                                                                    $('#day').append($('<option>', {
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
                                                                });

                                                                $('#day').on('change', function () {
                                                                    var professorId = $('#prof_id').val();
                                                                    var selectedDay = $(this).val();

                                                                    // Fetch hourly availability based on the selected professor and day
                                                                    $.ajax({
                                                                        url: './functions/fetch_hourly_availability.php',
                                                                        method: 'GET',
                                                                        data: { prof_id_resched1: professorId, edit_day: selectedDay },
                                                                        dataType: 'json',
                                                                        success: function (response) {
                                                                            if (response.success) {
                                                                                $('#time_start').html('<option selected>Consultation Hours</option>');
                                                                                response.timeSlots.forEach(function (timeSlot) {
                                                                                    $('#time_start').append($('<option>', {
                                                                                        value: timeSlot,
                                                                                        text: timeSlot
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
                                                                });
                                                            });
                                                        </script>


                                                        <div class="form-floating mt-2">
                                                            <select id="firstChoice" onchange="updateOptions()" name="type_of_concern" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                                                <option selected>Please select a type of concern</option>
                                                                <option value="Advising">Advising</option>
                                                                <option value="Consultation">Consultation</option>
                                                            </select>
                                                            <label for="floatingInput">Type of Concern</label>
                                                        </div>
                                                        
                                                        <div class="form-floating mt-2">
                                                            <select id="secondChoice" class="form-select mt-2" name="specific_concern" aria-label="Default select example" style="height: 60px;" >
                                                                <option selected>Specify your concern</option>
                                                            </select>
                                                            <label for="floatingInput">Specific Concern</label>
                                                        </div>

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
                                                    <div class="modal-footer">
                                                        <button type="submit" name="insertData" class="btn btn-primary" onclick="generatePDF()">Set Appointment</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- closing of modal-->

                            <!--Scripts for Modal-->
                            <script>
                                $(document).ready(function () {
                                    $('button.appBtn').on('click', function (e) {
                                        e.preventDefault(); // Prevent form submission

                                        $('#appointmentModal').modal('show');
                                        $tr = $(this).closest('tr');
                                        var data = $tr.children("td").map(function () {
                                            return $(this).text();
                                        }).get();
                                        $('#username').val(data[0]);
                                        $('#hidden_student_id').val(data[0]);
                                        $('#student_name').val(data[1]);
                                        $('#student_year_section').val(data[2]);
                                        $('#student_email').val(data[3]);
                                        $('#day').val(data[4]);
                                        $('#hidden_day').val(data[4]);
                                        $('#time_start').val(data[5]);
                                        $('#hidden_time_start').val(data[5]);
                                        $('#time_end').val(data[6]);
                                        $('#hidden_time_end').val(data[6]);
                                        
                                    });
                                });
                            </script>
                        </div>

                        <?php
                        if (isset($_POST['search'])) {
                            // Assuming $conn is your mysqli connection
                            $searchTerm = "%{$_POST['search']}%"; // Adding wildcards to search term
                            
                            // Using prepared statements to prevent SQL injection
                            $sql_appointments = "SELECT 
                                                    s.firstname AS student_firstname,
                                                    s.lastname AS student_lastname,
                                                    s.middlename AS student_middlename,
                                                    p.firstname AS professor_firstname,
                                                    p.lastname AS professor_lastname,
                                                    p.middlename AS professor_middlename,
                                                    a.appointment_id,
                                                    a.day,
                                                    a.type_of_concern,
                                                    a.appointment_status,
                                                    a.remarks
                                                FROM appointments a 
                                                JOIN student s ON a.student_id = s.username
                                                JOIN prof p ON a.prof_id = p.username
                                                WHERE a.student_id = ? AND a.remarks = 'Pending'
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
                                                    a.evaluation_status
                                                    
                                                FROM appointments a 
                                                JOIN student s ON a.student_id = s.username
                                                JOIN prof p ON a.prof_id = p.username
                                                WHERE a.student_id = '$username' AND a.remarks = 'Pending'";
                            
                            $result_appointments = mysqli_query($conn, $sql_appointments);
                        }

                        // Output HTML
                        ?>
                        <div class='text-dark shadow-sm rounded table-responsive mb-3'>
                            <?php
                            if ($result_appointments) {
                                if (mysqli_num_rows($result_appointments) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_appointments)) {
                                        echo "<div class='text-dark shadow-sm rounded table-responsive mb-3'>";
                                        echo "<div class='card'>";
                                        echo "<div class='card-body'>";
                                        echo "<b class='card-title fs-5'>Appointment ID: {$row['appointment_id']}</b>";
                                        echo "<p class='card-text mt-1'>";
                                        $firstLetterMiddleName = substr($row['student_middlename'], 0, 1);
                                        $firstLetterMiddleNameProf = substr($row['professor_middlename'], 0, 1);
                                        echo "<strong>Student Name: </strong>" . htmlspecialchars("{$row['student_lastname']}, {$row['student_firstname']} {$firstLetterMiddleName}") . '.' . "<br>";
                                        echo "<strong>Appointment with: </strong>" . htmlspecialchars("{$row['professor_lastname']}, {$row['professor_firstname']} {$firstLetterMiddleName}") . '.' ."<br>";
                                        echo "<strong>Type of Concern:</strong> " . htmlspecialchars($row['type_of_concern']) . "<br>";
                                        echo "<strong>Appointment Status:</strong> " . htmlspecialchars($row['appointment_status']) . "<br>";
                                        echo "<strong>Date of Appointment:</strong> " . htmlspecialchars($row['day']) . "<br>";
                                        echo "<strong>Time of Appointment:</strong> " . date('h:i A', strtotime($row['time_start']));
                                        echo "</p>";
                                        echo "</div>";    

                                        echo "<div class='card-footer'>";
                                        echo "<a href='./student-pending-appointment.php' class='btn btn-success m-1' title='Reschedule' data-bs-toggle='modal'>
                                                <i class='bx bxs-edit-alt' style='color:#ffffff'></i>Reschedule Appointment
                                            </a>";
                            
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
                                            <button type="submit" class="btn btn-dark mt-1" name="addBtn" onclick="generatePDF()" >Add Availability</button>
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