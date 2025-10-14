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
    const params = new URLSearchParams(window.location.search);
    if (params.has('login')) {
        const loginStatus = params.get('login');

        if (loginStatus === 'success') showPopup('Login realizado com sucesso!', 3000, true);
    } else if (params.has('logout')){
        const logoutStatus = params.get('logout');

        if (logoutStatus === 'success') showPopup('Logout realizado com sucesso!', 3000, true);
    }
});