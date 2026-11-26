// Journal: filters apply as soon as a dropdown changes, and deleting a dream asks first.
(function () {
	var form = document.querySelector('form[data-autosubmit]');
	if (form) {
		form.classList.add('is-live'); // hides the Search button; Enter in the search box still submits
		form.querySelectorAll('select').forEach(function (select) {
			select.addEventListener('change', function () { form.submit(); });
		});
	}
	document.querySelectorAll('form[data-confirm]').forEach(function (del) {
		del.addEventListener('submit', function (e) {
			if (!window.confirm(del.getAttribute('data-confirm'))) e.preventDefault();
		});
	});
})();
