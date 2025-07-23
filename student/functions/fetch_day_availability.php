<?php
include_once("../../connection.php");

if (isset($_GET['prof_id_resched1'])) {
    $prof_id = $_GET['prof_id_resched1'];

    $sql_day_availability = "SELECT day FROM prof_availability WHERE prof_id = '$prof_id'";
    $result_day_availability = $conn->query($sql_day_availability);

    if ($result_day_availability) {
        $day_availability = array();
        while ($row = $result_day_availability->fetch_assoc()) {
            $day_availability[] = $row['day'];
        }

        echo json_encode(array('success' => true, 'day' => $day_availability));
    } else {
        echo json_encode(array('success' => false, 'error' => $conn->error));
    }
} else {
    echo json_encode(array('success' => false, 'error' => 'Professor ID not set'));
}

$conn->close();
?>
