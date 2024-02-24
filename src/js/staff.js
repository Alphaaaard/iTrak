let role = "manager";

let managerPill = document.querySelector("#manager-pill");
let personnelPill = document.querySelector("#personnel-pill");

if(managerPill) {
  managerPill.addEventListener("click", () => {
      role = "manager";
  });
}

if(personnelPill) {
  personnelPill.addEventListener("click", () => {
    role = "personnel";
  });
}


// Define an array of color values
const colors = ["Blue", "Red", "Green", "Yellow", "Pink", "Black", "White"];

// Generate a random index based on the array's length
const randomIndex = Math.floor(Math.random() * colors.length);

// Use the random index to select a random color from the array
const randomColor = colors[randomIndex];



function checkRole() {
  if (role == "manager") {
    document.querySelector("#roleField").value = "Maintenance Manager";
    document.querySelector("#userLevelField").value = "2";
  } else if (role == "personnel") {
    document.querySelector("#roleField").value = "Maintenance Personnel";
    document.querySelector("#userLevelField").value = "3";
  }
  // Set the text box to readonly
  document.querySelector("#roleField").setAttribute("readonly", true);
  document.querySelector("#userLevelField").setAttribute("readonly", true);
}

const rfidModalContainer = document.getElementById("staticBackdrop112");

let action = "";
function setAction(act) {
  action = act;
  console.log(action);
}


// rfidModalContainer.addEventListener("click", function () {
//   saveRFID(action);
// });

function saveRFID(action) {
  let rfidFieldEdit = document.getElementById("rfidFieldEdit");
  let rfidFieldAdd = document.getElementById("rfidFieldAdd");
  let rfidNumber = document.getElementById("rfid");

  if (action == "add") {
    rfidFieldAdd.value = rfidNumber.value;
    console.log("ADD: " + rfidFieldAdd.value);

    var addModal = new bootstrap.Modal(
      document.getElementById("exampleModal1"),
      {}
    );
    addModal.show();
    $("#closeAddModal").click(function () {
      $("#exampleModal1").modal("hide");
      $("#staticBackdrop112").modal("hide");
    });
  } else if (action == "edit") {
    rfidFieldEdit.value = rfidNumber.value;
    console.log("Edit: " + rfidFieldEdit.value);

    var editModal = new bootstrap.Modal(
      document.getElementById("updateModal"),
      {}
    );
    console.log(editModal);
    editModal.show();
  } 
}

//* function for getting account_id from table to archiveConfirmModal hidden input
// function getAccountIdForArchive(accId, fname, lname) {
//   document.getElementById("archiveAccId").value = accId;
//   sessionStorage.setItem("uname", fname + " " + lname);
// }

function getInfoFromAdd() {
  let userLevel = document.querySelector("#userLevelField");
  let fname = document.querySelector("#firstNameField");
  let mname = document.querySelector("#middleNameField");
  let lname = document.querySelector("#lastNameField");
  let contact = document.querySelector("#contactField");
  let email = document.querySelector("#emailField");
  let password = document.querySelector("#passwordField");
  let birthday = document.querySelector("#birthdayField");
  let role = document.querySelector("#roleField");
  let rfid = document.querySelector("#rfidFieldAdd");
  let picture = document.querySelector("#pictureField");

  document.querySelector("#adduserLevel").value = userLevel.value;
  document.querySelector("#addfName").value = fname.value;
  document.querySelector("#addmName").value = mname.value;
  document.querySelector("#addlName").value = lname.value;
  document.querySelector("#addContact").value = contact.value;
  document.querySelector("#addEmail").value = email.value;
  document.querySelector("#addPassword").value = password.value;
  document.querySelector("#addBirthday").value = birthday.value;
  document.querySelector("#addRole").value = role.value;
  document.querySelector("#rfidFieldAdd").value = rfid.value;
}

function showErrorAlert(msg) {
  Swal.fire({
    icon: "error",
    title: "Error",
    text: msg,
    timer: 800,
    showConfirmButton: false
  });
}

