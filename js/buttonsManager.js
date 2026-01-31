document.addEventListener("DOMContentLoaded", () => {
    const backToTop = document.getElementById("back-to-top");

    if (!backToTop) return;

    // Mostra il bottone dopo un po' di scroll
    window.addEventListener("scroll", () => {
        if (window.scrollY > 10) {
            backToTop.style.display = "flex";
        } else {
            backToTop.style.display = "none";
        }
    });

    // Torna in cima
    backToTop.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
});