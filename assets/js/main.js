/* Kestrel: mobile drawer + reveal-on-scroll. No dependencies. */
(() => {
	'use strict';
	document.documentElement.classList.add('js');

	const toggle = document.querySelector('[data-ka-toggle]');
	const drawer = document.querySelector('[data-ka-drawer]');
	if (toggle && drawer) {
		const setOpen = (open) => {
			toggle.setAttribute('aria-expanded', String(open));
			drawer.hidden = !open;
		};
		toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && !drawer.hidden) { setOpen(false); toggle.focus(); }
		});
		drawer.addEventListener('click', (e) => { if (e.target.closest('a')) { setOpen(false); } });
	}

	const items = document.querySelectorAll('[data-ka-reveal]');
	if (!('IntersectionObserver' in window)) {
		items.forEach((el) => el.classList.add('is-in'));
		return;
	}
	const io = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) { entry.target.classList.add('is-in'); io.unobserve(entry.target); }
		});
	}, { rootMargin: '0px 0px -8% 0px' });
	items.forEach((el, i) => { el.style.transitionDelay = `${(i % 3) * 70}ms`; io.observe(el); });
})();
