'use strict';
document.addEventListener('DOMContentLoaded', () => {
  // Menú móvil
  const b = document.querySelector('.burger'), m = document.getElementById('menu');
  b?.addEventListener('click', () => { const o = m.classList.toggle('abierto'); b.setAttribute('aria-expanded', String(o)); });
  // Aparición suave
  const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } }), { threshold: .15 });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));
  // Contadores animados
  document.querySelectorAll('[data-contar]').forEach(el => {
    const meta = Number(el.dataset.contar); let n = 0; const paso = Math.max(1, Math.ceil(meta / 60));
    const t = setInterval(() => { n = Math.min(meta, n + paso); el.textContent = String(n); if (n >= meta) clearInterval(t); }, 25);
  });
  // Filtro de experiencias
  const bs = document.querySelectorAll('[data-filtro]');
  bs.forEach(x => x.addEventListener('click', () => {
    bs.forEach(y => y.classList.toggle('activo', y === x));
    document.querySelectorAll('[data-cat]').forEach(c => { c.hidden = x.dataset.filtro !== 'todas' && c.dataset.cat !== x.dataset.filtro; });
  }));
  // Teléfono: solo dígitos
  document.querySelectorAll('input[type=tel]').forEach(i => i.addEventListener('input', () => { i.value = i.value.replace(/[^\d\s-]/g, ''); }));
});
