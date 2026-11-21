<?php $viewer_id = (int) session()->get('user_id'); ?>
<header class="dl-page-head dl-row-between">
	<div>
		<div class="dl-eyebrow">The commons</div>
		<h1>What's everyone dreaming?</h1>
	</div>
	<a class="dl-btn" href="<?= site_url('dream/add'); ?>"><i class="bi bi-plus-lg"></i> Log a dream</a>
</header>

<?php if (! empty($pulse)): ?>
	<section class="dl-sticker dl-pulse" aria-label="Lately in the commons">
		<div class="dl-eyebrow">Last 30 days</div>
		<ul>
			<?php foreach ($pulse as $p): ?>
				<li><strong><?= (int) $p['dreamers'] ?> dreamers</strong> dreamt about <span class="dl-chip"><?= esc($p['symbol']) ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>

<div class="dl-feed">
	<?php if (! empty($public_dreams)): ?>
		<?php foreach ($public_dreams as $dream): ?>
			<?= view('templates/dream-card', ['dream' => $dream, 'viewer_id' => $viewer_id]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="dl-sticker dl-empty">
			<h2>It's quiet in here.</h2>
			<p>No one you can see has shared a dream yet. <a href="<?= site_url('search?search=a'); ?>">Find dreamers to link with</a>, or share one of your own.</p>
		</div>
	<?php endif; ?>
</div>
