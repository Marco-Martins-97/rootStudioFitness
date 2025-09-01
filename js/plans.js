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

function validateCheckbox(input){
    const isChecked = $(input).is(':checked');
    const field = $(input).closest('.field-container');

    if (!isChecked && field.hasClass('required')){
        field.addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
    } else {
        field.removeClass('invalid').find('.error').html('');
    }
}

//key, é o que ativa a funçao, input é o que vai ser limpo
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

        // Radio Butons
        if (input.is('input[type="radio"]') && $(this).find('input[type="radio"]:checked').length === 0){
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
        }
        // Ckeckbox
        if (input.is('input[type="checkbox"]') && $(this).find('input[type="checkbox"]:checked').length === 0){
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
        }

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
    const applicationForm = $('#application-form');

    $('.join-btn').on('click', function(){
        const option = $(this).data('option');

        $('html, body').animate({ scrollTop: $('#application').offset().top - 80 }, 600 );
        applicationForm.find('select option').eq(option).prop('selected', true);

        applicationForm.find('select').trigger("change");
    });


    $('#fullName').on('input', function(){ validateField(this); });
    $('#birthDate').on('input', function(){ validateField(this); });
    $('input[name="gender"]').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#userAddress').on('input', function(){ validateField(this); });
    $('#nif').on('input', function(){ validateField(this); });
    $('#phone').on('input', function(){ validateField(this); });
    $('#training-plan').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#experience').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });
    $('#nutrition-plan').on('change', function(){ validateCheckbox(this); });
    $('#health-issues').on('change', function(){ validateCheckbox(this); cleanInput(this, '#health-details'); });
    $('#health-details').on('input', function(){ validateField(this); });
    $('#terms').on('change', function(){ validateCheckbox(this); });

    
    applicationForm.on('submit', function(e){
        e.preventDefault();
        
        if(noEmptyFields(applicationForm) && isFormValid(applicationForm)){
            //cria um popup
            let formPopup = $('<div class="popup popup-warn">Formulário válido! ✅</div>').appendTo("main");

            // remove popup depois de 1 segundo e envia o formulário
            setTimeout(function(){
                formPopup.fadeOut(300, function(){ $(this).remove(); });
                applicationForm.off('submit').submit();
            }, 1000);
        } else {
            console.error('Invalid Form!');
        }
    });
});