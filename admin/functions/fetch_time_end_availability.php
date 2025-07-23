<?php
// Include your database connection code here
include_once("../../connection.php");

if (isset($_GET['edit_day'], $_GET['student_id'])) {
    $studentId = $_GET['student_id'];
    $day = $_GET['edit_day'];
    $sql_time_end_availability = "SELECT DISTINCT time_end FROM student_availability WHERE student_id = ? AND day=?";
    $stmt = $conn->prepare($sql_time_end_availability);
    $stmt->bind_param("ss", $studentId, $day);
    $stmt->execute();
    $result_time_end_availability = $stmt->get_result();

    if ($result_time_end_availability->num_rows > 0) {
        $timeEnd = array();  

        while ($row = $result_time_end_availability->fetch_assoc()) {
                $timeEnd[] = date('g:i A', strtotime($row['time_end']));
            }
            

    
        echo json_encode(array('success' => true, 'timeEnd' => $timeEnd));
    } else {

        echo json_encode(array('success' => false, 'error' => 'No availability found for the selected student'));
    }
} else {

    echo json_encode(array('success' => false, 'error' => 'Student ID not set'));
}


$conn->close();
?>
