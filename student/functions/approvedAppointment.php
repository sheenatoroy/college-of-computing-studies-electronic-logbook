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
    $student_id = $_POST['hidden_student_id_view'];
    $prof_id = $_POST['prof_id_view'];
    $type_of_concern = $_POST['type_of_concern_view'];
    $specific_concern = $_POST['specific_concern_view'];
    $detailed_concern = $_POST['detailed_concern_view'];
    $appointment_status = $_POST['appointment_status_view'];
    $remarks = isset($_POST['remarks1']) ? $_POST['remarks1'] : ''; // Set default value if not provided
    $day = $_POST['day_view'];
    $time_start = $_POST['time_start_view'];
    $time_end = $_POST['time_end_view'];

    $formattedDateTimeStart = date('H:i:s', strtotime($time_start));
    $formattedDateTimeEnd = date('H:i:s', strtotime($time_end));

    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the remarks update
        $updateSql = "UPDATE appointments SET remarks = 'Approved' WHERE appointment_id = '$appointment_id'";
        $updateResult = mysqli_query($conn, $updateSql);

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
                $pdfContent = "
                <div style='margin-left:50%;'>
                <h2 style='font-weight: bold; text-align: center;'>APPOINTMENT ACCEPTANCE</h2>
                </div>
                <br>
                <p style='font-size: 14px; text-align: justify;'>Appointment Status: {$appointment_status}</p>
                
                <p style='font-size: 14px; text-align: justify;'>To Prof. {$profRow['firstname']} {$profRow['lastname']}</p>
                
                <p style='font-size: 14px; text-align: justify;'>Dangal Greetings!</p>
                
                <p style='font-size: 14px; text-align: justify;'>I am {$studentRow['firstname']} {$studentRow['lastname']}, one of your students. Thank you for scheduling our appointment regarding {$type_of_concern} on {$day} at {$time_start} to {$time_end} with you. I am writing to confirm my acceptance of the scheduled appointment.</p>
                
                <p style='font-size: 14px; text-align: justify;'>I appreciate the opportunity to discuss {$specific_concern}. Your guidance and support are invaluable to me, and I am eager to benefit from our discussion.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Rest assured that I will be punctual and come prepared for our meeting. I look forward to our conversation and working together to address any issues or questions I may have.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Thank you for your attention to this matter, and I am grateful for your willingness to meet with me.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>
                
                <p style='font-size: 14px; text-align: justify;'>
                {$studentRow['firstname']} {$studentRow['lastname']}<br>
                    Student
                </p>
            
                ";

                $pdf->writeHTML($pdfContent, true, false, false, false, '');

                // Set email content
                $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
                $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
                $mail->Subject = 'Approved Appointment';
                $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! You have a new appointment. Please find the details in the attached PDF.';
                $mail->isHTML(true);
                $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                // Send the email
                if ($mail->send()) {
                    echo '<script> alert("Approved Appointment Successfully! . Email Sent."); window.location.href = "../student-approved-appointment.php"; </script>';
                } else {
                    echo '<script> alert("Approved Appointment Successfully. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../student-approved-appointment.php"; </script>';
                }
            } else {
                echo '<script> alert("Approved Appointment Successfully"); window.location.href = "../student-approved-appointment.php"; </script>';
            }
        } else {
            echo '<script> alert("Failed to Approve the Appointment."); window.location.href = "../student-set-appointment.php"; </script>';
        }
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
}else if(isset($_POST['decline'])) {
    $appointment_id = $_POST['appointment_id_view'];
    $student_id = $_POST['hidden_student_id_view'];
    $prof_id = $_POST['prof_id'];
    $type_of_concern = $_POST['type_of_concern_view'];
    $specific_concern = $_POST['specific_concern_view'];
    $detailed_concern = $_POST['detailed_concern_view'];
    $appointment_status = $_POST['appointment_status_view'];
    $remarks = isset($_POST['remarks1']) ? $_POST['remarks1'] : ''; // Set default value if not provided
    $day = $_POST['day_view'];
    $time_start = $_POST['time_start_view'];
    $time_end = $_POST['time_end_view'];

    $formattedDateTimeStart = date('H:i:s', strtotime($time_start));
    $formattedDateTimeEnd = date('H:i:s', strtotime($time_end));

    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Student_id exists, proceed with the remarks update
        $updateSql = "UPDATE appointments SET remarks = 'Decline' WHERE appointment_id = '$appointment_id'";
        $updateResult = mysqli_query($conn, $updateSql);

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

                // PDF Generation
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('College of Computing Studies');
                $pdf->SetTitle('Appointment Details');
                $pdf->SetSubject('Appointment Details');
                $pdf->SetKeywords('Appointment');
                $pdf->SetMargins(25.4, 25.4, 25.4);
                $pdf->AddPage();

                $pdf->SetFont('times', '', 12);

                // Add content to PDF
                $pdfContent = "
                <div style='margin-left:50%;'>
                <h2 style='font-weight: bold; text-align: center;'>APPOINTMENT DECLINED</h2>
                </div>
                <br>
                <p style='font-size: 14px; text-align: justify;'>To Prof. {$profRow['firstname']} {$profRow['lastname']}</p>
                
                <p style='font-size: 14px; text-align: justify;'>Dangal Greetings!</p>
                
                <p style='font-size: 14px; text-align: justify;'>I am {$studentRow['firstname']} {$studentRow['lastname']}, one of your students. This letter is in regards to our scheduled appointment concerning {$type_of_concern} {$day} at {$time_start} to {$time_end}.</p>
                
                <p style='font-size: 14px; text-align: justify;'>I acknowledge the importance of addressing <?php echo $specific_concern; ?>. However, due to unforeseen circumstances <?php echo $reason_of_declining; ?>, I regret to inform you that I am unable to proceed with the appointment as planned.</p>
                
                <p style='font-size: 14px; text-align: justify;'>I apologize for any inconvenience this may cause. Nevertheless, I am fully committed to addressing your concerns and am eager to assist you. In light of this situation, I kindly request that we reschedule our appointment to a mutually convenient time.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Your cooperation and understanding in this matter are greatly appreciated. Should you have any questions, please do not hesitate to contact me.</p>
                
                <p style='font-size: 14px; text-align: justify;'>Sincerely,</p>
                
                <p style='font-size: 14px; text-align: justify;'>
                    {$studentRow['firstname']} {$studentRow['lastname']}<br>
                    Student
                </p>
            
                ";

                $pdf->writeHTML($pdfContent, true, false, false, false, '');

                // Email Configuration
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ccsorgs@gmail.com';
                $mail->Password = 'fzbhogfmmdnyrmip';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Set email content
                $mail->setFrom('ccsorgs@gmail.com', 'Pamantasan ng Cabuyao - College of Computing Studies');
                $mail->addAddress($studentRow["email"], $studentRow["lastname"] . ", " . $studentRow["firstname"]);
                $mail->Subject = 'Declined Appointment';
                $mail->Body = 'Dangal greetings, ' . $studentRow["firstname"] . " " . $studentRow["lastname"] . '! You have a new appointment. Please find the details in the attached PDF.';
                $mail->isHTML(true);
                $mail->addStringAttachment($pdf->Output('appointment_details.pdf', 'S'), 'appointment_details.pdf');

                // Send the email
                if ($mail->send()) {
                    echo '<script> alert("Declined the Appointment."); window.location.href = "../student-pending-appointment.php"; </script>';
                } else {
                    echo '<script> alert("Declined Appointment. Email Not Sent. Error: ' . $mail->ErrorInfo . '"); window.location.href = "../student-pending-appointment.php"; </script>';
                }
            } else {
                echo '<script> alert("Approved the Appointment Successfully"); window.location.href = "../student-pending-appointment.php"; </script>';
            }
        } else {
            echo '<script> alert("Failed to Update Remarks"); window.location.href = "../student-set-appointment.php"; </script>';
        }
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); </script>';
    }
}
?>