//*prevents user from erasing 09 in contact value
$('#contactField').on('keydown', function(e) {
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

// Sweet alertboxes
//* add alertbox
$("#addBtn").click(function (e) {
  let isValid = false;
  const firstName = document.getElementById("firstNameField").value.trim();
  const middleName = document.getElementById("middleNameField").value.trim();
  const lastName = document.getElementById("lastNameField").value.trim();
  const contact = document.getElementById("contactField").value.trim();
  const email = document.getElementById("emailField").value.trim();
  const password = document.getElementById("passwordField").value.trim();
  const birthday = document.getElementById("birthdayField").value.trim();
  const rfid = document.getElementById("rfidFieldAdd").value.trim();
  const picture = document.getElementById("pictureField").value;

  // Validate First Name
  if (!firstName) {
    showErrorAlert("First name is required.");
    isValid = false;
    return;
  }

  if (!lastName) {
    showErrorAlert("Last name is required.");
    isValid = false;
    return;
  }

  if (!contact) {
    showErrorAlert("Contact is required");
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

  if (!rfid) {
    showErrorAlert("RFID is required");
    isValid = false;
    return;
  }

  if (!picture) {
    showErrorAlert("Image is required");
    isValid = false;
    return;
  }

  isValid = true;

  let addedRole = (role == "manager") ? "Maintenance Manager" : "Maintenance Personnel";
  let fullname = $('#firstNameField').val() + ' ' + $('#lastNameField').val();  
 


  Swal.fire({
    icon: "info",
    title: `Are you sure you want to add this ${addedRole}?`,
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      let swalConfirm = document.querySelector(".swal2-confirm");
      swalConfirm.setAttribute("name", "submit");

      // AJAX
      let form = document.querySelector("#addForm");
      let xhr = new XMLHttpRequest();

      xhr.open("POST", "../../users/administrator/add_user.php", true);

      xhr.onerror = function () {
        console.error("An error occurred during the XMLHttpRequest");
      };

      let formData = new FormData(form);
      formData.set("submit", swalConfirm);
      xhr.send(formData);

      // success alertbox
      Swal.fire({
        text: fullname+" has been successfully added as "+addedRole ,
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

$(".clickable-row").click(function (e) {
  $("#updateModal").modal("show");
});

//* update staff alertbox
$(".updateBtn").click(function () {
  const firstName = document.getElementById("firstnameEdit").value.trim();
  const middleName = document.getElementById("middlenameEdit").value.trim();
  const lastName = document.getElementById("lastnameEdit").value.trim();
  const contact = document.querySelector(".contactEdit").value.trim();
  const email = document.getElementById("emailEdit").value.trim();
  const password = document.getElementById("passwordEdit").value.trim();
  const birthday = document.getElementById("birthdayEdit").value.trim();

  console.log(contact);

  // Validate First Name
  if (!firstName) {
    alert('asdas');
    showErrorAlert("First name is required.");
    isValid = false;
    return;
  }

  if (!lastName) {
    showErrorAlert("Last name is required.");
    isValid = false;
    return;
  }

  if (!contact) {
    showErrorAlert("Contact is required");
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
      let form = document.querySelector(".userUpdateForm");
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

function archive(accId, fname, lname, e) {
  e.stopPropagation();

  let uname = fname+' '+lname;

  Swal.fire({
    icon: "warning",
    html: `<h3>Are you sure you want to archive <b>${uname}</b>?</h3>`,
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if(result.isConfirmed) {
      $('#archiveAccId').val(accId);

      // $('#archiveDetailsForm').submit();

      let formData = new FormData(document.querySelector("#archiveDetailsForm"));

      $.ajax({
        url: "../../users/administrator/archive_user.php",
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(res) {

          Swal.fire({
            title: "Success",
            text: uname+" has been archived successfully!",
            icon: "success",
            timer: 1000,
            showConfirmButton: false,
          }).then((result) => {

            if (result.dismiss === Swal.DismissReason.timer) {
              window.location.reload();
            }
          });
        },
      });
    }
  });
}