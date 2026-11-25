<?php
use App\Libraries\MoonPhase;

$uid = (int) session()->get('user_id');
$pct = fn ($x) => (int) round($x * 100) . '%';
$avatar = fn ($image) => esc($image == '' ? base_url('assets/images/no-image.png') : base_url('uploads/' . $image), 'attr');
$top = array_slice($my_counts, 0, 10, true);
$top_max = max($top ?: [1]);
$moon_max = max(max($moon), 1);
$moon_top = array_sum($moon) ? MoonPhase::NAMES[array_search(max($moon), $moon, true)] : null;
?>
<header class="dl-page-head dl-row-between">
	<div>
		<div class="dl-eyebrow">Dreamscape</div>
		<h1><?= $compare ? 'You &amp; ' . esc($compare['name']) : 'Your symbol map' ?></h1>
	</div>
	<?php if (! empty($dreamers)): ?>
		<form class="dl-compare" action="<?= site_url('user/' . $uid . '/dreamscape'); ?>" method="get">
			<label class="dl-eyebrow" for="with">Compare with</label>
			<div class="dl-compare-row">
				<select class="dl-input" name="with" id="with">
					<?php foreach ($dreamers as $d): ?>
						<option value="<?= (int) $d['id'] ?>"<?= $compare && $compare['id'] === $d['id'] ? ' selected' : '' ?>>
							<?= esc($d['name']) ?> · <?= $pct($d['similarity']) ?> alike<?= $d['linked'] ? ' · linked' : '' ?>
						</option>
					<?php endforeach; ?>
				</select>
				<button class="dl-btn is-small" type="submit">Compare</button>
				<?php if ($compare): ?>
					<a class="dl-link-quiet" href="<?= site_url('user/' . $uid . '/dreamscape'); ?>">Just me</a>
				<?php endif; ?>
			</div>
		</form>
	<?php endif; ?>
</header>

<?php if (! empty($compare_note)): ?>
	<div class="dl-flash dl-sticker" role="status"><span class="dl-flash-icon"><i class="bi bi-info-circle"></i></span><div><?= esc($compare_note) ?></div></div>
<?php endif; ?>

<?php if (empty($mine)): ?>
	<div class="dl-sticker dl-empty">
		<h2>Your map is still blank.</h2>
		<p>Every dream you log adds symbols here, and the ones that keep turning up together get connected. <a href="<?= site_url('dream/add'); ?>">Log your first dream</a>.</p>
	</div>
<?php else: ?>

<section class="dl-stats" aria-label="At a glance">
	<div class="dl-sticker dl-stat"><span class="dl-eyebrow">Dreams logged</span><strong><?= count($mine) ?></strong></div>
	<div class="dl-sticker dl-stat"><span class="dl-eyebrow">Symbols</span><strong><?= count($my_counts) ?></strong></div>
	<div class="dl-sticker dl-stat"><span class="dl-eyebrow">Comes back most</span><strong class="is-word"><?= esc(array_key_first($my_counts)) ?></strong></div>
	<div class="dl-sticker dl-stat"><span class="dl-eyebrow">Your moon</span><strong class="is-word"><?= esc(strtolower($moon_top ?? '—')) ?></strong></div>
</section>

<section class="dl-map-wrap">
	<div class="dl-sticker dl-map">
		<div class="dl-map-head">
			<h2>Connection map</h2>
			<ul class="dl-legend">
				<?php if ($compare): ?>
					<li><span class="dot" style="background:var(--pink)"></span>Only you</li>
					<li><span class="dot" style="background:var(--teal)"></span>Only <?= esc($compare['name']) ?></li>
					<li><span class="dot" style="background:var(--yellow)"></span>Both of you</li>
				<?php else: ?>
					<li><span class="dot" style="background:var(--yellow)"></span>Spotted in the text</li>
					<li><span class="dot" style="background:var(--pink)"></span>Your own tags</li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="dl-map-canvas" id="dl-map" role="group" aria-label="Symbols linked by the dreams they share. Use Tab to move between symbols."></div>
		<p class="dl-hint">Bigger circles turn up in more dreams. Lines join symbols that appear in the same dream, and get thicker the more dreams they share. Drag to rearrange.</p>
	</div>
	<aside class="dl-sticker dl-map-panel" id="dl-map-panel" aria-live="polite">
		<div class="dl-eyebrow">Pick a symbol</div>
		<p class="dl-hint">Click or tab to any circle to see the dreams it comes from and what it connects to.</p>
	</aside>
