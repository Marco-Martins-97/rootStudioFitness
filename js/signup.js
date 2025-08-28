function validateField(input){
    const name = $(input).attr('name');
    const value = $(input).val();
    const field = $(input).closest('.field-container');

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

function validatePwds(){
    const pwd = $('input[name="pwd"]').val();
    const confPwd = $('input[name="confirmPwd"]').val();
    const fieldPwd = $('input[name="pwd"]').closest('.field-container');
    const fieldConfPwd = $('input[name="confirmPwd"]').closest('.field-container');

    $.post('includes/validateInputs.inc.php', {input: 'createPwd', valuePwd: pwd, valueConfPwd: confPwd}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);
        }
            
        if (response.status === 'invalid'){
            const invalid = response.message;

            if (invalid.pwd){
                console.warn('Input Invalido:', invalid.pwd);
                fieldPwd.addClass('invalid').find('.error').html(invalid.pwd);
            } else {
                fieldPwd.removeClass('invalid').find('.error').html('');
            }

            if (invalid.confPwd){
                console.warn('Input Invalido:', invalid.confPwd);
                fieldConfPwd.addClass('invalid').find('.error').html(invalid.confPwd);
            } else {
                fieldConfPwd.removeClass('invalid').find('.error').html('');
            }
        }
        if(response.status === 'valid'){
            fieldPwd.removeClass('invalid').find('.error').html('');
            fieldConfPwd.removeClass('invalid').find('.error').html('');
        }

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
    });
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('input').each(function(){
        if ($(this).val().trim() === ''){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
        }
    })
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.field-container.invalid').length === 0;
}

function validateAllFields(formId){
    $(formId).find('input').each(function(){
        const value = $(this).val().trim();
        if (value !== ''){
            validateField(this);
        }
    })
}

$(document).ready(function(){
    const signupForm = $('#signup-form');
    const params = new URLSearchParams(window.location.search);

    if (params.has('signup')) {
        const signupStatus = params.get('signup');
        let message = '';
        let status = '';

        switch (signupStatus) {
            case 'failed':
                message = 'Falha ao registar. Tente novamente.';
                status = 'fail';
                break;
            case 'invalid':
                message = 'Dados inválidos. Confira e tente novamente.';
                status = 'fail';
                break;
            case 'success':
                message = 'Registo realizado com sucesso!';
                status = 'success';
                break;
        }

        //mostra popup
        let sPopup = $(`<div class='popup popup-${status}'>${message}</div>`).appendTo('main');
        // remove popup depois de 3 segundo
        setTimeout(function(){
            sPopup.fadeOut(300, function(){ $(this).remove(); });
            if (status === 'success') {window.location.href = 'login.php';}
        }, 3000);
        
        
    }


    validateAllFields(signupForm);

    $('input[name="firstName"]').on('input', function(){ validateField(this); });
    $('input[name="lastName"]').on('input', function(){ validateField(this); });
    $('input[name="email"]').on('input', function(){ validateField(this); });
    $('input[name="pwd"], input[name="confirmPwd"]').on('input', function(){ validatePwds(); });



    signupForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(signupForm) && isFormValid(signupForm)){

            //cria um popup
            let fPopup = $('<div class="popup popup-success">Formulário válido! ✅</div>').appendTo("main");

            // remove popup depois de 1 segundo e envia o formulário
            setTimeout(function(){
                fPopup.fadeOut(300, function(){ $(this).remove(); });
                signupForm.off('submit').submit();
            }, 1000);
        }
    });

});