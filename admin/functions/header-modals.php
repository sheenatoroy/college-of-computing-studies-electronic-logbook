<!-- Modal for Profile Icon-->
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!--Account Details-->
                <h5 class="fw-bold">Employee Information:</h5>
                <div class="card mb-4" style="max-width: 1024px;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="/assets/img/profile-icon.png" class="img-fluid rounded-start p-3" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="mb-1">
                                    <label for="exampleFormControlInput1" class="form-label">Employee Number:</label>
                                    <input type="text" name="prof_id" id="prof_id" value="<?php echo $username?>" class="form-control"  placeholder="" disabled>
                                    <input type="hidden" name="prof_id" id="prof_id" value="<?php echo $username?>" class="form-control" placeholder="">
                                </div>
                                <div class="mb-1">
                                    <label for="exampleFormControlInput1" class="form-label">Full Name:</label>
                                    <input type="email" value="<?php echo $fullname?>" class="form-control" id="exampleFormControlInput1" placeholder="" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 m-2">
                    <div class="card-header fw-bold">
                        Availability schedule:
                    </div>
                        <div class="card-body">
                            <?php
                                
                                // Fetch availability data from the database
                                $query = "SELECT * FROM prof_availability WHERE prof_id = $username ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
                                $result = mysqli_query($conn, $query);

                                $currentDay = null; // Variable to store the current day

                                echo '<table class="table table-sm">';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Check if the day is equal to or later than the current day
                                    if (strtotime($row['day']) >= strtotime(date('Y-m-d', strtotime('today')))) {

                                        // Display the day label only if it's different from the current day
                                        if ($row['day'] != $currentDay) {

                                            // If it's not the first day, close the previous day's row
                                            if ($currentDay !== null) {
                                                echo '</td></tr>';
                                            }

                                            // Start a new row for the current day
                                            echo '<tr><th scope="row">' . date('F j, Y (l)', strtotime($row['day'])) . '</th><td>';
                                            $currentDay = $row['day'];
                                        }

                                        // Check if the keys exist in the row before accessing them
                                        if (isset($row['time_start_availability']) && isset($row['time_end_availability'])) {
                                            // Convert military time to 12-hour format with uppercase AM/PM
                                            $startTime = date('h:ia', strtotime($row['time_start_availability']));
                                            $endTime = date('h:ia', strtotime($row['time_end_availability']));

                                            // Format time in 12-hour format with uppercase AM/PM
                                            $startTimeFormatted = date('h:i A', strtotime($startTime));
                                            $endTimeFormatted = date('h:i A', strtotime($endTime));

                                            // Combine the time range into a single string
                                            $timeRange = $startTimeFormatted . ' - ' . $endTimeFormatted;

                                            // Display the clickable time range for the day
                                            echo '' . $timeRange . '<br>';
                                        }
                                    }
                                }

                                // Close the last day's row
                                if ($currentDay !== null) {
                                    echo '</td></tr>';
                                }

                                echo '</table>';
                            ?>

                        </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!--For logout-->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <h4>Are you sure you want to logout?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                <button type="submit" id="logoutBtn" class="btn btn-primary">Yes</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Your JavaScript code -->
    <script>
        $(document).ready(function () {
            $('#logoutBtn').on('click', function () {
                // Redirect to logout.php when the button is clicked
                window.location.href = '../../index.php';
            });
        });
</script>