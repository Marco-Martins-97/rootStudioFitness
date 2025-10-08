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

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container.required').each(function(){
        let input = $(this).find('input').first();

        if (input.val() === null || input.val().trim() === ''){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
        }
    });
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.field-container.invalid').length === 0;
}

$(document).ready(function(){
    const checkoutForm = $('#checkout-form');

    $('#fullName').on('input', function(){ validateField(this); });
    $('#birthDate').on('input', function(){ validateField(this); });
    $('#userAddress').on('input', function(){ validateField(this); });






    checkoutForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(checkoutForm) && isFormValid(checkoutForm)){
            $('.validSub').remove();
            const successDiv = $('<div class="validSub">Enviando...</div>');
            $('.form-disclaimer').after(successDiv);

            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                // checkoutForm.off('submit').submit();
                console.log("ENVIDADO!!")
            }, 1000);
        } else {
            console.error('Invalid Form!');
        }
    });

});