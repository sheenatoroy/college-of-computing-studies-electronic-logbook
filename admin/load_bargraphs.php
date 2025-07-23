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

// Query to fetch user data using prepared statement
$sql = "SELECT firstname, lastname, username, account_type FROM prof WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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

    <!--Bar Graph-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
    <title>CCS: E-LOG | Home</title>
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
                                <?php echo $action . ' an appointment with ' . $receiverName . ' on ' . date("F j, Y", strtotime($row["app_day"])) . ' at ' . date("h:i A", strtotime($row["time_start"])) . ' '; ?>
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

                        <div class="row mt-3">
                            <div class="col">
                                <h4 class="pt-2 mb-3"><i class='bx bxs-report mx-2'></i>Reports and Analytics</h4>
                                <!-- <button id="excelButton" class="btn btn-success" onclick="generateAndSaveExcel()" style="margin-bottom: 10px;">
                                    <i class="fas fa-file-excel"></i> Generate and Save Excel
                                </button> -->
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


                        

                        <div id="yearDropdownContainer" style="margin-bottom: 10px;">
                            <label for="yearDropdown">Select Year:</label>
                            <select id="yearDropdown" class="form-select" aria-label="Select Year">
                                <!-- Options will be dynamically added using JavaScript -->
                            </select>
                        </div>

                        <div id="barGraphsContainer" style="display: flex; flex-wrap: wrap; justify-content: space-between;">

                            <div class="graphContainer" style="width: 48%;">
                                <canvas id="advisingGraph"></canvas>
                            </div>

                            <div class="graphContainer" style="width: 48%;">
                                <canvas id="consultationGraph"></canvas>
                            </div>

                            <div class="graphContainer" style="width: 48%;">
                                <canvas id="unresolvedGraph"></canvas>
                            </div>

                            <div class="graphContainer" style="width: 48%;">
                                <canvas id="doneGraph"></canvas>
                            </div>

                        </div>

                        <script>
                        $(document).ready(function () {
                            // Fetch available years and populate the dropdown
                            fetchYears();

                            // Initialize the bar graphs with the default year
                            updateBarGraphs();

                            // Handle the change event of the year dropdown
                            $('#yearDropdown').change(function () {
                                // Update the bar graphs when the year dropdown changes
                                updateBarGraphs();
                            });
                        });

                        // Function to fetch available years and populate the dropdown
                        function fetchYears() {
                            var currentYear = new Date().getFullYear();
                            var startYear = currentYear - 5; // Five years in the past
                            var endYear = currentYear + 4;   // Four years in the future

                            // Populate the dropdown with available years
                            for (var year = startYear; year <= endYear; year++) {
                                $('#yearDropdown').append('<option value="' + year + '">' + year + '</option>');
                            }

                            // Set the default selected year to the current year
                            $('#yearDropdown').val(currentYear);
                        }

                        // Function to update and render the bar graphs based on the selected year
                        function updateBarGraphs() {
                            var selectedYear = $('#yearDropdown').val();

                            // Fetch data for Advising bar graph
                            $.ajax({
                                type: "GET",
                                url: "fetch_bar_graph_data.php?type=Advising&year=" + selectedYear,
                                success: function (advisingData) {
                                    var advisingGraphData = JSON.parse(advisingData);
                                    createBarGraph('advisingGraph', advisingGraphData, 'Total Number of Advising Type of Concern', 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)');
                                }
                            });

                            // Fetch data for Consultation bar graph
                            $.ajax({
                                type: "GET",
                                url: "fetch_bar_graph_data.php?type=Consultation&year=" + selectedYear,
                                success: function (consultationData) {
                                    var consultationGraphData = JSON.parse(consultationData);
                                    createBarGraph('consultationGraph', consultationGraphData, 'Total Number of Consultation Type of Concern', 'rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 1)');
                                }
                            });

                            // Fetch data for Unresolved bar graph
                            $.ajax({
                                type: "GET",
                                url: "fetch_bar_graph_data.php?type=Unresolved&year=" + selectedYear,
                                success: function (unresolvedData) {
                                    var unresolvedGraphData = JSON.parse(unresolvedData);
                                    createBarGraph('unresolvedGraph', unresolvedGraphData, 'Total Number of Unresolved Appointments', 'rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)');
                                }
                            });

                            // Fetch data for Done bar graph
                            $.ajax({
                                type: "GET",
                                url: "fetch_bar_graph_data.php?type=Done&year=" + selectedYear,
                                success: function (doneData) {
                                    var doneGraphData = JSON.parse(doneData);
                                    createBarGraph('doneGraph', doneGraphData, 'Total Number of Done Appointments', 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)');
                                }
                            });
                        }

                        // Function to create and render the bar graph
                        function createBarGraph(graphId, data, label, backgroundColor, borderColor) {
                            var ctx = document.getElementById(graphId).getContext('2d');

                            // Destroy the existing Chart instance if it exists
                            if (window.myBarCharts && window.myBarCharts[graphId]) {
                                window.myBarCharts[graphId].destroy();
                            }

                            // Create an array of month names from January to December
                            var monthNames = [
                                'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ];

                            // Extract labels (months) and data (counts) from the fetched data
                            var labels = monthNames.map(function (_, index) {
                                return monthNames[index];
                            });

                            var counts = Array.from({ length: 12 }, function (_, index) {
                                var item = data.find(function (item) {
                                    return item.month == (index + 1);
                                });
                                return item ? item.count : 0;
                            });

                            console.log("Bar Graph Labels:", labels); // Debug statement
                            console.log("Bar Graph Counts:", counts); // Debug statement

                            // Calculate the maximum value for the y-axis (adding some padding for better visualization)
                            var maxCount = Math.max(...counts);
                            var yAxisMaxValue = maxCount + 10;

                            // Create the bar graph
                            window.myBarCharts = window.myBarCharts || {};
                            window.myBarCharts[graphId] = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: label,
                                        data: counts,
                                        backgroundColor: backgroundColor,
                                        borderColor: borderColor,
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: yAxisMaxValue
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        }
                                    },
                                    // Set the bar width
                                    indexAxis: 'x', // Use the x-axis as the index axis
                                    barPercentage: 1.0 // Adjust this value to set the width of the bars
                                }
                            });
                        }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
