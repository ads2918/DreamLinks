<?php
/**
 * Deleting an account, one step at a time.
 * @var string     $step    password (signed in), email (signed out), confirm (from the emailed link) or done
 * @var string     $token   confirm only
 * @var array      $account confirm only
 */
$headings = [
	'password' => ['Leaving DreamLinks?', 'Deleting your account removes it for good. We can\'t bring it back.'],
	'email'    => ['Delete an account.', 'You can delete your account here even if you can\'t log in or never confirmed your email. We email you a link to make sure it is yours.'],
	'confirm'  => ['Last step.', 'Press the button and your account is gone for good.'],
	'done'     => ['Your account is gone.', 'Everything tied to it has been deleted. Thanks for dreaming with us.'],
][$step];
?>
<section class="dl-auth">
	<?= view('templates/auth-poster', ['heading' => $headings[0], 'blurb' => $headings[1]]) ?>

	<?php if ($step === 'done'): ?>
		<div class="dl-auth-card dl-sticker dl-form">
			<div>
				<div class="dl-eyebrow">Account deleted</div>
				<h2>Sweet dreams.</h2>
			</div>
			<p>Your dreams, readings, links, symbols and profile are deleted. You can sign up again any time.</p>
			<a class="dl-btn is-block" href="<?= site_url('/'); ?>">Back to DreamLinks</a>
		</div>
	<?php else: ?>
		<form class="dl-auth-card dl-sticker dl-form" method="post" action="<?= site_url($step === 'confirm' ? 'account/delete/confirm' : 'account/delete'); ?>">
			<?= csrf_field() ?>
			<div>
				<div class="dl-eyebrow">Delete account</div>
				<h2><?= $step === 'confirm' ? 'Delete ' . esc($account['username']) . '?' : 'What gets deleted' ?></h2>
			</div>

			<ul class="dl-delete-list">
				<li>Your profile, picture and settings</li>
				<li>Every dream you logged, with its tags and symbols</li>
				<li>Your dream readings</li>
				<li>Your links, blocks and dreamt-this-too marks</li>
				<li>Sign-ins on the app</li>
			</ul>

			<?php if ($step === 'password'): ?>
				<div class="dl-field">
					<label for="password">Your password</label>
					<div class="dl-pass">
						<input class="dl-input" type="password" name="password" id="password" autocomplete="current-password" required>
						<button type="button" data-dl-reveal="password">show</button>
					</div>
				</div>
				<button class="dl-btn is-block is-danger" type="submit">Delete my account</button>
				<hr class="dl-divider">
				<p class="dl-foot">Changed your mind? <a href="<?= site_url('user/edit'); ?>">Back to settings</a></p>
			<?php elseif ($step === 'email'): ?>
				<div class="dl-field">
					<label for="email">Email</label>
					<input class="dl-input" type="email" name="email" id="email" value="<?= esc(old('email') ?? '', 'attr') ?>" autocomplete="email" required>
					<p class="dl-hint">The link works for one hour.</p>
				</div>
				<button class="dl-btn is-block is-danger" type="submit">Email me the link</button>
				<hr class="dl-divider">
				<p class="dl-foot">Can log in? <a href="<?= site_url('user/login'); ?>">Log in</a> and come back here to delete it right away.</p>
			<?php else: ?>
				<input type="hidden" name="token" value="<?= esc($token, 'attr') ?>">
				<button class="dl-btn is-block is-danger" type="submit">Delete it for good</button>
				<hr class="dl-divider">
				<p class="dl-foot">Changed your mind? Just close this page.</p>
			<?php endif; ?>
		</form>
	<?php endif; ?>
</section>
