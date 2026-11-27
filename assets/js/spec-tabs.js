/* Kestrel: spec-sheet tabs (WAI-ARIA Authoring Practices, automatic activation). */
(() => {
	'use strict';
	document.querySelectorAll('[data-ka-tabs]').forEach((root) => {
		const list = root.querySelector('[role="tablist"]');
		const tabs = Array.from(root.querySelectorAll('[role="tab"]'));
		const panels = tabs.map((t) => document.getElementById(t.getAttribute('aria-controls')));
		if (!list || !tabs.length || panels.includes(null)) { return; }

		const select = (index, focus, user = true) => {
			tabs.forEach((tab, i) => {
				const on = i === index;
				tab.setAttribute('aria-selected', String(on));
				tab.tabIndex = on ? 0 : -1;
				panels[i].hidden = !on;
			});
			if (focus) { tabs[index].focus(); }
			if (user && history.replaceState) {
				history.replaceState(null, '', `#${tabs[index].id}`);
			}
		};

		list.hidden = false;
		root.classList.add('js-tabs');
		const fromHash = tabs.findIndex((t) => `#${t.id}` === location.hash);
		select(fromHash > -1 ? fromHash : 0, false, false);

		tabs.forEach((tab, i) => tab.addEventListener('click', () => select(i, false)));
		list.addEventListener('keydown', (e) => {
			const current = tabs.indexOf(document.activeElement);
			if (current < 0) { return; }
			const map = { ArrowRight: current + 1, ArrowLeft: current - 1, Home: 0, End: tabs.length - 1 };
			if (!(e.key in map)) { return; }
			e.preventDefault();
			select((map[e.key] + tabs.length) % tabs.length, true);
		});
	});
})();
