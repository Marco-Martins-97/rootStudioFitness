function loadProfile(){
    $.post('includes/loadServerData.inc.php', {action: 'loadProfile'}, function(response){
        // console.log(response);

        if (!response || typeof response !== 'object') {
            console.error('Invalid JSON response:', response);
            $('.profile-container').html(`Ocurreu Um Erro, Não Foi Possivel Carregar os dados do utilizador!`);
            return;
        }
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Unknown error');
            $('.profile-container').html(`Ocurreu Um Erro, Não Foi Possivel Carregar os dados do utilizador!`);
            return;
        }

        const userProfile = response.userData;

        let HTMLcontent = '';


        HTMLcontent += `
            <div class="profile-header">
                <div class='editable'>
                    <div class='username-container'>
                        <div class='field' data-field='firstName'>
                            <label></label>
                            <span class='value'>${userProfile.firstName}</span>
                        </div>
                        <div class='field' data-field='lastName'>
                            <label></label>
                            <span class='value'>${userProfile.lastName}</span>
                        </div>
                    </div>
                </div>
                <div class='role'>${userProfile.userRole}</div>
            </div>
            <div class="profile-body">
                <div class='editable'>
                    <div class='field' data-field='email'>
                        <label>Email:</label>
                        <span class='value'>${userProfile.email}</span>
                    </div>
                </div>
        `;
        // Reset PWD

        // o array fieldsConfig contem toda a informaçao para a criação da estrutura do perfil do cliente, para a parte visual e editavel
        const fieldsConfig = [
            {field: 'firstName', label: '', type: 'input', inputType: 'text'},
            {field: 'lastName', label: '', type: 'input', inputType: 'text'},
            {field: 'email', label: 'Email', type: 'input', inputType: 'text'},
            {field: 'fullName', label: 'Nome Completo', type: 'input', inputType: 'text'},
            {field: 'birthDate', label: 'Data de Nascimento', type: 'input', inputType: 'date'},
            {field: 'gender', label: 'Gênero', type: 'select', options: [
                                                                            {value: 'male', label: 'Masculino'},
                                                                            {value: 'female', label: 'Feminino'}
                                                                        ]},
            { field: 'userAddress', label: 'Morada', type: 'input', inputType: 'text' },
            { field: 'nif', label: 'NIF', type: 'input', inputType: 'text' },
            { field: 'phone', label: 'Telefone', type: 'input', inputType: 'tel' },
            { field: 'trainingPlan', label: 'Plano de Treino', type: 'select', options: [
                                                                                            { value: 'personalized1', label: 'Individual' },
                                                                                            { value: 'personalized2', label: 'Grupos Reduzidos' },
                                                                                            { value: 'group', label: 'Aulas de Grupo' },
                                                                                            { value: 'terapy', label: 'Treino Terapêutico' },
                                                                                            { value: 'padel', label: 'Padel' },
                                                                                            { value: 'openStudio', label: 'Acesso ao Estúdio' }
                                                                                        ]},
            { field: 'experience', label: 'Experiência', type: 'select', options: [
                                                                                    { value: 'beginner', label: 'Iniciante' },
                                                                                    { value: 'intermediate', label: 'Intermédio' },
                                                                                    { value: 'advanced', label: 'Avançado' }
                                                                                ]},
            { field: 'nutritionPlan', label: 'Plano Alimentar', type: 'select', options: [
                                                                                            { value: 'yes', label: 'Sim' },
                                                                                            { value: 'no', label: 'Não' }
                                                                                        ]},
            { field: 'healthIssues', label: 'Problemas de Saúde', type: 'select', options: [
                                                                                            { value: 'yes', label: 'Sim' },
                                                                                            { value: 'no', label: 'Não' }
                                                                                        ]},
            { field: 'healthDetails', label: 'Detalhes de Saúde', type: 'textarea', placeholder: 'Detalhe os problemas de saude.' }
        ];

        if (response.clientData !== null){
            const clientProfile = response.clientData;
            // Cria as linhas com os Dados
            $.each(fieldsConfig, function(_, f){
                const field = f.field;
                const type = f.type;
                const value = clientProfile[field] ?? '';
                const label = f.label ?? field;
                let displayValue = '';
                //nao mostra os dados do utilizador apenas os de cliente
                if (field !== 'email' && field !== 'firstName' && field !== 'lastName'){
                    if (type === 'select'){
                        const selected = f.options.find(opt => opt.value === value);
                        displayValue = selected ? selected.label : selected;
                    } else {
                        displayValue = value;
                    }

                    HTMLcontent += `<div class='editable'>
                                        <div class='field' data-field='${field}'>
                                            <label>${label}:</label>
                                            <span class='value'>${displayValue}</span>
                                        </div>
                                    </div>`;
                }
                
               
            });
        }

        HTMLcontent += '</div>';

        $('.profile-container').html(HTMLcontent);
        
        editField(fieldsConfig);

    }, 'json').fail(function () {
        $('.profile-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Dados de Utilizador!');
    });
}

