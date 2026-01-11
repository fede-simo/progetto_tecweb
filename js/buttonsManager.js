
// BOTTONE PER TORNARE AL TOP COMPARE QUANDO SCENDE SOTTO IL 70% DELLA PRIMA SECTION


const backToTop = document.getElementById('back-to-top');


const trigger = document.getElementById('section1')

const backToTopObs = new IntersectionObserver( (entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) {
            backToTop.classList.add("visible");
        } else {
            backToTop.classList.remove("visible");
        }
    });
},
  { threshold: 0.7 }
);

backToTopObs.observe(trigger);

backToTop.addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});