// Readings page: while a reading is being written, check back every 20 seconds for up to 5 minutes.
(function () {
	if (!document.querySelector('[data-reading-pending]')) {
		try { sessionStorage.removeItem('readingChecks'); } catch (e) {}
		return;
	}
	var checks = 0;
	try { checks = Number(sessionStorage.getItem('readingChecks')) || 0; } catch (e) {}
	if (checks >= 15) {
		return;
	}
	setTimeout(function () {
		try { sessionStorage.setItem('readingChecks', String(checks + 1)); } catch (e) {}
		window.location.reload();
	}, 20000);
})();
