<?php

include "../../connection.php";

if (isset($_POST['submitFeedback'])) {
    
    $appointment_id = $_POST['appointment_id'];
    $question_1 = $_POST['question_1'];
    $question_2 = $_POST['question_2'];
    $question_3 = $_POST['question_3'];
    $question_4 = $_POST['question_4'];
    $question_5 = $_POST['question_5'];
    $question_6 = $_POST['question_6'];
    $question_7 = $_POST['question_7'];
    $question_8 = $_POST['question_8'];
    $question_9 = $_POST['question_9'];

    // Insert feedback into ratings table
    $query = "INSERT INTO ratings (`appointment_id`, `question_1`, `question_2`, `question_3`, `question_4`, `question_5`, `question_6`, `question_7`, `question_8`, `question_9`) 
        VALUES ('$appointment_id', '$question_1', '$question_2', '$question_3', '$question_4', '$question_5', '$question_6', '$question_7', '$question_8', '$question_9')";
    $query_run = mysqli_query($conn, $query);

    // Update evaluation_status in appointments table to "Done"
    $update_query = "UPDATE appointments SET evaluation_status = 'Done' WHERE appointment_id = '$appointment_id'";
    $update_query_run = mysqli_query($conn, $update_query);

    if ($query_run && $update_query_run) {
        echo '<script> alert("Evaluation Done!"); window.location.href = "../admin-feedback.php"; </script>';
    } else {
        echo '<script> alert("Evaluation Failed!"); </script>';
    }
}

?>
