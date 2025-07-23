<?php
include "../connection.php";

// Function to count appointments based on the status
function countAppointmentsByStatus($status, $month, $year)
{
    global $conn;

    $conditions = array();

    if ($status !== '') {
        $conditions[] = "appointment_status = '$status'";
    }

    if ($month !== '') {
        $conditions[] = "MONTH(app_day) = $month";
    }

    if ($year !== '') {
        $conditions[] = "YEAR(app_day) = $year";
    }

    $condition = implode(' AND ', $conditions);

    $query = "SELECT COUNT(*) AS total FROM appointments WHERE $condition";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}

// Fetch the selected month and year
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

// Check if there are transactions for the selected month and year
$totalTransactions = countAppointmentsByStatus('', $selectedMonth, $selectedYear);

// Output HTML for the rows or a message if there are no transactions
if ($totalTransactions > 0) {
    // Count appointments for each status
    $totalAppointments = countAppointmentsByStatus('', $selectedMonth, $selectedYear);
    $totalAdvising = countAppointmentsByStatus('Advising', $selectedMonth, $selectedYear);
    $totalConsultation = countAppointmentsByStatus('Consultation', $selectedMonth, $selectedYear);
    $totalPriorityStatus = countAppointmentsByStatus('Priority', $selectedMonth, $selectedYear);
    $totalStandardStatus = countAppointmentsByStatus('Standard', $selectedMonth, $selectedYear);

    // Count appointments for type_of_concern and remarks
    $totalAdvising = countAppointmentsByTypeOfConcern('Advising', $selectedMonth, $selectedYear);
    $totalConsultation = countAppointmentsByTypeOfConcern('Consultation', $selectedMonth, $selectedYear);
    $totalUnresolvedAppointments = countAppointmentsByRemarks('Unresolved', $selectedMonth, $selectedYear);
    $totalPendingAppointments = countAppointmentsByRemarks('Pending', $selectedMonth, $selectedYear);
    $totalDoneAppointments = countAppointmentsByRemarks('Done', $selectedMonth, $selectedYear);

    // Output HTML for the rows
    echo "<table id='appointmentsTable' class='table table-bordered' cellpadding='40' cellspacing='40'>
    <thead>
        <tr style='text-align: center;'>
            <th>Total no. of Appointments</th>
            <th>Total no. of Advising</th>
            <th>Total no. of Consultation</th>
            <th>Total no. of Priority Status</th>
            <th>Total no. of Standard Status</th>
            <th>Total no. of Unresolved Appointments</th>
            <th>Total no. of Pending Appointments</th>
            <th>Total no. of Done Appointments</th>

        </tr>
    </thead>
    <tbody>
        <td style='text-align: center;'>$totalAppointments</td>
        <td style='text-align: center;'>$totalAdvising</td>
        <td style='text-align: center;'>$totalConsultation</td>
        <td style='text-align: center;'>$totalPriorityStatus</td>
        <td style='text-align: center;'>$totalStandardStatus</td>
        <td style='text-align: center;'>$totalUnresolvedAppointments</td>
        <td style='text-align: center;'>$totalPendingAppointments</td>
        <td style='text-align: center;'>$totalDoneAppointments</td>
    </tbody>
</table>";

    
} else {
    echo "<tr><td colspan='2'>No transactions for the selected month and year</td></tr>";
}

// Function to count appointments based on type_of_concern
function countAppointmentsByTypeOfConcern($type, $month, $year)
{
    global $conn;

    $conditions = array();

    if ($type !== '') {
        $conditions[] = "type_of_concern = '$type'";
    }

    if ($month !== '') {
        $conditions[] = "MONTH(app_day) = $month";
    }

    if ($year !== '') {
        $conditions[] = "YEAR(app_day) = $year";
    }

    $condition = implode(' AND ', $conditions);

    $query = "SELECT COUNT(*) AS total FROM appointments WHERE $condition";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}

// Function to count appointments based on remarks
function countAppointmentsByRemarks($remarks, $month, $year)
{
    global $conn;

    $conditions = array();

    if ($remarks !== '') {
        $conditions[] = "remarks = '$remarks'";
    }

    if ($month !== '') {
        $conditions[] = "MONTH(app_day) = $month";
    }

    if ($year !== '') {
        $conditions[] = "YEAR(app_day) = $year";
    }

    $condition = implode(' AND ', $conditions);

    $query = "SELECT COUNT(*) AS total FROM appointments WHERE $condition";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}

?>
