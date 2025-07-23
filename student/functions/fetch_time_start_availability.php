<?php

// fetch_time_start_availability.php
include_once("../../connection.php");

if (isset($_GET['prof_id_resched1']) && isset($_GET['edit_day'])) {
    $professorId = $_GET['prof_id_resched1'];
    $selectedDay = $_GET['edit_day'];

    $sql_availability = "SELECT time_start FROM prof_availability WHERE prof_id = '$professorId' AND day = '$selectedDay'";
    $result_availability = $conn->query($sql_availability);

    if ($result_availability->num_rows > 0) {
        $timeStart = array();
        while ($row = $result_availability->fetch_assoc()) {
            // Convert time to strtotime format
            $formattedTime = date('h:i A', strtotime($row['time_start']));
            $timeStart[] = $formattedTime;
        }

        echo json_encode(array('success' => true, 'timeStart' => $timeStart));
    } else {
        echo json_encode(array('success' => false, 'error' => $conn->error));
    }
} else {
    echo json_encode(array('success' => false, 'error' => 'Professor ID or selected day not set'));
}

$conn->close();

?>
