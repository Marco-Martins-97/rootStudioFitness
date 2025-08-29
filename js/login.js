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

    const params = new URLSearchParams(window.location.search);

    if (params.has('login')) {
        const loginStatus = params.get('login');
        let loginMsg = '';

        switch (loginStatus) {
            case 'failed':
                loginMsg = 'Erro na ligação à base de dados.';
                break;
            case 'empty':
                loginMsg = 'Campos de preenchimento obrigatório!';
                break;
            case 'invalid':
                loginMsg = 'Email ou palavra-passe incorretos.';
                break;
            /* case 'success':
                loginMsg = 'Login realizado com sucesso!';
                loginSts = 'success';
                break; */
        }

        //mostra popup
        let loginPopup = $(`<div class='popup popup-fail'>${loginMsg}</div>`).appendTo('main');
        // remove popup depois de 3 segundo
        setTimeout(function(){
            loginPopup.fadeOut(300, function(){ $(this).remove(); });
                // if (loginSts === 'success') {window.location.href = 'index.php';}
        }, 3000);
    }

    $('input[name="loginEmail"]').on('input', function(){ validateField(this); });

    loginForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(loginForm) && isFormValid(loginForm)){

            //cria um popup
            let formPopup = $('<div class="popup popup-warn">Formulário válido! ✅</div>').appendTo("main");

            // remove popup depois de 1 segundo e envia o formulário
            setTimeout(function(){
                formPopup.fadeOut(300, function(){ $(this).remove(); });
                loginForm.off('submit').submit();
            }, 1000);
        }
    });
});