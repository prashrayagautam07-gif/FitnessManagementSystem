

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

    let emailExists = true;
    let phoneExists = true;

    function updateButton() {
        submitBtn.disabled = emailExists || phoneExists;
    }

    // EMAIL VALIDATION
    emailInput.addEventListener("keyup", function () {
        fetch(`check_email.php?email=${emailInput.value}&type=${type}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    emailMsg.textContent = "Email already exists"; // FIXED: emailMsg, not phoneMsg
                    emailMsg.className = "msg-error"; // USING CSS CLASS
                    emailExists = true; 
                } else {
                    emailMsg.textContent = "Email available"; // FIXED: emailMsg, not phoneMsg
                    emailMsg.className = "msg-success"; // USING CSS CLASS
                    emailExists = false;
                }
                updateButton();
            });
    });

    // PHONE VALIDATION
    phoneInput.addEventListener("keyup", function () {
        fetch(`check_phone.php?phone=${phoneInput.value}&type=${type}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    phoneMsg.textContent = "Phone number already exists";
                    phoneMsg.className = "msg-error"; // USING CSS CLASS (not style.color)
                    phoneExists = true;
                } else {
                    phoneMsg.textContent = "Phone available";
                    phoneMsg.className = "msg-success"; // USING CSS CLASS (not style.color)
                    phoneExists = false;
                }
                updateButton();
            });
    });

    updateButton();
}


document.addEventListener("DOMContentLoaded", function () {
    // Initialize validation for add_member.php and add_trainer.php
    if (document.getElementById('email') && document.getElementById('phone')) {
        const formType = document.getElementById('formType') ? document.getElementById('formType').value : 'member';
        setupEmailPhoneValidation('email', 'phone', 'email-msg', 'phone-msg', 'submitBtn', formType);
    }
});