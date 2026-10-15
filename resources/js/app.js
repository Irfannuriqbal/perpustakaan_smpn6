import './bootstrap';

const sidebar = document.querySelector('[data-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');
const openButton = document.querySelector('[data-sidebar-open]');
const closeButton = document.querySelector('[data-sidebar-close]');

function openSidebar() {
  sidebar?.classList.remove('-translate-x-full');
  overlay?.classList.remove('hidden');
}

function closeSidebar() {
  sidebar?.classList.add('-translate-x-full');
  overlay?.classList.add('hidden');
}

openButton?.addEventListener('click', openSidebar);
closeButton?.addEventListener('click', closeSidebar);
overlay?.addEventListener('click', closeSidebar);
