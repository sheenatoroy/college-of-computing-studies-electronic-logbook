<?php

session_start();

include "../../connection.php";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap 5 CSS CDN-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!--Fontawesome-->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <!--Website Logo-->
    <link rel="icon" href="/assets/img/ccs-logo.png">

    <!--Boxicons-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/chat-bot-style.css">

    <!--Calendar-->
    <link rel="stylesheet" href="../fullcalendar/lib/main.min.css">
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../fullcalendar/lib/main.min.js"></script>

    <title>CCS: E-LOG | Student List</title>
<style>
body{
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
}
</style>
</head>
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
        WHERE a.appointment_status = 'Pending' AND (p.username = '$username')
        AND (s.username IS NOT NULL) AND (s.username <> '$username')
        ORDER BY a.day ASC;";

    $result = mysqli_query($conn, $sql);

    // Count the number of pending appointments
    $notificationCount = mysqli_num_rows($result);
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
                        $action = 'You have received';
                        $appointmentType = 'an Appointment with';
                        ?>
                        <div class="alert alert-info" role="alert">
                            <strong><?php echo $action; ?> <?php echo $appointmentType; ?>:</strong>
                            <a href="../faculty/faculty-pending-appointment.php">
                                <?php echo $action . ' ' . $appointmentType . ' ' . $row['student_firstname'] . ' ' . $row['student_lastname'] . ' on ' . date("F j, Y", strtotime($row["day"])) . ' at ' . date("h:i A", strtotime($row["time_start"])) . ' '; ?>
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
                    <a class="nav-link fs-6 p-1 m-1" href="/faculty/faculty-dashboard.php" style="color: #131313;"> 
                    <i class='bx bx-layout'></i>
                    <span style="margin-left: 3px;">My Dashboard</span>
                    </a>
                </div>

                <div class="dropdown">
                    <a class="nav-link fs-6 p-1 m-1 dropdown-toggle" href="" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false" style="color: #131313;">
                        <i class='bx bx-note'></i>
                        <span>Appointment Management</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="/faculty/faculty-set-appointment.php">Set Appointment</a></li>
                        <li><a class="dropdown-item" href="/faculty/faculty-history-appointment.php">History Appointments</a></li>
                    </ul>
                </div>

                
                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/faculty/faculty-feedback.php" style="color: #131313;">
                        <i class='bx bx-message-square-add'></i>
                        <span>Feedbacks</span>
                    </a>
                </div>


                <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1"  href="/faculty/faculty-terms-and-conditions.php"style="color: #131313;">
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
                        if (isset($_GET['year_section'])) {
                            $year_section = $_GET['year_section'];
                            
                            $sql = "SELECT 
                                s.username as student_id, 
                                s.lastname as student_lastname, 
                                s.firstname as student_firstname, 
                                s.middlename as student_middlename, 
                                s.year_section as student_year_section, 
                                s.email as student_email,
                                GROUP_CONCAT(sa.day) as days_available,
                                sa.time_start,
                                sa.time_end,
                                a.evaluation_status,
                                (SELECT evaluation_status FROM appointments WHERE student_id = s.username ORDER BY appointment_id DESC LIMIT 1) as previous_evaluation_status
                                FROM student s
                                LEFT JOIN student_availability sa ON sa.student_id = s.username
                                LEFT JOIN appointments a ON a.student_id = s.username
                                WHERE s.year_section = ?
                                GROUP BY s.username";

                
                                $stmt = mysqli_prepare($conn, $sql);
                            
                                // Bind the parameter
                                mysqli_stmt_bind_param($stmt, "s", $year_section);
                            
                                // Execute the statement
                                if (mysqli_stmt_execute($stmt)) {
                                    // Get the result
                                    $result = mysqli_stmt_get_result($stmt);
                                } else {
                                    // Print the error message
                                    echo "Error: " . mysqli_stmt_error($stmt);
                                    // Handle the error as needed
                                }
                            }
                                        
                        ?>

                        <!-- HTML table structure for displaying student list -->
                        <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3 table-responsive">
                            <?php if (isset($year_section)) : ?>
                                <h4 class="pt-2 mb-3"><i class="bx bx-list-ul mx-2"></i> Section: <?php echo $year_section; ?></h4>
                            <?php endif; ?>
                                <div class="alert alert-secondary text-dark border border-secondary p-4 table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <!-- Table header -->
                                        <thead>
                                            <tr>
                                                <th scope="col">Student Number</th>
                                                <th scope="col">Student Name</th>
                                                <th scope="col">Year and Section</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <!-- Table body -->
                                        <tbody>
                                            <?php
                                            if (isset($result) && mysqli_num_rows($result)) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                    <tr>

                                                        <!-- Display student details in each row -->
                                                        <td><?php echo $row["student_id"] ?></td>
                                                        <td><?php echo $row["student_lastname"] . ", " . $row["student_firstname"] . " " . substr($row["student_middlename"], 0, 1) . "."; ?></td>
                                                        <td><?php echo $row["student_year_section"] ?></td>
                                                        <td><?php echo $row["student_email"] ?></td>
                                                        <td style="display: none;"><?php echo isset($row['days_available']) ? $row['days_available'] : ''; ?></td>
                                                        <td style="display: none;"><?php echo $row["time_start"] ?></td>
                                                        <td style="display: none;"><?php echo $row["time_end"] ?></td>
                                                        <td>
                                                            <div class='btn-group btn-group-sm' role='group'>
                                                                <button type="button" class="btn btn-primary appBtn"   
                                                                    data-bs-toggle="modal" data-bs-target="#setApp"
                                                                    <?php echo ($row['previous_evaluation_status'] === 'Not Done') ? 'disabled' : ''; ?>>
                                                                    <?php echo ($row['previous_evaluation_status'] === 'Not Done') ? 'Not Available' : 'Set Appointment'; ?>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="5">No Records Found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Close tag for Main Content-->

