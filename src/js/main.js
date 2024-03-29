// const allSideMenu = document.querySelectorAll("#sidebar .side-menu.top li a");
// const mobileSideMenu = document.querySelectorAll("#sidebar .side-menu.top li a");

// allSideMenu.forEach((item) => {
//   const li = item.parentElement;

//   item.addEventListener("click", function () {
//     allSideMenu.forEach((i) => {
//       i.parentElement.classList.remove("active");
//     });
//     li.classList.add("active");
//   });
// });

// TOGGLE SIDEBAR
const menuBar = document.querySelector("#navbar nav .bi.bi-list");
const mobileMenuBar = document.querySelector("#sidebar .brand .mobile-sidebar-close .bi-arrow-left-circle");
const sidebar = document.getElementById("sidebar");
const hamburger = document.getElementById("navbar");
const content = document.getElementById("content");
const desktopProfile = document.getElementById("desktop")
const mobileProfile = document.getElementById("mobile")

// Function to close the sidebar
function closeSidebar() {
  sidebar.classList.add("hide");
  hamburger.classList.add("hide");
  content.classList.add("hide");
}

function openSidebar() {
  sidebar.classList.remove("hide");
  hamburger.classList.remove("hide");
  content.classList.remove("hide");
}

// // Initial state based on screen width
// if (window.innerWidth <= 992) {
//   closeSidebar();
// } else {
//   openSidebar();
// }

menuBar.addEventListener("click", function () {
  if (sidebar.classList.contains("hide")) {
    openSidebar();
  } else {
    closeSidebar();
  }
});

mobileMenuBar.addEventListener("click", function () {
  if (sidebar.classList.contains("hide")) {
    openSidebar();
  } else {
    closeSidebar();
  }
});

// Add a resize event listener to toggle the sidebar and close the modal based on screen width
// window.addEventListener("resize", function () {
//   if (window.innerWidth >= 992) {
//     openSidebar();
//     // Close the modal if it's open
//     const modal = document.querySelector(".modal");
//     if (modal) {
//       modal.style.display = "none";
//     }
//   } else {
//     closeSidebar();
//   }
// });

document.addEventListener("DOMContentLoaded", function () {
  const notificationButton = document.getElementById("notification-button");
  const notificationDropdown = document.getElementById(
    "notification-dropdown-content"
  );
  const settingsButton = document.querySelector(".profile-container");
  const settingsDropdown = document.getElementById("settings-dropdown");

  // Function to close both dropdowns
  function closeDropdowns() {
    notificationDropdown.style.display = "none";
    settingsDropdown.style.display = "none";
  }

  // Toggle the notification dropdown
  notificationButton.addEventListener("click", function (event) {
    event.stopPropagation();
    if (notificationDropdown.style.display === "block") {
      notificationDropdown.style.display = "none";
    } else {
      closeDropdowns(); // Close settings dropdown
      notificationDropdown.style.display = "block";
    }
  });

  // Toggle the settings dropdown
  settingsButton.addEventListener("click", function (event) {
    event.stopPropagation();
    if (settingsDropdown.style.display === "block") {
      settingsDropdown.style.display = "none";
    } else {
      closeDropdowns(); // Close notification dropdown
      settingsDropdown.style.display = "block";
    }
  });

  // Close both dropdowns when clicking outside of them
  document.addEventListener("click", function () {
    closeDropdowns();
  });

  // Prevent closing the dropdowns when clicking inside them
  notificationDropdown.addEventListener("click", function (event) {
    event.stopPropagation();
  });

  settingsDropdown.addEventListener("click", function (event) {
    event.stopPropagation();
  });

  // Rest of your code...
});


// Add a click event listener to the logout link
document.getElementById('logoutBtn').addEventListener('click', function() {
  // Display SweetAlert
  Swal.fire({
      text: 'Are you sure you want to logout?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
  }).then((result) => {
      if (result.isConfirmed) {
          // If user clicks "Yes, logout!" execute the logout action
          window.location.href = '../../logout.php';
      }
  });
});

