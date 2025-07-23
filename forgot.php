<?php
session_start();
$error = array();

require "./mail.php";

if (!$con = mysqli_connect("localhost", "root", "", "ccs_elogsystem")) {
    die("could not connect");
}

$mode = "enter_email";
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

// something is posted
if (count($_POST) > 0) {

    switch ($mode) {
        case 'enter_email':
            // code...
            $email = $_POST['email'];
            // validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error[] = "Please enter a valid email";
            } elseif (!valid_email($email)) {
                $error[] = "That email was not found";
            } else {

                $_SESSION['forgot']['email'] = $email;
                send_email($email);
                header("Location: forgot.php?mode=enter_code");
                die;
            }
            break;

        case 'enter_code':
            // code...
            $code = $_POST['code'];
            $result = is_code_correct($code);

            if ($result == "the code is correct") {

                $_SESSION['forgot']['code'] = $code;
                header("Location: forgot.php?mode=enter_password");
                die;
            } elseif ($result == "the code is incorrect") {
                $error[] = "The code is incorrect";
            } elseif ($result == "the code is expired") {
                $error[] = "The code is expired";
            } else {
                $error[] = "Invalid code";
            }
            break;

        case 'enter_password':
            // code...
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if ($password !== $password2) {
                $error[] = "Passwords do not match";
            } elseif (!isset($_SESSION['forgot']['email']) || !isset($_SESSION['forgot']['code'])) {
                header("Location: forgot.php");
                die;
            } elseif (!is_password_complex($password)) {
                $error[] = "Password does not meet complexity requirements";
            } else {

                save_password($password);
                if (isset($_SESSION['forgot'])) {
                    unset($_SESSION['forgot']);
                }

                header("Location: index.php");
                die;
            }
            break;

        default:
            // code...
            break;
    }
}

function send_email($email)
{
    global $con;

    $expire = time() + (60 * 1);
    $code = rand(10000, 99999);
    $email = addslashes($email);

    // Check if an entry with the same email already exists
    $query_check = "SELECT * FROM codes WHERE email = '$email' LIMIT 1";
    $result_check = mysqli_query($con, $query_check);

    if ($result_check && mysqli_num_rows($result_check) > 0) {
        // Update the existing entry
        $query_update = "UPDATE codes SET code = '$code', expire = '$expire' WHERE email = '$email' LIMIT 1";
        mysqli_query($con, $query_update);
    } else {
        // Insert a new entry
        $query_insert = "INSERT INTO codes (email, code, expire) VALUES ('$email', '$code', '$expire')";
        mysqli_query($con, $query_insert);
    }

    // Send email here
    send_mail($email, 'Password reset', "Your code is " . $code);
}

function save_password($password)
{
    global $con;

    $password = password_hash($password, PASSWORD_DEFAULT);
    $email = addslashes($_SESSION['forgot']['email']);
    $account_type = get_account_type($email); // Assuming you have a function to get the account type

    if ($account_type == 'student') {
        $query = "UPDATE student SET password = '$password' WHERE email = '$email' LIMIT 1";
    } elseif ($account_type == 'prof') {
        $query = "UPDATE prof SET password = '$password' WHERE email = '$email' LIMIT 1";
    } else {
        // Handle other account types or provide an error message
        return false;
    }

    mysqli_query($con, $query);
}

// Function to get the account type
function get_account_type($email)
{
    global $con;

    // Check in the student table
    $query_student = "SELECT 'student' as account_type FROM student WHERE email = '$email' LIMIT 1";
    $result_student = mysqli_query($con, $query_student);

    if ($row_student = mysqli_fetch_assoc($result_student)) {
        return $row_student['account_type'];
    }

    // Check in the prof table
    $query_prof = "SELECT 'prof' as account_type FROM prof WHERE email = '$email' LIMIT 1";
    $result_prof = mysqli_query($con, $query_prof);

    if ($row_prof = mysqli_fetch_assoc($result_prof)) {
        return $row_prof['account_type'];
    }

    return false; // Return false if account type is not found in both tables
}

