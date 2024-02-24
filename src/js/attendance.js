document.addEventListener("DOMContentLoaded", function () {
  let lastPill = sessionStorage.getItem('lastPillAttendance');

  // PILL
  $(document).ready(function () {

    if(!lastPill) {
      $(".nav-link[data-bs-target='pills-manager']").addClass("active");
      $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
    } else {
      switch(lastPill) {
        case 'manager':
          $(".nav-link[data-bs-target='pills-manager']").addClass("active");
          $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
        break;
        case 'personnel':
          $(".nav-link[data-bs-target='pills-profile']").addClass("active");
          $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
        break;
      }
    }
   

    $(".nav-link").click(function () {
      $(".nav-link").removeClass("active");
      $(this).addClass("active");
    });
  });
})