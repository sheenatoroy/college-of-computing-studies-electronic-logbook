<?php
// fetch_faculty.php
include "../connection.php";

// Perform database retrieval
$conn = new mysqli("localhost", "root", "", "ccs_elogsystem");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username AS username, firstname AS firstname, lastname AS lastname, middlename AS middlename, email AS email, contact_number AS contact_number, gender AS gender, address AS address, account_type as account_type, status 
        FROM prof
        ORDER BY username ASC";
$result = $conn->query($sql);

$data = array();

if ($result === false) {
    // Handle query error
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data in JSON format
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
