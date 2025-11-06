function loadApplications(){
    return new Promise((resolve) => {
        $.post('includes/loadServerData.inc.php', {action: 'loadClientApplications'}, function(response){
            if (!response || typeof response !== 'object') {
                console.error('Resposta JSON inválida:', response);
                resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
                return;
            }

            if (response.status === 'error') {
                console.warn('Erro do servidor:', response.message || 'Erro desconhecido');
                resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
                return;
            }

            let HTMLcontent = '';
            const applications = response.data;

            if(applications.length > 0){
                // Lista de palavras a substituir
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
                
                    for (let key in application) {
                        if (typeof application[key] === 'string') {
                            // Substitui para palavras em português
                            for (let word in replacements) {
                                const regex = new RegExp(`\\b${word}\\b`, "gi");
                                application[key] = application[key].replace(regex, replacements[word]);
                            }
                        }
                    }

                    // Cria o HTML com os dados da candidatura
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
            } else {
                HTMLcontent = '<h4>Não existem candidaturas Pendentes.</h4>';
            }

            resolve(`<div class="applications-container">${HTMLcontent}</div>`);
            

        }, 'json').fail(function () {
            resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
        });
    });
}

function loadPendingApplications(){
    return new Promise((resolve) => {
        $.post('includes/loadServerData.inc.php', {action: 'loadClientApplications'}, function(response){
            if (!response || typeof response !== 'object') {
                console.error('Resposta JSON inválida:', response);
                resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
                return;
            }

            if (response.status === 'error') {
                console.warn('Erro do servidor:', response.message || 'Erro desconhecido');
                resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
                return;
            }

            let HTMLcontent = '';
            // const applications = response.data;

            const applications = response.data.filter(app => app.status === 'pending');

            if(applications.length > 0){
                // Lista de palavras a substituir
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
                
                    for (let key in application) {
                        if (typeof application[key] === 'string') {
                            // Substitui para palavras em português
                            for (let word in replacements) {
                                const regex = new RegExp(`\\b${word}\\b`, "gi");
                                application[key] = application[key].replace(regex, replacements[word]);
                            }
                        }
                    }

                    // Cria o HTML com os dados da candidatura
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
            } else {
                HTMLcontent = '<h4>Não existem candidaturas Pendentes.</h4>';
            }

            resolve(`<div class="applications-container">${HTMLcontent}</div>`);
            

        }, 'json').fail(function () {
            resolve('Ocorreu um erro. Não foi possível carregar as candidaturas!');
        });
    });
}

async function loadExercises(){
    let exercisesHTML = `
        <button id="create-new-exercise-btn">Criar novo exercicio</button>
    `;

    exercisesHTML += await $.ajax({
        url: 'includes/loadServerData.inc.php',
        method: 'POST',
        dataType: 'json',
        data: { action: 'loadExercises' }
    }).then(response => {
        if (!response.data || response.data === 'error') {
            console.error('Erro do servidor:', response.message || 'Erro desconhecido')
            return `<p>Erro ao carregar exercícios.</p>`;
        }

        const exercisesData = response.data;

        let exerciseHTML = '';
        if (exercisesData.length > 0){
            // exercises.forEach(exercise => {
                
                exerciseHTML = exercisesData;
            // });

        } else {
            exerciseHTML = `<p>Sem exercícios disponíveis.</p>`;

        }

        return exerciseHTML;

    }).catch(() => `<p>Erro ao carregar exercícios.</p>`);

    // console.log(exercisesHTML);
    return exercisesHTML;
}

