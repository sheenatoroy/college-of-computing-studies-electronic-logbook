<?php
session_start(); // Add this line to start the session
include "../connection.php";

$selectedMonth = $_GET['month'] ?? date('n'); // Default to the current month
$selectedYear = $_GET['year'] ?? date('Y'); // Default to the current year
$username = $_SESSION['username'];

$sql = "SELECT * FROM appointments WHERE MONTH(app_day) = $selectedMonth AND YEAR(app_day) = $selectedYear";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <tr>
        <td><?php echo date('F j, Y', strtotime($row['app_day'])); ?></td>
        <td><?php echo $row['type_of_concern']; ?></td>
        <td><?php echo $row['specific_concern']; ?></td>
        <td><?php echo $row['remarks']; ?></td>

    </tr>
<?php
}
?>
