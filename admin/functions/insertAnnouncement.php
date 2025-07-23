<?php
include "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    if (isset($_POST['postAnnouncement'])) {
        // Validate input
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date = $_POST['date'];
        $time = $_POST['time'];

        if (empty($title) || empty($description)) {
            // Handle empty fields
            echo "Error: Title and description cannot be empty.";
            exit();
        }

        // Insert announcement into the database using a prepared statement
        $query_insert_announcement = "INSERT INTO announcement (title, date, time, description) VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert_announcement);

        // Check if the prepared statement was successful
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "ssss", $title, $date, $time, $description);
            mysqli_stmt_execute($stmt_insert);
            mysqli_stmt_close($stmt_insert);

            // Redirect to the page where the announcements are displayed
            header("Location: ../admin-announcement.php");
            exit();
        } else {
            // Handle the error if the prepared statement fails
            echo "Error: Unable to prepare statement.";
        }
    }
}
?>
