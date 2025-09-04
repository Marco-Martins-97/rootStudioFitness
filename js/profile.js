function loadProfile(){
    $.post('includes/loadServerData.inc.php', {action: 'loadProfile'}, function(response){
        console.log(response);

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

        const username = `<span class='editable' data-field='firstName' data-required='true'><span class="value">${userProfile.firstName}</span></span>
                        <span class='editable' data-field='lastName' data-required='true'><span class="value">${userProfile.lastName}</span></span>`;
        HTMLcontent += `
            <div class="profile-header">
                <div class='username-container'>
                    <p class='editable' data-field='firstName' data-required='true'><span class="value">${userProfile.firstName}</span></p>
                    <p class='editable' data-field='lastName' data-required='true'><span class="value">${userProfile.lastName}</span></p>
                </div>
                <p>${userProfile.userRole}</p>
            </div>
            <div class="profile-body">
                <p class='editable' data-field='email' data-required='true'>Email: <span class="value">${userProfile.email}</span></p>
        `;
        /* 
            <h2>${userProfile.firstName} ${userProfile.lastName}</h2>
        <p class='editable' data-field='firstName' data-required='true'>Nome: <span class="value">${userProfile.firstName}</span></p>
                <p class='editable' data-field='lastName' data-required='true'>Apelido: <span class="value">${userProfile.lastName}</span></p> */
        // Reset PWD

        if (response.clientData !== null){
            const clientProfile = response.clientData;

            HTMLcontent += `
                <p class='editable' data-field='fullName' data-required='true'>Nome Completo: <span class="value">${clientProfile.fullName}</span></p>
                <p class='editable' data-field='birthDate' data-required='true'>Data de Nascimento: <span class="value">${clientProfile.birthDate}</span></p>
                <p class='editable' data-field='gender' data-required='true'>Gênero: <span class="value">${clientProfile.gender}</span></p>
                <p class='editable' data-field='userAddress' data-required='true'>Morada: <span class="value">${clientProfile.userAddress}</span></p>
                <p class='editable' data-field='nif' data-required='true'>Nif: <span class="value">${clientProfile.nif}</span></p>
                <p class='editable' data-field='phone' data-required='true'>Telefone: <span class="value">${clientProfile.phone}</span></p>
                <p class='editable' data-field='trainingPlan' data-required='true'>Plano de Treino: <span class="value">${clientProfile.trainingPlan}</span></p>
                <p class='editable' data-field='experience' data-required='true'>Experiência: <span class="value">${clientProfile.experience}</span></p>
                <p class='editable' data-field='nutritionPlan' data-required='true'>Plano Alimentar: <span class="value">${clientProfile.nutritionPlan}</span></p>
                <p class='editable' data-field='healthIssues' data-required='true'>Problemas de Saúde: <span class="value">${clientProfile.healthIssues}</span></p>
                <p class='editable' data-field='healthDetails' data-required='true'>Detalhes de Saúde: <span class="value">${clientProfile.healthDetails}</span></p>
                `;
        }

        HTMLcontent += '</div>';

        
        $('.profile-container').html(HTMLcontent);
        
        editField();

    }, 'json').fail(function () {
        $('.profile-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Dados de Utilizador!');
    });
}

function editField(){
    $('.profile-container').off('click', '.editable').on('click', '.editable', function(){
        const $this = $(this).find('.value');
        const value = $this.text().trim();
        const field = $(this).data('field');
        const required = $(this).data('required');

        if($this.find('input').length > 0) return;

        const input = $(`<input type="text" class="edit-input" value="${value}"></input> <div class="error"></div>`);
        $this.html(input);
        input.focus();

        input.on('blur keyup', function(e){
            if (e.key === 'Escape') {
                $this.html(value);
                return;
            }

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
                        /* 
                            // $this.replaceWith(`<span class="value">${value}</span>`);
                            // console.warn('Server error:', res.message || 'Unknown error');
                          
                            // $this.replaceWith(`<span class="value">${value}</span>`);
                            // console.warn('Failed To save!');
                          
                            // $this.replaceWith(`<span class="value">${newValue}</span>`);
                            // console.log(`Field "${field}" changed to: ${newValue}`);
                        */
    
                    }, 'json').fail(function () {
                        $this.html(value);
                        console.error('Erro ao validar os dados.');
                    });


                }, 'json').fail(function () {
                    $this.html(value);
                    console.error('Erro ao validar os dados.');
                });

            }


        });


    });
}

$(document).ready(function(){
    loadProfile();
});