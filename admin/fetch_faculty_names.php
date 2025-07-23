<?php
include "../connection.php";

// Connect to the database
$conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute SQL command to fetch faculty names
$sql = "SELECT DISTINCT p.username, CONCAT(p.firstname, ' ', p.middlename, ' ', p.lastname) AS full_name
        FROM prof_availability pa 
        JOIN prof p 
        ON pa.prof_id = p.username";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check if query was successful
if (!$result) {
    // Handle error if query fails
    echo json_encode(array("error" => "SQL Error: " . mysqli_error($conn)));
    exit;
}

// Initialize an array to store faculty names
$facultyNames = array();

// Fetch data from the result set
while ($row = mysqli_fetch_assoc($result)) {
    $facultyNames[] = $row;
}

// Convert the array to JSON format
echo json_encode($facultyNames);

// No need to close the connection here
?>