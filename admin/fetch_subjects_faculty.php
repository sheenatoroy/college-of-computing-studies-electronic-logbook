<?php
// fetch_subjects_faculty.php
include "../connection.php";

// Perform database retrieval
$conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT subject_id, subject_type AS subjectType, subject_code AS subjectCode, subject_name AS subjectName, units, day, start_time AS startTime, end_time AS endTime, status 
        FROM subj_management
        ORDER BY subject_code ASC";
$result = $conn->query($sql);

$data = array();

if ($result === false) {
    // Handle query error
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data in JSON format
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
