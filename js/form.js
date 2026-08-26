function validationUsername() {
    let usernameElem = document.getElementById('username');
    let usernameError = document.getElementById('username-error');
    if(!usernameElem || !usernameError) return true;
    let username = usernameElem.value.trim();

    if(username.length === 0){
        usernameError.innerHTML = 'Name is required';
        return false;
    }else if(username.length < 2){
        usernameError.innerHTML = 'Please enter your full name';
        return false;
    }else{
        usernameError.innerHTML = '';
        return true;
    }    
}

function validationEmail(){
    let emailElem = document.getElementById('email');
    let emailError = document.getElementById('email-error');
    if(!emailElem || !emailError) return true;
    let email = emailElem.value.trim();

    if(email.length === 0){
        emailError.innerHTML = 'Email is required';
        return false;
    }else if(!email.match(/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/)){
        emailError.innerHTML = 'Please enter a valid email address';
        return false;
    }else{
        emailError.innerHTML = '';
        return true;
    }
}

function validationPhone(){
    let phoneElem = document.getElementById('phone');
    let phoneError = document.getElementById('phone-error');
    if(!phoneElem || !phoneError) return true;
    let phone = phoneElem.value.trim().replace(/[\s\-()]/g, '');

    if(phone.length === 0){
        phoneError.innerHTML = 'Phone number is required';
        return false;
    }else if(!phone.match(/^(\+44|0)[0-9]{9,11}$/)){
        phoneError.innerHTML = 'Please enter a valid UK phone number (e.g. 07123456789)';
        return false;
    }else{
        phoneError.innerHTML = '';
        return true;
    }
}

function validateForm(){
    let validName = validationUsername();
    let validEmail = validationEmail();
    let validPhone = validationPhone();
    let submitError = document.getElementById('submit-error');

    if(!validName || !validEmail || !validPhone){
        if(submitError){
            submitError.style.display = 'block';
            submitError.style.color = 'red';
            submitError.innerHTML = 'Please check and fix the errors above before submitting.';
            setTimeout(function(){ submitError.style.display = 'none'; }, 4000);
        }
        return false;
    }else{
        if(submitError){
            submitError.style.display = 'block';
            submitError.style.color = 'green';
            submitError.innerHTML = 'Submitting your request...';
        }
        return true;
    }
}
