const locTbl = document.getElementById("locationTbl");

function showLocation(){
    if (locTbl.style.display === 'none') {
        locTbl.style.display = 'block';
      } else {
        locTbl.style.display = 'none';
      }
}

function handleResize() {
    var currentWidth = window.innerWidth;
  
    // Check the width and add/remove the style accordingly
    if (currentWidth <= 600) {
        locTbl.style.display = 'none';
    } else {
        locTbl.style.display = ''; // Set to an empty string to remove inline style
    }
  }
  
  // Attach the handleResize function to the resize event
  window.addEventListener('resize', handleResize);
  
  // Initial call to handleResize to set the initial state
  handleResize();
  