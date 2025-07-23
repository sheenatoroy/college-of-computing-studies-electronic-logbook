<?php
// Include your database connection file
include_once '../../connection.php';

if(isset($_POST['appointment_id_view']) && isset($_POST['student_id_view'])){
    $appointment_id = $_POST['appointment_id_view'];
    $student_id = $_POST['student_id_view'];

    $fetch_details = "SELECT
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

                        s.firstname AS student_firstname,
                        s.lastname AS student_lastname,
                        s.middlename AS student_middlename,
                        s.year_section AS student_year_section,
                        s.email AS student_email,

                        p.firstname AS prof_firstname,
                        p.lastname AS prof_lastname,
                        p.middlename AS prof_middlename

                        FROM appointments a
                        JOIN student s ON a.student_id = s.username
                        JOIN prof p ON a.prof_id = p.username
                        WHERE a.appointment_id = '$appointment_id' AND s.username = '$student_id'";

    $fetch_details_query = mysqli_query($conn, $fetch_details);

    // Check if query executed successfully
    if($fetch_details_query) {
        // Fetch data
        $appointment_data = mysqli_fetch_assoc($fetch_details_query);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($appointment_data);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Error: Required parameters are missing.";
}

// Close connection
mysqli_close($conn);
?>
