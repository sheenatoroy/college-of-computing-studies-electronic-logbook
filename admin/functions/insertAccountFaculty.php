<?php
// insertAccountFaculty.php
include "../../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data

    // File upload configuration
    $file = $_FILES['image']['name'];
    $target_dir = "../../assets/img/";
    $target_dir2 = "../assets/img/" . $file;
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    $username = $_POST['username'];
    $account_type = $_POST['account_type'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Output entered password to the browser console
    echo "<script>console.log('Entered Password: " . $password . "');</script>";

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // File upload configuration
    // Get the file extension
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file extensions
    $allowed_extensions = array('jpg', 'jpeg', 'png');

    // Check if the file extension is allowed
    if (!in_array($file_extension, $allowed_extensions)) { 
        // File extension is not allowed
        echo "Only JPG, JPEG, and PNG files are allowed.";
    } else {
        // File extension is allowed, proceed with the upload
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            //echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])). " has been uploaded.";

            // Perform database insertion or update
            $conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // First insertion into 'prof' table
            $sql_insert = "INSERT INTO `prof`(`username`, `firstname`, `lastname`, `middlename`, `email`, `contact_number`, `gender`, `address`, `account_type`, `password`, `itempic`, `itemlocation`)
            VALUES('$username', '$firstname', '$lastname', '$middlename', '$email', '$contact_number', '$gender', '$address', '$account_type', '$hashed_password', '$target_file', '$target_dir2') ";

            $query = mysqli_query($conn, $sql_insert);

            if ($query) {
                // File upload successful, now move the uploaded file
                // No need to move the file again here

                // Professor availability insertion
                $day = $_POST['day'];
                $time_start_availability = $_POST['time_start_availability'];
                $time_end_availability = $_POST['time_end_availability'];

                // Check if the professor ID exists in the 'prof' table
                $check_query = "SELECT * FROM prof WHERE username = '$username'";
                $result = mysqli_query($conn, $check_query);

                if (mysqli_num_rows($result) > 0) {
                    // Professor ID exists, proceed with availability insertion
                    $query_availability = "INSERT INTO prof_availability (`prof_id`, `day`, `time_start`, `time_end`) 
                    VALUES ('$username', '$day', '$time_start_availability', '$time_end_availability')";
                    $query_run_availability = mysqli_query($conn, $query_availability);

                    if ($query_run_availability) {
                        echo "<script>
                                alert('Account Added, Availability Saved!');
                                window.location.href='../admin-account-tables.php?table=tableFaculty';
                                </script>";
                    } else {
                        // Availability insertion failed
                        echo "<script>
                                alert('Availability Insertion Failed!');
                                window.location.href='../admin-account-tables.php?table=tableFaculty';
                                </script>";
                    }
                } else {
                    // Professor ID does not exist in the 'prof' table
                    echo "<script>
                            alert('Invalid Professor ID');
                            window.location.href='../admin-account-management.php';
                            </script>";
                }
            } else {
                // First insertion failed
                echo "<script>
                        alert('Account Insertion Failed!');
                        window.location.href='../admin-account-management.php';
                        </script>";
            }
        } else {
            // File upload failed
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
