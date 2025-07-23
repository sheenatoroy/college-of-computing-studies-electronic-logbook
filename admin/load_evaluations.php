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
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--Handling records-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!--Library for Excel-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<!--Bootstrap 5 CSS CDN-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<!--Fontawesome-->
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

<!--Website Logo-->
<link rel="icon" href="/assets/img/ccs-logo.png">
<!--Boxicons-->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="/assets/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/assets/css/chat-bot-style.css">

<!--Calendar-->

<link rel="stylesheet" href="../fullcalendar/lib/main.min.css">
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../fullcalendar/lib/main.min.js"></script>
<title>CCS: E-LOG | Evaluation Feedbacks</title>
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
    a.remarks,
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
    ORDER BY a.day ASC;";

$result = mysqli_query($conn, $sql);

// Count the number of pending appointments
$notificationCount = mysqli_num_rows($result);
?>

<!--Modal for Notification icon-->
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
                    <div class="alert alert-info" role="alert">
                        <?php
                        $isSender = ($row['prof_username'] === $username);
                        $action = $isSender ? 'You have' : 'You have received';
                        $receiverName = $isSender ? $row['student_firstname'] . ' ' . $row['student_lastname'] : 'You';
                        ?>
                        <strong><?php echo $action; ?> an appointment:</strong>
                        <a href="../admin/admin-pending-appointment.php">
                            <?php echo $action . ' an appointment with ' . $receiverName . ' on ' . date("F j, Y", strtotime($row["day"])) . ' at ' . date("h:i A", strtotime($row["time_start"])) . ' '; ?>
                        </a>
                    </div>
                <?php endwhile; ?>
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
                    <?php echo ($notificationCount > 0) ? '<span class="badge bg-danger">' . $notificationCount . '</span>' : ''; ?>
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


