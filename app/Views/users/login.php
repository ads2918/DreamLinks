<section class="dl-auth">
	<?= view('templates/auth-poster', [
		'heading' => 'Welcome back, dreamer.',
		'blurb'   => 'Log what you remember before it fades, and see what the people you are linked to have been dreaming.',
	]) ?>

	<form class="dl-auth-card dl-sticker dl-form" action="<?= site_url('user/login'); ?>" method="post">
		<?= csrf_field() ?>
		<div>
			<div class="dl-eyebrow">Log in</div>
			<h2>Back to your journal</h2>
		</div>

		<div class="dl-field">
			<label for="email">Email</label>
			<input class="dl-input" type="email" name="email" id="email" value="<?= esc(set_value('email')) ?>" autocomplete="email" required autofocus>
		</div>

		<div class="dl-field">
			<div class="dl-row-between">
				<label for="password">Password</label>
				<a class="dl-hint" href="<?= site_url('user/forgot-password'); ?>">Forgot it?</a>
			</div>
			<div class="dl-pass">
				<input class="dl-input" type="password" name="password" id="password" autocomplete="current-password" required>
				<button type="button" data-dl-reveal="password">show</button>
			</div>
		</div>

		<button class="dl-btn is-block" type="submit">Log in</button>
		<hr class="dl-divider">
		<p class="dl-foot">New here? <a href="<?= site_url('user/register'); ?>">Create an account</a></p>
	</form>
</section>