function editField(fieldsConfig){
    $('.profile-container').off('click', '.field').on('click', '.field', function(){
        const $this = $(this);
        const wrapper = $this.find('.value');
        const $value = wrapper.text().trim();
        const $field = $this.data('field');
        const editable = $this.closest('.editable');
        
        // previne de criar um input ja existente
        if($this.find('input, select, textarea').length > 0) return;

        // procura no array fieldsConfig pelos dados do field correspondente clicado, caso não enconte, nao faz nada
        const fieldConf = fieldsConfig.find(f => f.field === $field);
        if (!fieldConf) return;

        // cria o input
        let $input;
        if (fieldConf.type === 'input'){
            $input = $('<input>', {
                type: fieldConf.inputType || 'text',
                value: $value
            });
        }
        if (fieldConf.type === 'textarea'){
            $input = $('<input>', {
                placeholder: fieldConf.placeholder || '',
                value: $value
            });
        }
        if (fieldConf.type === 'select'){
            $input = $('<select>');
            fieldConf.options.forEach(opt => {
                const $opt = $('<option>', {
                    value: opt.value,
                    text: opt.label,
                    selected: opt.value === $value || opt.label === $value
                });
                $input.append($opt);
            });
        }

        // apenas continua se o $input tiver sido criado
        if (!$input) return;


        // Entra em Modo de edição
        wrapper.replaceWith($input);    // troca o valor pelo $input
        $input.focus();                 // foca no input
        $this.addClass('notSaved');     // Add a variavel que indica que o valor nao está salvo
        // cria a div erro e insere no local correto
        const $error = $('<div>', { class: 'error' });
        if($field === 'firstName' || $field === 'lastName'){
            if(!editable.find('.error').length){
                $error.insertAfter(editable.find('.username-container'));
            }
            // editable.find('.error').remove();
            // $error.insertAfter(editable.find('.username-container'));
            //ajusta a larura do input ao texto (apenas para username)
            resizeInput();

        } else {
            $error.insertAfter($this);
        }
        // coloca o cursor no final do input (nao aplicavel em selects e data de nascimento)
        setTimeout(() => {
            if ($input.is('input, textarea') && $field !== 'birthDate') {
                const len = $input.val().length;
                $input[0].setSelectionRange(len, len);
            }
        }, 0);

        function resizeInput(){
            const $span = $('<span>').css({ visibility: 'hidden', position: 'absolute', whiteSpace: 'pre', font: $input.css('font') }).text($input.val() || $input.attr('placeholder') || '').appendTo('body');
            $input.width($span.width() + 2);
            $span.remove();
        }
        if($field === 'firstName' || $field === 'lastName'){
            $input.on("input", resizeInput);// atualza a largura conforme vai escrevendo
        }

        $input.on('blur change keyup', function(e){
            if (e.key === 'Escape') {   //cancela as alteraçoes e sai do modo de ediçao
                cancelEdit(wrapper);
                return;
            }
            if(e.type === 'blur' || e.type === 'change' || e.key === 'Enter'){
                let newValue = $input.val().trim();
                if(newValue === $value){    //cancela as alteraçoes e sai do modo de ediçao caso o valor seja igual
                    cancelEdit(wrapper);
                    return;
                }

                validateField($this, function(isValid){
                    if(!isValid) return;
                    
                    //SALVAR NOVO INPUT
                    saveField($this, function(isSaved){
                        if(!isSaved) return;
                    
                        // troca o input pelo span com o novo valor
                        if (fieldConf.type === 'select'){
                            const selected = fieldConf.options.find(opt => opt.value === newValue);
                            newValue = selected ? selected.label : newValue;
                        }

                        const $newSpan = $('<span>', { class: 'value', text: newValue });
                        cancelEdit($newSpan);
                    });
                
                });




            }
        });

        function cancelEdit(val){
            $input.replaceWith(val);
            $error.remove();
            $this.removeClass('notSaved');
        }
    });


}

