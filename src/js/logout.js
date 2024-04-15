document.getElementById("logoutBtn").addEventListener("click", function () {
  // Display SweetAlert
  Swal.fire({
    text: "Are you sure you want to logout?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "No",
  }).then((result) => {
    if (result.isConfirmed) {
      // If user clicks "Yes, logout!" execute the logout action
      window.location.href = "../../logout.php";
    }
  });
});
