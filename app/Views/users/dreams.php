<?php
use App\Libraries\MoonPhase;

/**
 * The journal: the user's own dreams as compact rows, grouped by month.
 * @var list<array> $dreams   rows from DreamModel::visible_to() run through DreamFeed::decorate()
 * @var array{q: string, since: string, sort: string, visibility: string} $filters
 * @var int  $page
 * @var bool $has_more
 */
$sorts = ['' => 'Newest first', 'oldest' => 'Oldest first', 'resonance' => 'Most dreamt'];
$spans = ['' => 'Any time', '7' => 'Past week', '30' => 'Past month', '365' => 'Past year'];
$kinds = ['' => 'All dreams', 'public' => 'Public', 'friends' => 'Links only', 'private' => 'Private'];
$vis_icons = ['public' => ['bi-globe2', 'Public'], 'friends' => ['bi-people', 'Shared with links'], 'private' => ['bi-lock', 'Private']];
$filtered = $filters['q'] !== '' || $filters['since'] !== '' || $filters['visibility'] !== '' || ! in_array($filters['sort'], ['', 'newest'], true);
$base = site_url('user/' . (int) session()->get('user_id') . '/dreams');
$page_url = fn (int $p) => $base . '?' . http_build_query(array_filter($filters + ['page' => $p > 1 ? $p : '']));
// month headings only make sense when the list runs in date order
$by_month = $filters['sort'] !== 'resonance';
$month = null;
?>
<header class="dl-page-head dl-row-between">
	<div>
		<div class="dl-eyebrow">Journal</div>
		<h1>Your dreams</h1>
	</div>
	<a class="dl-btn" href="<?= site_url('dream/add'); ?>"><i class="bi bi-plus-lg"></i> Log a dream</a>
</header>

<div class="dl-journal">
	<form class="dl-journal-filters" method="get" action="<?= $base ?>" data-autosubmit>
		<label class="dl-visually-hidden" for="journal-q">Search your dreams</label>
		<div class="dl-journal-search">
			<i class="bi bi-search" aria-hidden="true"></i>
			<input class="dl-input" type="search" id="journal-q" name="q" value="<?= esc($filters['q'], 'attr') ?>" placeholder="Search titles, text and tags">
		</div>
		<?php foreach (['sort' => [$sorts, 'Sort'], 'since' => [$spans, 'When'], 'visibility' => [$kinds, 'Who can see them']] as $name => [$options, $label]): ?>
			<select class="dl-input dl-journal-select" name="<?= $name ?>" aria-label="<?= $label ?>">
				<?php foreach ($options as $value => $text): ?>
					<option value="<?= $value ?>"<?= (string) $filters[$name] === (string) $value ? ' selected' : '' ?>><?= $text ?></option>
				<?php endforeach; ?>
			</select>
		<?php endforeach; ?>
		<button class="dl-btn is-small dl-journal-go" type="submit">Search</button>
		<?php if ($filtered): ?>
			<a class="dl-link-quiet dl-journal-reset" href="<?= $base ?>">Reset</a>
		<?php endif; ?>
	</form>

	<?php if (! $dreams && $filtered): ?>
		<div class="dl-sticker dl-empty">
			<h2>No dreams match.</h2>
			<p>Try other words, a longer time span or fewer filters. <a href="<?= $base ?>">Show every dream</a></p>
		</div>
	<?php elseif (! $dreams): ?>
		<div class="dl-sticker dl-empty">
			<h2>Nothing logged yet.</h2>
			<p>Write down last night's dream while you still remember it. <a href="<?= site_url('dream/add'); ?>">Log a dream</a></p>
		</div>
	<?php else: ?>
		<ol class="dl-journal-list">
			<?php foreach ($dreams as $dream): ?>
				<?php $this_month = date('F Y', strtotime($dream['date'])); ?>
				<?php if ($by_month && $this_month !== $month): $month = $this_month; ?>
					<li class="dl-journal-month"><h2><?= esc($month) ?></h2></li>
				<?php endif; ?>
				<?php [$vis_icon, $vis_label] = $vis_icons[$dream['visibility']] ?? $vis_icons['private']; ?>
				<li class="dl-sticker dl-journal-item">
					<div class="dl-journal-date" title="<?= esc($dream['moon'], 'attr') ?>">
						<span data-moon-phase="<?= round(MoonPhase::phase($dream['date']), 4) ?>" data-moon-size="22"></span>
						<strong><?= date('j', strtotime($dream['date'])) ?></strong>
						<small><?= date('M', strtotime($dream['date'])) ?></small>
					</div>
					<div class="dl-journal-body">
						<h3><a href="<?= site_url('dream/edit/' . (int) $dream['id']); ?>"><?= esc($dream['title']) ?></a></h3>
						<p><?= esc(mb_strimwidth($dream['full_description'], 0, 150, '…')) ?></p>
						<?php if (! empty($dream['symbols'])): ?>
							<div class="dl-chips">
								<?php foreach (array_slice($dream['symbols'], 0, 3) as $symbol): ?>
									<span class="dl-chip"><?= esc($symbol) ?></span>
								<?php endforeach; ?>
								<?php if (count($dream['symbols']) > 3): ?>
									<span class="dl-hint">+<?= count($dream['symbols']) - 3 ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="dl-journal-actions">
						<span class="dl-vis" title="<?= $vis_label ?>"><i class="bi <?= $vis_icon ?>" aria-hidden="true"></i><span class="dl-visually-hidden"><?= $vis_label ?></span></span>
						<?php if ($dream['resonance'] > 0): ?>
							<span class="dl-journal-res" title="Dreamers who have dreamt this too"><i class="bi bi-stars" aria-hidden="true"></i> <?= (int) $dream['resonance'] ?></span>
						<?php endif; ?>
						<a class="dl-icon-btn" href="<?= site_url('dream/edit/' . (int) $dream['id']); ?>" title="Edit"><i class="bi bi-pencil" aria-hidden="true"></i><span class="dl-visually-hidden">Edit <?= esc($dream['title']) ?></span></a>
						<form method="post" action="<?= site_url('dream/delete/' . (int) $dream['id']); ?>" data-confirm="Delete “<?= esc($dream['title'], 'attr') ?>”? This can't be undone.">
							<?= csrf_field() ?>
							<button class="dl-icon-btn" type="submit" title="Delete"><i class="bi bi-trash3" aria-hidden="true"></i><span class="dl-visually-hidden">Delete <?= esc($dream['title']) ?></span></button>
						</form>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

		<?php if ($page > 1 || $has_more): ?>
			<nav class="dl-journal-pager" aria-label="More dreams">
				<?php if ($page > 1): ?><a class="dl-btn is-small is-plain" href="<?= esc($page_url($page - 1), 'attr') ?>"><i class="bi bi-arrow-left"></i> Newer</a><?php endif; ?>
				<?php if ($has_more): ?><a class="dl-btn is-small is-plain" href="<?= esc($page_url($page + 1), 'attr') ?>">Older <i class="bi bi-arrow-right"></i></a><?php endif; ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
</div>
