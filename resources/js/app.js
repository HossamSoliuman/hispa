import './bootstrap';

const themeButtons = document.querySelectorAll('[data-theme-toggle]');

function syncThemeControls(theme) {
    themeButtons.forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
    });
}

function setPublicTheme(theme) {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('public_theme', theme);
    syncThemeControls(theme);
}

syncThemeControls(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');

themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
        setPublicTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
    });
});

window.addEventListener('storage', (event) => {
    if (event.key === 'public_theme' && (event.newValue === 'dark' || event.newValue === 'light')) {
        document.documentElement.dataset.theme = event.newValue;
        syncThemeControls(event.newValue);
    }
});

const menuButton = document.getElementById('menuBtn');
const closeMenuButton = document.getElementById('closeMenuBtn');
const menuOverlay = document.getElementById('mobileMenuOverlay');
const menuSheet = document.getElementById('mobileMenuSheet');

function setMenuOpen(isOpen) {
    if (! menuButton || ! menuOverlay || ! menuSheet) {
        return;
    }

    menuButton.setAttribute('aria-expanded', String(isOpen));
    menuOverlay.classList.toggle('hidden', ! isOpen);
    menuSheet.classList.remove('translate-x-full', '-translate-x-full', 'translate-x-0');
    menuSheet.classList.add(isOpen ? 'translate-x-0' : (document.documentElement.dir === 'rtl' ? '-translate-x-full' : 'translate-x-full'));
    document.body.classList.toggle('overflow-hidden', isOpen);
}

menuButton?.addEventListener('click', () => {
    setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
});

closeMenuButton?.addEventListener('click', () => setMenuOpen(false));
menuOverlay?.addEventListener('click', () => setMenuOpen(false));
menuSheet?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMenuOpen(false));
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setMenuOpen(false);
    }
});
