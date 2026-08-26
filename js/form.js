function validationUsername() {
    let firstNameElem = document.getElementById('first_name');
    let usernameElem = document.getElementById('username');
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

function validationDOB() {
    let dobElem = document.getElementById('dob');
    let dobError = document.getElementById('dob-error');
    if (!dobElem || !dobElem.value) return true;

    let selectedDate = new Date(dobElem.value);
    let today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate > today) {
        if (dobError) {
            dobError.innerHTML = 'Date of birth cannot be in the future.';
            dobError.style.display = 'block';
        } else {
            alert('Candidate Date of Birth cannot be in the future.');
        }
        dobElem.value = '';
        dobElem.focus();
        return false;
    } else {
        if (dobError) {
            dobError.innerHTML = '';
            dobError.style.display = 'none';
        }
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
    }else if(phone.length < 8){
        phoneError.innerHTML = 'Please enter a valid phone number';
        return false;
    }else{
        phoneError.innerHTML = '';
        return true;
    }
}

function validateForm(){
    let validName = validationUsername();
    let validDOB = validationDOB();
    let validEmail = validationEmail();
    let validPhone = validationPhone();
    let submitError = document.getElementById('submit-error');

    if(!validName || !validDOB || !validEmail || !validPhone){
        if(submitError){
            submitError.style.display = 'block';
            submitError.style.color = 'red';
            submitError.innerHTML = 'Please check and complete all required fields correctly.';
            setTimeout(function(){ submitError.style.display = 'none'; }, 4000);
        }
        return false;
    }else{
        if(submitError){
            submitError.style.display = 'block';
            submitError.style.color = 'green';
            submitError.innerHTML = 'Processing your booking...';
        }
        return true;
    }
}

// Save lead to browser localStorage
function saveLeadToStorage(lead) {
    try {
        var existing = JSON.parse(localStorage.getItem('digieleven_submissions') || '[]');
        if (!Array.isArray(existing)) existing = [];
        existing.unshift(lead);
        localStorage.setItem('digieleven_submissions', JSON.stringify(existing));
    } catch(e) {
        console.warn('Could not save to localStorage:', e);
    }
}

// Restrict DOB picker max date to today on load
function initDOBRestrictions() {
    var todayStr = new Date().toISOString().split('T')[0];
    var dobInputs = document.querySelectorAll('input[type="date"][name="dob"], input[type="date"]#dob, input[name="dob"]');
    dobInputs.forEach(function(input) {
        input.setAttribute('max', todayStr);
        input.addEventListener('change', validationDOB);
        input.addEventListener('input', validationDOB);
    });
}

