const contentMap = {
    home: () => `<div class="home-container">
                    <div class="selector-btn" data-id="training">
                        <h3>Plano de Treino</h3>
                    </div>
                    <div class="selector-btn" data-id="nutrition">
                        <h3>Plano Alimentar</h3>
                    </div>
                    <div class="selector-btn" data-id="assessment">
                        <h3>Avaliação Fisica</h3>
                    </div>
                    <div class="selector-btn" data-id="calendar">
                        <h3>Calendario</h3>
                    </div>
                    <div class="selector-btn" data-id="challenges">
                        <h3>Desafios</h3>
                    </div>
                    <div class="selector-btn" data-id="info">
                        <h3>Info</h3>
                    </div>
                </div>`,
    training: training,
    nutrition: () => `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página alimentar da tua área de cliente.</p>`,
    assessment: () => `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página avaliaçao da tua área de cliente.</p>`,
    calendar: () => `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página calendario da tua área de cliente.</p>`,
    challenges: () => `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página desafios da tua área de cliente.</p>`,
    info: () => `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página info da tua área de cliente.</p>`,
};

async function updateContent(id){
    const loader = `<div class="loader">A carregar...</div>`;
    $('.display-content').html(loader);

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
    $('.display-content').hide().html(content).fadeIn(200);
    $(window).scrollTop(0);
}

function training(){
    return `<div class="selector-btn return-btn" data-id="home"><h3>Voltar</h3></div><h2>Bem-vindo!</h2><p>Esta é a página treino da tua área de cliente.</p>`;
}


$(document).ready(function(){

    $(document).on('click', '.selector-btn', function() {
        const id = $(this).data('id');
        // updateContent(id);
        window.location.hash = id;
    });

    $(window).on('hashchange', function() {
        const id = window.location.hash.substring(1) || 'home';
        updateContent(id);
    });

    const defaultId = window.location.hash.substring(1) || 'home';
    updateContent(defaultId);

});