<!--Dapat dito na magstart sa reports-->
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

                    <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                        <div class="row mt-3">
                            <div class="col">
                            </div>
                        </div>
                            <h4 class="pt-2 mb-3"><i class='bx bxs-report mx-2'></i>Reports and Analytics</h4>
                            
                        <!-- Button for Excel and Bar Graph -->
                        <div class="row mt-3">
                            <div class="col">
                                <button id="excelButton" class="btn btn-success" onclick="generateAndSaveExcel()" style="margin-bottom:10px;">
                                    <i class="fas fa-file-excel"></i> Generate and Save Excel
                                </button>

                                <button id="barGraphButton" class="btn btn-primary" onclick="location.href='load_bargraphs.php'" style="margin-bottom: 10px;">
                                    <i class="fas fa-chart-bar"></i> Show Bar Graph
                                </button>
                                <button id="barGraphButton" class="btn btn-warning" onclick="location.href='load_evaluations.php'"  style="margin-bottom: 10px; color: white;">
                                    <i class="fas fa-chart-bar"></i> Evaluation Feedbacks
                                </button>
                                <button id="barGraphButton" class="btn btn-info" onclick="location.href='load_faculty.php'"  style="margin-bottom: 10px; color: white;">
                                    <i class="fas fa-chart-bar"></i> Faculty Progress
                                </button>
                                
                            </div>
                        </div>

                        <!-- Dropdown for selecting month -->
                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="form-floating mt-2">
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                
                                <div class="form-floating mt-2">
                                    
                                </div>
                            </div>
                        </div>

                        <h1>Summary of System Evaluation</h1>

                        <!--Prof System Table-->
                        <div class="row mt-3">
                            <div class="col">
                                <table id="systemEvalTable" class="table table-bordered" cellpadding="40" cellspacing="40">
                                    <thead>
                                        <tr>
                                            <th>Title</th> 
                                            <th>Total Ratings</th>
                                            <th>Calculated Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Faculty Evaluation data will be inserted here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <script>

                    // Function to generate excel file
                    function generateAndSaveExcel() {
                        // Fetch the headers for the faculty evaluation table
                        var facultyEvalHeaders = [];
                        $("#profEvalTable thead th").each(function () {
                            facultyEvalHeaders.push($(this).text());
                        });

                        // Fetch the data from the faculty evaluation table
                        var facultyEvalData = [];
                        $("#profEvalTable tbody tr").each(function () {
                            var rowData = [];
                            rowData.push($(this).find("td:first").text()); // Get the text of the first td (Faculty)
                            $(this).find("td:not(:first)").each(function () {
                                rowData.push($(this).text());
                            });
                            facultyEvalData.push(rowData);
                        });

                        // Include headers in the faculty evaluation data
                        facultyEvalData.unshift(facultyEvalHeaders);

                        // Fetch the headers for the system evaluation table
                        var systemEvalHeaders = [];
                        $("#systemEvalTable thead th").each(function () {
                            systemEvalHeaders.push($(this).text());
                        });

                        // Fetch the data from the system evaluation table
                        var systemEvalData = [];
                        $("#systemEvalTable tbody tr").each(function () {
                            var rowData = [];
                            $(this).find("td").each(function () {
                                rowData.push($(this).text());
                            });
                            systemEvalData.push(rowData);
                        });

                        // Include headers in the system evaluation data
                        systemEvalData.unshift(systemEvalHeaders);

                        // Create a workbook and add sheets
                        var workbook = XLSX.utils.book_new();

                        // Faculty Evaluation sheet
                        // XLSX.utils.book_append_sheet(workbook, XLSX.utils.aoa_to_sheet(facultyEvalData), "Faculty Evaluation");

                        // System Evaluation sheet
                        XLSX.utils.book_append_sheet(workbook, XLSX.utils.aoa_to_sheet(systemEvalData), "System Evaluation");

                        var fileName = "Evaluation_Report_" + ".xlsx";

                        // Save the workbook as an Excel file with the new file name
                        XLSX.writeFile(workbook, fileName);
                    }



                    // Load Faculty Evaluation data
                    function loadFacultyEvaluation() {
                        var selectedMonth = $("#month").val();
                        var selectedYear = $("#year").val();

                        // Load Faculty Evaluation
                        $.ajax({
                            type: "GET",
                            url: "load_faculty_evaluation.php",
                            data: { month: selectedMonth, year: selectedYear },
                            dataType: "json",
                            success: function (data) {
                                var profEvalTable = $("#profEvalTable tbody");

                                if (data.totals.length === 0) {
                                    profEvalTable.html("<tr><td colspan='3'>No records</td></tr>");
                                } else {
                                    var totals = data.totals;
                                    var averages = data.averages;

                                    // Clear existing rows
                                    profEvalTable.empty();

                                    // Create a new row for each total and average
                                    for (var i = 1; i <= 5; i++) {
                                        var label = getQuestionLabel2(i);
                                        // Use toFixed(2) to limit the decimal places to two
                                        var averageWithTwoDecimalPlaces = averages[i].toFixed(2);
                                        profEvalTable.append("<tr><td>" + label + "</td><td>" + totals[i] + "</td><td>" + averageWithTwoDecimalPlaces + "</td></tr>");
                                    }
                                }
                            }
                        });
                    }


                    // Load System Evaluation
                    function loadSystemEvaluation() {
                        var selectedMonth = $("#month").val();
                        var selectedYear = $("#year").val();

                        // Load System Evaluation
                        $.ajax({
                            type: "GET",
                            url: "load_system_evaluation.php",
                            data: { month: selectedMonth, year: selectedYear },
                            dataType: "json", // Specify the expected data type
                            success: function (data) {
                                var systemEvalTable = $("#systemEvalTable tbody");

                                if (data.totals.length === 0) {
                                    systemEvalTable.html("<tr><td colspan='3'>No records</td></tr>");
                                } else {
                                    var totals = data.totals;
                                    var averages = data.averages; // Retrieve averages from the JSON response

                                    // Clear existing rows
                                    systemEvalTable.empty();

                                    // Create a new row for each total and average
                                    for (var i = 1; i <= 8; i++) {
                                        // Use the desired labels in the table
                                        var label = getQuestionLabel(i);
                                        // Use toFixed(2) to limit the decimal places to two
                                        var averageWithTwoDecimalPlaces = averages[i].toFixed(2);
                                        systemEvalTable.append("<tr><td>" + label + "</td><td>" + totals[i] + "</td><td>" + averageWithTwoDecimalPlaces + "</td></tr>");
                                    }
                                }
                            }
                        });
                    }

                    // Helper function to get the label for a question
                    function getQuestionLabel(questionNumber) {
                        switch (questionNumber) {
                            case 1:
                                return "Overall Satisfaction";
                            case 2:
                                return "Ease of Use";
                            case 3:
                                return "Appointment Scheduling Process";
                            case 4:
                                return "User Interface Design";
                            case 5:
                                return "Notification System";
                            case 6:
                                return "Availability and Scheduling Options";
                            case 7:
                                return "Feedback and Communication";
                            case 8:
                                return "Technical Reliability";
                            default:
                                return "Question " + questionNumber;
                        }
                    }

                    // Helper function to get the label for a question
                    function getQuestionLabel2(questionNumber) {
                        switch (questionNumber) {
                            case 1:
                                return "Punctuality";
                            case 2:
                                return "Communication Skills";
                            case 3:
                                return "Constructive Dialogue";
                            case 4:
                                return "Satisfactory Resolution";
                            case 5:
                                return "Professionalism";
                            default:
                                return "Question " + questionNumber;
                        }
                    }

                    // Load Faculty Evaluation on page load
                    $(document).ready(function () {
                        loadFacultyEvaluation();
                        loadSystemEvaluation();
                        updateSummaryTitle();
                    });
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>

<!--Closing for TopNavbar and SideNavbar-->
</div>



<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>