</section>

<?php if ($compare): ?>
	<section class="dl-sticker dl-compare-card">
		<div class="dl-compare-score">
			<strong><?= $pct($compare['similarity']) ?></strong>
			<span class="dl-eyebrow">alike</span>
		</div>
		<div class="dl-compare-cols">
			<div>
				<div class="dl-eyebrow">You both dream about</div>
				<div class="dl-chips"><?php foreach (array_slice($compare['shared'], 0, 12) as $s): ?><span class="dl-chip is-both"><?= esc($s) ?></span><?php endforeach; ?><?= empty($compare['shared']) ? '<span class="dl-hint">Nothing yet.</span>' : '' ?></div>
			</div>
			<div>
				<div class="dl-eyebrow">Only you</div>
				<div class="dl-chips"><?php foreach ($compare['only_mine'] as $s): ?><span class="dl-chip is-mine"><?= esc($s) ?></span><?php endforeach; ?></div>
			</div>
			<div>
				<div class="dl-eyebrow">Only <?= esc($compare['name']) ?></div>
				<div class="dl-chips"><?php foreach ($compare['only_theirs'] as $s): ?><span class="dl-chip is-theirs"><?= esc($s) ?></span><?php endforeach; ?></div>
			</div>
		</div>
		<p class="dl-hint">Based on your dreams and the <?= (int) $compare['dreams'] ?> of theirs you're allowed to see. Their private dreams are never counted.</p>
	</section>
<?php endif; ?>

