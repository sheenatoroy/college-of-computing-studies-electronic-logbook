<?php
// fetch_subjects.php
include "../connection.php";

// Perform database retrieval
$conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT sm.subject_id, sm.subject_type AS subjectType, sm.subject_code AS subjectCode, sm.subject_name AS subjectName, sm.units, sm.day, sm.start_time AS startTime, sm.end_time AS endTime, 
        COALESCE(CONCAT(p.firstname, ' ', p.middlename, ' ', p.lastname), 'Not Assigned') AS status
        FROM subj_management sm
        LEFT JOIN prof p ON sm.status = p.username
        ORDER BY CAST(sm.subject_id AS UNSIGNED) ASC";

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
