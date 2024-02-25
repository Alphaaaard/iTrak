function updateCalendar(date) {
  currentDate1 = date; // Assuming 'currentDate1' is the variable used to store the current date in your calendar rendering logic
  renderCalendar1(); // Call the function that renders the calendar
}

// This function is responsible for clearing the content of building divs
function clearBuildingDivs() {
  const buildings = [
    "techVoc",
    "oldAcad",
    "belmonte",
    "metalcasting",
    "korphil",
    "multipurpose",
    "chineseA",
    "chineseB",
    "urbanFarming",
    "administration",
    "bautista",
    "newAcad",
  ];
  buildings.forEach((buildingId) => {
    const buildingDiv = document.getElementById(buildingId);
    if (buildingDiv) {
      buildingDiv.textContent = ""; // Clear the previous content
    }
  });
}
//-------------------
$(document).ready(function () {
  $(".clickMe, .clickMe.span-label-2").click(function () {
    // Ensure both month and year span elements can trigger the datepicker
    $("#datepicker")
      .datepicker({
        dateFormat: "MM yy",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        onClose: function (dateText, inst) {
          var month = $(
            "#ui-datepicker-div .ui-datepicker-month :selected"
          ).val();
          var year = $(
            "#ui-datepicker-div .ui-datepicker-year :selected"
          ).val();
          $(this).val(
            $.datepicker.formatDate("MM yy", new Date(year, month, 1))
          );
          $(this).datepicker("setDate", new Date(year, month, 1));

          updateCalendar(new Date(year, month, 1)); // Update the main calendar to reflect the chosen month and year
          updateCurrentDateDisplay(new Date(year, month, 1)); // Update any displays or elements that show the currently selected date

          setTimeout(function () {
            // Find the first day in the `.date-grid` for the selected month and year and click it
            $(".date-grid .date:not(.empty)").first().click();
          }, 100); // Delay to ensure the date-grid has been updated
        },
      })
      .focus(); // Open the datepicker
  });
});

function updateCurrentDateDisplay(date) {
  const dayNames = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  const dayOfWeek = dayNames[date.getDay()];
  const month = monthNames[date.getMonth()];
  const year = date.getFullYear();
  const day = date.getDate();

  // Update the month, year, and day of week
  $(".month-indicator .month").text(month);
  $(".month-indicator .year").text(year);

  // Update the element with id 'current-date' to show the full date with day of week
  $("#current-date").text(`${dayOfWeek}, ${month} ${day}, ${year}`);
}
//------------------------------------------
// This function checks if the selected date is today's date
function isToday(date) {
  const today = new Date();
  today.setHours(0, 0, 0, 0); // Ignore time part
  date.setHours(0, 0, 0, 0); // Ignore time part
  return date.getTime() === today.getTime();
}

// This function updates the visibility of schedule data based on the selected date
function updateScheduleVisibility(selectedDate) {
  // Check if the selected date is not today
  if (!isToday(selectedDate)) {
    // Hide or show elements accordingly
    $(".table-container").hide();
    $(".no-data-message").show(); // You should add this element to your HTML to display when there's no data
  } else {
    $(".table-container").show();
    $(".no-data-message").hide();
  }
}

// Call this function whenever a new date is selected in your calendar
updateScheduleVisibility(new Date()); // Initialize with the current date

//-----------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------------
//Script para sa calendar date selection sa building divs
const currentDateElement = document.getElementById("current-date");
const dateInputElement = document.getElementById("date-input");

let currentDate = new Date();

function renderCalendar() {
  currentDateElement.textContent = formatDate(currentDate);
}

function formatDate(date) {
  // Example format: Monday, January 1, 2024
  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  return date.toLocaleDateString("en-US", options);
}

renderCalendar();

//----------------------------------------------------------------------------------------------------------------------------------
//For Calendar (2)
const monthNames = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];
let currentDate1 = new Date();

