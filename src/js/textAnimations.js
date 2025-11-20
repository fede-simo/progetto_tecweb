// TITOLO, SOTTOTITOLO E p SLIDANO ALL'ACCESSO AL SITO

window.addEventListener('load', () => {
  const title = document.getElementById('hero-title');
  const subtitle = document.getElementById('hero-subtitle')
  const text = document.getElementById('hero-text');
  const linkContainer = document.getElementById('link-container');
  title.classList.add('visible');
  subtitle.classList.add('visible');
  text.classList.add('visible');
  linkContainer.classList.add('visible');
});



// SLIDE VERTICALE E ORIZZONTALE DEI CONTAINERS DELLA SECTION 2 E 3

const containersInfo = document.querySelectorAll('.feature-box');

const observerInfo = new IntersectionObserver((entries) => {
    entries.forEach((entry, index)=> {
        if(entry.isIntersecting){
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 800);

        }
    });
}, { threshold: 0.2 });

containersInfo.forEach(container => observerInfo.observe(container));


const containers2 = document.querySelectorAll('.feature-box2');

const observer2 = new IntersectionObserver((entries) => {
    entries.forEach((entry, index)=> {
        if(entry.isIntersecting){
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 800);

        }
    });
}, { threshold: 0.6 });

containers2.forEach(container => observer2.observe(container));
