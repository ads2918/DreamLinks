/* Dreamscape: draws the symbol connection map and the moon icons. No dependencies. */
(function () {
	var C = { pink: '#ff4f9a', teal: '#18b7a6', yellow: '#ffd93d', violet: '#7a5cff', ink: '#26134a', card: '#ffffff' };
	var NS = 'http://www.w3.org/2000/svg';

	// moon icons under the phase bars
	document.querySelectorAll('.dl-moonbars .moon').forEach(function (el) {
		el.innerHTML = DreamLinks.moonSVG(parseFloat(el.getAttribute('data-phase')), 20, C.yellow, C.ink);
	});

	// tap a moon bar to open that phase's symbols and dreams; tap again to close
	var moonTabs = document.querySelectorAll('[data-moon-tab]');
	moonTabs.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-pressed') !== 'true';
			moonTabs.forEach(function (b) {
				var on = open && b === btn;
				b.setAttribute('aria-pressed', on ? 'true' : 'false');
				document.getElementById(b.getAttribute('aria-controls')).hidden = !on;
			});
		});
	});

	var host = document.getElementById('dl-map');
	var dataEl = document.getElementById('dl-graph');
	if (!host || !dataEl) return;
	var DATA = JSON.parse(dataEl.textContent);
	var G = DATA.graph, comparing = !!DATA.compare;
	if (!G.nodes.length) return;

	var W = 900, H = 560;
	var nodes = G.nodes.map(function (n, i) {
		var total = n.mine + n.theirs;
		var a = i * 2.39996; // golden-angle spiral start so the layout is the same every load
		var r = 30 + 32 * Math.sqrt(i);
		return { id: n.id, mine: n.mine, theirs: n.theirs, total: total, spotted: n.spotted,
			r: 9 + Math.sqrt(total) * 6, x: W / 2 + Math.cos(a) * r, y: H / 2 + Math.sin(a) * r, vx: 0, vy: 0 };
	});
	var byId = {};
	nodes.forEach(function (n) { byId[n.id] = n; });
	var links = G.links.map(function (l) { return { s: byId[l.source], t: byId[l.target], w: l.weight }; });
	var maxW = Math.max.apply(null, links.map(function (l) { return l.w; }).concat([1]));
	var neighbors = {};
	links.forEach(function (l) {
		(neighbors[l.s.id] = neighbors[l.s.id] || {})[l.t.id] = l.w;
		(neighbors[l.t.id] = neighbors[l.t.id] || {})[l.s.id] = l.w;
	});

	/* ---- force layout, run to rest before drawing ---- */
	for (var step = 0; step < 400; step++) {
		var alpha = 1 - step / 400;
		for (var i = 0; i < nodes.length; i++) {
			for (var j = i + 1; j < nodes.length; j++) {
				var a = nodes[i], b = nodes[j];
				var dx = b.x - a.x, dy = b.y - a.y, d2 = dx * dx + dy * dy || 0.01, d = Math.sqrt(d2);
				var f = 7000 / d2;
				var min = a.r + b.r + 18;
				if (d < min) f += (min - d) * 0.5;
				dx /= d; dy /= d;
				a.vx -= dx * f; a.vy -= dy * f; b.vx += dx * f; b.vy += dy * f;
			}
		}
		links.forEach(function (l) {
			var dx = l.t.x - l.s.x, dy = l.t.y - l.s.y, d = Math.sqrt(dx * dx + dy * dy) || 0.01;
			var target = 90 + l.s.r + l.t.r - Math.min(l.w, 4) * 8;
			var f = (d - target) * 0.02 * Math.min(l.w, 3);
			dx /= d; dy /= d;
			l.s.vx += dx * f; l.s.vy += dy * f; l.t.vx -= dx * f; l.t.vy -= dy * f;
		});
		nodes.forEach(function (n) {
			n.vx += (W / 2 - n.x) * 0.006; n.vy += (H / 2 - n.y) * 0.009;
			n.x += Math.max(-20, Math.min(20, n.vx * alpha)); n.y += Math.max(-20, Math.min(20, n.vy * alpha));
			n.vx *= 0.5; n.vy *= 0.5;
			n.x = Math.max(n.r + 4, Math.min(W - n.r - 4, n.x));
			n.y = Math.max(n.r + 4, Math.min(H - n.r - 22, n.y));
		});
	}

	/* ---- stretch the settled layout to fill the box, leaving room for labels ---- */
	(function fit() {
		var x0 = Infinity, x1 = -Infinity, y0 = Infinity, y1 = -Infinity;
		nodes.forEach(function (n) { x0 = Math.min(x0, n.x); x1 = Math.max(x1, n.x); y0 = Math.min(y0, n.y); y1 = Math.max(y1, n.y); });
		var padX = 70, padTop = 40, padBottom = 50;
		var sx = (W - padX * 2) / Math.max(x1 - x0, 1), sy = (H - padTop - padBottom) / Math.max(y1 - y0, 1);
		// stretch each axis, but not so unevenly that the shape distorts
		sx = Math.min(sx, sy * 1.7); sy = Math.min(sy, sx * 1.7);
		var cx = (x0 + x1) / 2, cy = (y0 + y1) / 2;
		nodes.forEach(function (n) {
			n.x = W / 2 + (n.x - cx) * sx;
			n.y = padTop + (H - padTop - padBottom) / 2 + (n.y - cy) * sy;
		});
	})();

	/* ---- draw ---- */
	function el(tag, attrs, parent) {
		var e = document.createElementNS(NS, tag);
		for (var k in attrs) e.setAttribute(k, attrs[k]);
		if (parent) parent.appendChild(e);
		return e;
	}
	function fill(n) {
		if (comparing) return n.mine && n.theirs ? C.yellow : n.mine ? C.pink : C.teal;
		return n.spotted ? C.yellow : C.pink;
	}
	var svg = el('svg', { viewBox: '0 0 ' + W + ' ' + H, class: 'dl-map-svg' }, host);
	var gLinks = el('g', {}, svg), gNodes = el('g', {}, svg);

	links.forEach(function (l) {
		l.el = el('line', { stroke: C.ink, 'stroke-linecap': 'round', 'stroke-width': 1.5 + (l.w / maxW) * 5, 'stroke-opacity': 0.25 + (l.w / maxW) * 0.35 }, gLinks);
	});
	nodes.forEach(function (n) {
		n.g = el('g', { class: 'dl-node', tabindex: 0, role: 'button' }, gNodes);
		var label = n.id + ': ' + n.mine + (comparing ? ' of your dreams, ' + n.theirs + ' of theirs' : (n.mine === 1 ? ' dream' : ' dreams'));
		n.g.setAttribute('aria-label', label);
		el('title', {}, n.g).textContent = label;
		el('circle', { r: n.r, fill: fill(n), stroke: C.ink, 'stroke-width': 2.5 }, n.g);
		var t = el('text', { y: n.r + 15, 'text-anchor': 'middle', class: 'dl-node-label' }, n.g);
		t.textContent = n.id;
	});
	function place() {
		links.forEach(function (l) { l.el.setAttribute('x1', l.s.x); l.el.setAttribute('y1', l.s.y); l.el.setAttribute('x2', l.t.x); l.el.setAttribute('y2', l.t.y); });
		nodes.forEach(function (n) { n.g.setAttribute('transform', 'translate(' + n.x + ',' + n.y + ')'); });
	}
	place();

	/* ---- focus a symbol: light up its neighbors, list its dreams ---- */
	var panel = document.getElementById('dl-map-panel');
	var selected = null;
	function highlight(n) {
		var near = n ? neighbors[n.id] || {} : null;
		nodes.forEach(function (m) { m.g.classList.toggle('is-dim', !!n && m !== n && !near[m.id]); m.g.classList.toggle('is-on', m === n); });
		links.forEach(function (l) { l.el.classList.toggle('is-dim', !!n && l.s !== n && l.t !== n); });
	}
	function text(tag, cls, str, parent) { var e = document.createElement(tag); if (cls) e.className = cls; e.textContent = str; parent.appendChild(e); return e; }
	function showPanel(n) {
		panel.innerHTML = '';
		text('div', 'dl-eyebrow', 'Symbol', panel);
		text('h3', '', n.id, panel);
		text('p', 'dl-hint', comparing ? 'In ' + n.mine + ' of your dreams and ' + n.theirs + ' of ' + DATA.compare.name + "'s." : 'In ' + n.mine + (n.mine === 1 ? ' dream.' : ' dreams.'), panel);
		var near = Object.keys(neighbors[n.id] || {}).sort(function (a, b) { return neighbors[n.id][b] - neighbors[n.id][a]; }).slice(0, 8);
		if (near.length) {
			text('div', 'dl-eyebrow', 'Shows up with', panel);
			var chips = text('div', 'dl-chips', '', panel);
			near.forEach(function (id) {
				var b = text('button', 'dl-chip', id, chips);
				b.type = 'button';
				b.addEventListener('click', function () { select(byId[id]); byId[id].g.focus(); });
			});
		}
		var list = G.dreams.filter(function (d) { return d.symbols.indexOf(n.id) !== -1; });
		text('div', 'dl-eyebrow', 'Dreams', panel);
		var ul = text('ul', 'dl-panel-dreams', '', panel);
		list.slice(0, 12).forEach(function (d) {
			var li = text('li', d.side === 'theirs' ? 'is-theirs' : 'is-mine', '', ul);
			var a = text('a', '', d.title, li);
			a.href = d.side === 'mine' ? DATA.editUrl + d.id : DATA.userUrl + d.owner;
			text('span', 'dl-hint', ' ' + d.date + (comparing ? (d.side === 'mine' ? ' · you' : ' · ' + DATA.compare.name) : ''), li);
		});
		if (list.length > 12) text('p', 'dl-hint', 'and ' + (list.length - 12) + ' more', panel);
	}
	function select(n) { selected = n; highlight(n); showPanel(n); }

	nodes.forEach(function (n) {
		n.g.addEventListener('mouseenter', function () { if (!dragging) highlight(n); });
		n.g.addEventListener('mouseleave', function () { if (!dragging) highlight(selected); });
		n.g.addEventListener('focus', function () { select(n); });
		n.g.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(n); } });
	});

	/* ---- drag a circle to rearrange ---- */
	var dragging = null, moved = false;
	function point(e) {
		var r = svg.getBoundingClientRect();
		return { x: (e.clientX - r.left) / r.width * W, y: (e.clientY - r.top) / r.height * H };
	}
	nodes.forEach(function (n) {
		n.g.addEventListener('pointerdown', function (e) {
			dragging = n; moved = false;
			n.g.setPointerCapture(e.pointerId);
			e.preventDefault();
		});
		n.g.addEventListener('pointermove', function (e) {
			if (dragging !== n) return;
			var p = point(e);
			n.x = Math.max(n.r, Math.min(W - n.r, p.x)); n.y = Math.max(n.r, Math.min(H - n.r, p.y));
			moved = true; place();
		});
		n.g.addEventListener('pointerup', function () {
			if (dragging === n && !moved) { select(n); }
			dragging = null;
		});
	});
	svg.addEventListener('click', function (e) { if (e.target === svg) { selected = null; highlight(null); } });
})();
