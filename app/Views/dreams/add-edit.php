<?php
$editing = isset($id);
$posted = (bool) $_POST;
$val = function (string $field, $saved) use ($posted) {
	return esc($posted ? set_value($field) : ($saved ?? ''));
};
$visibility = $posted ? set_value('visibility') : ($visibility ?? 'private');
?>
<section class="dl-dream-form">
	<header class="dl-page-head">
		<div class="dl-eyebrow"><?= $editing ? 'Edit dream' : 'Log a dream' ?></div>
		<h1><?= $editing ? 'Fix the details.' : 'What did you see?' ?></h1>
	</header>

	<form class="dl-sticker dl-form dl-dream-card" action="" method="post">
		<?= csrf_field() ?>
		<div class="dl-dream-row">
			<div class="dl-field">
				<label for="title">Title</label>
				<input class="dl-input" type="text" maxlength="100" name="title" id="title" value="<?= $val('title', $editing ? $title : '') ?>" placeholder="The neon city" required>
			</div>
			<div class="dl-field">
				<label for="date">Night of</label>
				<input class="dl-input" type="date" name="date" id="date" value="<?= $val('date', $date ?? date('Y-m-d')) ?>" required>
			</div>
		</div>

		<div class="dl-field">
			<label for="full_description">The dream</label>
			<textarea class="dl-input" name="full_description" id="full_description" maxlength="700" rows="8" placeholder="Write it before it fades: where you were, who was there, how it felt." required><?= $val('full_description', $full_description ?? '') ?></textarea>
			<div class="dl-row-between">
				<p class="dl-hint" id="dl-count" aria-live="polite"></p>
			</div>
		</div>

		<div class="dl-symbols" id="dl-symbols" hidden>
			<div class="dl-eyebrow">Symbols spotted · tap to add as a tag</div>
			<div class="dl-symbol-list" id="dl-symbol-list"></div>
		</div>

		<div class="dl-field">
			<label for="tags">Tags</label>
			<input class="dl-input" name="tags" id="tags" value="<?= esc($posted ? set_value('tags') : ($tags ?? '')) ?>" placeholder="ocean, flying, grandma">
			<p class="dl-hint">People, places, feelings. Up to 15.</p>
		</div>

		<fieldset class="dl-field dl-visibility">
			<legend>Who can see it</legend>
			<?php foreach ([
				'private' => ['Only me', 'bi-lock'],
				'friends' => ['My links', 'bi-people'],
				'public'  => ['Everyone', 'bi-globe2'],
			] as $value => [$label, $icon]): ?>
				<label>
					<input type="radio" name="visibility" value="<?= $value ?>"<?= $visibility === $value ? ' checked' : '' ?> required>
					<span><i class="bi <?= $icon ?>"></i> <?= $label ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>

		<div class="dl-row-between">
			<a class="dl-link-quiet" href="<?= site_url('user/' . session()->get('user_id') . '/dreams'); ?>">Cancel</a>
			<button class="dl-btn" type="submit"><?= $editing ? 'Save changes' : 'Log this dream' ?></button>
		</div>
	</form>
</section>
<script type="application/json" id="dl-lexicon"><?= $lexicon ?></script>
