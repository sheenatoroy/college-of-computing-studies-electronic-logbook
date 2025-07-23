<?php
include "../../connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $profId = $_POST['profId'];

    // Make sure to use the correct column names in your SQL query
    $sql = "SELECT day, time_start, time_end FROM prof_availability WHERE prof_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $profId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if fields exist in the fetched row
            if (isset($row['time_start']) && isset($row['time_end']) && isset($row['day'])) {
                $startTime = new DateTime($row['time_start']);
                $endTime = new DateTime($row['time_end']);

                $formattedDate = new DateTime($row['day']);
                $formattedDate = $formattedDate->format('l, F j, Y');

                $timeOfDay = ($startTime->format('H') < 12) ? 'Morning' : 'Afternoon';

                echo "<p>{$formattedDate} ({$timeOfDay}) {$startTime->format('g:i A')} - {$endTime->format('g:i A')}</p>";
            } else {
                echo "<p>Availability data is incomplete for one or more records.</p>";
            }
        }
    } else {
        echo "No availability found.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "Invalid request.";
}
?>
