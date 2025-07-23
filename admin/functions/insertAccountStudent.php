<!-- insertAccountStudent.php -->
<?php
include "../../connection.php";

if (isset($_POST['save_account'])) {
    // Retrieve form data
    $account_type = $_POST['account_type'];
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $year_section = $_POST['year_section'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into 'student' table
    $sql_insert = "INSERT INTO `student`(`username`, `firstname`, `lastname`, `middlename`, `year_section`, `email`, `contact_number`, `gender`, `address`, `account_type`, `password`)
    VALUES('$username', '$firstname', '$lastname', '$middlename', '$year_section','$email', '$contact_number', '$gender', '$address', '$account_type', '$hashed_password') ";

    $query = mysqli_query($conn, $sql_insert);

    if ($query) {
        // Insertion successful, now you can handle the file upload or any other logic if needed
        echo "<script>
                alert('Account Successfully Added');
                window.location.href='../admin-account-tables.php?table=tableStudent';
                </script>";
    } else {
        // Insertion failed
        echo "<script>
                alert('Account Insertion Failed!');
                window.location.href='../admin-account-tables.php?table=tableStudent';
                </script>";
    }
}

session_abort();

?>
