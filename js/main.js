$(document).ready(function(){
    const params = new URLSearchParams(window.location.search);
    if (params.has('login')) {
        const loginStatus = params.get('login');

        if (loginStatus === 'success'){

            let loginPopup = $(`<div class='popup popup-success'>Login realizado com sucesso!</div>`).appendTo('main');
            // remove popup depois de 3 segundo
            setTimeout(function(){
                loginPopup.fadeOut(300, function(){ $(this).remove(); });
            }, 3000);
        }
    } else if (params.has('logout')){
        const logoutStatus = params.get('logout');

        if (logoutStatus === 'success'){

            let logoutPopup = $(`<div class='popup popup-success'>Logout realizado com sucesso!</div>`).appendTo('main');
            // remove popup depois de 3 segundo
            setTimeout(function(){
                logoutPopup.fadeOut(300, function(){ $(this).remove(); });
            }, 3000);
        }
    }
});