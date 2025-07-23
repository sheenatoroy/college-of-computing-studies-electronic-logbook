<?php
session_start();

include "../connection.php";

$professor = $_GET['prof'];
$month = intval($_GET['month']);

if (!empty($professor)) {
    $last_name = explode(" ", $professor);
    $last_name = end($last_name);

    $sql = "SELECT
                a.appointment_id,
                a.student_id,
                a.prof_id,
                a.time_start,
                a.time_end,
                a.day,
                a.appointment_status,
                a.type_of_concern,
                a.specific_concern,
                a.detailed_concern,
                a.remarks,
                a.evaluation_status,
                a.action_report,
                a.resched_reason,
                a.appoint_by,
                a.app_day,
                a.action_report_path,
                a.detailed_concern,
                a.action_report_textbox,
                a.services_rendered,
                a.total_hours,

                s.firstname AS student_firstname,
                s.lastname AS student_lastname,
                s.middlename AS student_middlename,
                s.year_section AS student_year_section,
                s.email AS student_email,
                s.account_type AS account_type,

                p.firstname AS prof_firstname,
                p.lastname AS prof_lastname,
                p.middlename AS prof_middlename,
                p.account_type as account_type

                FROM appointments a
                JOIN student s ON a.student_id = s.username
                JOIN prof p ON a.prof_id = p.username
                WHERE remarks IN ('Done', 'Unresolved', 'Approved', 'Pending')
                AND p.lastname LIKE '%$last_name%'";

    if (!empty($month)) {
        $sql .= " AND MONTH(a.app_day) = $month";
    }

    $sql .= " ORDER BY a.app_day ASC;";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $appointments_with_remaining_days = array();

        while ($row = mysqli_fetch_assoc($result)) {
            // Convert date format to "F j, Y"
            $appointmentDate = strtotime($row["app_day"]);
            $formattedDate = date("F j, Y", $appointmentDate);
            $row["app_day"] = $formattedDate;

            $currentDate = strtotime(date("Y-m-d"));
            $remainingDays = floor(($appointmentDate - $currentDate) / (60 * 60 * 24));

            $row["remaining_days"] = $remainingDays;

            $appointments_with_remaining_days[] = $row;
        }

        $counts = array(
            "totalAppointments" => count($appointments_with_remaining_days),
            "advisingConcerns" => count(array_filter($appointments_with_remaining_days, fn($appointment) => $appointment['type_of_concern'] === 'Advising')),
            "consultationConcerns" => count(array_filter($appointments_with_remaining_days, fn($appointment) => $appointment['type_of_concern'] === 'Consultation')),
            "doneAppointments" => count(array_filter($appointments_with_remaining_days, fn($appointment) => $appointment['remarks'] === 'Done')),
            "unresolvedAppointments" => count(array_filter($appointments_with_remaining_days, fn($appointment) => $appointment['remarks'] === 'Unresolved'))
        );

        echo json_encode(array("appointments" => $appointments_with_remaining_days, "counts" => $counts));
    } else {
        echo json_encode(array("appointments" => [], "counts" => array(
            "totalAppointments" => 0,
            "advisingConcerns" => 0,
            "consultationConcerns" => 0,
            "doneAppointments" => 0,
            "unresolvedAppointments" => 0
        )));
    }
} else {
    echo json_encode(array("appointments" => [], "counts" => array(
        "totalAppointments" => 0,
        "advisingConcerns" => 0,
        "consultationConcerns" => 0,
        "doneAppointments" => 0,
        "unresolvedAppointments" => 0
    )));
}

mysqli_close($conn);
?>
