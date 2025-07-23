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
                        <h4 class="pt-2 mb-3"><i class='bx bxs-report mx-2'></i>Reports and Analytics</h4>

                    <!-- Button for Excel and Bar Graph -->
                    <div class="row mt-3">
                        <div class="col">
                            <button id="excelButton" class="btn btn-success" onclick="generateAndSaveExcel()" style="margin-bottom: 10px;">
                            
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
                                <label for="month">Select Month:</label>
                                <div class="form-floating mt-2">
                                    <select class="form-select" id="month" name="month" onchange="loadAppointments(); updateSummaryTitle();">
                                        <?php
                                        $months = array(
                                            'January', 'February', 'March', 'April', 'May', 'June',
                                            'July', 'August', 'September', 'October', 'November', 'December'
                                        );

                                        $currentMonth = date('n'); // Current month
                                        foreach ($months as $index => $month) {
                                            $selected = ($index + 1 == $currentMonth) ? "selected" : "";
                                            echo "<option value='" . ($index + 1) . "' $selected>$month</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="year">Select Year:</label>
                                <div class="form-floating mt-2">
                                    <select class="form-select" id="year" name="year" onchange="loadAppointments(); updateSummaryTitle();">
                                        <?php
                                        $currentYear = date('Y'); // Current year
                                        $startYear = $currentYear - 5; // Adjust this value based on your needs
                                        $endYear = $currentYear + 5;  // You can adjust the number of future years

                                        for ($i = $startYear; $i <= $endYear; $i++) {
                                            $selected = ($i == $currentYear) ? "selected" : "";
                                            echo "<option value='$i' $selected>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <!-- Table for Monthly Transactions and Quantity -->
                        <h1 class="form-floating mt-4" id="summaryTitle"> Summary of Transactions for </h1>
                        <table class="table table-bordered mt-3" style="border-collapse: separate; border-spacing: 10px 0;">
                                <!-- <thead>
                                    <tr>
                                        <th>Monthly Transactions</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead> -->
                                <tbody id="monthlyTransactionsTableBody">
                                    <!-- Data will be loaded here using JavaScript -->
                                </tbody>
                            </table>

                            <h1> Monthly Transactions</h1>

                            <!-- Search Bar -->
                            <div class="row mt-3">
                                <div class="col">
                                    <input type="text" id="searchBar" class="form-control" placeholder="Search..." onkeyup="filterTable()">
                                </div>
                            </div>

                        <!--Appointments Table-->
                            <div class="row mt-3">
                                <div class="col">
                                    <table id="appointmentsTable" class="table table-bordered" cellpadding="40" cellspacing="40">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type of Concern</th>
                                                <th>Specific Concern</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </div>
                    

                    <script>
                       function updateSummaryTitle() {
    var selectedMonth = document.getElementById("month").value;
    var selectedYear = document.getElementById("year").value;

    var monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    var monthName = monthNames[selectedMonth - 1];

    document.getElementById("summaryTitle").innerText = "Summary of Transactions for " + monthName + " " + selectedYear;
}

function generateAndSaveExcel() {
    var appointmentsHeaders = [];
    $("#appointmentsTable thead th").each(function () {
        appointmentsHeaders.push($(this).text());
    });

    var appointmentsData = [];
    $("#appointmentsTable tbody tr").each(function () {
        if ($(this).is(":visible")) { // Only include visible rows
            var rowData = [];
            $(this).find("td").each(function () {
                rowData.push($(this).text());
            });
            appointmentsData.push(rowData);
        }
    });

    appointmentsData.unshift(appointmentsHeaders);

    var workbook = XLSX.utils.book_new();

    var monthlyTransactionsHeaders = ["Total no. of Appointments", "Total no. of Advising", "Total no. of Consultation", "Total no. of Priority Status", "Total no. of Standard Status", "Total no. of Unresolved Appointments", "Total no. of Pending Appointments", "Total no. of Done Appointments"];
    var monthlyTransactionsData = [];
    $("#monthlyTransactionsTableBody tr").each(function () {
        var rowData = [];
        $(this).find("td").each(function () {
            rowData.push($(this).text());
        });
        monthlyTransactionsData.push(rowData);
    });

    monthlyTransactionsData.unshift(monthlyTransactionsHeaders);

    XLSX.utils.book_append_sheet(workbook, XLSX.utils.aoa_to_sheet(appointmentsData), "Daily Transactions");
    XLSX.utils.book_append_sheet(workbook, XLSX.utils.aoa_to_sheet(monthlyTransactionsData), "Summary Report");

    var selectedMonth = $("#month").val();
    var selectedYear = $("#year").val();
    var monthName = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ][selectedMonth - 1];

    var fileName = "Elog_Report_for_" + monthName + "_" + selectedYear + ".xlsx";

    XLSX.writeFile(workbook, fileName);
}


function loadAppointments() {
    var selectedMonth = $("#month").val();
    var selectedYear = $("#year").val();

    $.ajax({
        type: "GET",
        url: "load_monthly_transactions.php",
        data: { month: selectedMonth, year: selectedYear },
        success: function (data) {
            $("#monthlyTransactionsTableBody").html(data);
        }
    });

    $.ajax({
        type: "GET",
        url: "load_appointments.php",
        data: { month: selectedMonth, year: selectedYear },
        success: function (data) {
            $("#appointmentsTable tbody").html(data);
        }
    });
}

function filterTable() {
    var searchInput = document.getElementById("searchBar").value.toLowerCase();
    var table = document.getElementById("appointmentsTable").getElementsByTagName("tbody")[0];
    var rows = table.getElementsByTagName("tr");

    var filteredRowCount = 0;
    var typeCounts = {
        total: 0,
        advising: 0,
        consultation: 0,
        priority: 0,
        standard: 0,
        unresolved: 0,
        pending: 0,
        done: 0
    };

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.getElementsByTagName("td");
        var match = false;

        for (var j = 0; j < cells.length; j++) {
            var cell = cells[j];
            if (cell.innerHTML.toLowerCase().indexOf(searchInput) > -1) {
                match = true;
                break;
            }
        }

        if (match) {
            row.style.display = "";
            filteredRowCount++;

            // Count types of concerns
            typeCounts.total++;
            var typeOfConcern = row.getElementsByTagName("td")[1].innerText.toLowerCase();
            var status = row.getElementsByTagName("td")[3].innerText.toLowerCase();

            if (typeOfConcern === 'advising') typeCounts.advising++;
            if (typeOfConcern === 'consultation') typeCounts.consultation++;
            if (status === 'priority') typeCounts.priority++;
            if (status === 'standard') typeCounts.standard++;
            if (status === 'unresolved') typeCounts.unresolved++;
            if (status === 'pending') typeCounts.pending++;
            if (status === 'done') typeCounts.done++;
        } else {
            row.style.display = "none";
        }
    }

    updateMonthlyTransactionsCount(typeCounts);
}

function updateMonthlyTransactionsCount(typeCounts) {
    var monthlyTransactionsTableBody = document.getElementById("monthlyTransactionsTableBody");

    var content = `
        <tr><td>Total no. of appointments</td><td>${typeCounts.total}</td></tr>
        <tr><td>Total no. of Advising</td><td>${typeCounts.advising}</td></tr>
        <tr><td>Total no. of Consultation</td><td>${typeCounts.consultation}</td></tr>
        <tr><td>Total no. of Priority Status</td><td>${typeCounts.priority}</td></tr>
        <tr><td>Total no. of Standard Status</td><td>${typeCounts.standard}</td></tr>
        <tr><td>Total no. of Unresolved Status</td><td>${typeCounts.unresolved}</td></tr>
        <tr><td>Total no. of Pending Appointments</td><td>${typeCounts.pending}</td></tr>
        <tr><td>Total no. of Done Appointments</td><td>${typeCounts.done}</td></tr>
    `;

    monthlyTransactionsTableBody.innerHTML = content;
}

$(document).ready(function () {
    loadAppointments();
    updateSummaryTitle();
});

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