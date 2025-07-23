<?php
include_once("../../connection.php");

if (isset($_GET['edit_day'], $_GET['student_id'])) {
    $studentId = $_GET['student_id'];
    $day = $_GET['edit_day'];
    // Prepared statement to fetch availability for the selected student
    $sql_time_start_availability = "SELECT DISTINCT time_start FROM student_availability WHERE student_id = ? AND day=?";
    $stmt = $conn->prepare($sql_time_start_availability);
    $stmt->bind_param("ss", $studentId, $day);
    $stmt->execute();
    $result_time_start_availability = $stmt->get_result();

    if ($result_time_start_availability->num_rows > 0) {
        $timeStart = array();
        while ($row = $result_time_start_availability->fetch_assoc()) {
            $timeStart[] = date('g:i A', strtotime($row['time_start']));
            
        }
        // Return the result as JSON
        echo json_encode(array('success' => true, 'timeStart' => $timeStart));
    } else {
        // Handle the case where no availability is found for the selected student
        echo json_encode(array('success' => false, 'error' => 'No availability found for the selected student'));
    }
    $stmt->close();
} else {
    // Handle the case where student_id is not set in the request
    echo json_encode(array('success' => false, 'error' => 'Student ID not set'));
}

// Close the database connection if needed
$conn->close();
?>
