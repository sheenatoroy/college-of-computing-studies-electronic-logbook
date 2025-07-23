<?php
include "../connection.php";

// Check if the type and year parameters are set
if (isset($_GET['type']) && isset($_GET['year'])) {
    $type = $_GET['type'];
    $year = $_GET['year'];

    // Fetch data from the appointments table based on the type_of_concern and year
    if ($type === 'Unresolved' || $type === 'Done') {
        // If the type is Unresolved or Done, count appointments based on the remarks attribute
        $sql = "SELECT MONTH(app_day) AS month, COUNT(*) AS count FROM appointments WHERE remarks = ? AND YEAR(app_day) = ? GROUP BY MONTH(app_day)";
    } else {
        // For Advising and Consultation, count appointments based on the type_of_concern attribute
        $sql = "SELECT MONTH(app_day) AS month, COUNT(*) AS count FROM appointments WHERE type_of_concern = ? AND YEAR(app_day) = ? GROUP BY MONTH(app_day)";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $type, $year);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the data into an associative array
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Convert the data to JSON and echo it
    echo json_encode($data);
} else {
    // If the type or year parameter is not set, return an empty array
    echo json_encode(array());
}
?>
