<!-- account-management-faculty.php -->
<?php include "../connection.php"?>

<!--Modal for Faculty/Employee-->
<div class="modal fade" id="facultyCategoryModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Faculty Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
    
                <div class="modal-body">
                    <form action="/admin/functions/insertAccountFaculty.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Please fill out the following fields</h6>
                                <select name="account_type" id="account_type" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                <option selected>Please select account type</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="admin">Admin</option>
                                </select>
                            
                                <div class="form-floating mt-2">
                                    <input type="text" name="username" id="username" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Employee Id</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="firstname" id="firstname"class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">First Name</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="middlename" id="middlename"class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Middle Name</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="lastname" id="lastname"class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <select name="gender" id="gender" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                    <option selected>Please select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>

                                <div class="form-floating mt-2">
                                    <input type="text" name="email" id="email"class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Email Address</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="contact_number" id="contact_number" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Contact Number</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="address" id="address" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Address</label>
                                </div>

                                <div class="form-floating mt-2">
                                    <input type="text" name="password" id="password"class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Password</label>
                                </div>
                            </div>
                        </div>

                        <br> 
                        <h6 class="mt-3">Set day for Consultation and Advising Availability</h6>
                        <select name="day" id="day_availability" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                            <option selected>Please select a day of availability</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mt-2">
                                    <input type="time" name="time_start_availability" id="time_start_availability" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Start Time</label>
                                </div>  
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mt-2">
                                    <input type="time" name="time_end_availability" id="time_end_availability" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">End Time</label>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <h6 class="mt-1">Upload Photo
                                <br>
                                <small class="text-muted">Note: 1x1 photo should have be in a plain white background.</small>
                            </h6>

                            <div class="input-group">
                                <input type="file" name="image" class="form-control" accept=".jpeg,.jpg,.png" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                            <button type="submit" name="save_changes" id="save_changes" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>

<!--EDIT FACULTY MODAL-->
<div class="modal fade" id="facultyEditCategoryModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Faculty Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../admin/functions/insertAccountFaculty.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Please fill out the following fields</h6>
                            <select name="account_type" id="edit_account_type" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                <option selected>Please select account type</option>
                                <option value="faculty">Faculty</option>
                                <option value="admin">Admin</option>
                            </select>

                            <div class="form-floating mt-2">
                                <input type="text" name="username" id="edit_username" class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Employee Id</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="firstname" id="edit_firstname"class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">First Name</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="middlename" id="edit_middlename"class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Middle Name</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="lastname" id="edit_lastname"class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Last Name</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <br>
                            <select name="gender" id="edit_gender" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                <option selected>Please select your gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>

                            <div class="form-floating mt-2">
                                <input type="text" name="email" id="edit_email"class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Email Address</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="contact_number" id="edit_contact_number" class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Contact Number</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="address" id="edit_address" class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Address</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" name="password" id="edit_password"class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Password</label>
                            </div>
                        </div>

                        <!--Account Settings-->
                        <br>
                        <h6 class="mt-3">Set day for Consultation and Advising Availability</h6>
                        <select name="day" id="edit_day_availability" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                            <option selected>Please select a day of availability</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mt-2">
                                    <input type="time" name="time_start_availability" id="edit_time_start_availability" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">Start Time</label>
                                </div>  
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mt-2">
                                    <input type="time" name="time_end_availability" id="edit_time_end_availability" class="form-control" id="floatingInput" placeholder="">
                                    <label for="floatingInput">End Time</label>
                                </div>
                            </div>
                        </div>
                        
                        <br>
                        <div class="row">
                            <h6 class="mt-1">Upload Photo
                            <br>
                            <small class="text-muted">Note: 1x1 photo should have be in a plain white background.</small>
                            </h6>

                            <div class="input-group">
                                <input type="file" name="image" class="form-control" accept=".jpeg,.jpg,.png" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="save_changes" id="saveEditFacultyChanges" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- VIEW FACULTY AVAILABILITY -->
<div class="modal fade" id="viewAvailabilityModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAvailabilityModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mt-3">Consultation and Advising Availability</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query to fetch availability data from your database
                                    $sql = "SELECT pa.prof_id, pa.day_availability, pa.time_start_availability, pa.time_end_availability FROM prof_availability";
                                    $result = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        // Output data of each row
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row["day_availability"] . "</td>";
                                            echo "<td>" . $row["time_start_availability"] . "</td>";
                                            echo "<td>" . $row["time_end_availability"] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No availability data found</td></tr>";
                                    }
                                    // Close database connection
                                    mysqli_close($conn);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
    </div>
</div>