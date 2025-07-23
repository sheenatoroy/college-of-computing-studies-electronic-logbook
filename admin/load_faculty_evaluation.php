<?php
include "../connection.php";

// Select the desired columns from the prof_eval table
$query = "SELECT 
    punctuality, 
    communication_skills, 
    constructive_dialogue, 
    satisfactory_resolution, 
    professionalism 
FROM prof_eval";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Initialize an array to store the total for each question
    $totals = array_fill(1, 5, 0);
    $count = 0; // Initialize a counter for the number of records

    // Fetch the data as an associative array
    while ($row = mysqli_fetch_assoc($result)) {
        // Iterate over each question and add the value to the total
        for ($i = 1; $i <= 5; $i++) {
            $totals[$i] += $row[getQuestionColumnName($i)];
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

// Helper function to get the column name for a question
function getQuestionColumnName($questionNumber) {
    switch ($questionNumber) {
        case 1:
            return "punctuality";
        case 2:
            return "communication_skills";
        case 3:
            return "constructive_dialogue";
        case 4:
            return "satisfactory_resolution";
        case 5:
            return "professionalism";
        default:
            return "question_" . $questionNumber;
    }
}
?>