<section class="dl-grid-2">
	<div class="dl-sticker dl-panel">
		<h2>Top symbols</h2>
		<ul class="dl-bars">
			<?php foreach ($top as $symbol => $n): ?>
				<li><span class="label"><?= esc($symbol) ?></span><span class="bar"><span style="width:<?= round($n / $top_max * 100) ?>%"></span></span><span class="n"><?= (int) $n ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="dl-sticker dl-panel">
		<h2>Dreams by moon phase</h2>
		<ul class="dl-moonbars">
			<?php foreach ($moon as $i => $n): ?>
				<li>
					<button type="button" data-moon-tab="<?= $i ?>" aria-controls="dl-moon-<?= $i ?>" aria-pressed="false" title="<?= esc(MoonPhase::NAMES[$i]) ?>: <?= (int) $n ?> dreams">
						<span class="bar"><span style="height:<?= round($n / $moon_max * 100) ?>%"></span></span>
						<span class="n"><?= (int) $n ?></span>
						<span class="moon" data-phase="<?= $i / 8 ?>"></span>
						<span class="dl-visually-hidden"><?= esc(MoonPhase::NAMES[$i]) ?></span>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="dl-hint" data-moon-hint>New moon on the left, full moon in the middle. Tap a phase to see what you dream about then.</p>
		<?php foreach ($moon_detail as $i => $phase): ?>
			<div class="dl-moon-detail" id="dl-moon-<?= $i ?>" hidden>
				<h3><?= esc($phase['name']) ?></h3>
				<?php if (! $phase['count']): ?>
					<p class="dl-hint">No dreams logged under this moon yet.</p>
				<?php else: ?>
					<p class="dl-hint"><?= (int) $phase['count'] ?> <?= $phase['count'] === 1 ? 'dream' : 'dreams' ?>, <?= (int) $phase['share'] ?>% of your journal.</p>
					<?php if ($phase['symbols']): ?>
						<div class="dl-eyebrow">Symbols under this moon</div>
						<div class="dl-chips">
							<?php foreach ($phase['symbols'] as $s): ?>
								<span class="dl-chip<?= $s['stands_out'] ? ' is-both' : '' ?>"<?= $s['stands_out'] ? ' title="Shows up more under this moon than usual"' : '' ?>><?= esc($s['symbol']) ?> · <?= (int) $s['n'] ?><?= $s['stands_out'] ? ' ↑' : '' ?></span>
							<?php endforeach; ?>
						</div>
						<?php if (array_filter(array_column($phase['symbols'], 'stands_out'))): ?>
							<p class="dl-hint">Yellow ↑ means it comes up more under this moon than in your dreams overall.</p>
						<?php endif; ?>
					<?php endif; ?>
					<div class="dl-eyebrow">Dreams</div>
					<ul class="dl-panel-dreams">
						<?php foreach ($phase['dreams'] as $d): ?>
							<li><a href="<?= site_url('dream/edit/' . (int) $d['id']); ?>"><?= esc($d['title']) ?></a> <span class="dl-hint"><?= esc(date('M j, Y', strtotime($d['date']))) ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php if ($phase['count'] > count($phase['dreams'])): ?>
						<p class="dl-hint">and <?= $phase['count'] - count($phase['dreams']) ?> more</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="dl-grid-2">
	<div class="dl-sticker dl-panel">
		<h2>Dreamers like you</h2>
		<?php if (empty($kin)): ?>
			<p class="dl-hint">No one you can see shares your symbols yet. As more dreams are shared, matches show up here.</p>
		<?php else: ?>
			<ul class="dl-kin">
				<?php foreach ($kin as $k): ?>
					<li>
						<a href="<?= site_url('user/' . (int) $k['id']); ?>"><img class="dl-avatar" src="<?= $avatar($k['image']) ?>" alt=""></a>
						<div class="who">
							<a class="dl-dream-name" href="<?= site_url('user/' . (int) $k['id']); ?>"><?= esc($k['name']) ?></a>
							<div class="dl-chips"><?php foreach (array_slice($k['shared'], 0, 4) as $s): ?><span class="dl-chip"><?= esc($s) ?></span><?php endforeach; ?></div>
						</div>
						<a class="dl-btn is-small is-plain" href="<?= site_url('user/' . $uid . '/dreamscape?with=' . (int) $k['id']); ?>"><?= $pct($k['similarity']) ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<div class="dl-sticker dl-panel dl-reading">
		<h2>Latest reading</h2>
		<?php if ($reading): ?>
			<div class="dl-eyebrow"><?= esc($reading['date']) ?></div>
			<p><?= esc($reading['overview']) ?></p>
			<?php foreach (['patterns' => 'Patterns', 'symbols' => 'Symbols', 'celestial' => 'In the sky'] as $key => $label): ?>
				<?php if (! empty($reading[$key])): ?>
					<div class="dl-eyebrow"><?= $label ?></div>
					<ul><?php foreach ($reading[$key] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
			<?php endforeach; ?>
			<a href="<?= site_url('user/' . $uid . '/analysis'); ?>">All readings</a>
		<?php elseif (! ($current_user['ai_analysis_enabled'] ?? 0)): ?>
			<p class="dl-hint">AI readings are off. <a href="<?= site_url('user/edit'); ?>">Turn them on</a> and each new dream gets read a minute or so after you save it.</p>
		<?php else: ?>
			<p class="dl-hint">Your first reading arrives a minute or so after your next dream.</p>
		<?php endif; ?>
	</div>
</section>

<?php endif; ?>
<script type="application/json" id="dl-graph"><?= json_encode([
	'graph'    => $graph,
	'compare'  => $compare ? ['name' => $compare['name']] : null,
	'editUrl'  => site_url('dream/edit/'),
	'userUrl'  => site_url('user/'),
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
