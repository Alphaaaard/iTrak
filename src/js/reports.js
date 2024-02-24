function confirmAlert(reportType) {

    Swal.fire({
        icon: 'info',
        text: 'Do you want to save changes?',
        confirmButtonText: 'Yes',
        showCancelButton: true,
        cancelButtonText: 'No',
    }).then((result) => {

        if(result.isConfirmed) {
            let formData;

            switch(reportType) {
                case 'working':
                    formData = new FormData(document.querySelector('#workingForm'));
                break;
                case 'maintenance':
                    formData = new FormData(document.querySelector('#maintenanceForm'));
                break;
                case 'replace':
                    formData = new FormData(document.querySelector('#replacementForm'));
                break;
                case 'repair':
                    formData = new FormData(document.querySelector('#repairForm'));
                break;
            }

            $.ajax({
                url: "../../users/administrator/reports.php",
                data: formData,
                type: 'POST',
                processData: false,
                contentType: false,
                success: function(res) {

                    Swal.fire({
                        title: "Changes Saved Successfully!",
                        icon: "success",
                        timer: 1000, 
                        showConfirmButton: false,
                    }).then((result) => {

                        if(result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload();
                        }

                    });

                }
            });
        }
    })
}

function assignPersonnel() {
    Swal.fire({
        icon: 'info',
        title: 'Assigned this personnel?',
        confirmButtonText: 'Yes',
        showCancelButton: true,
        cancelButtonText: 'No'
    }).then((result)=>{

        if(result.isConfirmed) {
            let formData = new FormData(document.querySelector('#assignPersonnelForm'));

            $.ajax({
                url: "../../users/administrator/reports.php",
                data: formData,
                type: 'POST',
                processData: false,
                contentType: false,
                success: function(res) {

                    Swal.fire({
                        title: "Maintenance Personnel has been assign",
                        icon: "success",
                        timer: 1000,
                        showConfirmButton: false,
                    })
                    .then((result) => {
    
                        if(result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload();
                        }
    
                    });
                }
            });
        }
        
    });
}