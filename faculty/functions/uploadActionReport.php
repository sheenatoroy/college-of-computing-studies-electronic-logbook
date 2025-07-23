<?php


include "../../connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//end

if (isset($_POST['updateActionReport'])) {


    // File upload configuration
    $file = $_FILES['action_report']['name'];
    $target_dir = "../../assets/files/";
    $target_dir2 = "../../assets/files/" . $file;
    $target_file = $target_dir . basename($_FILES["action_report"]["name"]);

    $appointment_id = $_POST['action_appointment_id'];
    $student_id = $_POST['action_student_id'];
    $prof_id = $_POST['prof_id'];
    $remarks = $_POST['action_remarks'];
    $action_report_textbox = $_POST['action_report_textbox'];
    // $action_report = $_POST['action_report'];
    // $action_report_path = $_POST['action_repo']

     // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        if (move_uploaded_file($_FILES["action_report"]["tmp_name"], $target_file)) {
            // Student_id exists, proceed with the appointment insertion
            $query = "UPDATE appointments SET 
            action_report = '$target_dir', action_report_path = '$target_dir2', action_report_textbox = '$action_report_textbox', remarks = 'Done' WHERE appointment_id = '$appointment_id'";
            $query_run = mysqli_query($conn, $query);

            $appointment_id = mysqli_insert_id($conn);
            if ($query_run) {
                    // Fetch student details for email
                    $studentDetailsQuery = "SELECT * FROM student WHERE username = '$student_id'";
                    $studentDetailsResult = mysqli_query($conn, $studentDetailsQuery);

                    // Fetch professor details for email
                    $profDetailsQuery = "SELECT * FROM prof WHERE username = '$prof_id'";
                    $profDetailsResult = mysqli_query($conn, $profDetailsQuery);

                    // Display success message using JavaScript alert
                    echo '<script> alert("Action report successfully uploaded!"); window.location.href = "../faculty-accomplished-appointment.php"; </script>';

                    
            }else {
                echo '<script> alert("Appointment Not Saved"); </script>';
            } 
        }
        
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
}
?>
