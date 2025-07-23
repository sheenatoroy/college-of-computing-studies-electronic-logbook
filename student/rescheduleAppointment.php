<?php


include "../connection.php";

//gmail and pdf
require_once('../student/functions/tcpdf/tcpdf.php');
require_once('../student/functions/phpmailer/src/PHPMailer.php');
require_once('../student/functions/phpmailer/src/SMTP.php');
require_once('../student/functions/phpmailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//end

if (isset($_POST['updateAppointment'])) {

    $appointment_id = $_POST['appointment_id'];
    $student_id = $_POST['student_id'];
    $prof_id = $_POST['prof_id_resched_hidden'];
    $day = $_POST['edit_day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $type_of_concern = $_POST['type_of_concern'];
    $specific_concern = $_POST['specific_concern'];
    $detailed_concern = $_POST['detailed_concern'];
    $appointment_status = $_POST['appointment_status'];
    $resched_reason = $_POST['resched_reason'];

    $formatted_date = date('Y-m-d', strtotime($day));
    $formatted_time_start = date('H:i:s', strtotime($time_start));
    $formatted_time_end = date('H:i:s', strtotime($time_end));

    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {

        //Checks if there's a pending appointment of that day and time
        $pending_check_query = "SELECT * FROM appointments 
                                WHERE student_id = '$student_id' 
                                AND day = '$day' 
                                AND time_start = '$formatted_time_start' 
                                AND remarks = 'Pending'
                                AND appointment_id != '$appointment_id'";
        $pending_check_result = mysqli_query($conn, $pending_check_query);

        //Checks if the reschedule is same day and time
        $check_reschedule_query = "SELECT * FROM appointments 
                                WHERE student_id = '$student_id' 
                                AND day = '$day' 
                                AND time_start = '$formatted_time_start' 
                                AND remarks = 'Pending'
                                AND appointment_id = '$appointment_id' ";
        $same_day_result = mysqli_query($conn, $check_reschedule_query);
        if (mysqli_num_rows($pending_check_result) > 0) {
            echo "<script>alert('The selected day and time are not available!'); window.location.href = 'student-pending-appointment.php'; </script>";
        } else if(mysqli_num_rows($same_day_result) > 0){
            echo "<script>alert('You are already schedule during this time and day!'); window.location.href = 'student-pending-appointment.php'; </script>";
        } else {
            // Student_id exists, proceed with the appointment insertion
            $query = "UPDATE appointments SET 
            type_of_concern = '$type_of_concern', specific_concern = '$specific_concern', detailed_concern = '$detailed_concern', 
            day = '$day', time_start = '$formattedDateTimeStart', time_end = '$formattedDateTimeEnd', resched_reason = '$resched_reason' WHERE appointment_id = '$appointment_id'";
            $query_run = mysqli_query($conn, $query);
            
            if ($query_run) {
                    // Fetch student details for email
                    $studentDetailsQuery = "SELECT * FROM student WHERE username = '$student_id'";
                    $studentDetailsResult = mysqli_query($conn, $studentDetailsQuery);

                    // Fetch professor details for email
                    $profDetailsQuery = "SELECT * FROM prof WHERE username = '$prof_id'";
                    $profDetailsResult = mysqli_query($conn, $profDetailsQuery);

                    if ($studentDetailsResult && mysqli_num_rows($studentDetailsResult) > 0 && $profDetailsResult && mysqli_num_rows($profDetailsResult) > 0) {
                        $studentRow = mysqli_fetch_assoc($studentDetailsResult);
                        $profRow = mysqli_fetch_assoc($profDetailsResult);

                        // Email Configuration
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'ccsorgs@gmail.com';
                        $mail->Password = 'fzbhogfmmdnyrmip';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        // PDF Generation
                        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                        $pdf->SetCreator(PDF_CREATOR);
                        $pdf->SetAuthor('CCS DEPT');
                        $pdf->SetTitle('Reschedule Appointments');
                        $pdf->SetSubject('Reschedule Appointments');
                        $pdf->SetKeywords('Appointment');
                        $pdf->SetMargins(25.4, 25.4, 25.4);
                        $pdf->AddPage();

                        $pdf->SetFont('times', '', 12);

                        $pdf->Image('images/UCLOGO.jpg', 20, 10, 28.35, 28.35);
                        $pdf->Image('images/CCSLOGO.jpg', $pdf->getPageWidth() - 20 - 28.35, 10, 28.35, 28.35);

                        $centerTextX = ($pdf->getPageWidth() - 28.35 * 5) / 2;
                        $pdf->SetCellHeightRatio(1.0);

                        $pdf->SetFont('helvetica', '', 11);
                        $pdf->SetXY($centerTextX, 10);
                        $pdf->Cell(0, 10, 'Republic of the Philippines', 0, 1, 'C');

                        $pdf->SetFont('helvetica', '', 20);
                        $pdf->SetTextColor(0, 128, 0);
                        $pdf->SetXY($centerTextX, 15);
                        $pdf->Cell(0, 10, 'Pamantasan ng Cabuyao', 0, 1, 'C');
                        $pdf->SetTextColor(0, 0, 0);

                        $pdf->SetFont('helvetica', '', 14);
                        $pdf->SetXY($centerTextX, 20);
                        $pdf->Cell(0, 10, '(University of Cabuyao)', 0, 1, 'C');

                        $pdf->SetFont('helvetica', 'BI', 11);
                        $pdf->SetXY($centerTextX, 25);
                        $pdf->Cell(0, 10, 'College of Computing Studies', 0, 1, 'C');

                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->SetXY($centerTextX, 30);
                        $pdf->Cell(0, 10, 'Katapatan Mutual Homes', 0, 1, 'C');

                        $pdf->SetFont('times', '', 11);

                        // Add tables
                        $letterContent = "
                            <div style='margin-left:50%;'>
                                <h2 style='font-weight: bold;'>CCS RESCHEDULE APPOINTMENT DETAILS</h2>
                            </div>

                            <br>

                            <p style='font-size: 14px; text-align: justify;'>Appointment Status: {$appointment_status}</p>

                            <p style='font-size: 14px; text-align: justify;'>Dangal Greetings, {$profRow['firstname']} {$profRow['lastname']}!</p>

                            <br>
                            <p style='font-size: 14px; text-align: justify;'>I am {$profRow['firstname']} {$studentRow['lastname']}, sending an appointment of Type of Concern to you on " . date("F j, Y", strtotime($day)) . "  and  {$time_start} - {$time_end}. <br>
                            My appointment with you is regarding {$type_of_concern} on {$specific_concern} due to {$detailed_concern}. Please come on time so we can address this properly.</p> 
                                        
                            <p style='font-size: 14px; text-align: justify;'>Thank you for your kind consideration!</p>

                            <p style='font-size: 14px; text-align: justify;'>From: Prof. {$studentRow['firstname']} {$studentRow['lastname']}</p>
                        ";

                        // Add tables to PDF content
                        $pdf->writeHTML($letterContent, true, false, false, false, '');

                        // Set email content
                        $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
                        $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
                        $mail->Subject = 'New Appointment';
                        $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! You have a new appointment. Please find the details in the attached PDF.';
                        $mail->isHTML(true);
                        $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                        // Send the email
                        if ($mail->send()) {
                            // Assume you have a variable $appointmentId containing the ID of the appointment being evaluated
                            $evaluationStatus = 'Not Done';

                            $updateSql = "UPDATE appointments SET evaluation_status = ? WHERE appointment_id = ?";
                            $updateStmt = mysqli_prepare($conn, $updateSql);
                            mysqli_stmt_bind_param($updateStmt, "si", $evaluationStatus, $appointmentId);

                            if (mysqli_stmt_execute($updateStmt)) {
                                // Update successful
                                echo '<script> alert("Appointment Successfully Saved and Email Sent"); window.location.href = "student-pending-appointment.php"; </script>';
                            } else {
                                // Handle the error
                                echo '<script> alert("Appointment Saved, Email Sent, but Evaluation Status Not Updated. Error: ' . mysqli_error($conn) . '"); window.location.href = "../student-set-appointment.php"; </script>';
                            }
                        } else {
                            echo '<script> alert("Appointment Saved, but Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../student-set-appointment.php"; </script>';
                        }
                    } else {
                        echo '<script> alert("Error fetching student or professor details for email"); </script>';
                    }    
            }else {
                echo '<script> alert("Appointment Not Saved"); </script>';
            } 
        }
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Professor ID"); </script>';
    }
}
?>
