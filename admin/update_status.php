<?php
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are present in POST data
    if (isset($_POST["subjectId"]) && isset($_POST["selectedProf"]) && isset($_POST["selectedYear"]) && isset($_POST["selectedCourse"]) && isset($_POST["selectedSection"])) {
        // Get subjectId, selectedProf, selectedYear, selectedCourse, and selectedSection from POST data
        $subjectId = $_POST["subjectId"];
        $selectedProf = $_POST["selectedProf"];
        $selectedYear = $_POST["selectedYear"];
        $selectedCourse = $_POST["selectedCourse"];
        $selectedSection = $_POST["selectedSection"];

        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch the professor name associated with the selected prof_id
        $sqlFetchProfName = "SELECT CONCAT(firstname, ' ', middlename, ' ', lastname) AS full_name FROM prof WHERE username = '$selectedProf'";
        $result = $conn->query($sqlFetchProfName);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $profName = $row["full_name"];
        } else {
            $profName = ""; // Handle case where professor name is not found
        }

        // Concatenate year, course, and section
        $yearCourseSection = $selectedYear . $selectedCourse . $selectedSection;

        // Update the prof_name and year_course_section in the database
        $sql = "UPDATE subj_management SET status = '$profName', status = '$selectedProf', year_section = '$yearCourseSection' WHERE subject_id = $subjectId";

        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            // Handle errors
            echo "Error updating record: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Missing parameters";
    }
} else {
    echo "Invalid request";
}
?>