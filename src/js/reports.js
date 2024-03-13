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
    title: "Assign this personnel?",
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
            title: `${assignedName} has been assigned`,
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
  var currentPage = 1;
  var currentRangeStart = 1;
  var pagesPerRange = 10;

  function initializePaginationForTab(activeTabContent) {
    currentPage = 1;
    currentRangeStart = 1;
    updateDataAndPagination(activeTabContent);
  }

  function updateDataAndPagination(activeTabContent) {
    var activeTabContent = document.querySelector(
      ".tab-pane.active .table-container"
    );
    if (!activeTabContent) return;

    var selectedValue = parseInt(
      document.getElementById("rows-display-dropdown").value
    );
    var rows = activeTabContent.querySelectorAll("table tr:not(.table-header)"); // Exclude header row if present
    var totalItems = rows.length - 1; // Adjust if there's a header row
    var totalPages = Math.ceil(totalItems / selectedValue);

    // Ensure the currentPage does not exceed the number of available pages
    if (currentPage > totalPages) {
      currentPage = totalPages || 1; // Ensure currentPage is not 0 when totalPages is 0
      currentRangeStart = Math.max(
        1,
        (Math.ceil(currentPage / pagesPerRange) - 1) * pagesPerRange + 1
      );
    }

    var pagination = document.querySelector(".pagination");
    pagination.innerHTML = "";
    appendPaginationButtons(pagination, totalPages, selectedValue);
    showPage(currentPage, selectedValue, activeTabContent);
  }

  // Function to reset and initialize pagination when tab is switched
  function resetAndInitializePaginationForTab(tabId) {
    var activeTabContent = document.querySelector(tabId + " .table-container");
    if (activeTabContent) {
      initializePaginationForTab(activeTabContent);
    }
  }

  // Function to create and append pagination buttons
  function appendPaginationButtons(pagination, totalPages, selectedValue) {
    appendButton(
      pagination,
      "Previous",
      "&laquo;",
      Math.max(1, currentPage - 1)
    );

    var endRange = Math.min(currentRangeStart + pagesPerRange - 1, totalPages);
    for (var i = currentRangeStart; i <= endRange; i++) {
      var li = document.createElement("li");
      li.className = "page-item " + (i === currentPage ? "active" : "");
      var link = document.createElement("a");
      link.className = "page-link";
      link.href = "#";
      link.textContent = i;
      (function (pageNum) {
        link.addEventListener("click", function (e) {
          e.preventDefault();
          currentPage = pageNum;
          if (pageNum === endRange && pageNum < totalPages) {
            currentRangeStart = endRange + 1;
          } else if (pageNum === currentRangeStart && pageNum > 1) {
            currentRangeStart = Math.max(1, currentRangeStart - pagesPerRange);
          }
          updateDataAndPagination();
        });
      })(i);
      li.appendChild(link);
      pagination.appendChild(li);
    }

    appendButton(
      pagination,
      "Next",
      "&raquo;",
      Math.min(totalPages, currentPage + 1)
    );
  }

  function appendButton(pagination, ariaLabel, symbol, page) {
    var li = document.createElement("li");
    li.className = "page-item " + (page === currentPage ? "disabled" : "");
    var link = document.createElement("a");
    link.className = "page-link";
    link.href = "#";
    link.setAttribute("aria-label", ariaLabel);
    link.innerHTML = symbol;
    link.addEventListener("click", function (e) {
      e.preventDefault();
      if (page !== currentPage) {
        currentPage = page;
        updateDataAndPagination();
      }
    });
    li.appendChild(link);
    pagination.appendChild(li);
  }

  // Function to show a specific page
  function showPage(pageNum, rowsPerPage, activeTabContent) {
    var start = (pageNum - 1) * rowsPerPage;
    var end = start + rowsPerPage;
    var rows = activeTabContent.querySelectorAll("table tr");
    rows.forEach(function (row, index) {
      row.style.display = "none";
      if (index >= start && index < end) {
        row.style.display = "";
      }
    });

    // Update the active class for pagination
    var paginationLinks = document.querySelectorAll(".pagination .page-item a");
    paginationLinks.forEach(function (link) {
      var parentLi = link.parentElement;
      parentLi.classList.remove("active");
      if (link.textContent == pageNum.toString()) {
        parentLi.classList.add("active");
        link.style.backgroundColor = "your-color-here"; // Apply your desired color
      } else {
        link.style.backgroundColor = ""; // Reset other buttons' color
      }
    });

    // Disable Previous button if on the first page
    var prevButton = document.querySelector(
      '.pagination .page-item.dynamic-button a[aria-label="Previous"]'
    );
    if (prevButton) {
      if (pageNum === 1) {
        prevButton.parentElement.classList.add("disabled");
      } else {
        prevButton.parentElement.classList.remove("disabled");
      }
    }

    // Disable Next button if on the last page
    var nextButton = document.querySelector(
      '.pagination .page-item.dynamic-button a[aria-label="Next"]'
    );
    if (nextButton) {
      if (pageNum === totalPages) {
        nextButton.parentElement.classList.add("disabled");
      } else {
        nextButton.parentElement.classList.remove("disabled");
      }
    }
  }

  // Event listener for dropdown change

  document
    .getElementById("rows-display-dropdown")
    .addEventListener("change", function () {
      // Reset to the first page
      currentPage = 1;
      currentRangeStart = 1;
      updateDataAndPagination(
        document.querySelector(".tab-pane.active .table-container")
      );
    });

  //Auto-click on the dropdown to initialize the display
  setTimeout(function () {
    document
      .getElementById("rows-display-dropdown")
      .dispatchEvent(new Event("change"));
  }, 100);

  // Listen for tab changes and update content accordingly
  var tabs = document.querySelectorAll(".nav-link");
  tabs.forEach(function (tab) {
    tab.addEventListener("click", function () {
      var tabId = "#" + this.getAttribute("aria-controls"); // Modify to match the correct tab ID

      // Reset the pagination for the newly activated tab
      resetAndInitializePaginationForTab(tabId);

      // Change dropdown to "dummy" option and revert to default value after a short delay
      var rowsDropdown = document.getElementById("rows-display-dropdown");
      rowsDropdown.value = ""; // Dummy option to trigger the change event

      setTimeout(function () {
        rowsDropdown.value = "20"; // Default value or last selected value
        rowsDropdown.dispatchEvent(new Event("change")); // Trigger the change event manually
      }, 100); // Adjust the timeout as needed
    });
  });

  // This function will be triggered when a new tab is clicked.
  function resetAndInitializePaginationForTab(tabId) {
    var activeTabContent = document.querySelector(tabId + " .table-container");
    currentPage = 1;
    currentRangeStart = 1;
    updateDataAndPagination(activeTabContent); // You need to pass the correct tab content to your existing update function
  }
});
