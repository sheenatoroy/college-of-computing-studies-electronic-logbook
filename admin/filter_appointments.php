<?php
session_start();
include "../connection.php";

$faculty = $_POST['faculty'];
$fromMonth = $_POST['fromMonth'];
$toMonth = $_POST['toMonth'];
$year = $_POST['year'];

// SQL query to fetch appointment data
$sql = "SELECT
            a.appointment_id,
            CONCAT(s.firstname, ' ', s.lastname) AS student_name,
            s.year_section AS student_year_section,
            CONCAT(p.firstname, ' ', p.lastname) AS professor_name,
            DATE_FORMAT(a.app_day, '%M %d, %Y') AS appointment_date,
            CONCAT(TIME_FORMAT(a.time_start, '%h:%i %p')) AS appointment_time,
            a.type_of_concern AS concern,
            DATEDIFF(a.app_day, CURDATE()) AS remaining_days,
            a.remarks,
            a.services_rendered,
            a.detailed_concern,
            a.action_report_textbox,
            a.total_hours
        FROM appointments a
        JOIN student s ON a.student_id = s.username
        JOIN prof p ON a.prof_id = p.username
        WHERE a.remarks IN ('Done', 'Unresolved')";

if (!empty($faculty)) {
    $sql .= " AND p.username = '$faculty'";
}

if (!empty($fromMonth) && !empty($toMonth)) {
    $sql .= " AND MONTH(a.app_day) BETWEEN '$fromMonth' AND '$toMonth'";
} elseif (!empty($fromMonth)) {
    $sql .= " AND MONTH(a.app_day) >= '$fromMonth'";
} elseif (!empty($toMonth)) {
    $sql .= " AND MONTH(a.app_day) <= '$toMonth'";
}

if (!empty($year)) {
    $sql .= " AND YEAR(a.app_day) = '$year'";
}

$sql .= " ORDER BY a.app_day ASC";

$result = mysqli_query($conn, $sql);

$appointments = [];
$monthlyData = array_fill(0, 12, ['doneAppointments' => 0, 'unresolvedAppointments' => 0]);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
        
        // Increment the monthly counts based on the remarks
        $monthIndex = date('n', strtotime($row['appointment_date'])) - 1; // Get month index (0-11)
        if ($row['remarks'] == 'Done') {
            $monthlyData[$monthIndex]['doneAppointments']++;
        } elseif ($row['remarks'] == 'Unresolved') {
            $monthlyData[$monthIndex]['unresolvedAppointments']++;
        }
    }
}

// Close the database connection
mysqli_close($conn);

// Response structure
$response = [
    'appointments' => $appointments,
    'monthlyData' => $monthlyData
];

echo json_encode($response);
?>
