<?php
/**
 * One dream as a sticker card.
 * @var array $dream  dream row plus username, image, symbols, moon, resonance, resonated
 * @var int   $viewer_id
 */
$own = (int) $dream['created_by'] === (int) $viewer_id;
$avatar = $dream['image'] == '' ? base_url('assets/images/no-image.png') : base_url('uploads/' . $dream['image']);
?>
<article class="dl-sticker dl-dream">
	<header class="dl-dream-head">
		<a href="<?= site_url('user/' . (int) $dream['created_by']); ?>"><img class="dl-avatar" src="<?= esc($avatar, 'attr') ?>" alt=""></a>
		<div class="dl-dream-who">
			<a class="dl-dream-name" href="<?= site_url('user/' . (int) $dream['created_by']); ?>"><?= esc($dream['username']) ?></a>
			<div class="dl-hint"><?= esc(date('M j, Y', strtotime($dream['date']))) ?> · <?= esc(strtolower($dream['moon'])) ?></div>
		</div>
		<?php if ($dream['visibility'] === 'friends'): ?>
			<span class="dl-vis" title="Shared with links"><i class="bi bi-people"></i></span>
		<?php endif; ?>
	</header>
	<h2><?= esc($dream['title']) ?></h2>
	<p class="dl-dream-text"><?= nl2br(esc($dream['full_description'])) ?></p>
	<?php /* a div, not <footer>: main.css pins every footer element to the page bottom */ ?>
	<div class="dl-dream-foot">
		<?php if (! empty($dream['symbols'])): ?>
			<div class="dl-chips">
				<?php foreach (array_slice($dream['symbols'], 0, 8) as $symbol): ?>
					<span class="dl-chip"><?= esc($symbol) ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ($own): ?>
			<span class="dl-resonance is-own" title="Dreamers who have dreamt this too"><i class="bi bi-stars"></i> <span class="n"><?= (int) $dream['resonance'] ?></span></span>
		<?php else: ?>
			<button type="button" class="dl-resonance" data-resonate="<?= site_url('dream/' . (int) $dream['id'] . '/resonate'); ?>" aria-pressed="<?= $dream['resonated'] ? 'true' : 'false' ?>">
				<i class="bi bi-stars"></i> I've dreamt this <span class="n"><?= (int) $dream['resonance'] ?></span>
			</button>
		<?php endif; ?>
	</div>
</article>
