<?php
    include '../connection.php'; // Include your database connection file

    if(isset($_POST['username'])) {
        $username = $_POST['username'];
        $query = "SELECT pa.prof_id, pa.day, pa.time_start, pa.time_end FROM prof_availability AS pa INNER JOIN prof AS p ON pa.prof_id = p.username WHERE p.username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['day'] . "</td>";
                    echo "<td>" . date("h:i A", strtotime($row['time_start'])) . "</td>";
                    echo "<td>" . date("h:i A", strtotime($row['time_end'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No available day for consultation</td></tr>";
            }
        } else {
            echo "Error: " . $conn->error; // Print any SQL errors
        }
        $stmt->close();
    }
?>
