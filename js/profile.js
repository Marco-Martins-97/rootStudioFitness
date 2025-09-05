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
                <div class='username-container'>
                    <div class='editable username first' data-field='firstName'><span class="value">${userProfile.firstName}</span></div>
                    <div class='editable username last' data-field='lastName'><span class="value">${userProfile.lastName}</span></div>
                </div>
                <p>${userProfile.userRole}</p>
            </div>
            <div class="profile-body">
                <div class='editable' data-field='email'>Email: <span class="value">${userProfile.email}</span></div>
        `;
        // Reset PWD

        // o array fieldsConfig contem toda a informaçao para a criação da estrutura do perfil do cliente, para a parte visual e editavel
        const fieldsConfig = [
            {field: 'firstName', label: '', type: 'input', inputType: 'text'},
            {field: 'lastName', label: '', type: 'input', inputType: 'text'},
            {field: 'email', label: 'Email', type: 'input', inputType: 'email'},
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

                    HTMLcontent += `<div class='editable' data-field='${field}'>${label}: <span class="value">${displayValue}</span></div>`;
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
    $('.profile-container').off('click', '.editable').on('click', '.editable', function(){
        const $this = $(this);
        const wrapper = $this.find('.value');
        const $value = wrapper.text().trim();
        const $field = $this.data('field');

        // prvine de criar um input ja existente
        if($this.find('input, select, textarea').length > 0) return;

        // procura no array fieldsConfig pelos dados do field correspondente clicado, caso naão enconte nao faz nada
        const fieldConf = fieldsConfig.find(f => f.field === $field);
        if (!fieldConf) return;

        
        // cria o input
        let $input;
        if (fieldConf.type === 'input'){
            
            $input = $('<input>', {
                class: 'edit-input',
                type: fieldConf.inputType || 'text',
                value: $value
            });
        }
        if (fieldConf.type === 'select'){
            $input = $('<select>', { class: 'edit-input' });
            fieldConf.options.forEach(opt => {
                const $opt = $('<option>', {
                    value: opt.value,
                    text: opt.label,
                    selected: opt.value === $value || opt.label === $value
                });
                $input.append($opt);
            });
        }
        if (fieldConf.type === 'textarea'){
            $input = $('<textarea>', {
                class: 'edit-input',
                placeholder: fieldConf.placeholder || '',
                text: $value
            });
        }

        // troca o span pelo input
        if (!$input) return;
        wrapper.replaceWith($input);
        const $error = $('<div>', { class: 'error' }).insertAfter($input);
        $input.focus();


        $input.on('blur keyup', function(e){
            if (e.key === 'Escape') {
                $(this).replaceWith(wrapper);
                $error.remove();
                return;
            }

            if(e.type === 'blur' || e.key === 'Enter'){
                let newValue = $input.val().trim();
                if(newValue === $value){
                    $(this).replaceWith(wrapper);
                    $error.remove();
                    return;
                }

                //Validar Field
                //Se retornar true(valido) - salva o valor
                //Caso contrario permanece em editar e mostra erro
                validateField($this, function(isValid){
                    if(!isValid) return;
                    


                    //SALVAR NOVO INPUT












                    // troca o input pelo span com o novo valor
                    if (fieldConf.type === 'select'){
                        const selected = fieldConf.options.find(opt => opt.value === newValue);
                        newValue = selected ? selected.label : newValue;
                    }

                    const $newSpan = $('<span>', { class: 'value', text: newValue });
                    $input.replaceWith($newSpan);
                    $error.remove();
                
                });
                
                // NORMAL
                // if (!validateField($this)){ 
                //     return;
                // }



                

            /* 
            if(e.type === 'blur' || e.key === 'Enter'){
                const newValue = input.val().trim();
                if(newValue === value){
                    $this.html(value);
                    return;
                }

                const $errorDiv = $this.find('.error');

                //Validar Field
                $.post('includes/validateInputs.inc.php', {input: field, value: newValue}, function(response){
                    if (response.status === 'error'){
                        console.error('Erro:', response.message);
                    }

                    if (response.status === 'invalid'){
                        console.warn('Input Invalido:', response.message);
                        $errorDiv.text(response.message);
                        input.addClass('invalid');
                        return;
                    }
                    $errorDiv.text('');
                    input.removeClass('invalid');

                    // Confirma alteração
                    if(field === 'email'){
                        const confirmChange = window.confirm(`Alterar o email para: ${newValue}`);
                        if (!confirmChange){
                            $this.html(value);
                            return;
                        }
                    }

                    // Salvar field
                    $.post('includes/saveServerData.inc.php', {action: 'saveProfileField', field: field, value: newValue}, function(response){
                        if (response.status === 'error') {
                            $this.html(value);
                            console.error('Erro:', response.message);
                        } else if (res.status !== 'success') {
                            $this.html(value);
                        } else {
                            $this.html(newValue);
                        } 
                        
                            // $this.replaceWith(`<span class="value">${value}</span>`);
                            // console.warn('Server error:', res.message || 'Unknown error');
                          
                            // $this.replaceWith(`<span class="value">${value}</span>`);
                            // console.warn('Failed To save!');
                          
                            // $this.replaceWith(`<span class="value">${newValue}</span>`);
                            // console.log(`Field "${field}" changed to: ${newValue}`);
                       
    
                    }, 'json').fail(function () {
                        $this.html(value);
                        console.error('Erro ao validar os dados.');
                    });


                }, 'json').fail(function () {
                    $this.html(value);
                    console.error('Erro ao validar os dados.');
                });

                
                */
            }
        });
    });
}

function validateField($this, callback){
    const field = $this.data('field');
    const error = $this.find('.error');
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
/* NORMAL

function validateField($this){
    const field = $this.data('field');
    const error = $this.find('.error');
    const input = $this.find('input');
    const value = input.val();

    $.post('includes/validateInputs.inc.php', {input: field, value: value}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);   //Motra o erro no console
            return false;
        }
        if (response.status === 'invalid'){
            console.warn('Input Invalido:', response.message);
            error.text(response.message);
            $this.addClass('invalid');
            return false;
        }

        $this.removeClass('invalid');
        return true;

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
        error.text('Erro ao validar os dados.');
        return false;
    });
} */

$(document).ready(function(){
    loadProfile();
});