'use strict';
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.nav');
  if (toggle && nav) toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('nav-open');
    toggle.setAttribute('aria-expanded', String(open));
  });
  document.querySelectorAll('[data-confirm]').forEach((button) => button.addEventListener('click', (event) => {
    if (!window.confirm(button.dataset.confirm)) event.preventDefault();
  }));
  document.querySelectorAll('form[novalidate]').forEach((form) => form.addEventListener('submit', (event) => {
    if (!form.reportValidity()) event.preventDefault();
  }));
});
