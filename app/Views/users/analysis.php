<?php
use App\Libraries\MoonPhase;

/**
 * @var list<array> $readings  newest first, from PromptModel::reading() plus moon, moon_phase
 * @var array<string,int> $top_symbols
 */
$latest = $readings[0] ?? null;
$earlier = array_slice($readings, 1);
$sections = ['patterns' => ['Patterns', 'is-pink'], 'symbols' => ['Symbols', 'is-yellow'], 'celestial' => ['In the sky', 'is-teal']];
$top_max = max($top_symbols ?: [1]);
// a dream is waiting and can be read today: the page asks for its reading and spins until it lands
$pending = $enabled && $waiting && ! $capped;
?>
<header class="dl-page-head dl-row-between">
	<div>
		<div class="dl-eyebrow">Readings</div>
		<h1>What your dreams are saying</h1>
	</div>
	<a class="dl-btn" href="<?= site_url('dream/add'); ?>"><i class="bi bi-plus-lg"></i> Log a dream</a>
</header>

<section class="dl-sticker dl-read-status" aria-label="Reading status">
	<div>
		<span class="dl-eyebrow">Next reading</span>
		<?php if (! $enabled): ?>
			<strong>Readings are off</strong>
		<?php elseif ($waiting): ?>
			<strong><?= (int) $waiting ?> <?= $waiting === 1 ? 'dream' : 'dreams' ?> waiting</strong>
		<?php else: ?>
			<strong>All caught up</strong>
		<?php endif; ?>
	</div>
	<div>
		<span class="dl-eyebrow">Arrives</span>
		<strong><?= ! $enabled || ! $waiting ? 'After your next dream' : ($capped ? 'Tomorrow · daily limit reached' : 'Any second now') ?></strong>
	</div>
	<div>
		<span class="dl-eyebrow">Tonight</span>
		<strong><span data-moon-phase="<?= round(MoonPhase::phase($tonight), 4) ?>" data-moon-size="20"></span> <?= esc(strtolower(MoonPhase::name($tonight))) ?></strong>
	</div>
	<div>
		<span class="dl-eyebrow">So far</span>
		<strong><?= count($readings) ?> <?= count($readings) === 1 ? 'reading' : 'readings' ?> · <?= (int) $dream_count ?> <?= $dream_count === 1 ? 'dream' : 'dreams' ?></strong>
	</div>
</section>

<?php if (! $enabled): ?>
	<div class="dl-sticker dl-empty dl-read-off">
		<h2>AI readings are switched off.</h2>
		<p>Turn them on and each new dream gets read for patterns, symbols and what the sky was doing, a few seconds after you save it. <a href="<?= site_url('user/edit'); ?>">Turn on readings</a></p>
	</div>
<?php endif; ?>

<div class="dl-read-layout">
	<div class="dl-read-main">
		<?php if ($pending): ?>
			<div class="dl-sticker dl-read-pending" data-reading-pending="<?= site_url('readings/now') ?>" role="status">
				<span class="dl-read-spinner" aria-hidden="true"></span>
				<div>
					<h2>Reading your dream…</h2>
					<p>This takes a few seconds. Your reading shows up here when it's ready.</p>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($latest): ?>
			<article class="dl-sticker dl-read-hero">
				<div class="dl-read-stamp">
					<span data-moon-phase="<?= round($latest['moon_phase'], 4) ?>" data-moon-size="34"></span>
					<span><?= esc($latest['date']) ?><br><small><?= esc(strtolower($latest['moon'])) ?></small></span>
				</div>
				<div class="dl-eyebrow">Latest reading</div>
				<p class="dl-read-overview"><?= esc($latest['overview']) ?></p>
				<div class="dl-read-cols">
					<?php foreach ($sections as $key => [$label, $tone]): ?>
						<?php if (! empty($latest[$key])): ?>
							<section class="dl-read-col <?= $tone ?>">
								<h2><?= $label ?></h2>
								<ol><?php foreach ($latest[$key] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ol>
							</section>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</article>
		<?php elseif ($enabled && ! $pending): ?>
			<div class="dl-sticker dl-empty">
				<h2>Your first reading is on its way.</h2>
				<?php if ($capped): ?>
					<p>You've had today's readings. Your new dreams get read tomorrow.</p>
				<?php else: ?>
					<p>Log a dream and your reading shows up here a few seconds later.</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($earlier): ?>
			<h2 class="dl-read-subhead">Earlier readings</h2>
			<div class="dl-read-list">
				<?php foreach ($earlier as $reading): ?>
					<details class="dl-sticker dl-read-item">
						<summary>
							<span data-moon-phase="<?= round($reading['moon_phase'], 4) ?>" data-moon-size="22"></span>
							<span class="when"><?= esc($reading['date']) ?></span>
							<span class="gist"><?= esc(mb_strimwidth($reading['overview'], 0, 120, '…')) ?></span>
							<i class="bi bi-chevron-down" aria-hidden="true"></i>
						</summary>
						<p><?= esc($reading['overview']) ?></p>
						<div class="dl-read-cols is-compact">
							<?php foreach ($sections as $key => [$label, $tone]): ?>
								<?php if (! empty($reading[$key])): ?>
									<section class="dl-read-col <?= $tone ?>">
										<h3><?= $label ?></h3>
										<ol><?php foreach ($reading[$key] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ol>
									</section>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<aside class="dl-read-side">
		<div class="dl-sticker dl-panel">
			<h2>Keeps coming back</h2>
			<?php if ($top_symbols): ?>
				<ul class="dl-bars">
					<?php foreach ($top_symbols as $symbol => $n): ?>
						<li><span class="label"><?= esc($symbol) ?></span><span class="bar"><span style="width:<?= round($n / $top_max * 100) ?>%"></span></span><span class="n"><?= (int) $n ?></span></li>
					<?php endforeach; ?>
				</ul>
				<p class="dl-hint">Counted from your own dreams and tags.</p>
			<?php else: ?>
				<p class="dl-hint">Symbols show up here once you've logged a few dreams.</p>
			<?php endif; ?>
		</div>
		<a class="dl-sticker dl-read-link" href="<?= site_url('user/' . (int) session()->get('user_id') . '/dreamscape'); ?>">
			<span class="dl-eyebrow">See how they connect</span>
			<strong>Open your Dreamscape <i class="bi bi-arrow-right"></i></strong>
		</a>
	</aside>
</div>
