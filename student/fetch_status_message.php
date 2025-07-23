<?php

include "../connection.php";

// Check if appointment_id is provided
if(isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    $sql = "SELECT a.remarks, a.student_id, a.prof_id, a.appointment_id, a.evaluation_status, s.firstname AS student_firstname, s.lastname AS student_lastname, p.firstname AS prof_firstname, p.lastname AS prof_lastname
    FROM appointments AS a
    JOIN student AS s ON a.student_id = s.username
    JOIN prof AS p ON a.prof_id = p.username
    WHERE a.appointment_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $remarks = $row['remarks'];
        $evaluation_status = $row['evaluation_status'];
        $fullname = $row['prof_firstname'] . ' ' . $row['prof_lastname'];
        $statusMessage = '';
        $evaluationMessage = '';
        

        // Set the correct statusMessage based on the remarks
        if ($remarks === "Pending") {
            $statusMessage = "Appointment Approval.";
        } elseif ($remarks === "Approved") {
            $statusMessage = "Your appointment is approved with <span style='color: green;'>$fullname</span>.";
        } elseif ($remarks === "Done") {
            $statusMessage = "Your appointment with <span style='color: green;'>$fullname</span> is already done.";
        }elseif ($evaluation_status === "Done"){
            $evaluationMessage = "You evaluation is already done.";
        } else {
            $statusMessage = "Unknown status";
        }

        // Output the statusMessage
        echo $statusMessage;
    } else {
        echo "Appointment not found";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>