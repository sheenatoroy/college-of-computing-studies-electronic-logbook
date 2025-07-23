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

    <title>CCS: E-LOG | Terms and Conditions</title>
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
                    
                <!-- <div class="nav-item">
                    <a class="nav-link fs-6 p-1 m-1" href="/faculty/faculty-feedback.php" style="color: #131313;">
                        <i class='bx bx-message-square-add'></i>
                        <span>Feedbacks</span>
                    </a>
                </div> -->
                
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
                    <div class="container">
                        <div class="alert alert-secondary text-dark shadow-sm rounded p-4 mt-3">
                            <h4 class="pt-2 mb-3"><i class='bx bxs-note mx-2'></i>Terms and Conditions</h4>
                            <p class="card-text" id="message">
                                Terms and Conditions for CCS ElogSystem Website <br>
                                <br>
                                1. Acceptance of Terms <br>
                                By accessing and using the CCS ElogSystem website, you agree to comply with and be bound by these Terms and Conditions. If you do not agree to these terms, please refrain from using the website.
                                <br>
                                <br>
                                2. Use of the Website<br>
                                2.1 You must use the CCS ElogSystem website in accordance with all applicable laws and regulations.
                                <br>
                                <br>
                                2.2 You are solely responsible for any content you submit or upload to the website. Do not submit or upload any content that is unlawful, harmful, threatening, abusive, defamatory, obscene, or otherwise objectionable.
                                <br>
                                <br>
                                2.3 The CCS ElogSystem website may only be used for lawful purposes. Transmitting any material that encourages conduct that could constitute a criminal offense or give rise to civil liability is strictly prohibited.
                                <br>
                                <br>
                                3. User Accounts <br>
                                3.1 To access certain features of the website, you may be required to create a user account. You are responsible for maintaining the confidentiality of your account information and password.
                                <br>
                                <br>
                                3.2 You agree to notify us immediately of any unauthorized use of your account or any other breach of security.
                                <br>
                                <br>
                                4. Intellectual Property <br>
                                4.1 All content on the CCS ElogSystem website, including text, graphics, logos, images, and software, is the property of CCS ElogSystem or its content suppliers and is protected by copyright and other intellectual property laws.
                                <br>
                                <br>
                                4.2 You may not reproduce, modify, distribute, display, perform, or otherwise use any content from the website without the prior written consent of CCS ElogSystem.
                                <br>
                                <br>
                                5. Privacy Policy <br>
                                5.1 Your use of the CCS ElogSystem website is also governed by our Privacy Policy, which can be found on the website.
                                <br>
                                <br>
                                5.2 By using the website, you consent to the collection and use of information as described in the Privacy Policy.
                                <br>
                                <br> 
                                6. Limitation of Liability <br>
                                6.1 CCS ElogSystem and its affiliates shall not be liable for any direct, indirect, incidental, special, or consequential damages arising out of or in any way connected with the use of the website.
                                <br>
                                <br>
                                6.2 CCS ElogSystem does not warrant that the website will be error-free, uninterrupted, or free of viruses or other harmful components.
                                <br>
                                <br>
                                7. Changes to Terms and Conditions <br>
                                CCS ElogSystem reserves the right to modify or replace these Terms and Conditions at any time. Your continued use of the website after any changes indicate your acceptance of the new terms.
                                <br>
                                <br>
                                8. Governing Law <br>
                                These Terms and Conditions shall be governed by and construed in accordance with the laws of [Jurisdiction], without regard to its conflict of law principles.
                                <br>
                                <br>
                                9. Contact Information <br>
                                If you have any questions about these Terms and Conditions, please contact us at [contact@ccs-elogsystem.com].
                                <br>
                                <br>
                                Last Updated: December 11, 2023 <br> <br>
                            </p>
                            <button type="button" id="ttsButton" class="btn btn-secondary">
                                <i class="bx bx-volume-full"></i> Speak
                            </button>
                        </div>
                    </div>

                    <!-- Bootstrap JS and Boxicons JS -->
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                    <script>
                        let isSpeaking = false;
                        let utterance;

                        function speakText() {
                            const messageElement = document.getElementById('message');
                            const message = messageElement.textContent.trim();

                            if ('speechSynthesis' in window && message) {
                                if (isSpeaking) {
                                    // Pause if already speaking
                                    window.speechSynthesis.pause(utterance);
                                    isSpeaking = false;
                                } else {
                                    // Resume if paused or start speaking if not started
                                    utterance = new SpeechSynthesisUtterance(message);
                                    const voices = window.speechSynthesis.getVoices();
                                    utterance.voice = voices[0];
                                    window.speechSynthesis.speak(utterance);
                                    isSpeaking = true;
                                }
                            } else {
                                // Handle cases where speech synthesis is not supported
                                alert("Speech synthesis is not supported in this browser.");
                            }
                        }

                        // Attach the speakText function to the button click event
                        document.getElementById('ttsButton').addEventListener('click', speakText);
                    </script>
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