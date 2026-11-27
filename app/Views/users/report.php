<?php
/**
 * @var array|null $dream    the dream being reported, or null when reporting the dreamer
 * @var array      $user     whose dream or profile it is
 * @var array<string,string> $reasons
 */
?>
<section class="dl-auth">
	<?= view('templates/auth-poster', [
		'heading' => 'Something wrong?',
		'blurb'   => 'Tell us what is going on. Reports are private: ' . $user['username'] . ' will not know who sent it.',
	]) ?>

	<form class="dl-auth-card dl-sticker dl-form" method="post" action="<?= site_url('report'); ?>">
		<?= csrf_field() ?>
		<input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
		<?php if ($dream): ?><input type="hidden" name="dream_id" value="<?= (int) $dream['id'] ?>"><?php endif; ?>
		<div>
			<div class="dl-eyebrow">Report</div>
			<h2><?= $dream ? '“' . esc($dream['title']) . '”' : esc($user['username']) ?></h2>
			<?php if ($dream): ?><p class="dl-hint">A dream by <?= esc($user['username']) ?></p><?php endif; ?>
		</div>

		<fieldset class="dl-field dl-report-reasons">
			<legend>What is wrong with it?</legend>
			<?php foreach ($reasons as $value => $label): ?>
				<label class="dl-report-reason"><input type="radio" name="reason" value="<?= $value ?>" required<?= old('reason') === $value ? ' checked' : '' ?>> <?= $label ?></label>
			<?php endforeach; ?>
		</fieldset>

		<div class="dl-field">
			<label for="note">Anything else? (optional)</label>
			<textarea class="dl-input" name="note" id="note" rows="3" maxlength="500"><?= esc(old('note') ?? '') ?></textarea>
		</div>

		<button class="dl-btn is-block is-danger" type="submit">Send report</button>
		<hr class="dl-divider">
		<p class="dl-foot"><a href="<?= site_url('user/' . (int) $user['id']); ?>">Back to <?= esc($user['username']) ?></a></p>
	</form>
</section>
