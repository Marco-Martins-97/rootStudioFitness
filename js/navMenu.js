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
    // Esta parte altera a cor do favicon (trocando a imagem), dependendo do tema do navegador
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const favicon = document.getElementById('favicon');
    
    function updateFavicon(e){
        favicon.href = e.matches ? 'imgs/logo/iconBranco.png' : 'imgs/logo/iconPreto.png';
    }

    updateFavicon(darkModeMediaQuery);
    darkModeMediaQuery.addEventListener('change',updateFavicon);


    // Abre e fecha o menu e altera o ícone
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    const menuIcon = menuToggle.querySelector('i');
    toggleMenu(menuToggle, menu, menuIcon, 'fa-bars', 'fa-times');

    // Abre e fecha o submenu e altera o ícone
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    if(dropdownToggle) {
        const dropdown = document.querySelector('.dropdown');
        const dropdownIcon = dropdownToggle.querySelector('i');
        toggleMenu(dropdownToggle, dropdown, dropdownIcon, 'fa-chevron-down', 'fa-chevron-up');
    }

    // Altera o início da página conforme o menu e o submenu estão abertos ou fechados
    const mainElement = document.querySelector('main');

    function updateMainMargin() {
        let offset = 80;    // offset padrao

        if (menu.classList.contains('active')) {    // Adiciona a altura do menu ao offset padrão
            offset += menu.offsetHeight;
        }

        if (window.innerWidth >= 768) { // Altera entre a barra larga (H = 200px - apenas para desktop) e a padrão
            if (mainElement.classList.contains('start')) {
                offset = menu.offsetHeight;
            }
        } else {    // Adiciona o submenu ao offset (apenas no mobile)
            if(dropdownToggle) {
                const dropdown = document.querySelector('.dropdown');
                if (dropdown.classList.contains('active')) {
                    offset += dropdown.offsetHeight;
                }
            }
        }

        // Aplica o offset ao elemento main
        mainElement.style.marginTop = offset + 'px';
    }

    // Atualiza ao abrir e fechar o menu/submenu
    menuToggle.addEventListener('click', () => {
        updateMainMargin();
    });

    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', () => {
            updateMainMargin();
        });
    }

    // Altera a imagem e a altura da navbar
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