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

if (isset($_POST['updateRemarks'])) {

    $appointment_id = $_POST['appointment_id_view'];
    $student_id = $_POST['student_id_view'];
    $prof_id = $_POST['prof_id'];
    $type_of_concern = $_POST['hidden_type_of_concern_view'];
    $specific_concern = $_POST['hidden_specific_concern_view'];
    $detailed_concern = $_POST['hidden_detailed_concern_view'];
    $appointment_status = $_POST['hidden_appointment_status_view'];
    $remarks = isset($_POST['remarks1']) ? $_POST['remarks1'] : ''; // Set default value if not provided
    $day = $_POST['hidden_day_view'];
    $time_start = $_POST['hidden_time_start_view'];
    $time_end = $_POST['hidden_time_end_view'];

    $formattedDateTimeStart = date('h:i:s A', strtotime($time_start));
    $formattedDateTimeEnd = date('h:i:s A', strtotime($time_end));


    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the remarks update
        $updateSql = "UPDATE appointments SET remarks = 'Approved' WHERE appointment_id = '$appointment_id'";
        $updateResult = mysqli_query($conn, $updateSql);

        // Get the ID of the inserted appointment
        $appointmentId = mysqli_insert_id($conn);

        //gmail and pdf
        if ($updateResult) {
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
                $pdf->SetAuthor('College of Computing Studies');
                $pdf->SetTitle('New Scheduled Appointments');
                $pdf->SetSubject('New Scheduled Appointments');
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

                // Add content to PDF
                $letterContent = "
                <div style='margin-left:50%;'>
                <h2 style='font-weight: bold; text-align: center;'>APPOINTMENT ACCEPTANCE</h2>
                </div>
                <br>
                <p style='font-size: 14px; text-align: justify;'>Appointment Status: {$appointment_status}</p>
                
                <p style='font-size: 14px; text-align: justify;'>To Mr/Ms. {$studentRow['firstname']} {$studentRow['lastname']},</p>
                
                <p style='font-size: 14px; text-align: justify;'>Dangal Greetings!</p>
                
                <p style='font-size: 14px; text-align: justify;'>This letter is to confirm my acceptance of our scheduled appointment regarding <span style='font-weight: bold;'>{$type_of_concern}</span> on <span style='font-weight: bold;'>{$day}</span> at <span style='font-weight: bold;'>{$formattedDateTimeStart}</span> to <span style='font-weight: bold;'>{$formattedDateTimeEnd}</span>.</p>
                
                <p style='font-size: 14px; text-align: justify;'>As your Professor, I am looking forward to discuss the <span style='font-weight: bold;'>{$specific_concern}</span> with you. Your academic progress is important to me, and I am committed to providing the support and guidance you need to succeed.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Rest assured that I will be fully prepared for our meeting and will make every effort to ensure it is productive and beneficial for you.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Thank you for taking the initiative to schedule this appointment, and I appreciate your dedication to your academic growth.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>
                
                <p style='font-size: 14px; text-align: justify;'>
                    Prof. {$profRow['firstname']} {$profRow['lastname']}<br>
                    Faculty Member
                </p>
            
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
                    echo '<script> alert("Approving Appointment Successfully!"); window.location.href = "../admin-approved-appointment.php"; </script>';
                } else {
                    echo '<script> alert("Remarks Updated Successfully. Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../admin-pending-appointment.php"; </script>';
                }
            } else {
                echo '<script> alert("Error fetching student details for email"); window.location.href = "../admin-pending-appointment.php"; </script>';
            }
        } else {
            echo '<script> alert("Failed to Update Remarks"); window.location.href = "../admin-set-appointment.php"; </script>';
        }
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
}else if (isset($_POST['decline'])) {

    $appointment_id = $_POST['appointment_id_view'];
    $student_id = $_POST['student_id_view'];
    $prof_id = $_POST['prof_id'];
    $type_of_concern = $_POST['type_of_concern_view'];
    $specific_concern = $_POST['specific_concern_view'];
    $detailed_concern = $_POST['detailed_concern_view'];
    $appointment_status = $_POST['appointment_status_view'];
    $remarks = isset($_POST['remarks1']) ? $_POST['remarks1'] : ''; // Set default value if not provided
    $day = $_POST['day_view'];
    $time_start = $_POST['time_start_view'];
    $time_end = $_POST['time_end_view'];

    $formattedDateTimeStart = date('h:i:s A', strtotime($time_start));
    $formattedDateTimeEnd = date('h:i:s A', strtotime($time_end));    

    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the remarks update
        $updateSql = "UPDATE appointments SET remarks = 'Pending' WHERE appointment_id = '$appointment_id'";
        $updateResult = mysqli_query($conn, $updateSql);

        // Get the ID of the inserted appointment
        $appointmentId = mysqli_insert_id($conn);

        //gmail and pdf
        if ($updateResult) {
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
                $pdf->SetAuthor('College of Computing Studies');
                $pdf->SetTitle('Declined Scheduled Appointment');
                $pdf->SetSubject('Declined Scheduled Appointment');
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

                // Add content to PDF
                $letterContent = "
                    <div style='margin-left:50%;'>
                    <h2 style='font-weight: bold; text-align: center;'>APPOINTMENT STATUS</h2>
                    </div>
                    <br>
                    <p style='font-size: 14px; text-align: justify;'>Appointment Status: {$appointment_status}</p>

                    <p style='font-size: 14px; text-align: justify;'>To Mr/Ms. {$studentRow['firstname']} {$studentRow['lastname']},</p>

                    <p style='font-size: 14px; text-align: justify;'>Dangal Greetings!</p>

                    <p style='font-size: 14px; text-align: justify;'>This letter is in regards to our scheduled appointment concerning <span style='font-weight: bold;'>{$type_of_concern}</span> on <span style='font-weight: bold;'>{$day}</span> at <span style='font-weight: bold;'>{$formattedDateTimeStart}</span> to <span style='font-weight: bold;'>{$formattedDateTimeEnd}</span>.</p>

                    <p style='font-size: 14px; text-align: justify;'>As your Professor, I acknowledge the importance of addressing <span style='font-weight: bold;'>{$specific_concern}</span>. However, due to unforeseen circumstances, I regret to inform you that I am unable to proceed with the appointment as planned.</p>

                    <p style='font-size: 14px; text-align: justify;'>I apologize for any inconvenience this may cause. Nevertheless, I am fully committed to addressing your concerns and am eager to assist you. In light of this situation, I kindly request that we reschedule our appointment to a mutually convenient time.</p>

                    <p style='font-size: 14px; text-align: justify;'>Your cooperation and understanding in this matter are greatly appreciated. Should you have any questions, please do not hesitate to contact me.</p>

                    <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>

                    <p style='font-size: 14px; text-align: justify;'>
                        Prof. {$profRow['firstname']} {$profRow['lastname']}<br>
                        Faculty Member
                    </p>
                ";


                // Add tables to PDF content
                $pdf->writeHTML($letterContent, true, false, false, false, '');

                // Set email content
                $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
                $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
                $mail->Subject = 'Declined Appointment';
                $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! You have a message. Please find the details in the attached PDF.';
                $mail->isHTML(true);
                $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                // Send the email
                if ($mail->send()) {
                    echo '<script> alert("Declining the Appointment Successfully!."); window.location.href = "../admin-pending-appointment.php"; </script>';
                } else {
                    echo '<script> alert("Remarks Updated Successfully. Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../admin-pending-appointment.php"; </script>';
                }
            } else {
                echo '<script> alert("Error fetching student details for email"); window.location.href = "../admin-pending-appointment.php"; </script>';
            }
        } else {
            echo '<script> alert("Failed to Update Remarks"); window.location.href = "../admin-set-appointment.php"; </script>';
        }
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
}
?>
