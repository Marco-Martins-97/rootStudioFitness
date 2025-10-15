document.addEventListener("DOMContentLoaded", () => {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach(item => {
        const question = item.querySelector(".faq-question");
        const icon = item.querySelector("i");


        question.addEventListener("click", () => {
            const isOpen = item.classList.contains("open");

            // Fecha todos os itens do FAQ
            faqItems.forEach(faq => {
                faq.classList.remove("open");
                const faqIcon = faq.querySelector("i");
                faqIcon.classList.remove("fa-minus");
                faqIcon.classList.add("fa-plus");
            });

            // Altera o ícone e adiciona ou remove a classe "open"
            if (!isOpen) {
                item.classList.add("open");
                icon.classList.remove("fa-plus");
                icon.classList.add("fa-minus");
            }
        });
    });
});