function validateField($this, callback){
    const field = $this.data('field');
    const editable = $this.closest('.editable');
    const error = editable.find('.error');
    const input = $this.find('input, select, textarea');
    const value = input.val();


    $.post('includes/validateInputs.inc.php', {input: field, value: value}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);   //Motra o erro no console
            callback(false);
            return;
        }
        if (response.status === 'invalid'){
            console.warn('Input Invalido:', response.message);
            error.text(response.message);
            callback(false);
            return;
        }

        callback(true);

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
        error.text('Erro ao validar os dados.');
        callback(false);
    });
}

function saveField($this, callback){
    const field = $this.data('field');
    const editable = $this.closest('.editable');
    const error = editable.find('.error');
    const input = $this.find('input, select, textarea');
    const value = input.val();


    $.post('includes/saveServerData.inc.php', {action: 'saveProfileField', field: field, value: value}, function(response){
        console.log(response);
    
        if (response.status === 'error'){
            console.error('Erro:', response.message);
            callback(false);
            return;
        }
        if (response.status !== 'success'){
            console.warn('Falha ao salvar.');
            callback(false);
            return;
        }

       
        callback(true);

    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
        error.text('Erro na ligação ao servidor.');
        callback(false);
    }); 
}

function saveField2($this, callback){
    const field = $this.data('field');
    const error = $this.find('.error');
    const input = $this.find('input, select, textarea');
    const value = input.val();

    $.post('includes/saveServerData.inc.php', {action: 'saveProfileField', input: field, value: value}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);   //Motra o erro no console
            callback(false);
            return;
        }
        if (response.status === 'invalid'){
            console.warn('Input Invalido:', response.message);
            error.text(response.message);
            $this.addClass('invalid');
            callback(false);
            return;
        }

        $this.removeClass('invalid');
        callback(true);

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
        error.text('Erro ao validar os dados.');
        callback(false);
    });
}
// NORMAL

// function validateField($this){
//     const field = $this.data('field');
//     const error = $this.find('.error');
//     const input = $this.find('input');
//     const value = input.val();

//     $.post('includes/validateInputs.inc.php', {input: field, value: value}, function(response){
//         if (response.status === 'error'){
//             console.error('Erro:', response.message);   //Motra o erro no console
//             return false;
//         }
//         if (response.status === 'invalid'){
//             console.warn('Input Invalido:', response.message);
//             error.text(response.message);
//             $this.addClass('invalid');
//             return false;
//         }

//         $this.removeClass('invalid');
//         return true;

//     }, 'json').fail(function () {
//         console.error('Erro ao validar os dados.');
//         error.text('Erro ao validar os dados.');
//         return false;
//     });
// }

$(document).ready(function(){
    loadProfile();
});