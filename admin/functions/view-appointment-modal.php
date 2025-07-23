<?php
$statusMessage = "";
$remarks = "";
$disabledButton = true;

// Query to get appointment details and statusMessage
if(isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    $sql1 = "SELECT a.remarks, a.student_id, a.prof_id, a.appointment_id, s.firstname, s.lastname
            FROM appointments AS a
            JOIN student AS s ON a.student_id = s.username
            WHERE a.appointment_id = ?";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $remarks = $row['remarks'];
        $fullname = $row['firstname'] . ' ' . $row['lastname'];
        $prof_id = $row['prof_id'];
        
        // Set the correct statusMessage based on the appointment_status
        if ($remarks === "Pending") {
            $statusMessage = "Wait for approval of your appointment.";
            $disabledButton = true; // Disable the button if remarks is Pending
        } elseif ($remarks === "Approved") {
                echo "Remarks is Approved"; // Debug message
                $statusMessage = "Your appointment is approved with <span style='color: green;'>$fullname</span>.";
                $disabledButton = false; // Enable the button if remarks is Approved
        } else {
            $statusMessage = "Unknown status";
        }
    } else {
        $statusMessage = "Appointment not found";
    }

    $stmt->close();
}
?>


<style>
.button-container {
    display: flex;
    gap: 10px; /* Pagitan sa pagitan ng mga button */
}

</style>
<!-- View Modal -->
<div class="modal fade" id="setAppointment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Hidden input field for appointment_id -->
                <input type="hidden" id="appointment_id_input" name="appointment_id">
                <div class="timeline">
                    <div class="timeline-outer">
                        <div class="timeline-card">
                            <div class="timeline-info">
                                <h5 class="timeline-title">Step 1: <span style="font-style:italic; font-size: 15px">Fill out the details of your set appointment.</span></h5>
                            </div>
                            <button style="display:none;" type="button" id="setAppointmentBtn" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#setApp" <?php if($remarks === "Pending" || $remarks === "Approved") echo 'disabled'; ?>>Set Appointment</button>
                            <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#viewAppointment">View Appointment Details</button>
                            <!-- Display of statusMessage -->
                            <div class="timeline-info">
                                <h5 class="timeline-title mt-2">Step 2:
                                    <span id="statusMessage" style="font-style: italic; font-size: 15px;"><?php echo $statusMessage; ?></span>
                                </h5>
                            </div>
                            <div class="timeline-info">
                                <h5 class="timeline-title mt-2">Step 3: <span style="font-style:italic; font-size: 15px">Accomplish the Action Report.</span></h5>
                            </div>
                            <span id="remarksMessage" style="font-style: italic; font-size: 15px;"><?php echo $remarks; ?></span>
                            <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#" value="<?php echo $remarks?>" <?php echo ($remarks === 'Pending' || $remarks === 'Approved') ? '' : 'disabled'; ?>>
                                Action Report
                            </button>
                            <div class="timeline-info">
                                <h5 class="timeline-title mt-2">Step 4:
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!--modal body closing-->
        </div>
    </div>
</div>

<!--View Appointment Details-->
<div class="modal fade" id="viewAppointment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./functions/approvedAppointment.php" method="POST">
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mt-1">Personal Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="">
                                <label for="floatingInput">Employee Number</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" value="<?php echo $fullname?>" placeholder="" disabled>
                                <label for="floatingInput">Professor Name</label>
                            </div>
                            
                            <h5 class="mt-2">Student Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="student_id" id="student_id" value="<?php echo $row['student_id']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="student_id" value="<?php echo $row['student_id']; ?>" id="hidden_student_id">
                                <label for="floatingInput">Student Number</label>
                            </div>
                            
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" id="student_firstname" value="<?php echo $row['student_firstname']  . ' ' . $row['student_lastname'];; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" value="<?php echo $row['student_firstname']; ?>">
                                <label for="floatingInput">Selected Student</label>
                            </div>  
                            
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="student_year_section" id="student_year_section"  value="<?php echo $row['student_year_section']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="student_year_section" value="<?php echo $row['student_year_section']; ?>" placeholder="">
                                <label for="floatingInput">Year and Section</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="student_email" id="student_email" value="<?php echo $row['student_email']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="student_email" value="<?php echo $row['student_email']; ?>" placeholder="">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                        <!--Appointment Transaction-->
                        <div class="col-md-6">
                            <h5 class="mt-1">Appointment Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="day" id="day" value="<?php echo $row['day']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="day" value="<?php echo $row['day']; ?>" placeholder="">
                                <label for="floatingInput">Day of Availability</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="time_start" id="time_start" value="<?php echo date('h:i A', strtotime($row['time_start'])) ; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="time_start" id="time_start"  value="<?php echo date('h:i A', strtotime($row['time_start'])) ; ?>" placeholder="">
                                <label for="floatingInput">Time Start</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="time_end" id="time_end" value="<?php echo date('h:i A', strtotime($row['time_end'])) ; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="time_end" id="time_end"  value="<?php echo date('h:i A', strtotime($row['time_end'])) ; ?>" placeholder="">
                                <label for="floatingInput">Time End</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="type_of_concern" id="type_of_concern" value="<?php echo $row['type_of_concern']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="type_of_concern" id="type_of_concern" value="<?php echo $row['type_of_concern']; ?>" placeholder="">
                                <label for="floatingInput">Type of Concern</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="specific_concern" id="specific_concern" value="<?php echo $row['specific_concern']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="specific_concern" id="specific_concern" value="<?php echo $row['specific_concern']; ?>" placeholder="">
                                <label for="floatingInput">Specific Concern</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="appointment_status" id="appointment_status" value="<?php echo $row['appointment_status']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="appointment_status" id="appointment_status" value="<?php echo $row['appointment_status']; ?>" placeholder="">
                                <label for="floatingInput">Appointment Status</label>
                            </div>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="detailed_concern" id="detailed_concern" value="<?php echo $row['detailed_concern']; ?>" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="detailed_concern" id="detailed_concern" value="<?php echo $row['detailed_concern']; ?>" placeholder="">
                                <label for="floatingInput">Detailed Concern</label>
                            </div>
                        </div>
                    </div>
                </div>
                <<input type="hidden" id="remarks1" name="remarks1" value="<?php echo $row['remarks']; ?>">
                <div class="modal-footer">
                    <button type="submit" name="decline" id="declineBtn" class="btn btn-danger" data-bs-dismiss="modal" onclick="generatePDF()">Decline</button>
                    <button type="submit" name="updateRemarks" id="approveBtn" class="btn btn-primary" onclick="generatePDF()">Approved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to disable buttons based on remarks value
    function disableButtons() {
        var remarks = document.getElementById("remarks1").value;
        var declineBtn = document.getElementById("declineBtn");
        var approveBtn = document.getElementById("approveBtn");

        if (remarks === "Approved") {
            declineBtn.disabled = true;
            approveBtn.disabled = true;
        } else if (remarks === "Unresolved") {
            declineBtn.disabled = false;
            approveBtn.disabled = false;
        }
    }
    // Call the function initially
    disableButtons();

</script>
<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#setAppointmentBtn').click(function() {
            $('#setAppointment').modal('hide');
            $('#setApp').modal('show');
        });

        $('#setApp').on('hidden.bs.modal', function () {
            $('#setAppointment').modal('show');
        });
    });
</script>