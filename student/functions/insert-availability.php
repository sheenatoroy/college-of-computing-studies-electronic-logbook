<?php

include "../../connection.php";

if (isset($_POST['addBtn'])) {
    
   
    $student_id = $_POST['student_id'];
    $day = $_POST['day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
   

    // Check if the student_id exists in the student table
    $check_query = "SELECT * FROM student WHERE username = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        
        $query = "INSERT INTO student_availability (`student_id`, `day`, `time_start`, `time_end`) 
        VALUES ('$student_id', '$day', '$time_start', '$time_end')";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            echo '<script> alert("Availability Saved"); window.location.href = "../student-dashboard.php"; </script>';
        } else {
            echo '<script> alert("Availability Not Saved"); </script>';
        }
        
    } else {
        // Student_id does not exist in the student table
        echo '<script> alert("Invalid Student ID"); window.location.href = "../student-dashboard.php"; </script>';
    }
}

?>