function renderCalendar1() {
  const monthIndicator = document.querySelector(
    ".calendar .month-indicator .month"
  );
  const yearIndicator = document.querySelector(
    ".calendar .month-indicator .year"
  );
  const dateGrid = document.querySelector(".calendar .date-grid");

  monthIndicator.textContent = monthNames[currentDate1.getMonth()];
  yearIndicator.textContent = currentDate1.getFullYear();

  // Clear previous dates
  dateGrid.innerHTML = "";

  const firstDayOfMonth = new Date(
    currentDate1.getFullYear(),
    currentDate1.getMonth(),
    1
  );
  const lastDayOfMonth = new Date(
    currentDate1.getFullYear(),
    currentDate1.getMonth() + 1,
    0
  );

  // Get the day of the week for the first day of the month
  const firstDayOfWeek = firstDayOfMonth.getDay();

  // Create blank days to align the first day of the month with the correct day of the week
  for (let i = 0; i < firstDayOfWeek; i++) {
    const blankDayDiv = document.createElement("div");
    blankDayDiv.className = "date empty"; // Add a class for styling if needed
    dateGrid.appendChild(blankDayDiv);
  }

  // Fill in the days of the month
  for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
    const dateDiv = document.createElement("div");
    dateDiv.textContent = i;
    dateDiv.className = "date"; // Add a class for styling if needed
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset the time part for accurate comparison

    // Highlight the current day
    if (
      i === today.getDate() &&
      currentDate1.getMonth() === today.getMonth() &&
      currentDate1.getFullYear() === today.getFullYear()
    ) {
      dateDiv.classList.add("today");
    }

    // Add event listener for each date
    dateDiv.addEventListener("click", function () {
      // Remove 'selected' class from all dates
      document.querySelectorAll(".date").forEach(function (el) {
        el.classList.remove("selected");
      });

      // Add 'selected' class to the clicked date
      dateDiv.classList.add("selected");

      const selectedDate = new Date(
        currentDate1.getFullYear(),
        currentDate1.getMonth(),
        i
      );
      updateSelectedDate(selectedDate);
      updateScheduleVisibility(selectedDate);
    });

    dateGrid.appendChild(dateDiv);
  }

  // This function updates the divs with data for the selected date
  function updateSelectedDate(selectedDate) {
    // Format the date to YYYY-MM-DD
    const formattedDate = [
      selectedDate.getFullYear(),
      String(selectedDate.getMonth() + 1).padStart(2, "0"),
      String(selectedDate.getDate()).padStart(2, "0"),
    ].join("-");
    currentDateElement.textContent = formatDate(selectedDate);
    // Clear the divs before inserting new data
    clearBuildingDivs();

    // AJAX call to fetch data based on selected date
    fetch("dashboard_fetch_schedule.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=fetch_schedule&date=${formattedDate}`,
    })
      .then((response) => response.json())
      .then((data) => {
        // Append new data to each building div
        data.forEach((schedule) => {
          Object.keys(schedule).forEach((building) => {
            if (building !== "date" && building !== "sbId") {
              const buildingDiv = document.getElementById(building);
              if (buildingDiv) {
                // Check if the value is not just non-null, but also not an empty string or whitespace
                const value = schedule[building].trim(); // Remove whitespace from both ends of the string
                if (value) {
                  // Append names with a line break, using innerHTML to parse the <br> tag
                  buildingDiv.innerHTML += value + "<br>";
                }
              }
            }
          });
        });
      })
      .catch((error) => {
        console.error("Error:", error);
      });

    //Make the divs building-body non-clickable kaya dont change it ok?
    const today = new Date();
    const isSelectedDateToday =
      selectedDate.getDate() === today.getDate() &&
      selectedDate.getMonth() === today.getMonth() &&
      selectedDate.getFullYear() === today.getFullYear();

    // Get all building-body elements
    const buildingBodies = document.querySelectorAll(".building-body");

    // Add or remove the non-clickable class based on whether the selected date is today
    buildingBodies.forEach((element) => {
      if (isSelectedDateToday) {
        element.classList.remove("non-clickable");
        // Re-attach event listener if needed, or handle it through delegation
      } else {
        element.classList.add("non-clickable");
        // Remove event listener if they were individually attached
      }
    });
  }

  // When you initially render the calendar, you should also set up the correct state
  // for the building-body elements based on today's date
  updateSelectedDate(new Date()); // Call this function on initial load
}

