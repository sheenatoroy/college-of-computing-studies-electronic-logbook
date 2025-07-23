<?php
include_once("../../connection.php");

if (isset($_GET['edit_day'], $_GET['student_id'], $_GET['time_start'])) {
    $studentId = $_GET['student_id'];
    $day = $_GET['edit_day'];
    $time_start = $_GET['time_start'];

    // Prepared statement to fetch availability for the selected student
    $sql_time_start_availability = "SELECT DISTINCT time_start, time_end, day, remarks FROM appointments WHERE student_id = ? AND day = ?, AND time_start = ?";
    $stmt = $conn->prepare($sql_time_start_availability);
    $stmt->bind_param("sss", $studentId, $day, $time_start);
    $stmt->execute();
    $result_time_start_availability = $stmt->get_result();

    if ($result_time_start_availability->num_rows > 0) {
        while ($row = $result_time_start_availability->fetch_assoc()) {
            $remarks = $row['remarks'];
        }
        // Return the result as JSON
        echo json_encode(array('success' => true, 'timeStart' => $timeStart));
    } else {
        // Handle the case where no availability is found for the selected student
        echo json_encode(array('success' => false, 'error' => 'No availability found for the selected student'));
    }
    $stmt->close();

    if($remarks === "Pending"){
        echo json_encode(array('success' => true, 'error' => 'You cannot reschudle!'));
    }
} else {
    // Handle the case where student_id is not set in the request
    echo json_encode(array('success' => false, 'error' => 'Student ID not set'));
}

// Close the database connection if needed
$conn->close();
?>
