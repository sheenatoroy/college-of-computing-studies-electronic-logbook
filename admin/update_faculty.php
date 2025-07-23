<?php
// update_faculty.php
include "../connection.php";

// Check if the required POST parameters are set
if (
    isset(
        $_POST['edit_username'],
        $_POST['edit_account_type'],
        $_POST['edit_firstname'],
        $_POST['edit_middlename'],
        $_POST['edit_lastname'],
        $_POST['edit_gender'],
        $_POST['edit_email'],
        $_POST['edit_address'],
        $_POST['edit_password'],
        $_POST['edit_day_availability'],
        $_POST['edit_time_start_availability'],
        $_POST['edit_time_end_availability']
    )
) {
    // Get the data from the POST variables and perform basic sanitization
    $username = mysqli_real_escape_string($conn, $_POST['edit_username']);
    $accountType = mysqli_real_escape_string($conn, $_POST['edit_account_type']);
    $firstname = mysqli_real_escape_string($conn, $_POST['edit_firstname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['edit_middlename']);
    $lastname = mysqli_real_escape_string($conn, $_POST['edit_lastname']);
    $gender = mysqli_real_escape_string($conn, $_POST['edit_gender']);
    $email = mysqli_real_escape_string($conn, $_POST['edit_email']);
    $address = mysqli_real_escape_string($conn, $_POST['edit_address']);
    $password = mysqli_real_escape_string($conn, $_POST['edit_password']);
    $dayAvailability = mysqli_real_escape_string($conn, $_POST['edit_day_availability']);
    $timeStartAvailability = mysqli_real_escape_string($conn, $_POST['edit_time_start_availability']);
    $timeEndAvailability = mysqli_real_escape_string($conn, $_POST['edit_time_end_availability']);

    // Perform the database update
    $sql = "UPDATE prof SET
        account_type = '$accountType',
        username = '$username',
        firstname = '$firstname',
        middlename = '$middlename',
        lastname = '$lastname',
        gender = '$gender',
        email = '$email',
        address = '$address',
        password = '$password',
        day_availability = '$dayAvailability',
        time_start_availability = '$timeStartAvailability',
        time_end_availability = '$timeEndAvailability'
        WHERE username = '$username'";

    $result = $conn->query($sql);

    if ($result === false) {
        // Handle query error
        die("Update failed: " . $conn->error);
    }

    // Send a JSON response indicating success
    echo json_encode(['success' => true]);
} else {
    // Handle missing parameters
    die("Missing required parameters");
}

$conn->close();
?>