// Call the function to render the calendar
renderCalendar1();

//----------------------------------------------------------------------------------------------------------------------------------
//Pagination Script
const buildings = document.querySelectorAll(".building");
const buildingsPerPage = 4;
let currentPage = 1;

function showBuildings(page) {
  currentPage = page;

  const totalPages = Math.ceil(buildings.length / buildingsPerPage);
  currentPage = Math.min(Math.max(currentPage, 1), totalPages); // Ensure currentPage is within a valid range

  const startIndex = (currentPage - 1) * buildingsPerPage;
  const endIndex = startIndex + buildingsPerPage;

  buildings.forEach((building, index) => {
    if (index >= startIndex && index < endIndex) {
      building.style.display = "block";
    } else {
      building.style.display = "none";
    }
  });

  updatePaginationButtons(); // Update pagination buttons after changing the page
}

function updatePaginationButtons() {
  const totalPages = Math.ceil(buildings.length / buildingsPerPage);

  const prevButton = document.querySelector(".pagination li:nth-child(1) a");
  const nextButton = document.querySelector(".pagination li:last-child a");

  prevButton.classList.toggle("disabled", currentPage === 1);
  nextButton.classList.toggle("disabled", currentPage === totalPages);

  // Remove the 'active' class from all page buttons
  document
    .querySelectorAll(".pagination li:not(:first-child):not(:last-child)")
    .forEach((pageLi) => {
      pageLi.classList.remove("active");
    });

  // Add the 'active' class to the selected page button
  document
    .querySelector(`.pagination li:nth-child(${currentPage + 1})`)
    .classList.add("active");
}

// Show the initial page
showBuildings(1); // Start with page 1

// Add event listeners for page buttons
document
  .querySelectorAll(".pagination li:not(:first-child):not(:last-child) a")
  .forEach((pageLink, index) => {
    pageLink.addEventListener("click", () => showBuildings(index + 1));
  });

// Add event listeners for previous and next buttons
document
  .querySelector(".pagination li:nth-child(1) a")
  .addEventListener("click", () => showBuildings(currentPage - 1));
document
  .querySelector(".pagination li:last-child a")
  .addEventListener("click", () => showBuildings(currentPage + 1));

//----------------------------------------------------------------------------------------------------------------------------------
//JS PARA SA DELETE
let assignedEmployees = [];

function removeAssigned(sbId) {
  $.ajax({
    url: "dashboard_delete_schedule.php", // The script to call to delete data
    type: "POST",
    data: { sbId: sbId },
    success: function (response) {
      // On success, remove the row from the table and hide the modal
      // $(".pending-deletion").remove(); // Remove rows marked for deletion
      console.log(response.responseText);
    },
    error: function (xhr, status, error) {
      // On error, log the error and optionally display a message to the user
      console.error("Error: " + error);
      alert("An error occurred while deleting the record.");
    },
  });
  // alert(sbId+" was deleted hehe.");
}

$(document).ready(function () {
  // Bind click event to delete button in each row
  $("body").on("click", ".new-delete-btn", function () {
    let sbId = $(this).data("sbid"); // Get sbId from the button's data attribute
    assignedEmployees.push(sbId);

    // Hide the closest <tr> element
    $(this).closest("tr").hide();

    // Store the sbId in the confirmation button for later use if needed
    // $(".confirm-delete-btn").data("sbid", sbId);
  });

  // // Event binding for the confirmation button in the modal
  // $(".confirm-delete-btn").click(function () {
  //   var sbId = $(this).data("sbid"); // Get sbId from the confirmation button's data attribute

  //   // Make an AJAX call to delete data
  //   $.ajax({
  //     url: "dashboard_delete_schedule.php", // The script to call to delete data
  //     type: "POST",
  //     data: { sbId: sbId },
  //     success: function (response) {
  //       // On success, remove the row from the table and hide the modal
  //       $(".pending-deletion").remove(); // Remove rows marked for deletion
  //     },
  //     error: function (xhr, status, error) {
  //       // On error, log the error and optionally display a message to the user
  //       console.error("Error: " + error);
  //       alert("An error occurred while deleting the record.");
  //     },
  //   });
  // });
});