function createNewExercise(optionVal){
    const modal =  `<div class='modal' id='createNewExercise'>
                        <div class='modal-content'>
                            <span id='close-add-modal'>&times;</span>
                            <h2>Addicionar um Exercicio Novo</h2>
                            <div class='field-container'>
                                <div class='field'>
                                    <label for='exercise-img'>Imagem (upload):</label>
                                    <input type='file' name='exercise-img' id='exerciseImg' accept='image/*'>
                                </div>
                                <div class='field'>
                                    <label for='exercise-name'>Nome:</label>
                                    <input type='text' name='exercise-name' id='exerciseName' maxlength='255'>
                                </div>
                                <div class="error"></div>
                            </div>
                            <div class='btn-container'>
                                <button id='save'>Salvar</button>
                                <button id='cancelAdd'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.display-container').append(modal);

    $('#close-add-modal, #cancelAdd').on('click', function() {
        $('#createNewExercise').remove(); // remove o modal
    });

    $('#save').on('click', function() {
        const exerciseImg = $('#exerciseImg')[0].files[0];
        const exerciseName = $('#exerciseName').val().trim();
        const $error = $('.error');

        let uploadImg = false;
        let imgSize = 0;
        let imgType = '';

        if (exerciseImg){
            uploadImg = true;
            imgSize = exerciseImg.size;
            imgType = exerciseImg.type;
        }

        const datapack = {
            uploadImg: uploadImg,
            valueImgSize: imgSize,
            valueImgType: imgType,
            valueName: exerciseName, 
        };

        //VALIDA OS INPUTS
        $.post('includes/validateInputs.inc.php', {input: 'addNewExercise', datapack}, function(response){
            if (response.status === 'error'){
                console.error('Erro:', response.message);
                return;
            }
            if (response.status === 'invalid'){
                let msg = '';
                $.each(response.message, function(field, message){
                    msg += message + '<br>';
                    console.warn(`Input Invalido: ${field}: ${message}`);
                });

                $error.html(msg);
                return;
            }
            $error.text('');
            
            //SALVA O PRODUTO
            const formData = new FormData();
            formData.append('action', 'saveNewExercise');
            formData.append('imgFile', exerciseImg);
            formData.append('valueName', exerciseName);
            
            $.ajax({
                url: 'includes/saveServerData.inc.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                   if (response.status === 'error'){
                        console.error('Erro:', response.message);
                        return;
                    }
                    if (response.status === 'processError'){
                        console.error('Erro:', response.error);
                        $error.html(response.message);
                        return;
                    }
                    if (response.status === 'invalid'){
                        let msg = '';
                        $.each(response.message, function(field, message){
                            msg += message + '<br>';
                            console.warn(`Input Inválido: ${field}: ${message}`);
                        });

                        $error.html(msg);
                        return;
                    }

                    $error.css('color', 'green').text('Exercicio salvo com sucesso!');
                    setTimeout(() => {
                        $('#createNewExercise').remove();
                        updateContent(optionVal);
                    }, 1000);

                }, error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $error.text('Erro ao validar os dados.');
                }
            });
        }, 'json').fail(function () {
            console.error('Erro ao validar os dados.');
            $error.text('Erro ao validar os dados.');
        });
    });
}

function training(){
    return `<h2>Bem-vindo!</h2><p>Esta é a página treino da tua área de cliente.</p>`;
}

function toggleApplication(){
    $(document).on('click', '.application-title',  function(){
        const container = $(this).closest('.application-container');
        if (!container.hasClass('open')){
            container.addClass('open');
        } else {
            container.removeClass('open');
        }
    });
}

function reviewApplication(applicationId, review, optionVal){
    $.post('includes/saveServerData.inc.php', {action: 'reviewApplication', applicationId: applicationId, review: review}, function(response){
        if (response.status === 'error') {
            console.error('Erro do servidor:', response.message || 'Erro desconhecido');
        } else if (response.status !== 'success') {
            console.warn('Falha ao executar a ação!');
        }
       
        updateContent(optionVal);
    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
    });
}

const contentMap = {
    general: loadPendingApplications,
    applications: loadApplications,
    exercises: loadExercises,
    training: training,
    nutrition: () => `<h2>Bem-vindo!</h2><p>Esta é a página alimentar da tua área de cliente.</p>`,
    assessment: () => `<h2>Bem-vindo!</h2><p>Esta é a página avaliaçao da tua área de cliente.</p>`,
    calendar: () => `<h2>Bem-vindo!</h2><p>Esta é a página calendario da tua área de cliente.</p>`,
    challenges: () => `<h2>Bem-vindo!</h2><p>Esta é a página desafios da tua área de cliente.</p>`,
};

async function updateContent(id){
    const loader = `<div class="loader">A carregar...</div>`;
    $('.display-container').html(loader);

    let content = `<h2>Conteúdo não encontrado</h2>`;

    if (contentMap[id]) {
        const entry = contentMap[id];

        if (typeof entry === 'function') {
            const result = entry();
            content = result instanceof Promise ? await result : result;
        } else {
            content = entry;
        }
    }
    $('.display-container').hide().html(content).fadeIn(200);
    $(window).scrollTop(0);
}









$(document).ready(function(){
    let optionVal = 'general'
    updateContent(optionVal);

    $("#sub-menu").on("change", function() {
        optionVal = $(this).val();
        updateContent(optionVal);
    });

    toggleApplication();

    $(document).on('click', '#accept-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'accepted', optionVal);
    });

    $(document).on('click', '#reject-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'rejected', optionVal);
    });
    
    $(document).on('click', '#create-new-exercise-btn', function() {
        createNewExercise(optionVal);
    });
});