function valid_email($email)
{
    global $con;

    $email = addslashes($email);

    // Check in the "prof" table
    $queryProf = "SELECT * FROM prof WHERE email = '$email' LIMIT 1";
    $resultProf = mysqli_query($con, $queryProf);

    if ($resultProf && mysqli_num_rows($resultProf) > 0) {
        return true;
    }

    // Check in the "student" table
    $queryStudent = "SELECT * FROM student WHERE email = '$email' LIMIT 1";
    $resultStudent = mysqli_query($con, $queryStudent);

    if ($resultStudent && mysqli_num_rows($resultStudent) > 0) {
        return true;
    }

    return false;
}

function is_code_correct($code)
{
    global $con;

    $code = addslashes($code);
    $expire = time();
    $email = addslashes($_SESSION['forgot']['email']);

    $query = "select * from codes where code = '$code' && email = '$email' order by id desc limit 1";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['expire'] > $expire) {
                return "the code is correct";
            } else {
                return "the code is expired";
            }
        } else {
            return "the code is incorrect";
        }
    }

    return "the code is incorrect";
}

function is_password_complex($password)
{
    // Check if the password meets the complexity requirements
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    return $uppercase && $lowercase && $number && $specialChars && strlen($password) >= 8;
}
?>




<!--dfdfd-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--Bootstrap 5 CSS CDN-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--Website Logo-->
    <link rel="icon" href="/assets/img/ccs-logo.png">
    <!--Boxicons-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <title>CCS: E-LOG | Login</title>
    <!-- Custom styles for this template -->
    <link href="/assets/css/index.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('floatingPassword');
            const passwordFeedback = document.getElementById('password-feedback');

            passwordInput.addEventListener('input', function () {
                const password = passwordInput.value;

                // Your password requirements
                const requirements = [
                    { regex: /[0-9]/, message: 'Must include at least one digit' },
                    { regex: /[a-z]/, message: 'Must include at least one lowercase letter' },
                    { regex: /[A-Z]/, message: 'Must include at least one uppercase letter' },
                    { regex: /[!@#$%^&*(),.?":{}|<>]/, message: 'Must include at least one special character' },
                    { regex: /.{8,}/, message: 'Must be at least 8 characters long' }
                ];

                let feedbackMessage = '';

                for (const requirement of requirements) {
                    if (!requirement.regex.test(password)) {
                        feedbackMessage += requirement.message + '<br>';
                    }
                }

                passwordFeedback.innerHTML = feedbackMessage === '' ? 'Password meets requirements' : feedbackMessage;
            });
        });
    </script>
</head>
<body>

<?php 