<!-- Modals -->
<?php include '../../faculty/functions/header-modal.php'; ?>

<!--Modal for Set Appointment-->
<div class="modal fade" id="setApp">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
        <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Set Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../functions/insertAppointment.php" method="POST">
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                        <h5 class="mt-1">Personal Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="">
                                <input type="hidden" class="form-control" name="appoint_by" value="<?php echo $username?>" id="floatingInput" placeholder="">
                                <label for="floatingInput">Employee Number</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" value="<?php echo $fullname?>" placeholder="" disabled>
                                <label for="floatingInput">Professor Name</label>
                            </div>
                        </div>
                
                        <div class="col-md-6">
                            <h5 class="mt-1">Student Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="student_id" id="student_id" value="<?php echo $row['student_id']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="student_id" value="<?php echo $row['student_id']; ?>" id="hidden_student_id">
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
                                <!-- Student Info Form -->
                                <div class="form-floating mt-2">
                                    <select class="form-select" name="day" id="day">
                                        <option selected>Day Availability</option>
                                    </select>
                                </div>

                                <div class="form-floating mt-2">
                                    <select class="form-select" name="time_start" id="time_start">
                                        <option selected>Time Start</option>
                                    </select>
                                </div>

                                <div class="form-floating mt-2">
                                    <select class="form-select" name="time_end" id="time_end">
                                        <option selected>Time End</option>
                                    </select>
                                </div>

                                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            $('button.appBtn').on('click', function (e) {
                                                e.preventDefault();

                                                $('#setApp').modal('show');
                                                $tr = $(this).closest('tr');
                                                var data = $tr.children('td').map(function () {
                                                    return $(this).text();
                                                }).get();

                                                $('#student_id').val(data[0]);
                                                $('#hidden_student_id').val(data[0]);
                                                $('#student_name').val(data[1]);
                                                $('#student_year_section').val(data[2]);
                                                $('#student_email').val(data[3]);

                                                // Fetch remarks
                                                var remarks = ""; // Fetch the remarks here from your data

                                                // Check if remarks are "Pending"
                                                if (remarks.trim().toLowerCase() === "Pending") {
                                                    // If remarks are "Pending," hide the availability fields
                                                    $('#day').parent().hide();
                                                    $('#time_start').parent().hide();
                                                    $('#time_end').parent().hide();
                                                } else {
                                                    // Fetch availability based on the selected student
                                                    $.ajax({
                                                        url: 'fetch_day_availability.php',
                                                        method: 'GET',
                                                        data: { student_id: data[0] },
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
                                                }
                                            });

                                            $('#day').on('change', function () {
                                                var selectedDay = $(this).val();
                                                var studentId = $('#student_id').val();

                                                // Fetch start time availability
                                                $.ajax({
                                                    url: 'fetch_time_start_availability.php',
                                                    method: 'GET',
                                                    data: { student_id: studentId, edit_day: selectedDay },
                                                    dataType: 'json',
                                                    success: function (response) {
                                                        if (response.success) {
                                                            var uniqueTimeStarts = [...new Set(response.timeStart)];
                                                            $('#time_start').html('<option selected>Time Start</option>');
                                                            uniqueTimeStarts.forEach(function (timeStart) {
                                                                $('#time_start').append($('<option>', {
                                                                    value: timeStart,
                                                                    text: timeStart
                                                                }));
                                                            });
                                                        } else {
                                                            console.error('Error fetching start time availability:', response.error);
                                                        }
                                                    },
                                                    error: function (xhr, status, error) {
                                                        console.error('Error fetching start time availability:', error);
                                                    }
                                                });

                                                // Fetch end time availability
                                                $.ajax({
                                                    url: 'fetch_time_end_availability.php',
                                                    method: 'GET',
                                                    data: { student_id: studentId, edit_day: selectedDay },
                                                    dataType: 'json',
                                                    success: function (response) {
                                                        if (response.success) {
                                                            var uniqueTimeEnds = [...new Set(response.timeEnd)];
                                                            $('#time_end').html('<option selected>Time End</option>');
                                                            uniqueTimeEnds.forEach(function (timeEnd) {
                                                                $('#time_end').append($('<option>', {
                                                                    value: timeEnd,
                                                                    text: timeEnd
                                                                }));
                                                            });
                                                        } else {
                                                            console.error('Error fetching end time availability:', response.error);
                                                        }
                                                    },
                                                    error: function (xhr, status, error) {
                                                        console.error('Error fetching end time availability:', error);
                                                    }
                                                });
                                            });
                                        });
                                    </script>


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

                                <!-- <div class="form-floating mt-2" style="display: none;">
                                    <textarea class="form-control" name="appoint_by" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                                    <label for="floatingTextarea2">Appoint by</label>
                                </div> -->

                        </div>
                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                            <button type="submit" name="insertAppointment" class="btn btn-primary"  onclick="generatePDF()">Save changes</button>
                    </div>
                </div>
            </form> 
        </div>
    </div>
</div><!--closing for modal-->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
            $('#student_id').val(data[0]);
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
<!--Chatbot if necessary-->

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

