<?php
include_once("../../connection.php");

if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    // Prepared statement to fetch availability for the selected student
    $sql_day_availability = "SELECT DISTINCT day FROM student_availability WHERE student_id = ?";
    $stmt = $conn->prepare($sql_day_availability);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result_day_availability = $stmt->get_result();

    if ($result_day_availability->num_rows > 0) {
        $day = array();
        while ($row = $result_day_availability->fetch_assoc()) {
            $day[] = $row['day'];
        }
        // Return the result as JSON
        echo json_encode(array('success' => true, 'day' => $day));
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
