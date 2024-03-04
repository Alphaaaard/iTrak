function confirmAlert(reportType) {
    Swal.fire({
      icon: "info",
      text: "Do you want to save changes?",
      confirmButtonText: "Yes",
      showCancelButton: true,
      cancelButtonText: "No",
    }).then((result) => {
      if (result.isConfirmed) {
        let formData;
  
        switch (reportType) {
          case "working":
            formData = new FormData(document.querySelector("#workingForm"));
            break;
          case "maintenance":
            formData = new FormData(document.querySelector("#maintenanceForm"));
            break;
          case "replace":
            formData = new FormData(document.querySelector("#replacementForm"));
            break;
          case "repair":
            formData = new FormData(document.querySelector("#repairForm"));
            break;
          // Consider adding a default case for unexpected reportTypes
          default:
            console.error("Invalid report type:", reportType);
            return; // Exit the function early if reportType is invalid
        }
  
        $.ajax({
          url: "../../users/administrator/reports.php",
          data: formData,
          type: "POST",
          processData: false,
          contentType: false,
          success: function (res) {
            Swal.fire({
              title: "Changes Saved Successfully!",
              icon: "success",
              timer: 1000, //timer (in ms) for the success alertbox before closing
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
  
  function assignPersonnel() {
    //checks if there is a name assigned
    let assignedName = $(".assignedName").val();
  
    if (!assignedName) {
      showErrorAlert("Please assign a maintenance personnel");
      return;
    }
  
    Swal.fire({
      icon: "info",
      title: "Assigned this personnel?",
      confirmButtonText: "Yes",
      showCancelButton: true,
      cancelButtonText: "No",
    }).then((result) => {
      if (result.isConfirmed) {
        let formData = new FormData(
          document.querySelector("#assignPersonnelForm")
        );
  
        $.ajax({
          url: "../../users/administrator/reports.php",
          data: formData,
          type: "POST",
          processData: false,
          contentType: false,
          success: function (res) {
            Swal.fire({
              title: "Maintenance Personnel has been assigned",
              icon: "success",
              timer: 1000, //timer (in ms) for the success alertbox before closing
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
  
  document.addEventListener("DOMContentLoaded", function () {
    // Function to update displayed data and pagination based on user selection
    function updateDataAndPagination() {
      var selectedValue = parseInt(
        document.getElementById("rows-display-dropdown").value
      ); // Get the selected value from the dropdown
      var rows = document.querySelectorAll(".table-container table tr"); // Select all table rows
      var totalItems = rows.length; // Total number of items (rows)
      var totalPages = Math.ceil(totalItems / selectedValue); // Calculate total pages
  
      // Update pagination
      var pagination = document.querySelector(".pagination");
      pagination.innerHTML = ""; // Clear existing pagination
  
      // Previous button
      var prevLi = document.createElement("li");
      prevLi.className = "page-item";
      var prevLink = document.createElement("a");
      prevLink.className = "page-link";
      prevLink.href = "#";
      prevLink.setAttribute("aria-label", "Previous");
      prevLink.innerHTML =
        '<span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span>';
      prevLi.appendChild(prevLink);
      pagination.appendChild(prevLi);
  
      // Page numbers
      for (var i = 1; i <= totalPages; i++) {
        var li = document.createElement("li");
        li.className = "page-item";
        var link = document.createElement("a");
        link.className = "page-link";
        link.href = "#";
        link.textContent = i;
        li.appendChild(link);
        pagination.appendChild(li);
  
        // Pagination click event
        (function (pageNum) {
          link.addEventListener("click", function (e) {
            e.preventDefault();
            showPage(pageNum, selectedValue);
          });
        })(i);
      }
  
      // Next button
      var nextLi = document.createElement("li");
      nextLi.className = "page-item";
      var nextLink = document.createElement("a");
      nextLink.className = "page-link";
      nextLink.href = "#";
      nextLink.setAttribute("aria-label", "Next");
      nextLink.innerHTML =
        '<span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span>';
      nextLi.appendChild(nextLink);
      pagination.appendChild(nextLi);
  
      showPage(1, selectedValue); // Show first page initially
    }
  
    // Function to show a specific page
    function showPage(pageNum, rowsPerPage) {
      var start = (pageNum - 1) * rowsPerPage;
      var end = start + rowsPerPage;
      var rows = document.querySelectorAll(".table-container table tr");
  
      // Hide all rows, then show the specific slice
      rows.forEach(function (row, index) {
        row.style.display = "none";
        if (index >= start && index < end) {
          row.style.display = "";
        }
      });
    }
  
    // Event listener for dropdown change
    document
      .getElementById("rows-display-dropdown")
      .addEventListener("change", updateDataAndPagination);
  });
  
  //Auto-click sa 20 sa dropdown
  setTimeout(function () {
    document
      .getElementById("rows-display-dropdown")
      .dispatchEvent(new Event("change"));
  }, 100); // Adjust the delay as needed
  