document.addEventListener('DOMContentLoaded', function() {
    const closeFloorIcons = document.querySelectorAll('.closeFloor');
    const modelContainer = document.getElementById('model-container');
    const buildings = document.getElementById('buildings');
    const contentSections = document.querySelectorAll('.content');

    closeFloorIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                buildings.style.display = 'flex';
            } else {
                modelContainer.style.display = 'block';
            }
            contentSections.forEach(content => {
                content.classList.remove('active-content');
            });
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const modelContainer = document.getElementById('model-container');
    const buildings = document.getElementById('buildings');
    const contentSections = document.querySelectorAll('.content');
    const activeContentClass = 'active-content'; // Class used to denote active content
    const closeFloorIcons = document.querySelectorAll('.closeFloor'); // Assuming this is the class for your close buttons

    function updateVisibility() {
        const hasActiveContent = document.querySelector(`.${activeContentClass}`);
        const isMobileView = window.innerWidth <= 768;

        if (hasActiveContent) {
            contentSections.forEach(content => {
                if (content.classList.contains(activeContentClass)) {
                    content.style.display = 'block'; // Show active content
                } else {
                    content.style.display = 'none'; // Hide other content sections
                }
            });

            modelContainer.style.display = 'none';
            buildings.style.display = 'none';
        } else {
            // No active content, decide based on screen size
            // modelContainer.style.display = isMobileView ? 'none' : 'block';
            // buildings.style.display = isMobileView ? 'flex' : 'none';
        }
    }

    // Handle closing of active content
    closeFloorIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            // Remove active-content class from all sections
            contentSections.forEach(content => {
                content.classList.remove(activeContentClass);
            });

            updateVisibility(); // Update visibility based on new state
        });
    });

    // Initial check and update on page load
    updateVisibility();

    // Update visibility on window resize
    window.addEventListener('resize', updateVisibility);
});


const tabButtons = document.querySelectorAll('.nav-link');
const contentSections = document.querySelectorAll('.content');
const modelContainer = document.getElementById('model-container');
const mobileModelContainer = document.getElementById('buildings');
const modalClose1 = document.getElementById('myModal1');
const modalClose2 = document.getElementById('myModal2');
const modalClose3 = document.getElementById('myModal3');
const modalClose4 = document.getElementById('myModal4');
const modalClose5 = document.getElementById('myModal5');
const modalClose6 = document.getElementById('myModal6');
const modalClose7 = document.getElementById('myModal7');
const modalClose8 = document.getElementById('myModal8');
const modalClose9 = document.getElementById('myModal9');
const modalClose10 = document.getElementById('myModal10');
const modalClose11 = document.getElementById('myModal11');

function handleTabClick(targetContentId) {
    modelContainer.style.display = 'none';
    mobileModelContainer.style.display = 'none';
    modalClose1.style.display = 'none';
    modalClose2.style.display = 'none';
    modalClose3.style.display = 'none';
    modalClose4.style.display = 'none';
    modalClose5.style.display = 'none';
    modalClose6.style.display = 'none';
    modalClose7.style.display = 'none';
    modalClose8.style.display = 'none';
    modalClose9.style.display = 'none';
    modalClose10.style.display = 'none';
    modalClose11.style.display = 'none';

    contentSections.forEach(content => {
        content.classList.remove('active-content');
    });

    const targetContent = document.querySelector(targetContentId);
    targetContent.classList.add('active-content');
}

tabButtons.forEach(button => {
    button.addEventListener('click', function() {
        const targetContentId = this.getAttribute('data-floor-target');
        handleTabClick(targetContentId);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const buildings = document.querySelectorAll('.building');
    const modals = [
        document.getElementById('myModal1'),
        document.getElementById('myModal2'),
        document.getElementById('myModal3'),
        document.getElementById('myModal4'),
        document.getElementById('myModal5'),
        document.getElementById('myModal6'),
        document.getElementById('myModal7'),
        document.getElementById('myModal8'),
        document.getElementById('myModal9'),
        document.getElementById('myModal10'),
        document.getElementById('myModal11')
    ];

    buildings.forEach((building, index) => {
        building.addEventListener('click', () => {
            modals.forEach(modal => {
                if (modal) {
                    modal.style.display = 'none';
                }
            });

            if (modals[index]) {
                modals[index].style.display = 'block';
            }
        });
    });

    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach((closeButton, index) => {
        closeButton.addEventListener('click', () => {
            modals[index].style.display = 'none';
        });
    });
});

function togglePassword() {
  var passwordField = document.getElementById("passwordEditSelf");
  var toggleIcon = document.getElementById("togglePassword");

  if (passwordField.type === "password") {
      passwordField.type = "text";
      toggleIcon.classList.remove("bi-eye-slash");
      toggleIcon.classList.add("bi-eye");
  } else {
      passwordField.type = "password";
      toggleIcon.classList.remove("bi-eye");
      toggleIcon.classList.add("bi-eye-slash");
  }
}

document.addEventListener("DOMContentLoaded", function() {
  const button = document.getElementById("legendButton");
  const legendBody = document.getElementById("legendBody");

  // Hide legendBody by default
  legendBody.style.display = "none";

  button.addEventListener("click", function() {
      // Toggle the visibility of the legend body
      legendBody.style.display = legendBody.style.display === "none" ? "block" : "none";
  });
});






