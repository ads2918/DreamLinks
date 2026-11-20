<section class="dl-auth">
	<?= view('templates/auth-poster', [
		'heading' => 'Lost the key?',
		'blurb'   => 'It happens. Enter the email you signed up with and we will send a link to set a new password. The link works for one hour.',
	]) ?>

	<form class="dl-auth-card dl-sticker dl-form" action="<?= site_url('user/sendResetLink'); ?>" method="post">
		<?= csrf_field() ?>
		<div>
			<div class="dl-eyebrow">Reset password</div>
			<h2>Send me a link</h2>
		</div>

		<div class="dl-field">
			<label for="email">Email</label>
			<input class="dl-input" type="email" name="email" id="email" autocomplete="email" required autofocus>
		</div>

		<button class="dl-btn is-block" type="submit">Send reset link</button>
		<hr class="dl-divider">
		<p class="dl-foot">Remembered it? <a href="<?= site_url('user/login'); ?>">Log in</a></p>
	</form>
</section>
