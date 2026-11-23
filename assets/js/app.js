(function () {
	'use strict';
	var loaded = Date.now();
	var toast = document.getElementById('toast');
	function notify(message) {
		if (!toast) { return; }
		toast.textContent = message;
		toast.classList.add('open');
		clearTimeout(notify._t);
		notify._t = setTimeout(function () { toast.classList.remove('open'); }, 6000);
	}

	// Menu mobile
	var burger = document.getElementById('burger');
	var navlinks = document.getElementById('navlinks');
	if (burger && navlinks) {
		burger.addEventListener('click', function () { var open = navlinks.classList.toggle('open'); burger.setAttribute('aria-expanded', open ? 'true' : 'false'); burger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu'); });
		navlinks.querySelectorAll('a').forEach(function (a) {
			a.addEventListener('click', function () { navlinks.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); burger.setAttribute('aria-label', 'Ouvrir le menu'); });
		});
	}

	// Recherche en direct dans les références
	var search = document.getElementById('roster-search');
	if (search) {
		var norm = function (s) { return s.toLocaleLowerCase('fr').normalize('NFD').replace(/[\u0300-\u036f]/g, ''); };
		search.addEventListener('input', function () {
			var term = norm(search.value).trim();
			var visible = 0;
			document.querySelectorAll('.all-references .roster-item').forEach(function (item) {
				item.hidden = !norm(item.textContent).includes(term);
				if (!item.hidden) { visible++; }
			});
			var empty = document.getElementById('roster-empty');
			if (empty) { empty.hidden = visible > 0; }
		});
	}

	// Formulaire de contact (envoi réel par e-mail via WordPress)
	var form = document.getElementById('klo-contact');
	if (form && window.KLO) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (!form.reportValidity()) { return; }
			var btn = form.querySelector('button[type="submit"]');
			var label = btn.textContent;
			btn.disabled = true;
			btn.textContent = 'Envoi en cours…';
			var data = new FormData(form);
			data.append('action', 'klo_contact');
			data.append('t', String(Date.now() - loaded));
			fetch(window.KLO.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					var msg = (res && res.data && res.data.message) || 'Une erreur est survenue.';
					notify(msg);
					if (res && res.success) { form.reset(); }
				})
				.catch(function () { notify('L’envoi a échoué. Merci de réessayer dans quelques instants.'); })
				.finally(function () { btn.disabled = false; btn.textContent = label; });
		});
	}
})();
