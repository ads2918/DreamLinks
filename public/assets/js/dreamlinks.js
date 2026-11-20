/* DreamLinks shell effects: code rain, moon portal, small UI helpers. No dependencies. */
(function () {
	var INK = { pink: '#ff4f9a', orange: '#ff8a3d', yellow: '#ffd93d', teal: '#18b7a6', violet: '#7a5cff', ink: '#26134a', card: '#ffffff' };
	var RING = [INK.pink, INK.orange, INK.yellow, INK.teal, INK.violet];
	var still = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------- moon phase ---------- */
	// fraction of the synodic month, from a reference new moon (2000-01-06 18:14 UTC)
	function moonPhase(date) {
		var syn = 29.530588853, days = (date.getTime() - Date.UTC(2000, 0, 6, 18, 14)) / 86400000;
		return ((days % syn) + syn) % syn / syn;
	}
	function phaseName(p) {
		return ['New moon', 'Waxing crescent', 'First quarter', 'Waxing gibbous', 'Full moon', 'Waning gibbous', 'Last quarter', 'Waning crescent'][Math.floor(p * 8 + 0.5) % 8];
	}
	function moonSVG(p, size, lit, dark) {
		var r = 10, k = Math.cos(2 * Math.PI * p), waxing = p < 0.5;
		var outer = waxing ? 1 : 0, inner = waxing ? (k > 0 ? 0 : 1) : (k > 0 ? 1 : 0);
		return '<svg width="' + size + '" height="' + size + '" viewBox="-11.5 -11.5 23 23" aria-hidden="true">' +
			'<circle r="' + r + '" fill="' + dark + '"/>' +
			'<path d="M0 ' + (-r) + ' A' + r + ' ' + r + ' 0 0 ' + outer + ' 0 ' + r + ' A' + (Math.abs(k) * r) + ' ' + r + ' 0 0 ' + inner + ' 0 ' + (-r) + 'Z" fill="' + lit + '"/>' +
			'<circle r="' + r + '" fill="none" stroke="' + INK.ink + '" stroke-width="1.6"/></svg>';
	}
	window.DreamLinks = { moonPhase: moonPhase, phaseName: phaseName, moonSVG: moonSVG };

	/* ---------- code rain: always a trickle, heavy bursts every so often ---------- */
	function rain() {
		var cv = document.getElementById('dl-rain');
		if (!cv || still) return;
		var ctx = cv.getContext('2d');
		var CH = 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワン0123456789☾✦';
		var W, H;
		function size() { var d = Math.min(window.devicePixelRatio || 1, 2); W = innerWidth; H = innerHeight; cv.width = W * d; cv.height = H * d; ctx.setTransform(d, 0, 0, d, 0, 0); }
		size(); addEventListener('resize', size);
		function rc() { return CH[Math.random() * CH.length | 0]; }
		// far columns are small, slow and faint; near ones are big, fast and strong
		var LAYERS = [
			{ size: 11, alpha: 0.45, step: [90, 140] },
			{ size: 15, alpha: 0.75, step: [55, 95] },
			{ size: 21, alpha: 1, step: [35, 60] }
		];
		var HEADS = [[255, 79, 154], [24, 183, 166], [122, 92, 255], [255, 138, 61]];
		var drops = [], burstUntil = 0, nextBurst = performance.now() + 1200, scan = -1;
		function spawn(now) {
			var L = LAYERS[Math.random() < 0.45 ? 0 : Math.random() < 0.7 ? 1 : 2];
			var x = Math.random() * W;
			for (var i = 0; i < drops.length; i++) if (Math.abs(drops[i].x - x) < L.size && drops[i].row < drops[i].len + 3) return;
			var len = 10 + Math.random() * 20 | 0, chars = [];
			for (var j = 0; j < len; j++) chars.push(rc());
			drops.push({ x: x, L: L, row: 0, len: len, step: L.step[0] + Math.random() * (L.step[1] - L.step[0]), last: now, head: HEADS[Math.random() * HEADS.length | 0], chars: chars });
		}
		function frame(now) {
			if (now > nextBurst) { burstUntil = now + 4000 + Math.random() * 4000; nextBurst = burstUntil + 4000 + Math.random() * 5000; scan = 0; }
			var bursting = now < burstUntil;
			if (Math.random() < (bursting ? 0.9 : 0.06)) spawn(now);
			if (bursting && Math.random() < 0.4) spawn(now);
			ctx.clearRect(0, 0, W, H);
			ctx.textAlign = 'center';
			drops = drops.filter(function (d) { return (d.row - d.len) * d.L.size * 1.15 < H; });
			drops.sort(function (a, b) { return a.L.size - b.L.size; });
			drops.forEach(function (d) {
				var lh = d.L.size * 1.15;
				if (now - d.last > d.step) {
					d.row++; d.last = now; d.chars.unshift(rc()); d.chars.length = d.len;
					if (Math.random() < 0.35) d.chars[Math.random() * d.len | 0] = rc();
				}
				ctx.font = '500 ' + d.L.size + 'px "DM Mono", monospace';
				for (var i = 0; i < d.len; i++) {
					var y = (d.row - i) * lh;
					if (y < 0 || y > H + lh) continue;
					if (i === 0) ctx.fillStyle = 'rgba(255,255,255,' + (0.9 * d.L.alpha).toFixed(3) + ')';
					else if (i < 3) ctx.fillStyle = 'rgba(' + d.head.join(',') + ',' + (0.7 * d.L.alpha).toFixed(3) + ')';
					else ctx.fillStyle = 'rgba(38,19,74,' + (0.32 * (1 - i / d.len) * d.L.alpha).toFixed(3) + ')';
					ctx.fillText(d.chars[i], d.x, y);
				}
			});
			// a faint scan band sweeps down at the start of each burst
			if (scan >= 0) {
				var sy = scan * (H + 120) - 60, g = ctx.createLinearGradient(0, sy - 60, 0, sy + 60);
				g.addColorStop(0, 'rgba(122,92,255,0)'); g.addColorStop(0.5, 'rgba(122,92,255,.12)'); g.addColorStop(1, 'rgba(122,92,255,0)');
				ctx.fillStyle = g; ctx.fillRect(0, sy - 60, W, 120);
				scan += 0.012; if (scan > 1) scan = -1;
			}
			requestAnimationFrame(frame);
		}
		requestAnimationFrame(frame);
	}

	/* ---------- moon portal: rippling rings around tonight's moon ---------- */
	function portal(el) {
		var cv = el.querySelector('canvas'), ctx = cv.getContext('2d');
		var p = moonPhase(new Date());
		var moon = el.querySelector('.dl-portal-moon'), label = el.querySelector('.dl-portal-label');
		if (moon) moon.innerHTML = moonSVG(p, Math.round(el.offsetWidth * 0.32), INK.yellow, INK.ink);
		if (label) label.textContent = 'Tonight · ' + phaseName(p).toLowerCase();
		function size() { var d = Math.min(window.devicePixelRatio || 1, 2), r = cv.getBoundingClientRect(); cv.width = r.width * d; cv.height = r.height * d; }
		function draw(t) {
			var W = cv.width, R = W / 2 * 1.08, N = 13, c = W / 2;
			var s = (t * 0.12) % 1, k = Math.floor(t * 0.12);
			ctx.clearRect(0, 0, W, W);
			for (var i = -1; i < N; i++) {
				var base = R * (1 - (i - s) / N);
				if (base <= 0) continue;
				ctx.beginPath();
				for (var a = 0; a <= Math.PI * 2 + 0.01; a += 0.04) {
					var rr = base + Math.sin(a * 5 + t * 0.9 + i * 0.8) * W * 0.018 * (base / R);
					var x = c + Math.cos(a) * rr, y = c + Math.sin(a) * rr;
					if (a === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
				}
				ctx.closePath();
				ctx.fillStyle = RING[(((i + k) % 5) + 5) % 5];
				ctx.fill();
				ctx.lineWidth = W * 0.008; ctx.strokeStyle = INK.ink; ctx.stroke();
			}
			ctx.beginPath(); ctx.arc(c, c, W * 0.19, 0, Math.PI * 2); ctx.fillStyle = INK.ink; ctx.fill();
		}
		size(); addEventListener('resize', function () { size(); if (still) draw(3); });
		if (still) { draw(3); return; }
		var t0 = performance.now();
		(function loop() { draw((performance.now() - t0) / 1000); requestAnimationFrame(loop); })();
	}

	/* ---------- small helpers ---------- */
	function helpers() {
		// show / hide password fields
		document.querySelectorAll('[data-dl-reveal]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var input = document.getElementById(btn.getAttribute('data-dl-reveal'));
				var show = input.type === 'password';
				input.type = show ? 'text' : 'password';
				btn.textContent = show ? 'hide' : 'show';
			});
		});
		// close an open dropdown when clicking elsewhere
		document.addEventListener('click', function (e) {
			document.querySelectorAll('details.dl-drop[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
		});
	}

	function init() {
		rain();
		document.querySelectorAll('.dl-portal').forEach(portal);
		helpers();
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
