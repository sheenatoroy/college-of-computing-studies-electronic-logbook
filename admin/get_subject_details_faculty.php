<?php
// get_subjects_details_faculty.php
include "../connection.php";

// Include your database connection code here
// Replace these variables with your actual database connection details
$host = 'localhost';
$dbname = 'ccs_elogsystem';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Check if subjectId is provided
if (isset($_GET['subject_name'])) {
    $subjectName = $_GET['subject_name'];

    // Modify the query based on your actual database schema
    $query = "SELECT * FROM subj_management WHERE subject_name = :subject_name";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':subject_name', $subjectName, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch subject details as an associative array
    $subjectDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return subject details as JSON
    header('Content-Type: application/json');
    echo json_encode($subjectDetails);
} else {
    // Handle the case where subjectId is not provided
    echo json_encode(['error' => 'Subject ID not provided']);
}

?>
