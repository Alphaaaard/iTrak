<?php
include_once 'get_current_user_data.php';
$user = get_current_user_data();
?>

<div class="profile-container">
    <div class="modal-parent">
        <div class="modal modal-xl fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="mb-5">Profile Information</h5>
                        <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                    </div>
                    <div class="modal-body">

                        <form method="post" class="row g-3">
                            <input type="hidden" name="accountId" id="hiddenId" value="<?php echo $user['accountId'] ?>">
                            <div class="col-4">
                                <div class="profile-img">
                                    <img src="<?php echo 'data:image/jpeg;base64,' . base64_encode($user['picture']) ?>" width="40%" alt="Profile" id="image">
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="firstname" class="form-label">First name</label>
                                <p id="fName"><?php echo $user['firstName'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="middlename" class="form-label">Middle name</label>
                                <p id="mName"><?php echo $user['middleName'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="lastname" class="form-label">Last name</label>
                                <p id="lName"><?php echo $user['lastName'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="contact" class="form-label">Contact Number</label>
                                <p id="contactDisplay"><?php echo $user['contact'] ?></p>
                            </div>


                            <div class="col-4 text-break">
                                <label for="email" class="form-label">Email</label>
                                <p id="emailDisplay"><?php echo $user['email'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="password" class="form-label">Password</label>
                                <p id="passwordDisplay"><?php echo $user['password'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="birthdayDisplay" class="form-label">Birthday</label>
                                <p id="birthdayDisplay"><?php echo $user['birthday'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="role" class="form-label">Role</label>
                                <p id="roleDisplay"><?php echo $user['role'] ?></p>
                            </div>

                            <div class="col-4">
                                <label for="user_pass" class="form-label" value="<?php echo $user['rfidNumber'] ?>">Register RFID</label>
                                <button type="button" class="form-control btn-custom">RFID</button>
                            </div>
                        </form>
                    </div>

                    <div class="footer">
                        <button type="button" class="btn add-modal-btn edit-btn" data-bs-toggle="modal" data-bs-target="#updateSelfModal" id="editBtn">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-parent">
        <div class="modal modal-xl fade" id="updateSelfModal" tabindex="-1" aria-labelledby="updateModalSelfLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Profile Information</h5>
                        <button class="btn btn-close-modal-emp close-modal-btn" id="closeAddModal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>

                    <div class="modal-body ">

                        <form class="row g-3 userUpdateFormSelf" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="accountId" id="accountIdEditSelf" value="<?php echo $user['accountId'] ?>">

                            <div class="col-4">
                                <label for="firstname" class="form-label">First name</label>
                                <input type="text" class="form-control disabled firstnameEdit" id="firstnameEditSelf" name="firstname" value="<?php echo $user['firstName'] ?>" readonly />
                            </div>

                            <div class="col-4">
                                <label for="middlename" class="form-label">Middle name</label>
                                <input type="text" class="form-control disabled middlenameEdit" id="middlenameEditSelf" name="middlename" value="<?php echo $user['middleName'] ?>" readonly />
                            </div>

                            <div class="col-4">
                                <label for="lastname" class="form-label">Last name</label>
                                <input type="text" class="form-control disabled lastnameEdit" id="lastnameEditSelf" name="lastname" value="<?php echo $user['lastName'] ?>" readonly />
                            </div>

                            <div class="col-4">
                                <label for="contact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control contactEdit" id="contactEditSelf" name="contact" maxlength="11" value="<?php echo $user['contact'] ?>" />
                            </div>


                            <div class="col-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control emailEdit" id="emailEditSelf" name="email" value="<?php echo $user['email'] ?>" />
                            </div>

                            <div class="col-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control passwordEdit" id="passwordEditSelf" name="password" value="<?php echo $user['password'] ?>" />
                            </div>

                            <div class="col-4">
                                <label for="birthday" class="form-label">Birthday</label>
                                <input type="date" class="form-control birthdayEdit" id="birthdayEditSelf" name="birthday" value="<?php echo $user['birthday'] ?>" />
                            </div>

                            <div class="col-4">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control disabled roleEdit" id="roleEditSelf" name="role" value="<?php echo $user['role'] ?>" readonly />
                            </div>

                            <div class="col-4">
                                <label for="user_pass" class="form-label">Register RFID</label>
                                <button type="button" class="form-control btn-custom" data-bs-toggle="modal" data-bs-target="#staticBackdrop112" onclick="setAction('edit')">RFID</button>

                                <input type="password rfidFieldEdit" id='rfidFieldEditSelf' name="rfid" class="d-none" data-bs-toggle="modal" data-bs-target="#staticBackdrop112" value="<?php echo $user['rfidNumber'] ?>">
                            </div>

                            <div class="col-4">
                                <label for="picture" class="form-label">Picture:</label>
                                <input type="file" class="form-control pictureEdit" id="pictureEditSelf" name="picture" />
                            </div>
                        </form>
                    </div>

                    <div class="footer">
                        <button type="button" class="btn add-modal-btn updateSelfBtn">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>