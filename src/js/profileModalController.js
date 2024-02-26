//* if .edit-btn is clicked, hides the .modal
$('.edit-btn').on('click', function() {
  $('.modal').modal('hide');
});

// * for contact input
//*prevents user from erasing 09 in contact value
let contactCurrentValue = $('.contactEdit').val();

$('.contactEdit').on('keydown', function(e) {
contactCurrentValue = $(this).val();

if(e.ctrlKey) {
  e.preventDefault();
  return false;
}

// Check if the input length is 1 and the event type is 'deleteContentBackward'
if(contactCurrentValue.length <= 2 && e.keyCode == 8) {
  e.preventDefault();
  return false;
}
});



//*update current user alertbox
$(".updateSelfBtn").click(function () {

  const firstName = $('#firstnameEditSelf').val().trim();
  const middleName = $('#middlenameEditSelf').val().trim();
  const lastName = $('#lastnameEditSelf').val().trim();
  const contact = $('#contactEditSelf').val().trim();
  const email = $('#emailEditSelf').val().trim();
  const password = $('#passwordEditSelf').val().trim();
  const birthday = $('#birthdayEditSelf').val().trim();

  // Validate First Name
  if (!firstName) {
    showErrorAlert("First name is required.");
    isValid = false;
    return;
  }

  if (!middleName) {
    showErrorAlert("Middle name is required.");
    isValid = false;
    return;
  }

  if (!lastName) {
    showErrorAlert("Last name is required.");
    isValid = false;
    return;
  }

  if (!contact) {
    showErrorAlert("Contact is required.");
    isValid = false;
    return;
  }

  if(contact.length != 11) {
    showErrorAlert("Contact must contain 11 numbers.");
    isValid = false;
    return;
  }

  // Validate Email
  if (!email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
    showErrorAlert("Invalid email.");
    isValid = false;
    return;
  }

  // Validate Password - Example: At least 6 characters
  if (password.length < 6) {
    showErrorAlert("Password length must be at least 6 characters.");
    isValid = false;
    return;
  }

  if (!birthday) {
    showErrorAlert("Birthdate is required");
    isValid = false;
    return;
  }


  Swal.fire({
    icon: "warning",
    title: "Are you sure you want to save changes?",
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      let swalConfirm = document.querySelector(".swal2-confirm");
      swalConfirm.setAttribute("name", "updateBtn");

      // AJAX
      let form = document.querySelector(".userUpdateFormSelf");
      let xhr = new XMLHttpRequest();

      xhr.open("POST", "../../users/administrator/update_user.php", true);

      // xhr.onload = function () {
      //   if (this.status == 200) {
      //     console.log(this.response);
      //   }
      // };

      let formData = new FormData(form);
      formData.set("updateBtn", swalConfirm);
      xhr.send(formData);

      

      // success alertbox
      Swal.fire({
        title: "Success",
        text: "Changes Saved Successfully!",
        icon: "success",
        timer: 1000,
        showConfirmButton: false,
      }).then((result) => {
        if (result.dismiss || Swal.DismissReason.timer) {
          window.location.reload();
        }
      });
    }
  });
});
