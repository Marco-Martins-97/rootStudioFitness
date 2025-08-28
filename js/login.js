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
        let message = '';
        let status = '';

        switch (loginStatus) {
            case 'failed':
                message = 'Erro na ligação à base de dados.';
                status = 'fail';
                break;
            case 'empty':
                message = 'Campos de preenchimento obrigatório!';
                status = 'fail';
                break;
            case 'invalid':
                message = 'Email ou palavra-passe incorretos.';
                status = 'fail';
                break;
            case 'success':
                message = 'Login realizado com sucesso!';
                status = 'success';
                break;
        }

        //mostra popup
        let sPopup = $(`<div class='popup popup-${status}'>${message}</div>`).appendTo('main');
        // remove popup depois de 3 segundo
        setTimeout(function(){
            sPopup.fadeOut(300, function(){ $(this).remove(); });
            if (status === 'success') {window.location.href = 'index.php';}
        }, 3000);
    }

    $('input[name="loginEmail"]').on('input', function(){ validateField(this); });

    loginForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(loginForm) && isFormValid(loginForm)){

            //cria um popup
            let fPopup = $('<div class="popup popup-success">Formulário válido! ✅</div>').appendTo("main");

            // remove popup depois de 1 segundo e envia o formulário
            setTimeout(function(){
                fPopup.fadeOut(300, function(){ $(this).remove(); });
                loginForm.off('submit').submit();
            }, 1000);
        }
    });
});