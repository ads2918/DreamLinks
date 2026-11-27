<section class="dl-auth">
	<?= view('templates/auth-poster', [
		'heading' => 'Who are you…?',
		'blurb'   => 'Keep a dream journal, link with people you trust, and get a reading of the patterns in your dreams. We email you a link to confirm your address, and then you are in.',
	]) ?>

	<form class="dl-auth-card dl-sticker dl-form" action="<?= site_url('user'); ?>" method="post">
		<?= csrf_field() ?>
		<div>
			<div class="dl-eyebrow">Join</div>
			<h2>Create an account</h2>
		</div>

		<div class="dl-trap" aria-hidden="true">
			<label for="website">Leave this empty</label>
			<input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
		</div>

		<div class="dl-field">
			<label for="email">Email</label>
			<input class="dl-input" type="email" name="email" id="email" value="<?= esc(set_value('email')) ?>" maxlength="50" autocomplete="email" required>
		</div>

		<div class="dl-field">
			<label for="username">Username</label>
			<input class="dl-input" type="text" name="username" id="username" value="<?= esc(set_value('username')) ?>" minlength="3" maxlength="50" autocomplete="username" required>
			<p class="dl-hint">This is what other dreamers see.</p>
		</div>

		<div class="dl-field">
			<label for="password">Password</label>
			<div class="dl-pass">
				<input class="dl-input" type="password" name="password" id="password" minlength="8" maxlength="30" autocomplete="new-password" required>
				<button type="button" data-dl-reveal="password">show</button>
			</div>
			<p class="dl-hint">8 to 30 characters.</p>
		</div>

		<div class="dl-field">
			<label for="password2">Repeat password</label>
			<input class="dl-input" type="password" name="password2" id="password2" autocomplete="new-password" required>
		</div>

		<button class="dl-btn is-block" type="submit">Create account</button>
		<hr class="dl-divider">
		<p class="dl-foot">Already a member? <a href="<?= site_url('user/login'); ?>">Log in</a></p>
	</form>
</section>
