function validateField(input){
    const name = $(input).attr('name');
    const value = $(input).val();
    const field = $(input).closest('.form-container');

    $.post('includes/validateInputs.inc.php', {input: name, value: value}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);
        }
        
        if (response.status === 'invalid'){
            console.warn('Input Invalido:', response.message);
            field.addClass('invalid').find('.error').html(response.message);
            return;
        }

        field.removeClass('invalid').find('.error').html('');

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
    });
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('input').each(function(){
        if ($(this).val().trim() === ''){
            emptyFields = true;
            $(this).closest('.form-container').addClass('invalid').find('.error').html('Campos de preenchimento obrigatório!');
        }
    })
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.form-container.invalid').length === 0;
}


$(document).ready(function(){
    const loginForm = $('#login-form');
    const error = $('.error')

    const params = new URLSearchParams(window.location.search);

    if (params.has('login')) {
        const loginStatus = params.get('login');
        const loginMessages = {
            failed: 'Erro na ligação à base de dados.',
            empty: 'Campos de preenchimento obrigatório!',
            invalid: 'Email ou palavra-passe incorretos.'
        };
        const loginMsg = loginMessages[loginStatus] || '';

        //mostra popup
        let loginPopup = $(`<div class='popup popup-fail'>${loginMsg}</div>`).appendTo('main');
        // remove popup depois de 3 segundo
        setTimeout(() => loginPopup.fadeOut(300, () => loginPopup.remove()), 3000);
    }

    $('#loginEmail').on('input', function(){ validateField(this); });

    loginForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(loginForm) && isFormValid(loginForm)){
            error.css('color', 'green').text('Enviado Com Sucesso!').show();

            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                loginForm.off('submit').submit();
            }, 1000);
        }
    });
});