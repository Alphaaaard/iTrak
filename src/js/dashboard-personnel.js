function updateCalendar(date) {
  currentDate1 = date; // Assuming 'currentDate1' is the variable used to store the current date in your calendar rendering logic
  renderCalendar1(); // Call the function that renders the calendar
}

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

  // Clear the existing building divs
  buildings.forEach((buildingId) => {
    const buildingDiv = document.getElementById(buildingId);
    if (buildingDiv) {
      buildingDiv.textContent = ""; // Clear the previous content
    }
  });

  // Clear the personnel-building div
  const personnelBuildingDiv = document.querySelector(".personnel-building");
  if (personnelBuildingDiv) {
    personnelBuildingDiv.textContent = ""; // Clear the content
  }
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
      updateCurrentDateDisplay(
        new Date(currentDate1.getFullYear(), currentDate1.getMonth(), i)
      );
    });

    dateGrid.appendChild(dateDiv);
  }

  // This function updates the divs with data for the selected date
  function updateSelectedDate(selectedDate) {
    // Create a new date object with the UTC date corresponding to the local date selected.
    const dateWithUTC = new Date(
      Date.UTC(
        selectedDate.getFullYear(),
        selectedDate.getMonth(),
        selectedDate.getDate()
      )
    );
    // Format the date to YYYY-MM-DD
    const formattedDate = dateWithUTC.toISOString().split("T")[0];

    // Mapping of building codes to full building names
    const buildingNameMap = {
      techVoc: "Tech-Voc Building",
      oldAcad: "Yellow Building",
      belmonte: "Belmonte Building",
      metalcasting: "Metal Casting",
      korphil: "KorPhil Building",
      multipurpose: "Multipurpose Building",
      chineseA: "Chinese A Building",
      chineseB: "Chinese B Building",
      urbanFarming: "Urban Farming",
      administration: "Administration Building",
      bautista: "Bautista Building",
      newAcad: "New Academic Building",
    };

    // Clear the 'personnel-building' div before inserting new data
    const personnelBuildingDiv = document.querySelector(".personnel-building");
    personnelBuildingDiv.innerHTML = "";

    // AJAX call to fetch schedule data
    fetch("dashboard_fetch_schedule.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `date=${formattedDate}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.length > 0) {
          data.forEach((buildingCode) => {
            // Create a paragraph for each building and append to div
            const pElement = document.createElement("p");
            pElement.textContent =
              buildingNameMap[buildingCode] || buildingCode; // Display the full building name
            personnelBuildingDiv.appendChild(pElement);
          });
        } else {
          personnelBuildingDiv.textContent = "No schedule for today.";
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  // Call this function on initial load to populate with today's date
  updateSelectedDate(new Date());
}

// Call the function to render the calendar
renderCalendar1();

//----------------------------------------------------------------------------------------------------------------------------------
document.querySelector(".today-btn").addEventListener("click", function () {
  const today = new Date(); // Get today's date
  updateCalendar(today); // Update the calendar to today's date
  updateCurrentDateDisplay(today); // Update the current-date span to today's date
});
//----------------------------------------------------------------------------------------------------------------------------------
