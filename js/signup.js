function validateField(input){
    const name = $(input).attr('name');
    const value = $(input).val();
    const field = $(input).closest('.field-container');

    $.post('includes/validateInputs.inc.php', {input: name, value: value}, function(response){
        if (!response || typeof response.status === 'undefined') {
            console.error('A resposta do servidor é inválida.');
            return;
        }

        if (response.status === 'error'){
            console.error('Erro:', response.message);
            return;
        }
        
        if (response.status === 'invalid'){
            console.warn('Campo inválido:', response.message);
            field.addClass('invalid').find('.error').html(response.message);
            return;
        }

        field.removeClass('invalid').find('.error').html('');

    }, 'json').fail(function () {
        console.error('Ocorreu um erro ao validar os dados.');
    });
}

function validatePwds(){
    const pwd = $('#pwd').val();
    const confPwd = $('#confirmPwd').val();
    const fieldPwd = $('#pwd').closest('.field-container');
    const fieldConfPwd = $('#confirmPwd').closest('.field-container');

    $.post('includes/validateInputs.inc.php', {input: 'createPwd', valuePwd: pwd, valueConfPwd: confPwd}, function(response){
        if (!response || typeof response.status === 'undefined') {
            console.error('A resposta do servidor é inválida.');
            return;
        }

        if (response.status === 'error'){
            console.error('Erro:', response.message);
            return;
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
        console.error('Ocorreu um erro ao validar os dados.');
    });
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container.required').each(function(){
        let input = $(this).find('input').first();
        if (input.val() === null || input.val().trim() === ''){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        }
    });
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

function showPopup(msg, delay = 2000, success = false) {
    $('.popup').remove();// Remove um popup antes de criar outro (se existir)
    
    // Cria o elemento popup
    const popup = $('<div class="popup"></div>').text(msg);
    
    // Adiciona a classe "popup-success" apenas se success for true ou 1
    if (success === true || success === 1 || success === '1') {
        popup.addClass('popup-success');
    }
    // Insere no main e aplica delay + fadeOut
    popup.appendTo('main').delay(delay).fadeOut(300, function() { $(this).remove(); });
}

$(document).ready(function(){
    const signupForm = $('#signup-form');
    const params = new URLSearchParams(window.location.search);

    if (params.has('signup')) {
        const status = params.get('signup');

        const messages = {  
            success: 'Registo realizado com sucesso!',
            invalid: 'Dados inválidos. Confira e tente novamente.',
            failed: 'Falha ao registar. Tente novamente.!',
        };
       
        // Mostra uma msg personalizada para alguns status e uma genérica para todos os outros
        const msg = messages[status] || 'Ocorreu um erro. Tente novamente!';   

        if (status === 'success'){
            const delay = 2000;
            showPopup(msg, delay, true);
            setTimeout(function(){ window.location.href = 'login.php'; }, delay);
        } else {
            showPopup(msg);
        }
    }

    validateAllFields(signupForm);

    $('#firstName, #lastName, #email').on('input', function(){ validateField(this); });
    $('#pwd, #confirmPwd').on('input', function(){ validatePwds(); });


    signupForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(signupForm) && isFormValid(signupForm)){
            $('.validSub').remove();
            const successDiv = $('<div class="validSub">A enviar...</div>');
            $('.form-disclaimer').after(successDiv);
            
            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                signupForm.off('submit').submit();
            }, 1000);
        } else {
            console.error('O formulário contém erros. Verifique os campos assinalados.');
        }
    });

});