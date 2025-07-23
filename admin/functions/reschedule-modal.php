<!--Reschedule Modal-->
<div class="modal fade" id="rescheduleAppointment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">   
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="./functions/insert-update-appointment.php" method="POST">
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mt-1">Personal Details:</h5>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="appointment_id"  id="appointment_id" value="<?php echo $row['appointment_id']; ?>"  placeholder="" disabled>
                                <input type="hidden" class="form-control" name="appointment_id" id="appointment_id" value="<?php echo $row['appointment_id']; ?>" >
                                <label for="floatingInput">Appointment Id</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="" disabled>
                                <input type="hidden" class="form-control" name="prof_id" value="<?php echo $username?>" id="floatingInput" placeholder="">
                                <label for="floatingInput">Employee Number</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" value="<?php echo $fullname?>" placeholder="" disabled>
                                <label for="floatingInput">Professor Name</label>
                            </div>
                            
                            <br>
                            <h5 class="mt-1">Student Details:</h5>
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
                                <input type="hidden" class="form-control" name="student_year_section" id="student_year_section" value="<?php echo $row['student_year_section']; ?>" placeholder="">
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
                                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                <div class="form-floating mt-2">
                                    <select class="form-select" name="day" id="day">
                                        <option selected>Day Availability</option>
                                    </select>
                                </div>

                                            <script>
                                                $(document).ready(function () {
                                                    $('button.editBtn').on('click', function (e) {
                                                        e.preventDefault();

                                                        $('#editingModal').modal('show');
                                                        $tr = $(this).closest('tr');
                                                        var data = $tr.children('td').map(function () {
                                                            return $(this).text();
                                                        }).get();

                                                        $('#student_id').val(data[1]);
                                                        $('#hidden_student_id').val(data[1]);
                                                        $('#student_name').val(data[2]);
                                                        $('#student_year_section').val(data[3]);
                                                        $('#student_email').val(data[4]);

                                                        // Clear existing options before fetching new ones
                                                        $('#day').html('<option selected>Day Availability</option>');

                                                        // Fetch availability based on the selected student
                                                        var xhr = new XMLHttpRequest();
                                                        xhr.onreadystatechange = function () {
                                                            if (this.readyState == 4) {
                                                                if (this.status == 200) {
                                                                    var response = JSON.parse(this.responseText);
                                                                    if (response.success) {
                                                                        response.day.forEach(function (day) {
                                                                            // Convert the date format for display
                                                                        var formattedDate = new Date(day);
                                                                        var currentDate = new Date(); // Current date

                                                                        // Check if the date is in the future
                                                                        if (formattedDate > currentDate) {
                                                                            formattedDate = formattedDate.toLocaleDateString('en-US', {
                                                                                year: 'numeric',
                                                                                month: 'long',
                                                                                day: 'numeric',
                                                                                weekday: 'long'
                                                                            });
                                                                            var option = document.createElement('option');
                                                                            option.value = day;
                                                                            option.text = formattedDate;
                                                                            $('#day').append(option);
                                                                            }
                                                                        });
                                                                    } else {
                                                                        console.error('Error fetching availability:', response.error);
                                                                    }
                                                                } else {
                                                                    console.error('Error fetching availability. HTTP status:', this.status);
                                                                }
                                                            }
                                                        };

                                                        xhr.open('GET', 'fetch_day_availability.php?student_id=' + data[1], true);
                                                        xhr.send();
                                                    });
                                                });
                                            </script>


                                            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                            <div class="form-floating mt-2">
                                                <select class="form-select" name="time_start" id="time_start">
                                                    <option selected>Time Start</option>
                                                </select>
                                            </div>

                                            <script>
                                                $(document).ready(function () {
                                                    $('button.editBtn').on('click', function (e) {
                                                        e.preventDefault();

                                                        $('#editingModal').modal('show');
                                                        $tr = $(this).closest('tr');
                                                        var data = $tr.children('td').map(function () {
                                                            return $(this).text();
                                                        }).get();

                                                        $('#student_id').val(data[1]);
                                                        $('#hidden_student_id').val(data[1]);
                                                        $('#student_name').val(data[2]);
                                                        $('#student_year_section').val(data[3]);
                                                        $('#student_email').val(data[4]);

                                                        // Clear existing options before fetching new ones
                                                        $('#time_start').html('<option selected>Time Start</option>');

                                                        // Fetch availability based on the selected student
                                                        var xhr = new XMLHttpRequest();
                                                        xhr.onreadystatechange = function () {
                                                            if (this.readyState == 4) {
                                                                if (this.status == 200) {
                                                                    var response = JSON.parse(this.responseText);
                                                                    if (response.success) {
                                                                        response.timeStart.forEach(function (timeStart) {
                                                                            var option = document.createElement('option');
                                                                            option.value = timeStart;
                                                                            option.text = timeStart;

                                                                            // Check if the timeStart is already selected by another user
                                                                            if (timeStart !== data[5]) {
                                                                                $('#time_start').append(option);
                                                                            } else {
                                                                                // Disable the option if already selected
                                                                                option.disabled = true;
                                                                                $('#time_start').append(option);
                                                                            }
                                                                        });
                                                                    } else {
                                                                        console.error('Error fetching availability:', response.error);
                                                                    }
                                                                } else {
                                                                    console.error('Error fetching availability. HTTP status:', this.status);
                                                                }
                                                            }
                                                        };

                                                        xhr.open('GET', 'fetch_time_start_availability.php?student_id=' + data[1], true);
                                                        xhr.send();
                                                    });
                                                });
                                            </script>

                                            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                            <div class="form-floating mt-2">
                                                <select class="form-select" name="time_end" id="time_end">
                                                    <option selected>Time End</option>
                                                </select>
                                            </div>

                                            <script>
                                               $(document).ready(function () {
                                                $('button.editBtn').on('click', function (e) {
                                                    e.preventDefault();

                                                    $('#editingModal').modal('show');
                                                    $tr = $(this).closest('tr');
                                                    var data = $tr.children('td').map(function () {
                                                        return $(this).text();
                                                    }).get();

                                                    $('#student_id').val(data[1]);
                                                    $('#hidden_student_id').val(data[1]);
                                                    $('#student_name').val(data[2]);
                                                    $('#student_year_section').val(data[3]);
                                                    $('#student_email').val(data[4]);

                                                    // Clear existing options before fetching new ones
                                                    $('#time_end').html('<option selected>Time End</option>');

                                                    // Fetch availability based on the selected student
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.onreadystatechange = function () {
                                                        if (this.readyState == 4) {
                                                            if (this.status == 200) {
                                                                var response = JSON.parse(this.responseText);
                                                                if (response.success) {
                                                                    response.timeEnd.forEach(function (timeEnd) {

                                                                        var option = document.createElement('option');
                                                                        option.value = timeEnd;
                                                                        option.text = timeEnd;
                                                                        $('#time_end').append(option);  // Use $('#time_start') to target the correct dropdown
                                                                        
                                                                        
                                                                    });
                                                                } else {
                                                                    console.error('Error fetching availability:', response.error);
                                                                }
                                                            } else {
                                                                console.error('Error fetching availability. HTTP status:', this.status);
                                                            }
                                                        }
                                                    };

                                                    xhr.open('GET', 'fetch_time_end_availability.php?student_id=' + data[1], true);
                                                    xhr.send();
                                                });
                                            });
                                            </script>
                                        <select id="firstChoice" onchange="updateOptions()" name="type_of_concern" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                            <option selected>Please select a type of concern</option>
                                            <option value="Advising">Advising</option>
                                            <option value="Consultation">Consultation</option>
                                        </select>

                                        <select id="secondChoice" class="form-select mt-2" name="specific_concern" aria-label="Default select example" style="height: 60px;" >
                                            <option selected>Specify your concern</option>
                                        </select>

                                      
                                        <select id="appointment_status" name="appointment_status" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                            <option value="default">Appointment Status</option>
                                            <option value="Priority">Priority</option>
                                            <option value="Standard">Standard</option>
                                            <!-- Add other appointment status options here -->
                                        </select>

                                        <script>
                                            function updateOptions() {
                                                var firstChoice = document.getElementById('firstChoice');
                                                var secondChoice = document.getElementById('secondChoice');
                                                var appointmentStatus = document.getElementById('appointment_status');

                                                // Clear existing options
                                                secondChoice.innerHTML = '';

                                                // Get the selected value from the first choice
                                                var selectedValue = firstChoice.value;

                                                // Add options based on the selected value
                                                if (selectedValue === 'Advising') {
                                                    var advising = document.createElement('option');
                                                    advising.value = 'Thesis Matter';
                                                    advising.text = 'Thesis Matters';
                                                    secondChoice.add(advising);

                                                    var advising = document.createElement('option');
                                                    advising.value = 'Grades Matter';
                                                    advising.text = 'Grades Matters';
                                                    secondChoice.add(advising);

                                                    if (secondChoice.value === 'Thesis Matter') {
                                                        appointmentStatus.value = 'Priority';
                                                    } else if (secondChoice.value === 'Grades Matter') {
                                                        appointmentStatus.value = 'Standard';
                                                    }
                                                } else if (selectedValue === 'Consultation') {
                                                    var consultation = document.createElement('option');
                                                    consultation.value = 'Personal Matter';
                                                    consultation.text = 'Personal Matter';
                                                    secondChoice.add(consultation);

                                                    // Reset appointment status for other concerns
                                                    appointmentStatus.value = 'Priority';
                                                } else {
                                                    // Reset appointment status for other concerns
                                                    appointmentStatus.value = '';
                                                }
                                            }
                                        </script>


                                        <div class="form-floating mt-2">
                                          <textarea class="form-control" name="detailed_concern" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                                          <label for="floatingTextarea2">Detailed Concern</label>
                                        </div>

                                        <div class="form-floating mt-2">
                                        <select name="remarks" id="remarks" class="form-select mb-2" aria-label="Default select example" style="height: 60px;" disabled>
                                          <option selected="Pending">Pending</option>
                                          <option value="Pending">Pending</option>
                                          <option value="Done">Done</option>
                                        </select>

                                        <select style="display:none;" name="remarks" id="remarks" class="form-select mb-2" aria-label="Default select example" style="height: 60px;">
                                          <option selected="Pending">Pending</option>
                                          <option value="Pending">Pending</option>
                                          <option value="Done">Done</option>
                                        </select>
                                        </div>

                                    <div class="modal-footer">
                                          <button type="submit" name="updateAppointment" class="btn btn-primary"  onclick="generatePDF()">Save changes</button>
                                    </div>
                                    
                                      </div>
                                      </form>
                                        
                                  </div>
                              </div>

                              <!--closing for modal-->
                            </div>

                            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                            <!--Scripts for Modal-->
                            <script>
                                $(document).ready(function () {

                                    $('button.editBtn').on('click', function () {

                                        $('#editingModal').modal('show');

                                        $tr = $(this).closest('tr');

                                        var data = $tr.children("td").map(function () {
                                            return $(this).text();
                                        }).get();

                                        console.log(data);

                                    
                                        $('#appointment_id').val(data[0]);
                                        $('#student_id').val(data[1]);
                                        $('#student_name').val(data[3]);
                                        $('#student_year_section').val(data[4]);
                                        $('#student_email').val(data[5]);
                                      
                                        $('#firstChoice').val(data[8]);
                                        updateOptions(); // Call the function to update the second select based on the first one
                                        $('#secondChoice').val(data[9]);
                                        $('#detailed_concern').val(data[9]);
                                        $('#remarks').val(data[10]);
                                        
                                    });
                                });
                            </script>
                        </div>