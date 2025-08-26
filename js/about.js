document.addEventListener("DOMContentLoaded", () => {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach(item => {
        const question = item.querySelector(".faq-question");
        const icon = item.querySelector("i");


        question.addEventListener("click", () => {
            const isOpen = item.classList.contains("open");

            // Fecha todos os itens
            faqItems.forEach(faq => {
                faq.classList.remove("open");
                faq.querySelector("i").classList.remove("fa-minus");
                faq.querySelector("i").classList.add("fa-plus");
            });

            // altera o icon e remove o "open"
            if (!isOpen) {
                item.classList.add("open");
                icon.classList.remove("fa-plus");
                icon.classList.add("fa-minus");
            }
        });
    });
});