<!-- accountManagementStudent.php -->
<?php include '../connection.php'?>

<!--Modal for Student-->
<div class="modal fade" id="studentCategoryModal">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel2">Create Student Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/functions/insertAccountStudent.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Please fill out the following fields</h6>
                            <div class="form-floating mt-2">
                                <input type="text" name="account_type" id="account_type" value="student" class="form-control" id="floatingInput" placeholder="" disabled>
                                <input type="hidden" name="account_type" id="account_type" value="student" class="form-control" id="floatingInput" placeholder="">
                                <label for="floatingInput">Account Type</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="username" id="username" placeholder="" required pattern="[0-9]+">
                                <label for="floatingInput">Student Id</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="">
                                <label for="floatingInput">First Name</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="middlename" id="middlename" placeholder="">
                                <label for="floatingInput">Middle Name</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="">
                                <label for="floatingInput">Last Name</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                        <br>
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="year_section" id="year_section" placeholder="">
                                <label for="floatingInput">Year and Section</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="email" id="email" placeholder="">
                                <label for="floatingInput">Email Address</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="">
                                <label for="floatingInput">Contact Number</label>
                            </div>

                            <select name="gender" id="gender" class="form-select mt-2" aria-label="Default select example" style="height: 60px;">
                                <option selected>Please select your gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="address" id="address" placeholder="">
                                <label for="floatingInput">Address</label>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="password" id="password"  placeholder="">
                                <label for="floatingInput">Password</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Discard Changes</button>
                    <button type="submit" name="save_account" id="save_account" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>