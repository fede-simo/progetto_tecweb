window.addEventListener('load', () => {
  const title = document.getElementById('heroTitle');
  const text = document.getElementById('heroText');
  title.classList.add('visible');
  text.classList.add('visible');
});



const containers = document.querySelectorAll('.feature-box');

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index)=> {
        if(entry.isIntersecting){
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 800);

        }
    });
}, { threshold: 0.2 });

containers.forEach(container => observer.observe(container));


const containers2 = document.querySelectorAll('.feature-box2');

const observer2 = new IntersectionObserver((entries) => {
    entries.forEach((entry, index)=> {
        if(entry.isIntersecting){
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 800);

        }
    });
}, { threshold: 0.2 });

containers2.forEach(container => observer2.observe(container));