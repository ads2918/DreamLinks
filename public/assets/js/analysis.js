// Readings page: when a dream is waiting, ask the server to read it now and spin until the answer lands.
(function () {
	var card = document.querySelector('[data-reading-pending]');
	if (!card || !window.DreamLinks) {
		return;
	}
	var tries = 0;

	function fail(text) {
		card.classList.add('is-failed');
		card.querySelector('h2').textContent = "Couldn't read it just now.";
		card.querySelector('p').textContent = text;
	}

	function ask() {
		DreamLinks.post(card.getAttribute('data-reading-pending'))
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res.status === 'busy' && ++tries < 6) {
					setTimeout(ask, 3000); // another reading run is finishing; try again shortly
				} else if (res.status === 'failed' || res.status === 'busy') {
					fail("We'll try again in a few minutes. Your dream is saved.");
				} else {
					window.location.reload(); // done, capped or nothing left: the page shows where things stand
				}
			})
			.catch(function () { fail('Check your connection and refresh. Your dream is saved.'); });
	}
	ask();
})();
