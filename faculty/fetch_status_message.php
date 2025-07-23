<?php

include "../connection.php";

// Check if appointment_id is provided
if(isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    $sql = "SELECT a.remarks, a.student_id, a.prof_id, a.appointment_id, a.evaluation_status, s.firstname, s.lastname
    FROM appointments AS a
    JOIN student AS s ON a.student_id = s.username
    WHERE a.appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $remarks = $row['remarks'];
        $evaluationStatus = $row['evaluation_status'];
        $fullname = $row['firstname'] . ' ' . $row['lastname'];
        $statusMessage = '';

        // Set the correct statusMessage based on the remarks
        if ($remarks === "Pending") {
            $statusMessage = "Wait for approval of your appointment.";
        }elseif ($remarks === "Approved") {
            $statusMessage = "Your appointment is approved with <span style='color: green;'>$fullname</span>.";
            
        }elseif ($remarks === "Done") {
            $statusMessage = "<span style='color: green;'>Your appointment is already Done.</span>";
        }else {
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