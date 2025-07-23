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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../fullcalendar/lib/main.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>


    <title>CCS: E-LOG | Report and Analytics</title>
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
                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                        <h4 class="pt-2 mb-3"><i class='bx bxs-report mx-2'></i>Faculty Progress</h4>

                        
                    <!-- Button for Excel and Bar Graph -->
                    <div class="row mt-3">
    <div class="col">
        <form action="generate_excel.php" method="post" id="exportForm">
            <input type="hidden" name="faculty" id="exportFaculty">
            <input type="hidden" name="fromMonth" id="exportFromMonth">
            <input type="hidden" name="toMonth" id="exportToMonth">
            <input type="hidden" name="year" id="exportYear">
            <button type="submit" class="btn btn-success" style="margin-bottom: 10px;">
                <i class="fas fa-file-excel"></i> Export Data as Excel
            </button>
        </form>
    </div>
</div>


                        <!-- Dropdown for selecting month -->
                        <div class="row">
                        <!-- Faculty -->
                        <div class="col-md-6">
                            <label for="faculty">Select Faculty:</label>
                            <div class="form-floating mt-2">
                                <select class="form-select" id="faculty" name="faculty" onchange="filterAppointments()">
                                    <option value="">Select Faculty</option>
                                    <?php
                                    $sql = "SELECT username, firstname, lastname FROM prof";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // Output data of each row
                                        while ($row = $result->fetch_assoc()) {
                                            $prof_id = $row["username"];
                                            $fullName = $row["firstname"] . " " . $row["lastname"];
                                            echo "<option value='" . $prof_id . "'>" . $fullName . "</option>"; // Ipasa ang pangalan ng propesor bilang value
                                        }
                                    } else {
                                        echo "<option value=''>No faculty available</option>";
                                    }
                                ?>
                                </select>
                            </div>
                        </div>

                        <!-- From Month -->
                        <div class="col-md-3">
                            <label for="fromMonth">Select From Month:</label>
                            <div class="form-floating mt-2">
                                <select class="form-select" id="fromMonth" name="fromMonth" onchange="filterAppointments()">
                                    <option value="">Select Month</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>

                        <!-- To Month -->
                        <div class="col-md-3">
                            <label for="toMonth">Select To Month:</label>
                            <div class="form-floating mt-2">
                                <select class="form-select" id="toMonth" name="toMonth" onchange="filterAppointments()">
                                    <option value="">Select Month</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>

                        <!-- Year -->
                        <div class="col-md-3">
                            <label for="year">Select Year:</label>
                            <div class="form-floating mt-2">
                                <select class="form-select" id="year" name="year" onchange="filterAppointments()">
                                    <option value="">Select Year</option>
                                    <?php
                                    for ($i = 2000; $i <= 2030; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                        <!--Appointments Table-->
                        <div class="alert alert-secondary text-dark border border-secondary p-4 table-responsive mt-2">
                                <table class="table table-sm table-hover overflow-x: auto">
                                    <thead>
                                        <tr style="text-align: center;">
                                            
                                            <th scope="col">Name of Student</th>
                                            <th scope="col">Grade/Year/Course & Section</th>
                                            <th scope="col">Professor Name</th>
                                            <th scope="col">Date Conducted</th>
                                            <th scope="col">Time Conducted</th>
                                            <th scope="col">Services Rendered (No. 1-6)</th>
                                            <th scope="col">Concern/s</th>
                                            <th scope="col">Action/s Taken</th>
                                            <th scope="col">No. of Hrs Rendered</th>

                                        </tr>
                                    </thead>
                                    <tbody id="tablePendings"> <!-- Open tbody tag here -->
                                        <?php
                                        // Retrieve pending appointments for the current professor
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
                                                    a.evaluation_status,
                                                    a.action_report,
                                                    a.resched_reason,
                                                    a.appoint_by,
                                                    a.app_day,
                                                    a.action_report_path,
                                                    a.action_report_textbox,
                                                    a.services_rendered,
                                                    a.total_hours,

                                                    s.firstname AS student_firstname,
                                                    s.lastname AS student_lastname,
                                                    s.middlename AS student_middlename,
                                                    s.year_section AS student_year_section,
                                                    s.email AS student_email,
                                                    s.account_type AS account_type,

                                                    p.firstname AS prof_firstname,
                                                    p.lastname AS prof_lastname,
                                                    p.middlename AS prof_middlename,
                                                    p.account_type as account_type

                                                    FROM appointments a
                                                    JOIN student s ON a.student_id = s.username
                                                    JOIN prof p ON a.prof_id = p.username
                                                    WHERE remarks IN ('Done', 'Unresolved')
                                                    ORDER BY a.day ASC;";
                                                $result = mysqli_query($conn, $sql);

                                                // Initialize counts
                                                $totalAppointments = 0;
                                                $advisingConcerns = 0;
                                                $consultationConcerns = 0;
                                                $doneAppointments = 0;
                                                $unresolvedAppointments = 0;

                                        // Initialize an empty array for appointments
                                        $appointments = array();

                                        if (mysqli_num_rows($result)) {
                                            while ($row = mysqli_fetch_assoc($result)) {

                                                // Increment total appointments count
                                                $totalAppointments++;

                                                // Increment counts based on concerns and remarks
                                                if ($row['type_of_concern'] == 'Advising') {
                                                    $advisingConcerns++;
                                                } elseif ($row['type_of_concern'] == 'Consultation') {
                                                    $consultationConcerns++;
                                                }

                                                switch ($row['remarks']) {
                                                    case 'Done':
                                                        $doneAppointments++;
                                                        break;
                                                    case 'Unresolved':
                                                        $unresolvedAppointments++;
                                                        break;
                                                }
                                                // Calculate the remaining days from the appointment date
                                                $appointmentDate = strtotime($row["app_day"]);
                                                $currentDate = strtotime(date("Y-m-d"));
                                                $remainingDays = floor(($appointmentDate - $currentDate) / (60 * 60 * 24));
                                                $action_report = $row["action_report"];
                                                $action_report_path = $row["action_report_path"];
                                                $file_path = $action_report_path;
                                                $file_name = basename($file_path);

                                                echo '<tr style="text-align: center;">';
                                                
                                                echo '<td>' . $row["student_firstname"] . " " . $row["student_lastname"] . '</td>';
                                                echo '<td>' . $row['student_year_section'] . '</td>';
                                                echo '<td>' . $row["prof_firstname"] . " " . $row["prof_lastname"] . '</td>';
                                                echo '<td>' . date("F j, Y", $appointmentDate) . '</td>';
                                                echo '<td>' . date('h:i A', strtotime($row["time_start"])) .'</td>';
                                                echo '<td>' . $row['services_rendered'] . '</td>';
                                                echo '<td>' . $row['detailed_concern'] . '</td>';
                                                echo '<td>' . $row['action_report_textbox'] . '</td>';
                                                echo '<td>' . $row['total_hours'] . " hour/s" . '</td>';
                                                echo '<td>';
                                        
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="10">No Records Found</td></tr>';
                                        }
                                        // Store counts in an array
                                        $counts = array(
                                            'totalAppointments' => $totalAppointments,
                                            'advisingConcerns' => $advisingConcerns,
                                            'consultationConcerns' => $consultationConcerns,
                                            'doneAppointments' => $doneAppointments,
                                            'unresolvedAppointments' => $unresolvedAppointments
                                        );

                                        // Encode counts as JSON and pass it along with appointments data
                                        $appointmentsData = array(
                                            'appointments' => $appointments,
                                            'counts' => $counts
                                        );
                                        
                                        ?>
                                    </tbody> <!-- Close tbody tag here -->
                                </table>
                            </div>
                    </div>

                    <h4 class="pt-2 mb-3"><i class='bx bxs-report mx-2'></i>Faculty Appointments Monitoring</h4>

                    <!-- Bar Graph Canvas -->
                    <div class="container">
                        <canvas id="barGraph"></canvas>
                    </div>

                    <script>
                        function filterAppointments() {
    var faculty = document.getElementById('faculty').value;
    var fromMonth = document.getElementById('fromMonth').value;
    var toMonth = document.getElementById('toMonth').value;
    var year = document.getElementById('year').value;

    // Update hidden inputs for export
    document.getElementById('exportFaculty').value = faculty;
    document.getElementById('exportFromMonth').value = fromMonth;
    document.getElementById('exportToMonth').value = toMonth;
    document.getElementById('exportYear').value = year;

    // AJAX request to filter appointments
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'filter_appointments.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            var response = JSON.parse(this.responseText);
            var tableBody = document.getElementById('tablePendings');
            if ('error' in response) {
                tableBody.innerHTML = '<tr><td colspan="9">No Records Found</td></tr>';
            } else {
                var appointments = response.appointments;
                var html = '';
                appointments.forEach(function(appointment) {
                    html += '<tr style="text-align: center;">';
                    html += '<td>' + appointment.student_name + '</td>';
                    html += '<td>' + appointment.student_year_section + '</td>';
                    html += '<td>' + appointment.professor_name + '</td>';
                    html += '<td>' + appointment.appointment_date + '</td>';
                    html += '<td>' + appointment.appointment_time + '</td>';
                    html += '<td>' + appointment.services_rendered + '</td>';
                    html += '<td>' + appointment.detailed_concern + '</td>';
                    html += '<td>' + appointment.action_report_textbox + '</td>';
                    html += '<td>' + appointment.total_hours + '</td>';
                    html += '</tr>';
                });
                tableBody.innerHTML = html;

                // Update the bar graph with new data
                var doneAppointments = new Array(12).fill(0);
                var unresolvedAppointments = new Array(12).fill(0);

                response.monthlyData.forEach(function(monthData, index) {
                    doneAppointments[index] = monthData.doneAppointments;
                    unresolvedAppointments[index] = monthData.unresolvedAppointments;
                });

                initBarGraph({
                    doneAppointments: doneAppointments,
                    unresolvedAppointments: unresolvedAppointments
                });
            }
        }
    };
    xhr.send('faculty=' + faculty + '&fromMonth=' + fromMonth + '&toMonth=' + toMonth + '&year=' + year);
}


                        var barGraph;
                        function initBarGraph(data) {
                            var ctx = document.getElementById('barGraph').getContext('2d');
                            if (barGraph) {
                                barGraph.destroy(); // Destroy existing chart if it exists
                            }
                            barGraph = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                    datasets: [{
                                        label: 'Done Appointments',
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1,
                                        data: data.doneAppointments
                                    }, {
                                        label: 'Unresolved Appointments',
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1,
                                        data: data.unresolvedAppointments
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            min: 0,
                                            max: 10,
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }
                                    }
                                }
                            });
                        }



        

                            

                    
                </script>

                </div>
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