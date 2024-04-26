function getUser(accountId) {
    let xhr = new XMLHttpRequest(); //prepares the xmlhttprequest

    //check if status is OK for request
    xhr.onload  = function() {
        if(this.status == 200) {
            
            let user = JSON.parse(this.responseText);

            document.querySelector('#accountIdEdit').value = user.accountId;
            document.querySelector('#firstnameEdit').value = user.firstName;
            document.querySelector('#middlenameEdit').value = user.middleName;
            document.querySelector('#lastnameEdit').value = user.lastName;
            document.querySelector('#contactEdit').value = user.contact;
            document.querySelector('#emailEdit').value = user.email;
            document.querySelector('#passwordEdit').value = user.password;
            document.querySelector('#birthdayEdit').value = user.birthday;
            document.querySelector('#roleEdit').value = user.role;
            document.querySelector('#expertiseEdit').value = user.expertise;
        } else {
            console.log('Error: ' + this.status);
        }
    }

    xhr.open('GET', '../../users/administrator/get_user.php?user='+accountId, true); 
    xhr.send(); 
}

//* for showing the update modal 
function getUserEdit() {
    let accountId = document.querySelector('#hiddenId').value;

    let xhr = new XMLHttpRequest(); //prepares the xmlhttprequest

    //check if status is OK for request
    xhr.onload  = function() {
        if(this.status == 200) {
            let user = JSON.parse(this.responseText);
            
            document.querySelector('#accountIdEdit').value = user.accountId;
            document.querySelector('#firstnameEdit').value = user.firstName;
            document.querySelector('#middlenameEdit').value = user.middleName;
            document.querySelector('#lastnameEdit').value = user.lastName;
            document.querySelector('#contactEdit').value = user.contact;
            document.querySelector('#emailEdit').value = user.email;
            document.querySelector('#passwordEdit').value = user.password;
            document.querySelector('#birthdayEdit').value = user.birthday;
            document.querySelector('#roleEdit').value = user.role; 
            document.querySelector('#expertiseEdit').value = user.expertise;
        } else {
            console.log('Error: ' + this.status);
        }
    }

    xhr.open('GET', '../../users/administrator/get_user.php?user='+accountId, true); 
    xhr.send(); 
}