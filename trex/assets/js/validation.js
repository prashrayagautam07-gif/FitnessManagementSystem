

function checkMembership() {
    const memberId = document.getElementById('member_id').value;
    const resultDiv = document.getElementById('result');
    
    if (!memberId) {
        resultDiv.innerHTML = '';
        return;
    }
    
    fetch(`validate.php?id=${memberId}`)
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = data;
        });
}

function setupEmailPhoneValidation(emailInputId, phoneInputId, emailMsgId, phoneMsgId, submitBtnId, type = 'member') {
    const emailInput = document.getElementById(emailInputId);
    const phoneInput = document.getElementById(phoneInputId);
    const emailMsg = document.getElementById(emailMsgId);
    const phoneMsg = document.getElementById(phoneMsgId);
    const submitBtn = document.getElementById(submitBtnId);

    let emailExists = false;
    let phoneExists = false;

    function updateButton() {
        if (submitBtn) {
            submitBtn.disabled = emailExists || phoneExists;
        }
    }

    const currentIdElem = document.getElementById('currentId');
    const currentId = currentIdElem ? currentIdElem.value : '';

    // EMAIL VALIDATION
    if (emailInput) {
        emailInput.addEventListener("keyup", function () {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                if (emailMsg) {
                    emailMsg.textContent = "Invalid email format";
                    emailMsg.className = "msg-error";
                }
                emailExists = true;
                updateButton();
                return;
            }
            
            fetch(`check_email.php?email=${encodeURIComponent(emailInput.value)}&type=${type}&exclude_id=${currentId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        if (emailMsg) {
                            emailMsg.textContent = "Email already exists";
                            emailMsg.className = "msg-error";
                        }
                        emailExists = true; 
                    } else {
                        if (emailMsg) {
                            emailMsg.textContent = "Email available";
                            emailMsg.className = "msg-success";
                        }
                        emailExists = false;
                    }
                    updateButton();
                });
        });
    }

    // PHONE VALIDATION
    if (phoneInput) {
        phoneInput.addEventListener("keyup", function () {
            const phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phoneInput.value)) {
                if (phoneMsg) {
                    phoneMsg.textContent = "Invalid phone format (must be 10 digits)";
                    phoneMsg.className = "msg-error";
                }
                phoneExists = true;
                updateButton();
                return;
            }

            fetch(`check_phone.php?phone=${encodeURIComponent(phoneInput.value)}&type=${type}&exclude_id=${currentId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        if (phoneMsg) {
                            phoneMsg.textContent = "Phone number already exists";
                            phoneMsg.className = "msg-error";
                        }
                        phoneExists = true;
                    } else {
                        if (phoneMsg) {
                            phoneMsg.textContent = "Phone available";
                            phoneMsg.className = "msg-success";
                        }
                        phoneExists = false;
                    }
                    updateButton();
                });
        });
    }

    updateButton();
}


document.addEventListener("DOMContentLoaded", function () {
    // Initialize validation for add_member.php and add_trainer.php
    if (document.getElementById('email') && document.getElementById('phone')) {
        const formType = document.getElementById('formType') ? document.getElementById('formType').value : 'member';
        setupEmailPhoneValidation('email', 'phone', 'email-msg', 'phone-msg', 'submitBtn', formType);
        
        // Trigger validation on load if editing (so existing valid values don't block submit)
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        if (emailInput && emailInput.value) emailInput.dispatchEvent(new Event('keyup'));
        if (phoneInput && phoneInput.value) phoneInput.dispatchEvent(new Event('keyup'));
    }
});