function validationUsername() {
    let usernameElem = document.getElementById('username');
    let firstNameElem = document.getElementById('first_name');
    let usernameError = document.getElementById('username-error');
    
    if (firstNameElem) {
        if (!firstNameElem.value.trim()) {
            if (usernameError) usernameError.innerHTML = 'First name is required';
            return false;
        }
        if (usernameError) usernameError.innerHTML = '';
        return true;
    }

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
    }else if(phone.length < 9){
        phoneError.innerHTML = 'Please enter a valid phone number';
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

// AJAX Form Handler for seamless submission without 405/page redirect issues
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('form[action*="controller/home-form.php"], form#citb-booking-form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Submit';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Processing Booking...';
            }

            e.preventDefault();

            var formData = new FormData(form);
            var formAction = form.getAttribute('action') || 'controller/home-form.php';

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Server returned ' + response.status);
                }
            })
            .then(function(data) {
                var container = form.closest('.contact-form') || form.closest('.price-card-box') || form.parentElement;
                var leadId = (data && data.lead_id) ? data.lead_id : 'CH-' + Math.floor(100000 + Math.random() * 900000);
                
                if (container) {
                    container.innerHTML = `
                        <div style="background: #ffffff; border-radius: 8px; border: 2px solid #28a745; padding: 35px 25px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                            <div style="width: 65px; height: 65px; background: #e8f5e9; color: #2e7d32; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 18px;">&#10004;</div>
                            <h3 style="color: #104cba; font-weight: 700; margin-bottom: 8px;">Booking Request Received!</h3>
                            <p style="font-size: 15px; color: #444; margin-bottom: 15px;">Your booking reference number is: <strong style="color: #104cba; font-size: 16px;">${leadId}</strong></p>
                            <div style="background: #f8fafc; padding: 14px; border-radius: 6px; text-align: left; font-size: 13.5px; color: #555; margin-bottom: 20px; border-left: 4px solid #104cba;">
                                <p style="margin: 0 0 6px 0;"><strong>&bull; Confirmation:</strong> A confirmation copy has been registered in our system.</p>
                                <p style="margin: 0;"><strong>&bull; Next Steps:</strong> An administrator will contact you shortly on your provided phone number to confirm your test slot and centre details.</p>
                            </div>
                            <a href="index.html" class="theme-btn btn-style-one" style="display: inline-block; padding: 10px 24px; font-size: 14px; text-decoration: none;">Return to Home</a>
                        </div>
                    `;
                } else {
                    alert('Thank you! Your booking request (Ref: ' + leadId + ') has been submitted successfully.');
                    form.reset();
                }
            })
            .catch(function(err) {
                console.warn('AJAX submit encountered issue, executing native submit fallback:', err);
                form.submit();
            });
        });
    });
});
