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
                <h2>${username}</h2>
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

    }, 'json').fail(function () {
        $('.profile-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Dados de Utilizador!');
    });
}

$(document).ready(function(){
    loadProfile();
});