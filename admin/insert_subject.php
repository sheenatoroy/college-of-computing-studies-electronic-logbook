<?php
// insert_subject.php
include "../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the POST request
    $subjectId = $_POST['subjectId']; // Check if subjectId is set

    $subjectType = $_POST['subjectType'];
    $subjectCode = $_POST['subjectCode'];
    $subjectName = $_POST['subjectName'];
    $units = $_POST['units'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $day = $_POST['day'];

    // Perform database insertion or update
    $conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use $subjectId to determine whether to insert or update the record
    if (empty($subjectId)) {
        // Insert new record
        $sql = "INSERT INTO subj_management (subject_type, subject_code, subject_name, units, start_time, end_time, day) 
                VALUES ('$subjectType', '$subjectCode', '$subjectName', '$units', '$startTime', '$endTime', '$day')";
    } else {
        // Update existing record
        $sql = "UPDATE subj_management 
                SET subject_type = '$subjectType', subject_code = '$subjectCode', subject_name = '$subjectName', 
                    units = '$units', start_time = '$startTime', end_time = '$endTime', day = '$day'
                WHERE subject_id = $subjectId";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Record inserted/updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
