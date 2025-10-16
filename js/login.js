function validateField(input){
    const name = $(input).attr('name');
    const value = $(input).val();
    const field = $(input).closest('.form-container');

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

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('input').each(function(){
        if ($(this).val().trim() === ''){
            emptyFields = true;
            $(this).closest('.form-container').addClass('invalid').find('.error').html('Preenchimento dos campos é obrigatório.');
        }
    })
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.form-container.invalid').length === 0;
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
    const loginForm = $('#login-form');
    const formContainer = $('.form-container');
    const error = $('.error')

    const params = new URLSearchParams(window.location.search);

    if (params.has('login')) {
        const status = params.get('login');
        const messages = {
            success: 'Sessão iniciada com sucesso!',
            failed: 'Ocorreu um erro na ligação à base de dados.',
            empty: 'Preenchimento dos campos é obrigatório.',
            invalid: 'O email ou a palavra-passe estão incorretos.'
        };
        // Mostra uma msg personalizada para alguns status e uma genérica para todos os outros
        const msg = messages[status] || 'Ocorreu um erro. Tente novamente!';

        if (status === 'success'){  // Mostra msg de sucesso e redireciona para a pagina inicial
            const delay = 2000;
            showPopup(msg, delay, true);
            setTimeout(function(){ window.location.href = 'index.php'; }, delay);
        } else {    // Mostra o popup e o erro
            showPopup(msg);
            formContainer.addClass('invalid');
            error.text(msg);
        }
    }

    $('#loginEmail').on('input', function(){ validateField(this); });

    loginForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(loginForm) && isFormValid(loginForm)){
            error.css('color', 'blue').text('A enviar...').show();

            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                loginForm.off('submit').submit();
            }, 1000);
        }
    });
});