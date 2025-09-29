function loadApplications(){
    $.post('includes/loadServerData.inc.php', {action: 'loadClientApplications'}, function(response){
        // console.log(response);

        if (!response || typeof response !== 'object') {
            console.error('Invalid JSON response:', response);
            $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
            return;
        }

        if (response.status === 'error') {
            console.warn('Server error:', response.message || 'Unknown error');
            $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
            return;
        }

        let HTMLcontent = '';
        const applications = response.data;

        //lista de palavras para serem substuidas
        const replacements = {
            'pending': 'Pendente',
            'accepted': 'Aceite',
            'rejected': 'Recusado',
            'male': 'Masculino',
            'female': 'Feminino',
            'yes': 'Sim',
            'no': 'Não',
            'beginner': 'Iniciante',
            'intermediate': 'Intermédio',
            'advanced': 'Avançado',
            'personalized1': 'Individual',
            'personalized2': 'Grupos Reduzidos',
            'group': 'Aulas de Grupo',
            'terapy': 'Treino Terapêutico',
            'padel': 'Padel',
            'openStudio': 'Acesso ao Estúdio',
        };

        applications.forEach(application => {
            const applicationStatus = application.status;
            
            //Substitui as variaveis or palavras em portugues
            for (let key in application) {
                if (typeof application[key] === 'string') {
                    // Replace all words based on the replacements map
                    for (let word in replacements) {
                        const regex = new RegExp(`\\b${word}\\b`, "gi"); // g = global, i = case-insensitive
                        application[key] = application[key].replace(regex, replacements[word]);
                    }
                }
            }

            // cria o HTML com os dados
            HTMLcontent += `
                <div class="application-container">
                    <div class="application-title">
                        <div class="application-info">
                            <h3>${application.username}</h3>
                            <p>${application.submissionDate}</p>
                        </div>
                        <div class="application-status ${applicationStatus}">
                            <p>${application.status}</p>
                        </div>
                        <div class="application-arrow"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="application-data">
                        <div class="data-container"><span>Nome Completo:</span><p>${application.fullName}</p></div>
                        <div class="data-container"><span>Data de Nascimento:</span><p>${application.birthDate}</p></div>
                        <div class="data-container"><span>Gênero:</span><p>${application.gender}</p></div>
                        <div class="data-container"><span>Morada:</span><p>${application.userAddress}</p></div>
                        <div class="data-container"><span>Nif:</span><p>${application.nif}</p></div>
                        <div class="data-container"><span>Telefone:</span><p>${application.phone}</p></div>
                        <div class="data-container"><span>Plano de Treino:</span><p>${application.trainingPlan}</p></div>
                        <div class="data-container"><span>Experiência:</span><p>${application.experience}</p></div>
                        <div class="data-container"><span>Plano Alimentar:</span><p>${application.nutritionPlan}</p></div>
                        <div class="data-container"><span>Problemas de Saúde:</span><p>${application.healthIssues}</p></div>
                        <div class="data-container"><span class="details">Detalhes de Saúde:</span><p>${application.healthDetails}</p></div>
                    </div>
                    <div class="application-btns">
            `;
            if(applicationStatus === 'pending'){
                HTMLcontent += `
                    <button id="accept-btn" data-id="${application.applicationId}">Aceitar</button>
                    <button id="reject-btn" data-id="${application.applicationId}">Recusar</button>
                `;
            }
            HTMLcontent += `
                    </div>
                </div>
            `;
        });

        $('.applications-container').html(HTMLcontent);

    }, 'json').fail(function () {
        $('.applications-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Inscrições!');
    });
}

function toggleApplication(){
    $(document).on('click', '.application-title',  function(){
        const container = $(this).closest('.application-container');
        if (!container.hasClass('open')){
            container.addClass('open');
        } else {
            container.removeClass('open');
        }
        // $(this).closest('.application-container').find('.application-data').stop(true, true).slideToggle(300);
    });



    /* $(document).on('click', '.application-title', function() {
        const container = $(this).closest('.application-container');
        const data = container.find('.application-data');
        // const arrowIcon = $(this).find('.application-arrow i');

        // arrowIcon.toggleClass('fa-chevron-up fa-chevron-down');
        container.toggleClass('open');
        data.stop(true, true).slideToggle(300);
    }); */
}

function reviewApplication(applicationId, review){
    $.post('includes/saveServerData.inc.php', {action: 'reviewApplication', applicationId: applicationId, review: review}, function(response){
        console.log(response);
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Unknown error');
        } else if (response.status !== 'success') {
            console.warn('Falha ao executar!');
        } else {
            console.log(`Application - Id= "${applicationId}", alterada para ${review} com ${response.status}`);
        }
       
        loadApplications(); 
    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
    });
}


$(document).ready(function(){
    loadApplications();
    toggleApplication();

    $(document).on('click', '#accept-btn', function() {
         const applicationId = $(this).data('id');
         reviewApplication(applicationId, 'accepted');
     });

    $(document).on('click', '#reject-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'rejected');
    });

});