//-------------------------------------------------------------------------------------------------------------------------------
//JS PARA SA PAGDISABLE SA MGA DATA NA ALREADY ON THE TABLE WITHIN THAT DAY
function disableOptionsInDropdown() {
  // Get all the options in the select dropdown for Tech-Voc and Old Acad
  const selectOptionstechVoc = document.querySelectorAll("#techVoc option");
  const selectOptionsoldAcad = document.querySelectorAll("#oldAcad option");
  const selectOptionsbelmonte = document.querySelectorAll("#belmonte option");
  const selectOptionsmetalcasting = document.querySelectorAll(
    "#metalcasting option"
  );
  const selectOptionskorphil = document.querySelectorAll("#korphil option");
  const selectOptionsmultipurpose = document.querySelectorAll(
    "#multipurpose option"
  );
  const selectOptionschineseA = document.querySelectorAll("#chineseA option");
  const selectOptionschineseB = document.querySelectorAll("#chineseB option");
  const selectOptionsurbanFarming = document.querySelectorAll(
    "#urbanFarming option"
  );
  const selectOptionsadministration = document.querySelectorAll(
    "#administration option"
  );
  const selectOptionsbautista = document.querySelectorAll("#bautista option");
  const selectOptionsnewAcad = document.querySelectorAll("#newAcad option");

  // Function to disable options based on table data and the date
  const disableOptions = (selectOptions, tableData, buildingName) => {
    const today = new Date().toISOString().slice(0, 10);

    selectOptions.forEach((option) => {
      const optionValue = option.textContent.trim();
      const isAssignedToday = tableData.some(
        (entry) =>
          entry.date === today && entry[buildingName].trim() === optionValue
      );

      option.disabled = isAssignedToday;
    });
  };

  // Call fetchTableData for each dropdown and use the new disableOptions function
  fetchTableData("techVoc")
    .then((tableDatatechVoc) => {
      disableOptions(selectOptionstechVoc, tableDatatechVoc, "techVoc");
    })
    .catch((error) => {
      console.error("Error fetching Tech-Voc table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("oldAcad")
    .then((tableDataoldAcad) => {
      disableOptions(selectOptionsoldAcad, tableDataoldAcad, "oldAcad");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("belmonte")
    .then((tableDatabelmonte) => {
      disableOptions(selectOptionsbelmonte, tableDatabelmonte, "belmonte");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("belmonte")
    .then((tableDatabelmonte) => {
      disableOptions(selectOptionsbelmonte, tableDatabelmonte, "belmonte");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("metalcasting")
    .then((tableDatametalcasting) => {
      disableOptions(
        selectOptionsmetalcasting,
        tableDatametalcasting,
        "metalcasting"
      );
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("korphil")
    .then((tableDatakorphil) => {
      disableOptions(selectOptionskorphil, tableDatakorphil, "korphil");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("multipurpose")
    .then((tableDatamultipurpose) => {
      disableOptions(
        selectOptionsmultipurpose,
        tableDatamultipurpose,
        "multipurpose"
      );
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("chineseA")
    .then((tableDatachineseA) => {
      disableOptions(selectOptionschineseA, tableDatachineseA, "chineseA");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("chineseB")
    .then((tableDatachineseB) => {
      disableOptions(selectOptionschineseB, tableDatachineseB, "chineseB");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("urbanFarming")
    .then((tableDataurbanFarming) => {
      disableOptions(
        selectOptionsurbanFarming,
        tableDataurbanFarming,
        "urbanFarming"
      );
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("administration")
    .then((tableDataadministration) => {
      disableOptions(
        selectOptionsadministration,
        tableDataadministration,
        "administration"
      );
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("bautista")
    .then((tableDatabautista) => {
      disableOptions(selectOptionsbautista, tableDatabautista, "bautista");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });

  // Repeat for other buildings
  fetchTableData("newAcad")
    .then((tableDatanewAcad) => {
      disableOptions(selectOptionsnewAcad, tableDatanewAcad, "newAcad");
    })
    .catch((error) => {
      console.error("Error fetching Old Acad table data:", error);
    });
}

// Function to fetch table data for a specific column via AJAX
function fetchTableData(columnName) {
  return fetch("dashboard_fetch_table_data.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `column=${columnName}`, // Send the column name as a parameter to the server
  })
    .then((response) => response.json())
    .catch((error) => {
      console.error(`Error fetching ${columnName} table data:`, error);
      return []; // Return empty array in case of error
    });
}

// Call the function initially to disable options based on current table data
disableOptionsInDropdown();
//--------------------

//*sweet alert
let buildingSelected = "";

//* fetching building selected (for adding personnel purposes)
//* then used later for deterimining which form to use.
function setBuilding(buildingName) {
  buildingSelected = buildingName;
}

function confirmAlert() {
  Swal.fire({
    icon: "info",
    title: "Are you sure you want to save changes?",
    showCancelButton: true,
    cancelButtonText: "No",
    focusConfirm: false,
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      for (let i = 0; i < assignedEmployees.length; i++) {
        removeAssigned(assignedEmployees[i]);
      }

      let formData;

      switch (buildingSelected) {
        case "techvoc":
          formData = new FormData(document.querySelector("#techVocForm"));
          break;
        case "oldacad":
          formData = new FormData(document.querySelector("#oldacadForm"));
          break;
        case "belmonte":
          formData = new FormData(document.querySelector("#belmonteForm"));
          break;
        case "metalcasting":
          formData = new FormData(document.querySelector("#metalcastingForm"));
          break;
        case "korphil":
          formData = new FormData(document.querySelector("#korphilForm"));
          break;
        case "bautista":
          formData = new FormData(document.querySelector("#bautistaForm"));
          break;
        case "multipurpose":
          formData = new FormData(document.querySelector("#multipurposeForm"));
          break;
        case "newacad":
          formData = new FormData(document.querySelector("#newacadForm"));
          break;
        case "administration":
          formData = new FormData(
            document.querySelector("#administrationForm")
          );
          break;
        case "chineseA":
          formData = new FormData(document.querySelector("#chineseAForm"));
          break;
        case "urbanfarming":
          formData = new FormData(document.querySelector("#urbanFarmingForm"));
          break;
        case "chineseB":
          formData = new FormData(document.querySelector("#chineseBForm"));
          break;
      }

      const building = buildingSelected;

      //* AJAX call for adding schedule
      $.ajax({
        url: "../../users/administrator/dashboard_add_schedule.php",
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (res) {
          Swal.fire({
            title: `Maintenance Personnel has been assigned to ${building}`,
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

//Para sa today button
document.querySelector(".today-btn").addEventListener("click", function () {
  const today = new Date();
  updateCalendar(today); // Adjusts the calendar to show the current month and year
});
//---------------------------------
//Para pagwalang naka-assign naka hide yung section for Assigned Personnel
document.addEventListener("DOMContentLoaded", function () {
  // Check if the 'No personnel assigned for today.' message exists
  var noDataMessages = document.querySelectorAll(".no-data-message");
  noDataMessages.forEach(function (message) {
    if (message.textContent.trim() === "No personnel assigned for today.") {
      // If the message is found, hide the parent 'table-personnel' section
      message.closest(".table-personnel").style.display = "none";
    }
  });
});
