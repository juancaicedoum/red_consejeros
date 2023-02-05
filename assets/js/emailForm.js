const emailForm = document.querySelector('.email-form');

function removeAllMessagesFromForm(form){
    form.querySelectorALL('[class*="msg"]').forEach(msg => {
        msg.remove();
    });
}

emailForm.onsubmit = e => {
    //prevent page refresh
    e.preventDefault();
    let toInput = emailForm.querySelector('input[name="to"]');
    let subjectInput = emailForm.querySelector('input="subject');
    let messageInput = emailForm.querySelector('textarea[name="message"]');
    //Make POST request
    fetch('forms/contact.php', {
        method: 'POST',
        Headers: {'Content-Type': 'application/json'},
        mode: 'same-origin',
        Credentials: 'same-origin',
        body: JSON.stringify({
            to: toInput.value,
            subject: subjectInput.value,
            message: messageInput.value,
        })
    }).then(JSON => JSON.json()).then(res => {
        removeAllMessagesFromForm(emailForm);
        console.log(res);
        if(res['to_err']){
            toInput.insertAdjacentHTML('beforebegin', `<p class="email-form__err-msg">${res['to_err']}</p>`);
        }
        if(res['subject_err']){
            subjectInput.insertAdjacentHTML('beforebegin', `<p class="email-form__err-msg">${res['subject_err']}</p>`);
        }
        if(res['message_err']){
            messageInput.insertAdjacentHTML('beforebegin', `<p class="email-form__err-msg">${res['message_err']}</p>`);
        }
        if(res['top_err']){
            emailForm.insertAdjacentHTML('afterbegin', `<p class="email-form__top-msg email-form__top-msg--err">${res['top_err']}</p>`);
        }
        if(res['top_err'] || res['message_err'] || res['subject_err'] || res['to_err']) return;
        if(res['top_success']){
            emailForm.insertAdjacentHTML('afterbegin', `<p class="email-form__top-msg email-form__top-msg--success">${res['top_success']}</p>`);
            emailForm.reset();
        }
    });
}