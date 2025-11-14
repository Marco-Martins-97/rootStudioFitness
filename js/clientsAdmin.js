function loadApplications(type = 'all'){
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
            let applications = response.data;

            if (type === 'pending'){
                applications = response.data.filter(app => app.status === 'pending');
            }

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
                            <button class="accept-btn" data-id="${application.applicationId}">Aceitar</button>
                            <button class="reject-btn" data-id="${application.applicationId}">Recusar</button>
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
        <div class='create-new-exercise-container'>
            <button id="create-new-exercise-btn">Criar novo exercicio</button>
        </div>
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
            exerciseHTML += `<ul class='exercises-container'>`;
            exercisesData.forEach(exercise => {
                exerciseHTML += `
                    <li class='exercise-card'>
                        <img src='imgs/exercises/${exercise.exerciseImgSrc}' alt='${exercise.exerciseName}' class='exercise-img' onerror='this.src="imgs/logo/logoOriginal.png"'>
                        <h4 class='exercise-name'>${exercise.exerciseName}</h4>
                        <div class='exercise-actions'>
                            <button class='btn-edit-exercise' data-id='${exercise.id}' data-name='${exercise.exerciseName}'>Editar</button>
                            <button class='btn-delete-exercise' data-id='${exercise.id}' data-name='${exercise.exerciseName}'>Apagar</button>
                        </div>
                    </li>
                `;
            });
            exerciseHTML += `</ul>`;
        } else {
            exerciseHTML += `<p>Sem exercícios disponíveis.</p>`;
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

function editExercise(optionVal, exerciseId, exerciseName){
    const modal =  `<div class='modal' id='editExercise'>
                        <div class='modal-content'>
                            <span id='close-edit-modal'>&times;</span>
                            <h2>Editar Exercicio: ${exerciseName}</h2>
                            <div class='field-container'>
                                <div class='field'>
                                    <label for='exercise-img'>Imagem (upload):</label>
                                    <input type='file' name='exercise-img' id='exerciseImg' accept='image/*'>
                                </div>
                                <div class='field'>
                                    <label for='exercise-name'>Nome:</label>
                                    <input type='text' name='exercise-name' id='exerciseName' maxlength='255' value='${exerciseName}'>
                                </div>
                                <div class="error"></div>
                            </div>
                            <div class='btn-container'>
                                <button id='saveEdit'>Salvar</button>
                                <button id='canceledit'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.display-container').append(modal);

    $('#close-edit-modal, #canceledit').on('click', function() {
        $('#editExercise').remove(); // remove o modal
    });

    $('#saveEdit').on('click', function() {
        const exerciseNewImg = $('#exerciseImg')[0].files[0];
        const exerciseNewName = $('#exerciseName').val().trim();
        const $error = $('.error');

        const sameName = exerciseName === exerciseNewName;

        // verifica se foram realizadas alteraçoes antes de validar e salvar
        if (!exerciseNewImg && sameName){
            console.warn('Não foram realizadas alterações.')
            $('#editExercise').remove();
            return;
        }

        let uploadImg = false;
        let imgSize = 0;
        let imgType = '';

        if (exerciseNewImg){
            uploadImg = true;
            imgSize = exerciseNewImg.size;
            imgType = exerciseNewImg.type;
        }

        const datapack = {
            uploadImg: uploadImg,
            valueImgSize: imgSize,
            valueImgType: imgType,
            valueName: exerciseNewName, 
        };

        //VALIDA OS INPUTS
        $.post('includes/validateInputs.inc.php', {input: 'updateExercise', datapack}, function(response){
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
            formData.append('action', 'updateExercise');
            formData.append('exerciseId', exerciseId);
            formData.append('imgFile', exerciseNewImg);
            formData.append('valueName', exerciseNewName);
            
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
                        $('#editExercise').remove();
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

function deleteExercise(optionVal, exerciseId, exerciseName){
    const modal =  `<div class='modal' id='deleteExercise'>
                        <div class='modal-content'>
                            <span id='close-del-modal'>&times;</span>
                            <h2>Apagar ${exerciseName}</h2>
                            <div class="error"></div>
                            <div class='btn-container'>
                                <button id='delete'>Apagar</button>
                                <button id='cancelDel'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.display-container').append(modal);

    $('#close-del-modal, #cancelDel').on('click', function() {
        $('#deleteExercise').remove(); // remove o modal
    });

    $('#delete').on('click', function() {
        $.post('includes/saveServerData.inc.php', {action: 'deleteExercise', exerciseId: exerciseId}, function(response){
            if (response.status === 'error') {
                console.error('Erro do servidor:', response.message || 'Erro desconhecido');
                return;
            }
            if (response.status === 'processError') {
                console.error('Erro: ', response.error);
                $('.error').text(response.message);
                return;
            } 

            const msg = response.message || 'Exercicio apagado com sucesso!';
            
            $('.error').css('color', 'green').text(msg);
            setTimeout(() => {
                $('#deleteExercise').remove();
                updateContent(optionVal);
            }, 1000);   
        
        }, 'json').fail(function () {
            console.error('Erro na ligação ao servidor.');
        });
    });
}

async function loadTrainingPlan(){
    let plansHTML = `
        <div class='create-new-plan-container'>
            <button id="create-new-plan-btn">Criar novo Plano de treino</button>
        </div>
    `;

    plansHTML += await $.ajax({
        url: 'includes/loadServerData.inc.php',
        method: 'POST',
        dataType: 'json',
        data: { action: 'loadClientsTrainingPlans' }
    }).then(response => {
        // console.log(response);
        if (!response.data || response.data === 'error') {
            console.error('Erro do servidor:', response.message || 'Erro desconhecido')
            return `<p>Erro ao carregar os planos de treino.</p>`;
        }

        const plansData = response.data;
        let planHTML = '';

        if (plansData.length > 0){
            // planHTML += `<ul class='exercises-container'>`;
            // plansData.forEach(exercise => {
            //     planHTML += `
            //         <li class='exercise-card'>
            //             <img src='imgs/exercises/${exercise.exerciseImgSrc}' alt='${exercise.exerciseName}' class='exercise-img' onerror='this.src="imgs/logo/logoOriginal.png"'>
            //             <h4 class='exercise-name'>${exercise.exerciseName}</h4>
            //             <div class='exercise-actions'>
            //                 <button class='btn-edit-exercise' data-id='${exercise.id}' data-name='${exercise.exerciseName}'>Editar</button>
            //                 <button class='btn-delete-exercise' data-id='${exercise.id}' data-name='${exercise.exerciseName}'>Apagar</button>
            //             </div>
            //         </li>
            //     `;
            // });
            // planHTML += `</ul>`;
            console.log(plansData);
        } else {
            planHTML += `<p>Sem planos de treino disponíveis.</p>`;
        }

        return planHTML;
    }).catch(() => `<p>Erro ao carregar os planos de treino.</p>`);

    return plansHTML;
}

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

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container').each(function(){
        let input = $(this).find('input, textarea, select').first();

        /* // Radio Buttons
        if (input.is('input[type="radio"]') && $(this).find('input[type="radio"]:checked').length === 0){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        }
        // Ckeckbox
        if (input.is('input[type="checkbox"]') && $(this).find('input[type="checkbox"]:checked').length === 0){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        } */

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

  


/* function showPopup(msg, delay = 2000, success = false) {
    $('.popup').remove();// Remove um popup antes de criar outro (se existir)
    
    // Cria o elemento popup
    const popup = $('<div class="popup"></div>').text(msg);
    
    // Adiciona a classe "popup-success" apenas se success for true ou 1
    if (success === true || success === 1 || success === '1') {
        popup.addClass('popup-success');
    }
    // Insere no main e aplica delay + fadeOut
    popup.appendTo('main').delay(delay).fadeOut(300, function() { $(this).remove(); });
} */

function createNewTrainingPlan(optionVal){
    // let exercises = [];
    // const clients = loadClients();

    const modal =  `<div class='modal' id='createNewTrainingPlan'>
                        <div class='modal-content'>
                            <span id='close-add-modal'>&times;</span>
                            <h2>Novo Plano de Treino</h2>
                            <div class='plan-container'>
                                <div class='field-container'>
                                    <div class='field'>
                                        <label for='trainingPlanName'>Nome do Plano:</label>
                                        <input type='text' name='trainingPlanName' id='trainingPlanName' maxlength='255'>
                                    </div>
                                    <div class="error"></div>
                                </div>
                                <div class='field-container'>
                                    <div class='field'>
                                        <label for='trainingPlanClient'>Nome do Cliente:</label>
                                        <select name="trainingPlanClient" id="trainingPlanClients">
                                            <option value="" disabled selected>Selecione um Cliente</option>
                                        </select>
                                    </div>
                                    <div class="error"></div>
                                </div>
                                <div class='btn-container'>
                                    <button id='addExercise'>Exercicio</button>
                                </div>
                            </div>
                            <div class='btn-container'>
                                <button id='save'>Salvar</button>
                                <button id='cancelAdd'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.display-container').append(modal);

    $.post('includes/loadServerData.inc.php', {action: 'loadClients'}, function(response){
        console.log(response);
        let clients;
        if (response.status !== 'success') {
            console.error('Erro do servidor:', response.message || 'Erro desconhecido');
        }

        clients = response.data;

        clients.forEach(client => {
        $("#trainingPlanClients").append(
            $("<option>", {
                value: client.id,
                text: client.username
            })
        );
    });

    }, 'json').fail(function () {
        console.error('Ocorreu um erro. Não foi possível carregar os clientes!');
    });

    


    $('#addExercise').on('click', function() {
        $(this).parent().before(`
            <div class="exercise-container">
                NEW
                <div class="error"></div>
            </div>
            `);
    });


            // <div class="field">
            //     <label for='exercise-name'>Nome:</label>
            //     <input type='text' name='exercise-name' id='exerciseName' maxlength='255'>
            // </div>
            // <div class="field">
            //     <label for='exercise-rep'>Repetições:</label>
            //     <input type='number' name='exercise-rep' id='exerciseRep' min='1' value='1' step='1'>
            // </div>
            // <div class="field">
            //     <label for='load-rep'>Carga:</label>Load' min='0' value='0' step='1'>
            // </div>







    $('#close-add-modal, #cancelAdd').on('click', function() {
        $('#createNewTrainingPlan').remove(); // remove o modal
    });

    $('#trainingPlanName').on('input', function(){ validateField(this); });
    $('#trainingPlanClients').on('change', function(){ $(this).closest('.field-container').removeClass('invalid').find('.error').html(''); });

    $('#save').on('click', function() {
        const modalId = $('#createNewTrainingPlan');

        if(noEmptyFields(modalId) && isFormValid(modalId)){
            // compact data to a json
            const data = {
                planName: $('#trainingPlanName').val().trim(),
                userId: $('#trainingPlanClients').val().trim(),
                exercises: []
            }


            $.post('includes/saveServerData.inc.php', {action: 'saveTrainingPlan', planData: JSON.stringify(data)}, function(response){
                if (response.status === 'error') {
                    console.error('Erro do servidor:', response.message || 'Erro desconhecido');
                    return;
                } else if (response.status !== 'success') {
                    console.warn('Falha ao processar a ação!');
                    return
                }
            
                console.log(response.data);

                setTimeout(function(){
                    $('#createNewTrainingPlan').remove();
                    updateContent(optionVal);
                }, 1000);

            }, 'json').fail(function () {
                console.error('Erro na ligação ao servidor.');
            });
            
        } else {
            console.error('O Plano contém erros. Verifique os campos assinalados.');
        }
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
    general: () => loadApplications('pending'),
    applications: loadApplications,
    exercises: loadExercises,
    trainingPlans: loadTrainingPlan,
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
    // let optionVal = 'general';
    let optionVal = 'trainingPlans';
    updateContent(optionVal);

    $("#sub-menu").on("change", function() {
        optionVal = $(this).val();
        updateContent(optionVal);
    });

    // General / Applications
    toggleApplication();

    $(document).on('click', '.accept-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'accepted', optionVal);
    });

    $(document).on('click', '.reject-btn', function() {
        const applicationId = $(this).data('id');
        reviewApplication(applicationId, 'rejected', optionVal);
    });
    
    // Exercises
    $(document).on('click', '#create-new-exercise-btn', function() {
        createNewExercise(optionVal);
    });

    $(document).on('click', '.btn-edit-exercise', function() {
        const exerciseId = $(this).data('id');
        const exerciseName = $(this).data('name');
        editExercise(optionVal, exerciseId, exerciseName);
    });

    $(document).on('click', '.btn-delete-exercise', function() {
        const exerciseId = $(this).data('id');
        const exerciseName = $(this).data('name');
        deleteExercise(optionVal, exerciseId, exerciseName);
    });

    // Plans
    $(document).on('click', '#create-new-plan-btn', function() {
        createNewTrainingPlan(optionVal);
    });
});