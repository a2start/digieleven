let usernameError = document.getElementById('username-error');
let emailError = document.getElementById('email-error');
let phoneError = document.getElementById('phone-error');
let submitError = document.getElementById('submit-error');

function validationUsername() {
    let username = document.getElementById('username').value;

    if(username.length == 0){
        usernameError.innerHTML = 'Name is required!';
        return false;
    }else if(!username.match(/^[A-Za-z]*\s{1}[A-Za-z]*$/)){
        usernameError.innerHTML = 'Please Fill Your Full Name';
        return false;
    }else{
        usernameError.innerHTML = '';
        return true;
    }    
}

function validationEmail(){
    let email = document.getElementById('email').value;

    if(email.length == 0){
        emailError.innerHTML = 'Email is Required';
        return false;
    }else if(!email.match(/^[A-Za-z\._\-[0-9]*[@][A-Za-z]*[\.][a-z]{2,4}$/)){
        emailError.innerHTML = 'Email Invalid';
        return false;
    }else{
        emailError.innerHTML = '';
        return true;
    }
}

function validationPhone(){

    let phone = document.getElementById('phone').value;

    if(phone.length == 0){
        phoneError.innerHTML = 'Phone Number Is Required';
        return false;
    }else if(!phone.match(/^[0-9]{10}$/)){
        phoneError.innerHTML = 'Phone Is Required!';
        return false;
    }else if(phone.length !== 10){
        phoneError.innerHTML = 'Phone Number Should Be 10 Digits';
        return false;
    }else{
        phoneError.innerHTML = '';
        return true;
    }
}

function validateForm(){

    if(!validationUsername() || !validationEmail() || !validationPhone()){
        submitError.style.display = 'block';
        submitError.style.color = 'red';
        submitError.innerHTML = 'Please Fix Error To Submit';
        setTimeout(function(){ submitError.style.display = 'none'; }, 3000)
        return false;
    }else{

        // let user_name = document.getElementById('username').value;
        // let email_valid = document.getElementById('email').value;
        // let phone_valid = document.getElementById('phone').value;
        // let subject_valid = document.getElementById('subject').value;
       
        // const ajax = new XMLHttpRequest();
        // ajax.open('POST', './../controller/home-form.php', true);
        // ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        // ajax.onreadystatechange = function() {
        //     if(ajax.readyState == 4 && ajax.status == 200){
        //         submitError.innerHTML = ajax.responseText;
        //     }
        // }
        // ajax.send("username="+user_name+"email="+email_valid+"phone="+phone_valid+"subject="+subject_valid);
        submitError.innerHTML = 'Thank you For Submitting!!';
        return true;
    }
}

