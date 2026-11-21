/* Dream form: live symbol spotting and a character count.
   The symbol list comes from App\Libraries\DreamSymbols, so it matches what the server counts. */
(function () {
	var text = document.getElementById('full_description');
	var title = document.getElementById('title');
	var box = document.getElementById('dl-symbols');
	var list = document.getElementById('dl-symbol-list');
	var count = document.getElementById('dl-count');
	var lexEl = document.getElementById('dl-lexicon');
	if (!text || !lexEl) return;

	var LEX = JSON.parse(lexEl.textContent);
	var patterns = Object.keys(LEX).map(function (sym) {
		return { sym: sym, re: new RegExp('\\b(?:' + LEX[sym] + ')', 'i') };
	});

	function tagify() { return window.dlTagify; }
	function hasTag(sym) {
		var t = tagify();
		return t ? t.value.some(function (v) { return v.value.toLowerCase() === sym; }) : false;
	}

	function render() {
		var src = title.value + ' ' + text.value;
		var found = patterns.filter(function (p) { return p.re.test(src); }).map(function (p) { return p.sym; });
		list.innerHTML = '';
		found.forEach(function (sym) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'dl-chip';
			b.textContent = sym;
			var added = hasTag(sym);
			b.setAttribute('aria-pressed', added ? 'true' : 'false');
			b.addEventListener('click', function () {
				var t = tagify();
				if (!t || hasTag(sym)) return;
				t.addTags([sym]);
				b.setAttribute('aria-pressed', 'true');
			});
			list.appendChild(b);
		});
		box.hidden = found.length === 0;
		var left = 700 - text.value.length;
		count.textContent = left < 120 ? left + ' characters left' : '';
	}

	var timer;
	function soon() { clearTimeout(timer); timer = setTimeout(render, 150); }
	text.addEventListener('input', soon);
	title.addEventListener('input', soon);
	// re-mark chips when tags are removed by hand
	if (tagify()) tagify().on('remove', render);
	render();
})();
