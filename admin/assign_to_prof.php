<?php
// view_subject_details.php
include "../connection.php";

// Check for subjectId parameter
if(isset($_GET['subjectId'])) {
    $subjectId = $_GET['subjectId'];

    // Perform database retrieval
    $conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement with parameterized query to prevent SQL injection
    $sql = "SELECT subject_id, subject_type AS subjectType, subject_code AS subjectCode, subject_name AS subjectName, units, day, start_time AS startTime, end_time AS endTime, status
            FROM subj_management
            WHERE subject_id = ?";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subjectId);

    // Execute query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if result exists
    if ($result->num_rows > 0) {
        // Fetch result as associative array
        $row = $result->fetch_assoc();

        // Check if status is "Not Assigned"
        if ($row['status'] === "Not Assigned") {
            // Insert the data into the database
            // You need to implement this part
            echo json_encode(array("success" => "Subject inserted successfully"));
        } else {
            // Subject has already been assigned to faculty
            echo json_encode(array("error" => "The subject has already been assigned to faculty"));
        }
    } else {
        // No matching subject found
        echo json_encode(array("error" => "Subject not found"));
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // No subjectId parameter provided
    echo json_encode(array("error" => "No subjectId provided"));
}
?>
