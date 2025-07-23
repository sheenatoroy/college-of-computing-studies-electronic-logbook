<?php
    include '../connection.php'; // Include your database connection file

    if(isset($_POST['username'])) {
        $username = $_POST['username'];
        $query = "SELECT s.subject_code, s.subject_name, s.day, s.start_time, s.end_time FROM subj_management AS s INNER JOIN prof AS p ON s.status = p.username WHERE p.username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['subject_code'] . "</td>";
                    echo "<td>" . $row['subject_name'] . "</td>";
                    echo "<td>" . $row['day'] . "</td>";
                    echo "<td>" . date("h:i A", strtotime($row['start_time'])) . "</td>";
                    echo "<td>" . date("h:i A", strtotime($row['end_time'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No subjects assigned</td></tr>";
            }
        } else {
            echo "Error: " . $conn->error; // Print any SQL errors
        }
        $stmt->close();
    }
?>
