<?php

include "../connection.php";

// Gmail and PDF
require_once('../faculty/functions/tcpdf/tcpdf.php');
require_once('../faculty/functions/phpmailer/src/PHPMailer.php');
require_once('../faculty/functions/phpmailer/src/SMTP.php');
require_once('../faculty/functions/phpmailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// End

if (isset($_POST['updateAppointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $student_id = $_POST['student_id'];
    $prof_id = $_POST['prof_id'];
    $day = $_POST['edit_day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $type_of_concern = $_POST['type_of_concern'];
    $specific_concern = $_POST['specific_concern'];
    $detailed_concern = $_POST['detailed_concern'];
    $resched_reason = $_POST['resched_reason'];
    $appointment_status = $_POST['appointment_status'];

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
            echo "<script>alert('The selected day and time are not available!'); window.location.href = 'faculty-pending-appointment.php'; </script>";
        } else if(mysqli_num_rows($same_day_result) > 0){
            echo "<script>alert('You are already schedule during this time and day!'); window.location.href = 'faculty-pending-appointment.php'; </script>";
        }else{
            // Student_id exists, proceed with the appointment update
            $update_query = "UPDATE appointments SET 
                                type_of_concern = '$type_of_concern', 
                                specific_concern = '$specific_concern', 
                                detailed_concern = '$detailed_concern', 
                                resched_reason = '$resched_reason', 
                                day = '$day', 
                                time_start = '$formatted_time_start', 
                                time_end = '$formatted_time_end' 
                                WHERE appointment_id = '$appointment_id'";
            $update_result = mysqli_query($conn, $update_query);

            if ($update_result) {
                // Fetch student details for email
                $student_details_query = "SELECT * FROM student WHERE username = '$student_id'";
                $student_details_result = mysqli_query($conn, $student_details_query);

                // Fetch professor details for email
                $prof_details_query = "SELECT * FROM prof WHERE username = '$prof_id'";
                $prof_details_result = mysqli_query($conn, $prof_details_query);

                if ($student_details_result && mysqli_num_rows($student_details_result) > 0 && 
                    $prof_details_result && mysqli_num_rows($prof_details_result) > 0) {

                    $student_row = mysqli_fetch_assoc($student_details_result);
                    $prof_row = mysqli_fetch_assoc($prof_details_result);

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

                        <p style='font-size: 14px; text-align: justify;'>Dangal Greetings, {$student_row['firstname']} {$student_row['lastname']}!</p>

                        <br>
                        <p style='font-size: 14px; text-align: justify;'>This letter is to request a rescheduling of our upcoming appointment regarding {$type_of_concern} on {$day} at {$formatted_time_start} to {$formatted_time_end}.</p> 
                                    
                        <p style='font-size: 14px; text-align: justify;'>Unfortunately, due to {$resched_reason}, I am unable to attend the appointment at the originally scheduled time.</p>

                        <p style='font-size: 14px; text-align: justify;'>I apologize for any inconvenience this may cause and assure you of my commitment to addressing {$specific_concern}. To accommodate the change, I propose {$day} at {$formatted_time_start } to {$formatted_time_end} to be the alternative time for our appointment.</p>
                        <p style='font-size: 14px; text-align: justify;'>I appreciate your understanding and flexibility in this request. Should you have any questions, please do not hesitate to contact me.</p>
                        <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>
                        <p style='font-size: 14px; text-align: justify;'>
                        Prof. {$prof_row['firstname']} {$prof_row['lastname']} <br>
                        Faculty Member
                        </p>
                        ";

                    // Add tables to PDF content
                    $pdf->writeHTML($letterContent, true, false, false, false, '');

                    // Set email content
                    $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
                    $mail->addAddress($student_row["email"], $student_row["lastname"] . ", " . $student_row["firstname"]);
                    $mail->Subject = 'Reschedule Appointment';
                    $mail->Body = 'Dangal greetings, ' . $student_row["firstname"] . " " . $student_row["lastname"] . '! You have a rescheduled appointment. Please find the details in the attached PDF.';
                    $mail->isHTML(true);
                    $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                    // Send the email
                    if ($mail->send()) {
                        $evaluation_status = 'Not Done';

                        $update_status_query = "UPDATE appointments SET evaluation_status = ? WHERE appointment_id = ?";
                        $update_status_stmt = mysqli_prepare($conn, $update_status_query);
                        mysqli_stmt_bind_param($update_status_stmt, "si", $evaluation_status, $appointment_id);

                        if (mysqli_stmt_execute($update_status_stmt)) {
                            echo '<script>alert("Reschedule Appointment Successfully!"); window.location.href = "faculty-pending-appointment.php";</script>';
                        } else {
                            echo '<script>alert("Appointment Saved, Email Sent, but Evaluation Status Not Updated. Error: ' . mysqli_error($conn) . '"); window.location.href = "../faculty
                            -set-appointment.php";</script>';
                        }
                    } else {
                        echo '<script>alert("Appointment Saved, but Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../faculty-set-appointment.php";</script>';
                    }
                } else {
                    echo '<script>alert("Error fetching student or professor details for email");</script>';
                }
            } else {
                echo '<script>alert("Appointment Not Saved");</script>';
            }
        }
    } else {
        echo '<script>alert("Invalid Student ID");</script>';
    }
}
?>
