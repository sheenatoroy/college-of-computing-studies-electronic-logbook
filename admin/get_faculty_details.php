<?php
// get_faculty_details.php
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

// Check if username is provided
if (isset($_GET['username'])) {
    $user_name = $_GET['username'];

    // Modify the query based on your actual database schema
    $query = "SELECT * FROM prof WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $user_name, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch subject details as an associative array
    $facultyDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return subject details as JSON
    header('Content-Type: application/json');
    echo json_encode($facultyDetails);
} else {
    // Handle the case where username is not provided
    echo json_encode(['error' => 'Faculty ID not provided']);
}

?>
