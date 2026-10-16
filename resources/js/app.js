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

const kioskInput = document.querySelector('[data-kiosk-input]');
const kioskResult = document.querySelector('[data-kiosk-result]');

function focusKioskInput() {
  kioskInput?.focus();
}

if (kioskInput) {
  focusKioskInput();
  document.addEventListener('pointerdown', focusKioskInput);
}

if (kioskResult) {
  const resetUrl = kioskResult.dataset.kioskResetUrl;
  const countdown = kioskResult.querySelector('[data-kiosk-countdown]');
  let seconds = Number(countdown?.textContent ?? 4);

  const interval = window.setInterval(() => {
    seconds -= 1;

    if (countdown) {
      countdown.textContent = String(Math.max(seconds, 0));
    }

    if (seconds <= 0) {
      window.clearInterval(interval);
      window.location.assign(resetUrl);
    }
  }, 1000);
}
