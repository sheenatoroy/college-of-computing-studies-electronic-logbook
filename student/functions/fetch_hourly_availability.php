<?php

// fetch_hourly_availability.php
include_once("../../connection.php");

if (isset($_GET['prof_id_resched1']) && isset($_GET['edit_day'])) {
    $professorId = $_GET['prof_id_resched1'];
    $selectedDay = $_GET['edit_day'];

    // Fetch time start and end
    $sql_availability = "SELECT time_start, time_end FROM prof_availability WHERE prof_id = '$professorId' AND day = '$selectedDay'";
    $result_availability = $conn->query($sql_availability);

    if ($result_availability->num_rows > 0) {
        $row = $result_availability->fetch_assoc();
        $timeStart = strtotime($row['time_start']);
        $timeEnd = strtotime($row['time_end']);

        // Fetch occupied or pending time slots
        $sql_appointments = "SELECT time_start, day FROM appointments WHERE prof_id = '$professorId' AND remarks = 'Pending'";
        $result_appointments = $conn->query($sql_appointments);

        $occupiedTimeSlots = array();
        if ($result_appointments->num_rows > 0) {
            while ($row_appointment = $result_appointments->fetch_assoc()) {
                $occupiedTimeSlots[] = $row_appointment['time_start'];
            }
        }

        // Generate hourly time slots, excluding occupied or pending slots
        $timeSlots = array();
        while ($timeStart < $timeEnd) {
            $formattedTimeSlot = date('h:i A', $timeStart);
            if (!in_array($formattedTimeSlot, $occupiedTimeSlots)) {
                $timeSlots[] = $formattedTimeSlot;
            }
            $timeStart = strtotime('+1 hour', $timeStart);
        }

        echo json_encode(array('success' => true, 'timeSlots' => $timeSlots));
    } else {
        echo json_encode(array('success' => false, 'error' => 'No availability found'));
    }
} else {
    echo json_encode(array('success' => false, 'error' => 'Professor ID or selected day not set'));
}

$conn->close();

?>
