function showTransferConfirmation() {
  Swal.fire({
    icon: "info",
    title: `Are you sure you want to transfer this task?`,
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      let swalConfirm = document.querySelector(".swal2-confirm");
      swalConfirm.setAttribute("name", "approval");

      // AJAX
      let form = document.querySelector("#requestForm");
      let xhr = new XMLHttpRequest();

      xhr.open("POST", "../../users/personnel/request.php", true);

      xhr.onerror = function () {
        console.error("An error occurred during the XMLHttpRequest");
      };

      let formData = new FormData(form);
      formData.set("approval", swalConfirm);
      xhr.send(formData);

      // success alertbox
      Swal.fire({
        text: "Transfer Completed",
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
}

function showTaskConfirmation() {
  Swal.fire({
    icon: "info",
    title: `Are you sure you want to mark this task as completed?`,
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      let swalConfirm = document.querySelector(".swal2-confirm");
      swalConfirm.setAttribute("name", "done");

      // AJAX
      let form = document.querySelector("#requestForm");
      let xhr = new XMLHttpRequest();

      xhr.open("POST", "../../users/personnel/request.php", true);

      xhr.onerror = function () {
        console.error("An error occurred during the XMLHttpRequest");
      };

      let formData = new FormData(form);
      formData.set("done", swalConfirm);
      xhr.send(formData);

      // success alertbox
      Swal.fire({
        text: "The task has been marked as done!",
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
}

function showFeedbackConfirmation() {
  Swal.fire({
    icon: "info",
    title: `Are you sure you want to approve this task?`,
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      let swalConfirm = document.querySelector(".swal2-confirm");
      swalConfirm.setAttribute("name", "feedback");

      // AJAX
      let form = document.querySelector("#approvalForm");
      let xhr = new XMLHttpRequest();

      xhr.open("POST", "../../users/personnel/request.php", true);

      xhr.onerror = function () {
        console.error("An error occurred during the XMLHttpRequest");
      };

      let formData = new FormData(form);
      formData.set("approval", swalConfirm);
      xhr.send(formData);

      // success alertbox
      Swal.fire({
        text: "The task has been successfully approved",
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
}
