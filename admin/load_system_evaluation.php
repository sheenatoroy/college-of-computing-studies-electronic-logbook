<?php
include "../connection.php";

// Select the desired columns from the ratings table
$query = "SELECT question_1, question_2, question_3, question_4, question_5, question_6, question_7, question_8 FROM ratings";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Initialize an array to store the total for each question
    $totals = array_fill(1, 8, 0);
    $count = 0; // Initialize a counter for the number of records

    // Fetch the data as an associative array
    while ($row = mysqli_fetch_assoc($result)) {
        // Iterate over each question and add the value to the total
        for ($i = 1; $i <= 8; $i++) {
            $totals[$i] += $row["question_$i"];
        }

        $count++; // Increment the counter for each record
    }

    // Calculate the averages
    $averages = array_map(function ($total) use ($count) {
        return $total / $count;
    }, $totals);

    // Return the totals and averages as JSON
    echo json_encode(['totals' => $totals, 'averages' => $averages]);
} else {
    // Handle the case where the query fails
    echo json_encode(['error' => mysqli_error($conn)]);
}

// Close the database connection
mysqli_close($conn);
?>