switch ($mode) {
    case 'enter_email':
        // code...
        ?>
        <div class="container-fluid d-flex justify-content-center align-items-center vh-100" style="background: rgb(236,161,107);
            background: radial-gradient(circle, rgba(236,161,107,1) 0%, rgba(249,106,3,1) 50%, rgba(236,161,107,1) 100%);">
            <div class="shadow-sm p-3 bg-body rounded w-100 w-sm-100 w-md-75 w-lg-50">
                <main class="form-signin">
                    <form autocomplete="off" method="post" action="forgot.php?mode=enter_email"> 
                        <img class="w-100" src="/assets/img/ccs-header-textonly.png" style="margin-bottom: -50px;">
                        <img class="w-100" src="/assets/img/ccs-elog-logo.png" style="margin-bottom: -50px;">
                     
                        <div class="alert alert-secondary mt-4 pb-1" style="background-color: #ff964b;">
                            <h6 class="text-center">
                                <b class="text-light">Forgot Password</b>
                            </h6>
                        </div>

                        <!--for error-->
                        <span style="font-size: 12px;color:red;">
                        <?php 
                            foreach ($error as $err) {
                                // code...
                                echo $err . "<br>";
                            }
                        ?>
                        </span>

                        <div class="form-floating m-2 rounded">
                            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email" required>
                            <label for="floatingInput">Email</label>
                        </div>

                        <br style="clear: both;">
                        <button class="w-100 btn btn-primary text-light rounded-pill" type="submit" value="Next">Next</button>
                        <br>
                        <button class="w-100 btn btn-secondary text-light rounded-pill mt-2" type="button" onclick="window.location.href='./index.php'">Login</button>
                    </form>
                </main>
            </div>
        </div>
        <?php                
        break;

    case 'enter_code':
        // code...
        ?>
        <div class="container-fluid d-flex justify-content-center align-items-center vh-100" style="background: rgb(236,161,107);
            background: radial-gradient(circle, rgba(236,161,107,1) 0%, rgba(249,106,3,1) 50%, rgba(236,161,107,1) 100%);">
            <div class="shadow-sm p-3 bg-body rounded w-100 w-sm-100 w-md-75 w-lg-50">
                <main class="form-signin">
                    <form method="post" action="forgot.php?mode=enter_code"> 
                        <img class="w-100" src="/assets/img/ccs-header-textonly.png" style="margin-bottom: -50px;">
                        <img class="w-100" src="/assets/img/ccs-elog-logo.png" style="margin-bottom: -50px;">
                     
                        <div class="alert alert-secondary mt-4 pb-1" style="background-color: #ff964b;">
                            <h6 class="text-center">
                                <b class="text-light">Forgot Password</b>
                            </h6>
                        </div>
                        <h6 class="text-center">Enter the code sent to your email</h6>                        
                    
                    <span style="font-size: 12px;color:red;">
                    <?php 
                        foreach ($error as $err) {
                            // code...
                            echo $err . "<br>";
                        }
                    ?>
                    </span>

                    <div class="form-floating m-2 rounded">
                            <input type="text" class="form-control" id="floatingInput" name="code" placeholder="12345" >
                            <label for="floatingInput">Code</label>
                    </div>
                    
                    <br style="clear: both;">
                    <button class="w-50 btn btn-success text-light rounded-pill ml-2" type="submit" value="Next" style="float: right; ">Next</button>
                    <button class="w-49 btn btn-primary text-light rounded-pill mr-2" type="button" onclick="window.location.href='./forgot.php'" style="float: left;">Get code</button>
                    <br><br>
                    <button class="w-100 btn btn-secondary text-light rounded-pill mt-2" type="button" onclick="window.location.href='./index.php'">Back to Login</button>
                </form>
                </main>
            </div>
        </div>
        <?php
        break;

    case 'enter_password':
        // code...
        ?>

        <div class="container-fluid d-flex justify-content-center align-items-center vh-100" style="background: rgb(236,161,107);
            background: radial-gradient(circle, rgba(236,161,107,1) 0%, rgba(249,106,3,1) 50%, rgba(236,161,107,1) 100%);">
            <div class="shadow-sm p-3 bg-body rounded w-100 w-sm-100 w-md-75 w-lg-50">
                <main class="form-signin">
                    <form method="post" action="forgot.php?mode=enter_password"> 
                        <img class="w-100" src="/assets/img/ccs-header-textonly.png" style="margin-bottom: -50px;">
                        <img class="w-100" src="/assets/img/ccs-elog-logo.png" style="margin-bottom: -50px;">
                     
                        <div class="alert alert-secondary mt-4 pb-1" style="background-color: #ff964b;">
                            <h6 class="text-center">
                                <b class="text-light">Forgot Password</b>
                            </h6>
                        </div>
                        <h6 class="text-center">Enter your new password</h6>                
                    
                    <span style="font-size: 12px;color:red;">
                    <?php 
                        foreach ($error as $err) {
                            // code...
                            echo $err . "<br>";
                        }
                    ?>
                    </span>

                    <div class="form-floating m-2 rounded">
                            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                            <div id="password-feedback" style="font-size: 12px;"></div>
                    </div>

                    <div class="form-floating m-2 rounded">
                            <input type="password" class="form-control" id="floatingPassword" name="password2" placeholder="Retype Password" required>
                            <label for="floatingPassword">Confirm Password</label>
                    </div>
                    
                    <br style="clear: both;">
                    <button class="w-50 btn btn-success text-light rounded-pill ml-2" type="submit" value="Next" style="float: right; ">Next</button>
                    <button class="w-49 btn btn-primary text-light rounded-pill mr-2" type="button" onclick="window.location.href='./forgot.php'" style="float: left;">Start Over</button>
        
                    <br><br>
                    <button class="w-100 btn btn-secondary text-light rounded-pill mt-2" type="button" onclick="window.location.href='./index.php'">Back to Login</button>
                </form>
                </main>
            </div>
        </div>
        <?php
        break;
    
    default:
        // code...
        break;
}

?>

</body>
</html>
