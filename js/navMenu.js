function toggleMenu(toggleBtn, _menu, icon, iconBefore, iconAfter) {
    toggleBtn.addEventListener('click', function() {
        _menu.classList.toggle('active');

        if(icon.classList.contains(iconBefore)){
            icon.classList.remove(iconBefore);
            icon.classList.add(iconAfter);
        } else{
            icon.classList.remove(iconAfter);
            icon.classList.add(iconBefore);
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    //Esta parte altera a cor do favicon (trocando a imagem), dependendo do tema do navegador.
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const favicon = document.getElementById('favicon');
    
    function updateFavicon(e){
        favicon.href = e.matches ? 'imgs/logo/iconBranco.png' : 'imgs/logo/iconPreto.png';
    }

    updateFavicon(darkModeMediaQuery);
    darkModeMediaQuery.addEventListener('change',updateFavicon);


    //Abre e Fecha o menu, e altera o icon
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    const menuIcon = menuToggle.querySelector('i');
    toggleMenu(menuToggle, menu, menuIcon, 'fa-bars', 'fa-times');

    //Abre e Fecha o submenu, e altera o icon
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    if(dropdownToggle) {
        const dropdown = document.querySelector('.dropdown');
        const dropdownIcon = dropdownToggle.querySelector('i');
        toggleMenu(dropdownToggle, dropdown, dropdownIcon, 'fa-chevron-down', 'fa-chevron-up');
    }

    //altera o inicio da pagina conforme o menu e submenu está aberto ou fechado
    const mainElement = document.querySelector('main');

    function updateMainMargin() {
        let offset = 80;    // offset padrao

        if (menu.classList.contains('active')) {    //addiciona a altura do menu ao offset padrao
            offset += menu.offsetHeight;
        }

        if (window.innerWidth >= 768) { //altera entre a barra larga (H = 200px - apenas para desktop) e a padrao
            if (mainElement.classList.contains('start')) {
                offset = menu.offsetHeight;
            }
        } else {    // adicona o submenu ao offset (apenas no mobile)
            if(dropdownToggle) {
                const dropdown = document.querySelector('.dropdown');
                if (dropdown.classList.contains('active')) {
                    offset += dropdown.offsetHeight;
                }
            }
        }

        // aplica o offset no elemento main
        mainElement.style.marginTop = offset + 'px';
    }

    // atualiza ao abrir e fechar o menu/submenu
    menuToggle.addEventListener('click', () => {
        updateMainMargin();
    });

    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', () => {
            updateMainMargin();
        });
    }

    //Altera a imagem e a altura da navbar
    const navBar = document.querySelector('nav');
    const logo = document.querySelector('nav a img');

    function updateNavBar(){
        if (window.scrollY === 0 && window.innerWidth >= 768) {
            mainElement.classList.add('start');
            navBar.classList.add('start');
            logo.src = 'imgs/logo/logoOriginal.png';
        } else {
            mainElement.classList.remove('start');
            navBar.classList.remove('start');
            logo.src = 'imgs/logo/iconNomeOriginal.png';
        }
    }


    window.addEventListener('scroll', updateNavBar);
    window.addEventListener('resize', updateNavBar);
    window.addEventListener('scroll', updateMainMargin);
    window.addEventListener('resize', updateMainMargin);
    updateNavBar();
    updateMainMargin();
});