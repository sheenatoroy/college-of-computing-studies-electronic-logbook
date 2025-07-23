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

    <title>CCS: E-LOG | Feedback</title>
</head>
<style>
    body{
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }
    .rating {
        font-size: 2rem;
        color: #ccc; /* Set the default star color */
        display: flex;
    }

        .rating label {
        cursor: pointer;
        transition: color 0.3s;
    }

    .rating input[type="radio"]:checked+label,
    .rating input[type="radio"]:checked+label~label {
      color: #ffc107; /* Set the selected star color and stars before it */
    }

    .rating input[type="radio"]:checked+label~label {
      color: #ccc; /* Set the color of stars after the selected one back to default */
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
                            <a href="../faculty/faculty-pending-appointment.php">
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
                    <a class="nav-link fs-6 p-1 m-1" href="/faculty/faculty-dashboard.php" style="color: #131313;"> 
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
                            $sql = "SELECT 
                            a.appointment_id, 
                            a.student_id, 
                            a.type_of_concern,
                            a.day, 
                            a.time_start,
                            a.time_end,
                            a.appointment_status,
                            a.remarks,

                            p.username, 

                            s.firstname,
                            s.lastname,
                            s.middlename
                            FROM appointments a
                            JOIN student s ON a.student_id = s.username
                            JOIN prof p ON a.prof_id = p.username
                            WHERE remarks = 'Done' AND evaluation_status = 'Not Done'";



                        $result = mysqli_query($conn, $sql);
                    ?>
                            <!-- Feedbacks Remaining -->
                            <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                                <h4 class="pt-2 mb-3"><i class='bx bx-error-circle mx-2'></i>Remaining Feedbacks</h4>

                                <?php
                                // Loop through each appointment and display it in a card
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $appointment_id = $row['appointment_id'];
                                    $student_name = $row['firstname'] . ' ' . substr($row['middlename'], 0, 1) . '. ' . $row['lastname'];
                                    $type_of_concern = $row['type_of_concern'];
                                    $day = $row['day'];
                                    $formatted_date = date('F d, Y', strtotime($day));

                                   // Set a fixed number of days (e.g., 3 days) for evaluation after each appointment date
                                    $days_for_evaluation = 3;

                                    // Calculate remaining days for evaluation
                                    $remaining_days = max(0, ceil((strtotime($day) + ($days_for_evaluation * 24 * 60 * 60)) - time()) / (60 * 60 * 24));
                                    $remaining_days = intval($remaining_days); // Convert to integer
                                    ?>

                                    <div class="card shadow-sm bg-body rounded mb-3">
                                        <h5 class="card-header">Appointment with <?php echo $student_name; ?></h5>
                                        <div class="card-body">
                                            <p class="card-text">Appointment ID: <?php echo $appointment_id; ?></p>

                                            <p class="card-text">Concern: <?php echo $type_of_concern; ?></p>
                                            
                                            <p class="card-text">Date: <?php echo $formatted_date; ?></p>
                                            
                                            <button type="button" class="btn btn-primary evaluate-btn" data-bs-toggle="modal" data-bs-target="#feedbackQuestions" data-appointment-id="<?php echo $appointment_id; ?>">
                                                Evaluate
                                            </button>

                                            <script>
                                                // Add an event listener to the "Evaluate" button
                                                document.querySelectorAll('.evaluate-btn').forEach(function(button) {
                                                    button.addEventListener('click', function() {
                                                        // Get the appointment_id from the data attribute of the button
                                                        var appointmentId = button.getAttribute('data-appointment-id');
                                                        
                                                        // Set the value of the hidden input field in the modal
                                                        document.getElementById('modal_appointment_id').value = appointmentId;
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <div class="card-footer text-muted">
                                            Remaining days: <?php echo $remaining_days; ?>
                                        </div>
                                    </div>

                                <?php
                                }
                                ?>
                            </div>
                            <!--Modal for Remaining Feedbacks-->
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">Feedback Section</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row text-center">
                                                <h5 class="mb-3">Following are feedback questions that are required to answer.</h5>
                                            </div>
                                            <div class="row-4 d-flex justify-content-center">
                                                <button type="button" class="btn btn-primary" data-bs-target="#feedbackQuestions" data-bs-toggle="modal" data-bs-dismiss="modal">Understood</button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Modal for the Feedback Questions-->
                            <div class="modal fade" id="feedbackQuestions" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalToggleLabel2">Feedback Section</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                    <div class="modal-body p-4">
                                        <form class="text-center" action="../faculty/functions/insert-feedback.php" method="POST">
                                            <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mt-2">
                                                            <h6>Evaluation for the appointment</h6>
                                                            <!-- Question for prof -->
                                                            <!-- Question 1 -->
                                                            <div class="mb-2 border border-secondary rounded-1 p-3">
                                                                <h6>PUNCTUALITY</h6>
                                                                <label for="e1" class="form-label">The person I had the appointment with was punctual and adhered to the agreed-upon meeting time.</label>
                                                                <div class="rating justify-content-center" id="e1">
                                                                    <input type="radio" name="eval_1" value="1" id="e1_1" hidden>
                                                                    <label for="e1_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                                    <input type="radio" name="eval_1" value="2" id="e1_2" hidden>
                                                                    <label for="e1_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                                    <input type="radio" name="eval_1" value="3" id="e1_3" hidden>
                                                                    <label for="e1_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                                    <input type="radio" name="eval_1" value="4" id="e1_4" hidden>
                                                                    <label for="e1_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                                    <input type="radio" name="eval_1" value="5" id="e1_5" hidden>
                                                                    <label for="e1_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                                </div>
                                                                <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                            </div>

                                                            <!-- Question for prof -->
                                                            <!-- Question 2 -->
                                                            <div class="mb-2 border border-secondary rounded-1 p-3">
                                                                <h6>COMMUNICATION SKILLS</h6>
                                                                <label for="e2" class="form-label">Clear and effective communication was maintained by the person during the appointment, ensuring that all relevant information was conveyed.</label>
                                                                <div class="rating justify-content-center" id="e2">
                                                                    <input type="radio" name="eval_2" value="1" id="e2_1" hidden>
                                                                    <label for="e2_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                                    <input type="radio" name="eval_2" value="2" id="e2_2" hidden>
                                                                    <label for="e2_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                                    <input type="radio" name="eval_2" value="3" id="e2_3" hidden>
                                                                    <label for="e2_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                                    <input type="radio" name="eval_2" value="4" id="e2_4" hidden>
                                                                    <label for="e2_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                                    <input type="radio" name="eval_2" value="5" id="e2_5" hidden>
                                                                    <label for="e2_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                                </div>
                                                                <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                            </div>

                                                            <!-- Question for prof -->
                                                            <!-- Question 3 -->
                                                            <div class="mb-2 border border-secondary rounded-1 p-3">
                                                            <h6>CONSTRUCTIVE DIALOGUE</h6>
                                                                <label for="e3" class="form-label">The person actively listened and engaged in constructive dialogue, fostering a productive and collaborative atmosphere.</label>
                                                                <div class="rating justify-content-center" id="e3">
                                                                    <input type="radio" name="eval_3" value="1" id="e3_1" hidden>
                                                                    <label for="e3_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                                    <input type="radio" name="eval_3" value="2" id="e3_2" hidden>
                                                                    <label for="e3_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                                    <input type="radio" name="eval_3" value="3" id="e3_3" hidden>
                                                                    <label for="e3_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                                    <input type="radio" name="eval_3" value="4" id="e3_4" hidden>
                                                                    <label for="e3_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                                    <input type="radio" name="eval_3" value="5" id="e3_5" hidden>
                                                                    <label for="e3_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                                </div>
                                                                <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                            </div>


                                                            <!-- Question for prof -->
                                                            <!-- Question 4 -->
                                                            <div class="mb-2 border border-secondary rounded-1 p-3">
                                                            <h6>SATISFACTORY RESOLUTION</h6>
                                                                <label for="e4" class="form-label">Any questions or concerns I had during the appointment were addressed in a satisfactory manner.</label>
                                                                <div class="rating justify-content-center" id="e4">
                                                                    <input type="radio" name="eval_4" value="1" id="e4_1" hidden>
                                                                    <label for="e4_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                                    <input type="radio" name="eval_4" value="2" id="e4_2" hidden>
                                                                    <label for="e4_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                                    <input type="radio" name="eval_4" value="3" id="e4_3" hidden>
                                                                    <label for="e4_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                                    <input type="radio" name="eval_4" value="4" id="e4_4" hidden>
                                                                    <label for="e4_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                                    <input type="radio" name="eval_4" value="5" id="e4_5" hidden>
                                                                    <label for="e4_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                                </div>
                                                                <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                            </div>

                                                            <!-- Question for prof -->
                                                            <!-- Question 5 -->
                                                            <div class="mb-2 border border-secondary rounded-1 p-3">
                                                            <h6>PROFESSIONALISM</h6>
                                                                <label for="e5" class="form-label">I feel that the appointment was conducted professionally, and the individual I met with achieved my expectations in terms of their conduct and effectiveness.</label>
                                                                <div class="rating justify-content-center" id="e5">
                                                                    <input type="radio" name="eval_5" value="1" id="e5_1" hidden>
                                                                    <label for="e5_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                                    <input type="radio" name="eval_5" value="2" id="e5_2" hidden>
                                                                    <label for="e5_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                                    <input type="radio" name="eval_5" value="3" id="e5_3" hidden>
                                                                    <label for="e5_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                                    <input type="radio" name="eval_5" value="4" id="e5_4" hidden>
                                                                    <label for="e5_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                                    <input type="radio" name="eval_5" value="5" id="e5_5" hidden>
                                                                    <label for="e5_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                                </div>
                                                                <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <div class="col-md-6">
                                                <div class="form-floating mt-2">
                                                    <!-- EVAL SA SYSTEM -->
                                                    <form class="text-center" action="../admin/functions/insert-feedback.php" method="POST">
                                                    <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id?>">

                                                    <h6>Evaluation for the ELOG System</h6>
                                                    <!-- Question 1 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>OVERALL SATISFACTION</h6>
                                                        <label for="q1" class="form-label">Please rate your overall satisfaction with the web-based appointment system.</label>
                                                        <div class="rating justify-content-center" id="q1">
                                                            <input type="radio" name="question_1" value="1" id="q1_1" hidden>
                                                            <label for="q1_1"><i class='bx bxs-star'></i><span class="m-2">1</span></label>
                                                            <input type="radio" name="question_1" value="2" id="q1_2" hidden>
                                                            <label for="q1_2"><i class='bx bxs-star'></i><span class="m-2">2</span></label>
                                                            <input type="radio" name="question_1" value="3" id="q1_3" hidden>
                                                            <label for="q1_3"><i class='bx bxs-star'></i><span class="m-2">3</span></label>
                                                            <input type="radio" name="question_1" value="4" id="q1_4" hidden>
                                                            <label for="q1_4"><i class='bx bxs-star'></i><span class="m-2">4</span></label>
                                                            <input type="radio" name="question_1" value="5" id="q1_5" hidden>
                                                            <label for="q1_5"><i class='bx bxs-star'></i><span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                    </div>

                                                    <!--Question 2-->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>EASE OF USE</h6>
                                                        <label for="q2" class="form-label">How easy was it for you to navigate and use the appointment system?</label>
                                                        <div class="rating justify-content-center" id="q2">
                                                        <input type="radio" name="rating_q2" value="1" id="q2_1" hidden>
                                                        <label for="q2_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_2" value="2" id="q2_2" hidden>
                                                        <label for="q2_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_2" value="3" id="q2_3" hidden>
                                                        <label for="q2_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_2" value="4" id="q2_4" hidden>
                                                        <label for="q2_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_2" value="5" id="q2_5" hidden>
                                                        <label for="q2_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being very difficult while 5 being very easy</i></small>
                                                    </div>

                                                    <!-- Question 3 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>APPOINTMENT SCHEDULING PROCESS</h6>
                                                        <label for="q3" class="form-label">Rate the ease and efficiency of the appointment scheduling process.</label>
                                                        <div class="rating justify-content-center" id="q3">
                                                        <input type="radio" name="rating_q3" value="1" id="q3_1" hidden>
                                                        <label for="q3_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_3" value="2" id="q3_2" hidden>
                                                        <label for="q3_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_3" value="3" id="q3_3" hidden>
                                                        <label for="q3_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_3" value="4" id="q3_4" hidden>
                                                        <label for="q3_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_3" value="5" id="q3_5" hidden>
                                                        <label for="q3_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being very complex and time consuming while 5 being very straightforward and quick</i></small>
                                                    </div>

                                                    <!-- Question 4 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>USER INTERFACE DESIGN</h6>
                                                        <label for="q4" class="form-label">How visually appealing and user-friendly did you find the design of the appointment system?</label>
                                                        <div class="rating justify-content-center" id="q4">
                                                        <input type="radio" name="question_4" value="1" id="q4_1" hidden>
                                                        <label for="q4_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_4" value="2" id="q4_2" hidden>
                                                        <label for="q4_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_4" value="3" id="q4_3" hidden>
                                                        <label for="q4_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_4" value="4" id="q4_4" hidden>
                                                        <label for="q4_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_4" value="5" id="q4_5" hidden>
                                                        <label for="q4_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being not appealing at all while 5 being very appealing</i></small>
                                                    </div>

                                                    <!-- Question 5 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>NOTIFICATION SYSTEM</h6>
                                                        <label for="q5" class="form-label">Rate the effectiveness of the system's notifications for appointment confirmations and reminders.</label>
                                                        <div class="rating justify-content-center" id="q5">
                                                        <input type="radio" name="question_5" value="1" id="q5_1" hidden>
                                                        <label for="q5_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_5" value="2" id="q5_2" hidden>
                                                        <label for="q5_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_5" value="3" id="q5_3" hidden>
                                                        <label for="q5_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_5" value="4" id="q5_4" hidden>
                                                        <label for="q5_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_5" value="5" id="q5_5" hidden>
                                                        <label for="q5_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being not effective at all while 5 being very effective</i></small>
                                                    </div>

                                                    <!-- Question 6 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>AVAILABILITY AND SCHEDULING OPTIONS</h6>
                                                        <label for="q6" class="form-label">How satisfied are you with the availability of time slots and scheduling options?</label>
                                                        <div class="rating justify-content-center" id="q6">
                                                        <input type="radio" name="question_6" value="1" id="q6_1" hidden>
                                                        <label for="q6_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_6" value="2" id="q6_2" hidden>
                                                        <label for="q6_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_6" value="3" id="q6_3" hidden>
                                                        <label for="q6_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_6" value="4" id="q6_4" hidden>
                                                        <label for="q6_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_6" value="5" id="q6_5" hidden>
                                                        <label for="q6_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being not satisfied at all while 5 being very satisfied</i></small>
                                                    </div>

                                                    <!-- Question 7 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>FEEDBACK AND COMMUNICATION</h6>
                                                        <label for="q7" class="form-label">Rate the system's ability to facilitate feedback and communication between students and faculty members.</label>
                                                        <div class="rating justify-content-center" id="q7">
                                                        <input type="radio" name="question_7" value="1" id="q7_1" hidden>
                                                        <label for="q7_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_7" value="2" id="q7_2" hidden>
                                                        <label for="q7_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_7" value="3" id="q7_3" hidden>
                                                        <label for="q7_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_7" value="4" id="q7_4" hidden>
                                                        <label for="q7_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_7" value="5" id="q7_5" hidden>
                                                        <label for="q7_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being not effective at all while 5 being very effective</i></small>
                                                    </div>

                                                    <!-- Question 8 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>TECHNICAL RELIABILITY</h6>
                                                        <label for="q8" class="form-label">How reliable was the system in terms of technical performance and responsiveness?</label>
                                                        <div class="rating justify-content-center" id="q8">
                                                        <input type="radio" name="question_8" value="1" id="q8_1" hidden>
                                                        <label for="q8_1"><i class='bx bxs-star'></i>
                                                        <span class="m-2">1</span></label>
                                                        <input type="radio" name="question_8" value="2" id="q8_2" hidden>
                                                        <label for="q8_2"><i class='bx bxs-star'></i>
                                                        <span class="m-2">2</span></label>
                                                        <input type="radio" name="question_8" value="3" id="q8_3" hidden>
                                                        <label for="q8_3"><i class='bx bxs-star'></i>
                                                        <span class="m-2">3</span></label>
                                                        <input type="radio" name="question_8" value="4" id="q8_4" hidden>
                                                        <label for="q8_4"><i class='bx bxs-star'></i>
                                                        <span class="m-2">4</span></label>
                                                        <input type="radio" name="question_8" value="5" id="q8_5" hidden>
                                                        <label for="q8_5"><i class='bx bxs-star'></i>
                                                        <span class="m-2">5</span></label>
                                                        </div>
                                                        <small><i>note: 1 being very unreliable at all while 5 being very reliable</i></small>
                                                    </div>

                                                    <!-- Question 9 -->
                                                    <div class="mb-2 border border-secondary rounded-1 p-3">
                                                        <h6>IMPROVEMENT SUGGESTIONS</h6>
                                                        <label for="q9_1" class="form-label">Please provide any suggestions or comments on how the web-based appointment system could be improved.</label>
                                                        <div class="rating justify-content-center">
                                                        <div class="form-floating">
                                                            <textarea class="form-control"  name="question_9" id="q9_1" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px; width: 300px;"></textarea>
                                                            <label for="floatingTextarea2"></label>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" name="submitFeedback">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end ng feedback-->
                </div>
            </div>
        </div>
    </div>
<!--Main Content-->
</div>
<!-- Modals -->
<?php include 'functions/header-modal.php'; ?>

<!--Chatbot if necessary-->

<script src="/assets/js/chat-bot-script.js"></script>
<script src="/assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>