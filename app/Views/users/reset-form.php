<section class="dl-auth">
	<?= view('templates/auth-poster', [
		'heading' => 'A fresh start.',
		'blurb'   => 'Choose a new password. Once it is saved you can log straight back in.',
	]) ?>

	<form class="dl-auth-card dl-sticker dl-form" action="<?= site_url('user/updatePassword'); ?>" method="post">
		<?= csrf_field() ?>
		<input type="hidden" name="token" value="<?= esc($token) ?>">
		<div>
			<div class="dl-eyebrow">Reset password</div>
			<h2>New password</h2>
		</div>

		<?php if (isset($validation)): ?>
			<div class="dl-flash is-error" role="alert" style="margin:0"><?= $validation->listErrors() ?></div>
		<?php endif; ?>

		<div class="dl-field">
			<label for="password">New password</label>
			<div class="dl-pass">
				<input class="dl-input" type="password" name="password" id="password" minlength="8" autocomplete="new-password" required autofocus>
				<button type="button" data-dl-reveal="password">show</button>
			</div>
			<p class="dl-hint">At least 8 characters.</p>
		</div>

		<div class="dl-field">
			<label for="password2">Repeat new password</label>
			<input class="dl-input" type="password" name="password2" id="password2" autocomplete="new-password" required>
		</div>

		<button class="dl-btn is-block" type="submit">Save new password</button>
	</form>
</section>
