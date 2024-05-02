function showAddConfirmation() {
    Swal.fire({
        icon: "info",
        title: `Are you sure you want to add this task?`,
        showCancelButton: true,
        cancelButtonText: "No",
        focusConfirm: false,
        confirmButtonText: "Yes",
    })
    .then((result) => {
        if (result.isConfirmed) {
          let swalConfirm = document.querySelector(".swal2-confirm");
          swalConfirm.setAttribute("name", "add");
    
          // AJAX
          let form = document.querySelector("#");
          let xhr = new XMLHttpRequest();
    
          xhr.open("POST", "../../users/manager/batasan.php", true);
    
          xhr.onerror = function () {
            console.error("An error occurred during the XMLHttpRequest");
          };
    
          let formData = new FormData(form);
          formData.set("add", swalConfirm);
          xhr.send(formData);
    
          // success alertbox
          Swal.fire({
            text: "The task has been successfully added",
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
  
  function showOutsourcesConfirmation() {
    Swal.fire({
        icon: "info",
        title: `Are you sure you want to mark this task as completed?`,
        showCancelButton: true,
        cancelButtonText: "No",
        focusConfirm: false,
        confirmButtonText: "Yes",
    })
    .then((result) => {
        if (result.isConfirmed) {
          let swalConfirm = document.querySelector(".swal2-confirm");
          swalConfirm.setAttribute("name", "done");
    
          // AJAX
          let form = document.querySelector("#outsourcesForm");
          let xhr = new XMLHttpRequest();
    
          xhr.open("POST", "../../users/manager/batasan.php", true);
    
          xhr.onerror = function () {
            console.error("An error occurred during the XMLHttpRequest");
          };
    
          let formData = new FormData(form);
          formData.set("outsource", swalConfirm);
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
  
  function showApprovalConfirmation() {
    Swal.fire({
        icon: "info",
        title: `Are you sure you want to approve this task?`,
        showCancelButton: true,
        cancelButtonText: "No",
        focusConfirm: false,
        confirmButtonText: "Yes",
    })
    .then((result) => {
        if (result.isConfirmed) {
          let swalConfirm = document.querySelector(".swal2-confirm");
          swalConfirm.setAttribute("name", "done");
    
          // AJAX
          let form = document.querySelector("#approvalForm");
          let xhr = new XMLHttpRequest();
    
          xhr.open("POST", "../../users/manager/batasan.php", true);
    
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
  