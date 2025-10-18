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
            console.error('Ocorreu um erro:', response.message);
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

function validateCheckbox(input){
    const isChecked = $(input).is(':checked');
    const field = $(input).closest('.field-container');

    if (!isChecked && field.hasClass('required')){
        field.addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
    } else {
        field.removeClass('invalid').find('.error').html('');
    }
}

// key é o que ativa a funçao, input é o que vai ser limpo
function cleanInput(key, input){
    const isChecked = $(key).is(':checked');

    if (!isChecked){
        $(input).val('');
    }
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container.required').each(function(){
        let input = $(this).find('input, textarea, select').first();

        // Radio Buttons
        if (input.is('input[type="radio"]') && $(this).find('input[type="radio"]:checked').length === 0){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        }
        // Ckeckbox
        if (input.is('input[type="checkbox"]') && $(this).find('input[type="checkbox"]:checked').length === 0){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        }

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
    const applicationForm = $('#application-form');
    const params = new URLSearchParams(window.location.search);

    if (params.has('application')) {
        const status = params.get('application');
        const messages = {
            success: 'Candidatura enviada com sucesso.',
            failed: 'Ocorreu um erro ao guardar a candidatura..',
            duplicated: 'Não é possível enviar múltiplas candidaturas.',
            invalid: 'A candidatura contém dados incorretos.'
        };
        // Mostra uma msg personalizada para alguns status e uma genérica para todos os outros
        const msg = messages[status] || 'Ocorreu um erro. Tente novamente!';

        if (status === 'success'){
            const delay = 2000;
            showPopup(msg, delay, true);
        } else {    // Mostra o popup e o erro
            showPopup(msg);
        }
    }

    $('.join-btn').on('click', function(){
        const option = $(this).data('option');

        $('html, body').animate({ scrollTop: $('#application').offset().top - 80 }, 600 );
        applicationForm.find('select option').eq(option).prop('selected', true);

        applicationForm.find('select').trigger("change");
    });

    $('#fullName, #birthDate, #userAddress, #nif, #phone, #health-details').on('input', function(){ validateField(this); });
    $('input[name="gender"]').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#training-plan').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#experience').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#nutrition-plan').on('change', function(){ validateCheckbox(this); });
    $('#health-issues').on('change', function(){ validateCheckbox(this); cleanInput(this, '#health-details'); });
    $('#terms').on('change', function(){ validateCheckbox(this); });

    applicationForm.on('submit', function(e){
        e.preventDefault();
        
        if(noEmptyFields(applicationForm) && isFormValid(applicationForm)){
            $('.validSub').remove();
            const successDiv = $('<div class="validSub">A enviar...</div>');
            $('.form-disclaimer').after(successDiv);

            // Após 1 segundo, envia o formulário
            setTimeout(function(){
                applicationForm.off('submit').submit();
            }, 1000);
        } else {
            console.error('O formulário contém erros. Verifique os campos assinalados.');
        }
    });
});