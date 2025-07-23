<?php
// Include your database connection code
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get professor ID from the AJAX request
    $profId = mysqli_real_escape_string($conn, $_POST['profId']);

    // Query to fetch professor's availability (replace with your query)
    $sql = "SELECT * FROM prof_availability WHERE prof_id = '$profId'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Process the fetched data and display it
        while ($row = mysqli_fetch_assoc($result)) {
            // Create DateTime objects for start and end times
            $startTime = new DateTime($row['time_start_availability']);
            $endTime = new DateTime($row['time_end_availability']);

            // Format the date
            $formattedDate = new DateTime($row['day_availability']);
            $formattedDate = $formattedDate->format('l, F j, Y');

            // Determine if it's morning or afternoon based on the start time
            $timeOfDay = ($startTime->format('H') < 12) ? 'Morning' : 'Afternoon';

            echo "<p>{$formattedDate} ({$timeOfDay}) {$startTime->format('g:i A')} - {$endTime->format('g:i A')}</p>"; // Adjust this line based on your availability data
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