// Global Instant Form Submission Handler
document.addEventListener('DOMContentLoaded', function() {
    initDOBRestrictions();

    var forms = document.querySelectorAll('form[action*="controller/home-form.php"], form#citb-booking-form, form.form-register');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (!validateForm()) {
                return false;
            }

            var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Processing Booking...';
            }

            var formData = new FormData(form);
            var leadId = 'CH-' + new Date().toISOString().slice(2,10).replace(/-/g,'') + '-' + Math.floor(1000 + Math.random() * 9000);
            var now = new Date();
            var timestamp = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');

            var firstName = (formData.get('first_name') || '').trim();
            var lastName = (formData.get('last_name') || '').trim();
            var candidateName = (firstName + ' ' + lastName).trim() || (formData.get('username') || '').trim() || 'Candidate';
            var service = formData.get('subject') || formData.get('service_type') || 'CITB Test Booking (£45.00)';

            var leadData = {
                id: leadId,
                created_at: timestamp,
                candidate_name: candidateName,
                first_name: firstName,
                last_name: lastName,
                dob: formData.get('dob') || '',
                ni_number: formData.get('ni_number') || '',
                address_line1: formData.get('address_line1') || '',
                city: formData.get('city') || '',
                postcode: formData.get('postcode') || '',
                phone: (formData.get('phone') || '').trim(),
                email: (formData.get('email') || '').trim(),
                subject: service,
                test_type: formData.get('test_type') || service,
                retake_package: formData.get('retake_package') ? 'Yes (+£20.00)' : 'No',
                preferred_location: formData.get('preferred_location') || '',
                source_page: window.location.pathname.split('/').pop() || 'Website Form',
                status: 'New'
            };

            // 1. Record in LocalStorage
            saveLeadToStorage(leadData);

            // 2. Save to Firebase Firestore (Real-time Cloud Sync)
            try {
                if (typeof chFirestore !== 'undefined' && chFirestore) {
                    chFirestore.collection('submissions').doc(leadId).set(leadData).catch(function(err) {
                        console.warn('Firestore submission write error:', err);
                    });
                }
            } catch(e) {
                console.warn('Firestore not available:', e);
            }

            // 2. Render Success UI in place
            var targetBox = document.getElementById('booking-form') || form.closest('.inner-column') || form.closest('.contact-form') || form.parentElement;
            
            if (targetBox) {
                targetBox.innerHTML = `
                    <div style="background: #ffffff; border-radius: 8px; border: 2px solid #28a745; padding: 35px 25px; text-align: center; box-shadow: 0 6px 20px rgba(0,0,0,0.06); animation: fadeIn 0.4s ease-in-out;">
                        <div style="width: 70px; height: 70px; background: #e8f5e9; color: #2e7d32; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 34px; margin-bottom: 18px;">&#10004;</div>
                        <h3 style="color: #104cba; font-weight: 700; font-size: 22px; margin-bottom: 8px;">Booking Request Received!</h3>
                        <p style="font-size: 15px; color: #333; margin-bottom: 15px;">Thank you, <strong>${candidateName}</strong>. Your reference ID is: <strong style="color: #104cba; font-size: 17px;">#${leadId}</strong></p>
                        
                        <div style="background: #f8fafc; padding: 16px; border-radius: 6px; text-align: left; font-size: 13.5px; color: #475569; margin-bottom: 20px; border-left: 4px solid #104cba; line-height: 1.6;">
                            <div style="margin-bottom: 5px;"><strong>&bull; Service:</strong> ${service}</div>
                            <div style="margin-bottom: 5px;"><strong>&bull; Candidate Phone:</strong> ${leadData.phone}</div>
                            <div style="margin-bottom: 5px;"><strong>&bull; Candidate Email:</strong> ${leadData.email}</div>
                            <div><strong>&bull; Next Steps:</strong> An advisor from Construction Helps will call you on <strong>${leadData.phone}</strong> to confirm your nearest test centre slot and ID requirements.</div>
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="index.html" class="theme-btn btn-style-one" style="padding: 10px 22px; font-size: 14px; text-decoration: none;">Return to Home</a>
                            <a href="citb-test.html" class="theme-btn btn-style-two" style="padding: 10px 22px; font-size: 14px; text-decoration: none; background: #64748b; color: #fff;">Book Another Test</a>
                        </div>
                    </div>
                `;
                targetBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert('Thank you, ' + candidateName + '! Your booking request (Ref: #' + leadId + ') has been received successfully.');
                form.reset();
            }

            // 3. Dispatch Live Email Notifications (FormSubmit Client-Side API for GitHub Pages)
            try {
                var emailCfgRaw = localStorage.getItem('digieleven_email_config');
                var emailCfg = emailCfgRaw ? JSON.parse(emailCfgRaw) : {};
                var adminEmailsStr = emailCfg.admin_notify_emails || 'info@constructionhelps.com, emailWazid@gmail.com';
                var fromEmail = emailCfg.from_email || 'info@constructionhelps.com';
                var fromName = emailCfg.from_name || 'Construction Helps';
                var sendCandidateAck = (emailCfg.send_candidate_ack !== false && emailCfg.send_candidate_ack !== 'false');
                var candidateNotes = emailCfg.candidate_email_notes || 'An advisor from Construction Helps will call you shortly to confirm your booking slot and test centre details.';

                var adminPayload = {
                    _subject: '🚨 New Lead #' + leadId + ' — ' + candidateName + ' (' + service + ')',
                    _template: 'table',
                    _captcha: 'false',
                    _replyto: leadData.email,
                    'Reference ID': '#' + leadId,
                    'Candidate Full Name': candidateName,
                    'Date of Birth': leadData.dob || 'Not provided',
                    'National Insurance': leadData.ni_number || 'Not provided',
                    'Phone Number': leadData.phone,
                    'Email Address': leadData.email,
                    'Home Address': [leadData.address_line1, leadData.city, leadData.postcode].filter(Boolean).join(', ') || 'Not provided',
                    'Requested Service': service,
                    'Test Type': leadData.test_type,
                    'Retake Protection': leadData.retake_package,
                    'Preferred Location / Date': leadData.preferred_location || 'Earliest available',
                    'Source Page': leadData.source_page,
                    'Received At': new Date().toLocaleString()
                };

                // Send to all configured admin recipients via FormSubmit
                adminEmailsStr.split(',').forEach(function(em) {
                    var cleanEm = em.trim();
                    if (cleanEm && cleanEm.indexOf('@') > -1) {
                        fetch('https://formsubmit.co/ajax/' + encodeURIComponent(cleanEm), {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(adminPayload)
                        }).catch(function(){});
                    }
                });

                // Send Candidate Acknowledgment Email if enabled
                if (sendCandidateAck && leadData.email && leadData.email.indexOf('@') > -1) {
                    var candidatePayload = {
                        _subject: 'Booking Request Received — #' + leadId + ' | Construction Helps',
                        _template: 'table',
                        _captcha: 'false',
                        _replyto: fromEmail,
                        'Booking Reference': '#' + leadId,
                        'Candidate Name': candidateName,
                        'Service Requested': service,
                        'Phone Number': leadData.phone,
                        'Retake Protection': leadData.retake_package,
                        'Preferred Venue / Date': leadData.preferred_location || 'Earliest available',
                        'Important ID Requirement': 'You must bring an acceptable, original Primary ID (UK/Intl Passport, UK/EU Driving Licence, or verified eVisa) on test day.',
                        'Next Steps': candidateNotes,
                        'Support Helpline': '0800 002 5614 (' + fromEmail + ')',
                        'Sender': fromName + ' <' + fromEmail + '>'
                    };

                    fetch('https://formsubmit.co/ajax/' + encodeURIComponent(leadData.email), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(candidatePayload)
                    }).catch(function(){});
                }

                // Send via EmailJS (from info@constructionhelps.com)
                if (typeof emailjs !== 'undefined' && typeof emailjsConfig !== 'undefined' && emailjsConfig.serviceId) {
                    var adminTpl = emailjsConfig.adminTemplateId || emailjsConfig.templateId;
                    var candidateTpl = emailjsConfig.candidateTemplateId;

                    var emailjsParams = {
                        candidate_name: candidateName,
                        lead_id: '#' + leadId,
                        phone: leadData.phone,
                        email: leadData.email,
                        service: service,
                        dob: leadData.dob || 'Not provided',
                        ni_number: leadData.ni_number || 'Not provided',
                        address: [leadData.address_line1, leadData.city, leadData.postcode].filter(Boolean).join(', ') || 'Not provided',
                        retake_package: leadData.retake_package,
                        preferred_location: leadData.preferred_location || 'Earliest available',
                        notes: candidateNotes,
                        from_email: fromEmail,
                        from_name: fromName,
                        to_email: adminEmailsStr
                    };

                    // 1. Send Admin Alert
                    if (adminTpl) {
                        emailjs.send(emailjsConfig.serviceId, adminTpl, emailjsParams).catch(function(err){
                            console.warn('EmailJS Admin alert notice:', err);
                        });
                    }

                    // 2. Send Candidate Confirmation Auto-Reply
                    if (sendCandidateAck && candidateTpl && leadData.email) {
                        var candParams = Object.assign({}, emailjsParams, { to_email: leadData.email });
                        emailjs.send(emailjsConfig.serviceId, candidateTpl, candParams).catch(function(err){
                            console.warn('EmailJS Candidate auto-reply notice:', err);
                        });
                    }
                }
            } catch(e) {
                console.warn('Email dispatch notice:', e);
            }

            // 4. Background POST to controller/home-form.php (if on PHP server)
            var formAction = form.getAttribute('action') || 'controller/home-form.php';
            try {
                fetch(formAction, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(function() {});
            } catch(err) {}

            return false;
        });
    });
});
