<?php

include "../../connection.php";

//gmail and pdf
require_once('tcpdf/tcpdf.php');
require_once('phpmailer/src/PHPMailer.php');
require_once('phpmailer/src/SMTP.php');
require_once('phpmailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//end

if (isset($_POST['insertAppointment'])) {

    $student_id = $_POST['student_id'];
    $prof_id = $_POST['prof_id'];
    $type_of_concern = $_POST['type_of_concern'];
    $specific_concern = $_POST['specific_concern'];
    $detailed_concern = $_POST['detailed_concern'];
    $appointment_status = $_POST['appointment_status'];
    $remarks = $_POST['remarks'];
    $day = $_POST['day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $appoint_by = $_POST['appoint_by'];
    $app_day = $_POST['day'];

    // Format the date into "0000-00-00"
    $formatted_date = date('Y-m-d', strtotime($app_day));
    $formatted_time_start = date('H:i:s', strtotime($time_start));
    $formatted_time_end = date('H:i:s', strtotime($time_end));
    
    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the appointment insertion
        $query = "INSERT INTO appointments (`student_id`, `prof_id`, `type_of_concern`, `specific_concern`, `detailed_concern`, `appointment_status`, `remarks`, `time_start`, `time_end`, `day`, `appoint_by`, `app_day`) 
                    VALUES ('$student_id', '$prof_id', '$type_of_concern', '$specific_concern', '$detailed_concern', '$appointment_status', '$remarks', '$formatted_time_start', '$formatted_time_end', '$day', '$appoint_by', '$formatted_date')";
        $query_run = mysqli_query($conn, $query);
        
        // Check if the insert was successful
        if ($query_run) {
            // Update the record if the remarks is "Unresolved"
            $update_query = "UPDATE appointments SET action_report_textbox = 'No appearance', total_hours = '0' WHERE remarks = 'Unresolved' AND student_id = '$student_id' AND prof_id = '$prof_id' AND time_start = '$formatted_time_start' AND time_end = '$formatted_time_end' AND day = '$day' AND app_day = '$formatted_date'";
            $update_query_run = mysqli_query($conn, $update_query);
    
            if ($update_query_run) {
                // Update successful
        echo '<script> alert("Appointment Successfully Saved"); window.location.href = "../admin-pending-appointment.php"; </script>';
    } else {
                // Update failed
                echo "Failed to update the appointment.";
            }
        } else {
            // Insert failed
            echo "Failed to insert the appointment.";
        }
    }
    
        // //gmail and pdf
        // if ($query_run) {
        //     // Fetch student details for email
        //     $studentDetailsQuery = "SELECT * FROM student WHERE username = '$student_id'";
        //     $studentDetailsResult = mysqli_query($conn, $studentDetailsQuery);

        //     // Fetch professor details for email
        //     $profDetailsQuery = "SELECT * FROM prof WHERE username = '$prof_id'";
        //     $profDetailsResult = mysqli_query($conn, $profDetailsQuery);

        //     if ($studentDetailsResult && mysqli_num_rows($studentDetailsResult) > 0 && $profDetailsResult && mysqli_num_rows($profDetailsResult) > 0) {
        //         $studentRow = mysqli_fetch_assoc($studentDetailsResult);
        //         $profRow = mysqli_fetch_assoc($profDetailsResult);

        //         // Email Configuration
        //         $mail = new PHPMailer(true);
        //         $mail->isSMTP();
        //         $mail->Host = 'smtp.gmail.com';
        //         $mail->SMTPAuth = true;
        //         $mail->Username = 'ccsorgs@gmail.com';
        //         $mail->Password = 'fzbhogfmmdnyrmip';
        //         $mail->SMTPSecure = 'tls';
        //         $mail->Port = 587;

        //         // PDF Generation
        //         $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //         $pdf->SetCreator(PDF_CREATOR);
        //         $pdf->SetAuthor('College of Computing Studies');
        //         $pdf->SetTitle('New Scheduled Appointments');
        //         $pdf->SetSubject('New Scheduled Appointments');
        //         $pdf->SetKeywords('Appointment');
        //         $pdf->SetMargins(25.4, 25.4, 25.4);
        //         $pdf->AddPage();

        //         $pdf->SetFont('times', '', 12);

        //         $pdf->Image('images/UCLOGO.jpg', 20, 10, 28.35, 28.35);
        //         $pdf->Image('images/CCSLOGO.jpg', $pdf->getPageWidth() - 20 - 28.35, 10, 28.35, 28.35);

        //         $centerTextX = ($pdf->getPageWidth() - 28.35 * 5) / 2;
        //         $pdf->SetCellHeightRatio(1.0);

        //         $pdf->SetFont('helvetica', '', 11);
        //         $pdf->SetXY($centerTextX, 10);
        //         $pdf->Cell(0, 10, 'Republic of the Philippines', 0, 1, 'C');

        //         $pdf->SetFont('helvetica', '', 20);
        //         $pdf->SetTextColor(0, 128, 0);
        //         $pdf->SetXY($centerTextX, 15);
        //         $pdf->Cell(0, 10, 'Pamantasan ng Cabuyao', 0, 1, 'C');
        //         $pdf->SetTextColor(0, 0, 0);

        //         $pdf->SetFont('helvetica', '', 14);
        //         $pdf->SetXY($centerTextX, 20);
        //         $pdf->Cell(0, 10, '(University of Cabuyao)', 0, 1, 'C');

        //         $pdf->SetFont('helvetica', 'BI', 11);
        //         $pdf->SetXY($centerTextX, 25);
        //         $pdf->Cell(0, 10, 'College of Computing Studies', 0, 1, 'C');

        //         $pdf->SetFont('helvetica', '', 9);
        //         $pdf->SetXY($centerTextX, 30);
        //         $pdf->Cell(0, 10, 'Katapatan Mutual Homes', 0, 1, 'C');

        //         $pdf->SetFont('times', '', 11);

        //         $letterContent = "
        //         <div style='margin-left:50%;'>
        //             <h2 style='font-weight: bold; text-align: center;'>APPOINTMENT DETAILS</h2>
        //         </div>
        //         <br>
        //         <p style='font-size: 14px; text-align: justify;'>Appointment Status: {$appointment_status}</p>

        //         <p style='font-size: 14px; text-align: justify;'>To Mr/Ms. {$studentRow['firstname']} {$studentRow['lastname']},</p>

        //         <p style='font-size: 14px; text-align: justify;'>Dangal Greetings!</p>

        //         <p style='font-size: 14px; text-align: justify;'>This letter is to set an appointment with you regarding <span style='font-weight: bold;'>{$type_of_concern}</span> on <span style='font-weight: bold;'>{$day}</span> at <span style='font-weight: bold;'>{$time_start}</span> to <span style='font-weight: bold;'>{$time_end}</span>.</p>

        //         <p style='font-size: 14px; text-align: justify;'>As your Professor, this appointment has been scheduled to address <span style='font-weight: bold;'>{$specific_concern}</span>. Your punctual attendance is crucial to ensure we can thoroughly discuss and resolve this matter.</p>

        //         <p style='font-size: 14px; text-align: justify;'>Thank you for your cooperation and attention to this matter. Should you have any questions or need to reschedule, please do not hesitate to contact me.</p>

        //         <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>

        //         <p style='font-size: 14px; text-align: justify;'>
        //             Prof. {$profRow['firstname']} {$profRow['lastname']}<br>
        //             Faculty Member
        //         </p>


        //     ";
            

        //         // Add tables to PDF content
        //         $pdf->writeHTML($letterContent, true, false, false, false, '');

        //         // Set email content
        //         $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
        //         $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
        //         $mail->Subject = 'New Appointment';
        //         $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! You have a new appointment. Please find the details in the attached PDF.';
        //         $mail->isHTML(true);
        //         $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

        //         // Send the email
        //         if ($mail->send()) {
        //             // Assume you have a variable $appointmentId containing the ID of the appointment being evaluated
        //             $evaluationStatus = 'Not Done';

        //             $updateSql = "UPDATE appointments SET evaluation_status = ? WHERE appointment_id = ?";
        //             $updateStmt = mysqli_prepare($conn, $updateSql);
        //             mysqli_stmt_bind_param($updateStmt, "si", $evaluationStatus, $appointmentId);

        //             if (mysqli_stmt_execute($updateStmt)) {
        //                 // Update successful
        //                 echo '<script> alert("Appointment Successfully Saved and Email Sent"); window.location.href = "../admin-pending-appointment.php"; </script>';
        //             } else {
        //                 // Handle the error
        //                 echo '<script> alert("Appointment Saved, Email Sent, but Evaluation Status Not Updated. Error: ' . mysqli_error($conn) . '"); window.location.href = "../admin-set-appointment.php"; </script>';
        //             }
        //         } else {
        //             echo '<script> alert("Appointment Saved, but Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../admin-set-appointment.php"; </script>';
        //         }
        //     } else {
        //         echo '<script> alert("Error fetching student or professor details for email"); </script>';
        //     }
        // } else {
        //     echo '<script> alert("Appointment Not Saved"); </script>';
        // } //end of gmail and pdf

    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
?>
