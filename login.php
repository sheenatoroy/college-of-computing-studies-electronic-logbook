<!-- login.php -->
<?php

include("connection.php");
session_start();

$account_type = "";

// Set the maximum number of login attempts
$max_attempts = 3;

// Set the lockout time in seconds (1 minute in this example)
$lockout_time = 20;

function is_password_complex($password) {
    // Check if the password meets the complexity requirements
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    return $uppercase && $lowercase && $number && $specialChars && strlen($password) >= 8;
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (isset($_SESSION['last_failed_attempt'])) {
    $time_since_last_attempt = time() - $_SESSION['last_failed_attempt'];

    // Check if enough time has passed since the last failed attempt
    if ($time_since_last_attempt < $lockout_time) {
        $remaining_lockout_time = $lockout_time - $time_since_last_attempt;
        echo '<script> alert("Too many login attempts. Please try again in ' . $remaining_lockout_time . ' seconds.");
            window.location.href = "index.php"; </script>';
        exit;
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user has exceeded the maximum login attempts
    // if ($_SESSION['login_attempts'] >= $max_attempts) {
    //     echo '<script> alert("Maximum login attempts exceeded. Please try again later.");
    //         window.location.href = "index.php"; </script>';
    //     exit;
    // }

    // Query to check if the user exists in the database
    $student_query = "SELECT * FROM student WHERE username = ? LIMIT 1";
    $student_stmt = mysqli_prepare($conn, $student_query);
    mysqli_stmt_bind_param($student_stmt, 's', $username);
    mysqli_stmt_execute($student_stmt);
    $student_result = mysqli_stmt_get_result($student_stmt);

    $prof_query = "SELECT * FROM prof WHERE username = ? LIMIT 1";
    $prof_stmt = mysqli_prepare($conn, $prof_query);
    mysqli_stmt_bind_param($prof_stmt, 's', $username);
    mysqli_stmt_execute($prof_stmt);
    $prof_result = mysqli_stmt_get_result($prof_stmt);

    if (mysqli_num_rows($student_result) > 0) {
        $row = mysqli_fetch_assoc($student_result);
        $hashed_password = $row['password'];

        // Check if the entered password matches the hashed password
        if (password_verify($password, $hashed_password)) {
            $username = $row['username'];
            $account_type = $row['account_type'];

            // Set the session variables
            $_SESSION['username'] = $username;
            $_SESSION['account_type'] = $account_type;

            // Reset login attempts on successful login
            $_SESSION['login_attempts'] = 0;

            // Redirect to the appropriate dashboard based on the user type
            if ($account_type == 'student' || $account_type == 'Student') {
                echo '<script> alert("Successfully Login!");
                    window.location.href = "/student/student-dashboard.php"; </script>';
            } else {
                echo '<script> alert("User is not found!");
                    window.location.href = "index.php"; </script>';
            }
        } else {
            // Increment the login attempts counter
            $_SESSION['login_attempts']++;

            // Set the timestamp of the last failed attempt
            $_SESSION['last_failed_attempt'] = time();

            echo '<script> alert("Incorrect password!");
                window.location.href = "index.php"; </script>';
        }
    } elseif (mysqli_num_rows($prof_result) > 0) {
        $row = mysqli_fetch_assoc($prof_result);
        $hashed_password = $row['password'];

        // Check if the entered password matches the hashed password
        if (password_verify($password, $hashed_password)) {
            $account_type = $row['account_type'];

            // Set the session variables
            $_SESSION['username'] = $username;
            $_SESSION['account_type'] = $account_type;

            // Reset login attempts on successful login
            $_SESSION['login_attempts'] = 0;

            // Redirect to the appropriate dashboard based on the user type
            if ($account_type == 'admin' || $account_type == 'Admin') {
                echo '<script> alert("Successfully Login!");
                    window.location.href = "admin/admin-dashboard.php"; </script>';
            } elseif ($account_type == 'faculty' || $account_type == 'Faculty') {
                echo '<script> alert("Successfully Login!");
                    window.location.href = "/faculty/faculty-dashboard.php"; </script>';
            } else {
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;

                echo '<script> alert("User is not found!");
                    window.location.href = "index.php"; </script>';
            }
        } else {
            // Increment the login attempts counter
            $_SESSION['login_attempts']++;

            // Set the timestamp of the last failed attempt
            $_SESSION['last_failed_attempt'] = time();

            echo '<script> alert("Incorrect password!");
                window.location.href = "index.php"; </script>';
        }
    } else {
        // Increment the login attempts counter
        $_SESSION['login_attempts']++;

        // Set the timestamp of the last failed attempt
        $_SESSION['last_failed_attempt'] = time();

        echo '<script> alert("User is not found!");
            window.location.href = "index.php"; </script>';
    }
}

?>
