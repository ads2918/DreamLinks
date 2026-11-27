<?php
$own = (int) $current_user['id'] === (int) $profile_user['id'];
$avatar = $profile_user['image'] == '' ? base_url('assets/images/no-image.png') : base_url('uploads/' . $profile_user['image']);
?>
<?php if ($blocked): ?>
	<div class="dl-flash is-error dl-sticker" role="status">
		<span class="dl-flash-icon"><i class="bi bi-slash-circle"></i></span>
		<div>This dreamer is on your <a href="<?= site_url('user/' . (int) $session_user . '/blocks'); ?>">block list</a>, so their profile is hidden.</div>
	</div>
<?php endif; ?>

<section class="dl-profile">
	<aside class="dl-sticker dl-profile-card">
		<img class="dl-avatar dl-avatar-lg" src="<?= esc($avatar, 'attr') ?>" alt="">
		<h1><?= esc($profile_user['username']) ?></h1>
		<?php if (! $blocked && $profile_user['bio'] != ''): ?>
			<p><?= esc($profile_user['bio']) ?></p>
		<?php endif; ?>

		<?php if ($own): ?>
			<a class="dl-btn is-small is-plain" href="<?= site_url('user/edit'); ?>">Edit profile</a>
		<?php elseif (! $blocked): ?>
			<div class="dl-profile-acts">
				<?php if ($link_approved): ?>
					<span class="dl-chip is-both"><i class="bi bi-link-45deg"></i> Linked</span>
				<?php elseif ($link_pending): ?>
					<span class="dl-chip"><i class="bi bi-clock"></i> Request pending</span>
				<?php else: ?>
					<button type="button" class="dl-btn is-small request-link" link_id="<?= (int) $profile_user['id'] ?>"><i class="bi bi-link-45deg"></i> Ask to link</button>
				<?php endif; ?>
				<a class="dl-btn is-small is-plain" href="<?= site_url('user/' . (int) $session_user . '/dreamscape?with=' . (int) $profile_user['id']); ?>"><i class="bi bi-diagram-3"></i> Compare dreamscapes</a>
				<button type="button" class="dl-link-quiet profile-block-link" link_id="<?= (int) $profile_user['id'] ?>"><i class="bi bi-slash-circle"></i> Block</button>
				<a class="dl-link-quiet" href="<?= site_url('report?user=' . (int) $profile_user['id']); ?>"><i class="bi bi-flag"></i> Report</a>
			</div>
		<?php endif; ?>
	</aside>

	<?php if (! $blocked): ?>
		<div class="dl-feed">
			<?php if (empty($recent_dreams)): ?>
				<div class="dl-sticker dl-empty">
					<h2>Nothing shared yet.</h2>
					<p><?= $own ? 'Dreams you share with your links or everyone show up here.' : 'When ' . esc($profile_user['username']) . ' shares a dream you can see, it shows up here.' ?></p>
				</div>
			<?php else: ?>
				<?php foreach ($recent_dreams as $dream): ?>
					<?= view('templates/dream-card', ['dream' => $dream, 'viewer_id' => (int) $session_user]) ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
