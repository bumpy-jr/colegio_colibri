'use strict';

document.addEventListener('DOMContentLoaded', () => {
  iniciarMenu();
  iniciarAparicion();
  iniciarContadores();
  iniciarFiltros();
  iniciarFormularios();
});

function iniciarMenu() {
  const boton = document.getElementById('menuBoton');
  const menu = document.getElementById('menuPrincipal');
  if (!boton || !menu) return;

  const cerrar = () => {
    menu.classList.remove('abierto');
    boton.setAttribute('aria-expanded', 'false');
  };
  boton.addEventListener('click', () => {
    const abierto = menu.classList.toggle('abierto');
    boton.setAttribute('aria-expanded', String(abierto));
  });
  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && menu.classList.contains('abierto')) {
      cerrar();
      boton.focus();
    }
  });
  menu.querySelectorAll('a').forEach((enlace) => enlace.addEventListener('click', cerrar));
  window.addEventListener('resize', () => {
    if (window.matchMedia('(min-width: 761px)').matches) cerrar();
  });
}

function iniciarAparicion() {
  const elementos = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window) ||
      window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    elementos.forEach((el) => el.classList.add('visible'));
    return;
  }
  const observador = new IntersectionObserver((entradas) => {
    entradas.forEach((entrada) => {
      if (entrada.isIntersecting) {
        entrada.target.classList.add('visible');
        observador.unobserve(entrada.target);
      }
    });
  }, { threshold: 0.12 });
  elementos.forEach((el) => observador.observe(el));
}

function iniciarContadores() {
  const elementos = document.querySelectorAll('[data-contador]');
  if (!elementos.length) return;
  const reducir = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  elementos.forEach((el) => {
    const meta = Number(el.dataset.contador);
    if (!Number.isFinite(meta)) return;
    if (reducir || !('IntersectionObserver' in window)) {
      el.textContent = String(meta);
      return;
    }
    const observador = new IntersectionObserver((entradas) => {
      if (!entradas[0].isIntersecting) return;
      const inicio = performance.now();
      const duracion = 1000;
      const paso = (ahora) => {
        const avance = Math.min((ahora - inicio) / duracion, 1);
        el.textContent = String(Math.round(meta * avance));
        if (avance < 1) requestAnimationFrame(paso);
      };
      requestAnimationFrame(paso);
      observador.disconnect();
    });
    observador.observe(el);
  });
}

function iniciarFiltros() {
  const botones = document.querySelectorAll('[data-filtro]');
  const tarjetas = document.querySelectorAll('[data-categoria]');
  const mensaje = document.getElementById('mensajeVacio');
  if (!botones.length || !tarjetas.length) return;

  botones.forEach((boton) => {
    boton.addEventListener('click', () => {
      const filtro = boton.dataset.filtro;
      let visibles = 0;
      botones.forEach((otro) => {
        const activo = otro === boton;
        otro.classList.toggle('filtro--activo', activo);
        otro.setAttribute('aria-pressed', String(activo));
      });
      tarjetas.forEach((tarjeta) => {
        const mostrar = filtro === 'todas' || tarjeta.dataset.categoria === filtro;
        tarjeta.hidden = !mostrar;
        if (mostrar) visibles++;
      });
      if (mensaje) mensaje.hidden = visibles !== 0;
    });
  });
}

function iniciarFormularios() {
  document.querySelectorAll('input[type="tel"]').forEach((campo) => {
    campo.addEventListener('input', () => {
      campo.value = campo.value.replace(/[^\d\s-]/g, '').slice(0, 14);
    });
  });

  document.querySelectorAll('form[novalidate]').forEach((formulario) => {
    formulario.addEventListener('submit', (evento) => {
      if (!formulario.checkValidity()) {
        evento.preventDefault();
        formulario.reportValidity();
      }
    });
  });
}
