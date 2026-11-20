<?php
helper('form');
$logged_in = (bool) session()->get('logged_in');
$uid = session()->get('user_id');
$uri = trim(uri_string(), '/');

/* marks the nav tab for the current page */
$tab = function (string $path) use ($uri) {
	return trim($path, '/') === $uri ? ' aria-current="page"' : '';
};
$avatar = function ($image) {
	return $image == '' ? base_url('assets/images/no-image.png') : base_url('uploads/' . $image);
};
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?= esc($description ?? 'DreamLinks: a dream journal you share with the people you are linked to.') ?>">
		<meta name="theme-color" content="#cfd6ff">
		<link rel="icon" href="<?= base_url('favicon.ico'); ?>?v=2" type="image/x-icon">
		<title>DreamLinks | <?= esc($title ?? '') ?></title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Shrikhand&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
		<link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
		<link href="<?= base_url('assets/css/main.css'); ?>" rel="stylesheet">
		<link href="<?= base_url('assets/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<link href="<?= base_url('assets/css/dreamlinks.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/dreamlinks.css') ?>" rel="stylesheet">
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-EDLB3K5XWK"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-EDLB3K5XWK');
		</script>
	</head>
	<body class="dl">
	<canvas id="dl-rain" aria-hidden="true"></canvas>
	<div class="dl-grain" aria-hidden="true"></div>

	<header class="dl-nav">
		<div class="dl-wrap">
			<nav class="dl-nav-bar dl-sticker" aria-label="Main">
				<a class="dl-brand" href="<?= site_url(''); ?>">DreamLinks</a>

				<?php if (! $logged_in): ?>
					<div class="dl-nav-guest">
						<a class="dl-link-quiet" href="<?= site_url('learn'); ?>">How to</a>
						<a class="dl-link-quiet" href="<?= site_url('contact'); ?>">Contact</a>
						<?php if ($uri !== 'user/login'): ?>
							<a class="dl-btn is-small is-plain" href="<?= site_url('user/login'); ?>">Log in</a>
						<?php endif; ?>
						<?php if ($uri !== 'user/register'): ?>
							<a class="dl-btn is-small" href="<?= site_url('user/register'); ?>">Join</a>
						<?php endif; ?>
					</div>
				<?php else: ?>
					<ul class="dl-tabs">
						<li><a href="<?= site_url(''); ?>"<?= $tab(''); ?>>Feed</a></li>
						<li><a href="<?= site_url('dream/add'); ?>"<?= $tab('dream/add'); ?>>Log a dream</a></li>
						<li><a href="<?= site_url('user/' . $uid . '/dreams'); ?>"<?= $tab('user/' . $uid . '/dreams'); ?>>Journal</a></li>
						<li><a href="<?= site_url('user/' . $uid . '/analysis'); ?>"<?= $tab('user/' . $uid . '/analysis'); ?>>Analysis</a></li>
						<li><a href="<?= site_url('user/' . $uid . '/links'); ?>"<?= $tab('user/' . $uid . '/links'); ?>>Links</a></li>
					</ul>

					<form class="dl-search" action="<?= site_url('search'); ?>" role="search">
						<input required name="search" type="search" value="<?= esc($_GET['search'] ?? '') ?>" placeholder="Find dreamers" aria-label="Find dreamers">
						<button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
					</form>

					<?php if (! empty($pending_links)): ?>
						<details class="dl-drop" id="dropdownMenuButton2">
							<summary class="dl-icon-btn" aria-label="Link requests">
								<i class="bi bi-link-45deg"></i>
								<span class="dl-badge pending-link-count"><?= count($pending_links); ?></span>
							</summary>
							<div class="dl-drop-panel dl-sticker">
								<div class="dl-eyebrow" style="padding:4px 8px 8px">Link requests</div>
								<ul class="dl-pending">
									<?php foreach ($pending_links as $link): ?>
										<li>
											<img class="dl-avatar dl-avatar-sm auto-link" href="<?= site_url('user/' . $link['request_user_id']); ?>" src="<?= $avatar($link['image']); ?>" alt="">
											<span class="who auto-link" href="<?= site_url('user/' . $link['request_user_id']); ?>"><?= esc($link['username']); ?></span>
											<div class="acts">
												<button type="button" class="accept-link" link_id="<?= esc($link['id']); ?>" title="Accept"><i class="bi bi-check-lg"></i></button>
												<button type="button" class="decline-link" link_id="<?= esc($link['id']); ?>" title="Decline"><i class="bi bi-x-lg"></i></button>
												<button type="button" class="block-link" link_id="<?= esc($link['request_user_id']); ?>" title="Block"><i class="bi bi-ban"></i></button>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</details>
					<?php endif; ?>

					<details class="dl-drop">
						<summary aria-label="Account menu">
							<img class="dl-avatar" src="<?= $avatar($current_user['image'] ?? ''); ?>" alt="">
						</summary>
						<div class="dl-drop-panel dl-sticker">
							<form class="dl-search" action="<?= site_url('search'); ?>" role="search">
								<input required name="search" type="search" placeholder="Find dreamers" aria-label="Find dreamers">
								<button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
							</form>
							<div class="dl-drop-item dl-eyebrow"><?= esc($current_user['username'] ?? ''); ?></div>
							<a href="<?= site_url('user/' . $uid); ?>"><i class="bi bi-person"></i> My profile</a>
							<a href="<?= site_url('user/' . $uid . '/account'); ?>"><i class="bi bi-gear"></i> Account</a>
							<a href="<?= site_url('user/' . $uid . '/blocks'); ?>"><i class="bi bi-slash-circle"></i> Blocked dreamers</a>
							<hr>
							<a href="<?= site_url('learn'); ?>"><i class="bi bi-moon-stars"></i> How to lucid dream</a>
							<a href="<?= site_url('contact'); ?>"><i class="bi bi-envelope"></i> Contact</a>
							<?php if (($current_user['role'] ?? '') == 'admin'): ?>
								<a href="<?= site_url('admin/users'); ?>"><i class="bi bi-shield-lock"></i> Admin</a>
							<?php endif; ?>
							<hr>
							<a href="<?= site_url('user/logout'); ?>"><i class="bi bi-box-arrow-right"></i> Log out</a>
						</div>
					</details>
				<?php endif; ?>
			</nav>
		</div>
	</header>

<main role="main" id="content-wrapper" class="dl-main">
	<div class="dl-wrap">
<?php if (session()->getFlashdata('error') || (isset($error) && $error)): ?>
	<div class="dl-flash is-error dl-sticker" role="alert">
		<span class="dl-flash-icon"><i class="bi bi-exclamation-octagon"></i></span>
		<div>
			<?= esc(session()->getFlashdata('error')) ?>
			<?php if (validation_list_errors()): ?>
				<?= validation_list_errors() ?>
			<?php endif; ?>
		</div>
	</div>
<?php elseif (session()->getFlashdata('success')): ?>
	<div class="dl-flash is-success dl-sticker" role="status">
		<span class="dl-flash-icon"><i class="bi bi-stars"></i></span>
		<div><?= esc(session()->getFlashdata('success')) ?></div>
	</div>
<?php endif; ?>

<?php if (! empty($breadcrumbs)): ?>
	<nav aria-label="Breadcrumb">
		<ol class="dl-crumbs">
			<?php foreach ($breadcrumbs as $breadcrumb): ?>
				<li<?= isset($breadcrumb['action']) ? ' aria-current="page"' : '' ?>><a href="<?= $breadcrumb['url']; ?>"><?= esc($breadcrumb['name']); ?></a></li>
			<?php endforeach; ?>
		</ol>
	</nav>
<?php endif; ?>
