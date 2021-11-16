// menu hamburger
const hamburger = document.querySelector('.hamburger');
const navOverlay = document.querySelector('.nav-overlay');
const mobileNav = document.querySelector('.mobile-nav');

const openMenu = () => {
    navOverlay.classList.add('open');
    mobileNav.classList.add('open');
}

const closeMenu = () => {
    navOverlay.classList.remove('open');
    mobileNav.classList.remove('open');
}

hamburger.addEventListener('click', openMenu);
navOverlay.addEventListener('click', closeMenu);


