<?php
// update_subject.php
include "../connection.php";

// Check if the required POST parameters are set
if (isset($_POST['subjectId'], $_POST['subjectType'], $_POST['subjectCode'], $_POST['subjectName'], $_POST['units'], $_POST['startTime'], $_POST['endTime'], $_POST['day'])) {
    // Assuming your subject ID is sent as a POST parameter
    $subjectId = $_POST['subjectId'];

    // Get other edited values from the POST data and perform basic sanitization
    $subjectType = mysqli_real_escape_string($conn, $_POST['subjectType']);
    $subjectCode = mysqli_real_escape_string($conn, $_POST['subjectCode']);
    $subjectName = mysqli_real_escape_string($conn, $_POST['subjectName']);
    $units = mysqli_real_escape_string($conn, $_POST['units']);
    $startTime = mysqli_real_escape_string($conn, $_POST['startTime']);
    $endTime = mysqli_real_escape_string($conn, $_POST['endTime']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);

    // Perform database update
    $sql = "UPDATE subj_management SET
            subject_type = '$subjectType',
            subject_code = '$subjectCode',
            subject_name = '$subjectName',
            units = '$units',
            start_time = '$startTime',
            end_time = '$endTime',
            day = '$day'
            WHERE subject_id = '$subjectId'";

    $result = $conn->query($sql);

    if ($result === false) {
        // Handle query error
        die("Update failed: " . $conn->error);
    }

    // Send a JSON response indicating success
    echo json_encode(['success' => true, 'message' => 'Subject updated successfully']);

} else {
    // Handle missing parameters
    die("Missing required parameters");
}

$conn->close();
?>
