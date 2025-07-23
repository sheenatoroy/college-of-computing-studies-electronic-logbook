<?php
// fetch_faculty_details.php
include "../connection.php";

// Perform database retrieval
$conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch faculty data from the database
$sql = "SELECT username AS username, firstname AS FROM prof"; // Replace 'faculty_table' with the actual name of your faculty table
$result = $conn->query($sql);

$data = array(); // Initialize an empty array to store the fetched data

// Check if there are rows returned from the query
if ($result->num_rows > 0) {
    // Loop through each row of the result set
    while ($row = $result->fetch_assoc()) {
        // Append each row to the data array
        $data[] = $row;
    }
}

// Close the database connection
$conn->close();

// Set the response header to JSON
header('Content-Type: application/json');

// Return the data as JSON
echo json_encode($data);
?>
