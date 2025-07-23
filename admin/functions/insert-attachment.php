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

if (isset($_POST['insertAttachment'])) {

    // Retrieve form data using the correct array keys
    $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';
    $prof_id = isset($_POST['prof_id']) ? $_POST['prof_id'] : '';
    $type_of_concern = isset($_POST['type_of_concern']) ? $_POST['type_of_concern'] : '';
    $specific_concern = isset($_POST['specific_concern']) ? $_POST['specific_concern'] : '';
    $detailed_concern = isset($_POST['detailed_concern']) ? $_POST['detailed_concern'] : '';
    $appointment_status = isset($_POST['appointment_status']) ? $_POST['appointment_status'] : '';
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    $day = isset($_POST['day']) ? $_POST['day'] : '';
    $time_start = isset($_POST['time_start']) ? $_POST['time_start'] : '';
    $time_end = isset($_POST['time_end']) ? $_POST['time_end'] : '';

    $formattedDateTimeStart = date('H:i:s', strtotime($time_start));
    $formattedDateTimeEnd = date('H:i:s', strtotime($time_end));


    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the appointment insertion
        $query = "INSERT INTO appointments (`student_id`, `prof_id`, `type_of_concern`, `specific_concern`, `detailed_concern`, `appointment_status`, `remarks`, `time_start`, `time_end`, `day`) 
                    VALUES ('$student_id', '$prof_id', '$type_of_concern', '$specific_concern', '$detailed_concern', '$appointment_status', '$remarks', '$formattedDateTimeStart', '$formattedDateTimeEnd', '$day')";
        $query_run = mysqli_query($conn, $query);


        //gmail and pdf
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
                $pdf->SetTitle('Appointment Reminder');
                $pdf->SetSubject('Appointment Reminder');
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
                        <h2 style='font-weight: bold;'>Subject: Remaining 1 day to accomplish the scheduled appointment</h2>
                    </div>

                    <br>

                    <p style='font-size: 14px; text-align: justify;'>Dangal Greetings, {$studentRow['firstname']} {$studentRow['lastname']}!</p>
                    <br>

                    <p style='font-size: 14px; text-align: justify;'>This is to remind you again that you only have remaining 1 day to accomplish the appointment scheduled to you. Here are again the deatils of the appointment:</p>
                    <br>

                    <p style='font-size: 14px; text-align: justify;'>I am {$profRow['firstname']} {$profRow['lastname']}, sending an appointment of $type_of_concern to you on " . date("F j, Y", strtotime($day)) . "  and  {$time_start} - {$time_end}. </p>
                    <br>

                    <p style='font-size: 14px; text-align: justify;'>My appointment with you is regarding {$type_of_concern} on {$specific_concern} due to {$detailed_concern}. Please come on time so we can address this properly.</p> 
                                
                    <p style='font-size: 14px; text-align: justify;'>Thank you for your kind consideration!</p>

                    <p style='font-size: 14px; text-align: justify;'>From: Prof. {$profRow['firstname']} {$profRow['lastname']}</p>
                ";

                // Add tables to PDF content
                $pdf->writeHTML($letterContent, true, false, false, false, '');

                // Set email content
                $mail->setFrom('ccsorgs@gmail.com', 'CCS Orgs');
                $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
                $mail->Subject = 'Remaining 1 day to accomplish appointment';
                $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! This is to remind you again of the scheduled appointment to you. Please find the details in the attached PDF.';
                $mail->isHTML(true);
                $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                // Send the email
                if ($mail->send()) {
                    echo '<script> alert("Appointment Successfully Saved and Email Sent"); window.location.href = "../admin-pending-appointment.php"; </script>';
                } else {
                    echo '<script> alert("Appointment Saved, but Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../admin-pending-appointment.php"; </script>';
                }
            } else {
                echo '<script> alert("Error fetching student or professor details for email"); </script>';
            }
        } else {
            echo '<script> alert("Appointment Not Saved"); </script>';
        } //end of gmail and pdf

    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID");  window.location.href = "../admin-pending-appointment.php"; </script>';
    }
}

?>