// Año dinámico
document.getElementById('year').textContent = new Date().getFullYear();

// Toggle menú principal
const navToggle = document.querySelector('.nav-toggle');
const mainNav = document.getElementById('main-nav');
if(navToggle){
  navToggle.addEventListener('click', () => {
    const expanded = navToggle.getAttribute('aria-expanded') === 'true';
    navToggle.setAttribute('aria-expanded', String(!expanded));
    mainNav.classList.toggle('open');
  });
}

// Toggle menú de usuario
const avatarBtn = document.querySelector('.avatar-btn');
const userMenu = document.getElementById('user-menu');
if(avatarBtn){
  avatarBtn.addEventListener('click', () => {
    const open = avatarBtn.getAttribute('aria-expanded') === 'true';
    avatarBtn.setAttribute('aria-expanded', String(!open));
    userMenu.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (!e.target.closest('.user-nav')) {
      userMenu.classList.remove('open');
      avatarBtn.setAttribute('aria-expanded','false');
    }
  });
}

/* ===== Carrusel controlado (auto 5s + flechas + dots + swipe) ===== */
(function(){
  const root = document.querySelector('.carousel');
  if(!root) return;

  const track = root.querySelector('.carousel-track');
  const slides = Array.from(root.querySelectorAll('.carousel-slide'));
  const prev = root.querySelector('.prev');
  const next = root.querySelector('.next');
  const dotsWrap = root.querySelector('.carousel-dots');
  const interval = parseInt(root.dataset.interval || '5000', 10);

  // Dots
  slides.forEach((_,i)=>{
    const b=document.createElement('button');
    b.type='button';
    b.setAttribute('aria-label', `Ir a la imagen ${i+1}`);
    b.addEventListener('click', ()=>goTo(i,true));
    dotsWrap.appendChild(b);
  });

  let index = 0, timer = null, hovering = false;
  function update(){
    track.style.transform = `translateX(-${index*100}%)`;
    dotsWrap.querySelectorAll('button').forEach((d,i)=>d.setAttribute('aria-current', i===index ? 'true': 'false'));
  }
  function goTo(i, resetTimer){
    index = (i+slides.length)%slides.length;
    update();
    if(resetTimer) restart();
  }
  function nextSlide(){ goTo(index+1,false); }
  function prevSlide(){ goTo(index-1,false); }

  // Auto
  function start(){ if(timer) return; timer=setInterval(()=>{ if(!hovering) nextSlide(); }, interval); }
  function stop(){ clearInterval(timer); timer=null; }
  function restart(){ stop(); start(); }

  // Eventos
  next.addEventListener('click', ()=>goTo(index+1,true));
  prev.addEventListener('click', ()=>goTo(index-1,true));
  root.addEventListener('mouseenter', ()=>{ hovering=true; });
  root.addEventListener('mouseleave', ()=>{ hovering=false; });

  // Teclado
  root.addEventListener('keydown', (e)=>{
    if(e.key==='ArrowRight'){ e.preventDefault(); goTo(index+1,true); }
    if(e.key==='ArrowLeft'){ e.preventDefault(); goTo(index-1,true); }
  });
  root.tabIndex = 0; // para foco accesible

  // Swipe táctil
  let x0=null;
  root.addEventListener('touchstart', (e)=>{ x0 = e.touches[0].clientX; }, {passive:true});
  root.addEventListener('touchend', (e)=>{
    if(x0===null) return;
    const dx = e.changedTouches[0].clientX - x0;
    if(Math.abs(dx) > 40){ dx<0 ? goTo(index+1,true) : goTo(index-1,true); }
    x0=null;
  });

  // Init
  update(); start();
})();