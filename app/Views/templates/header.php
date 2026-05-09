<?php helper('form'); ?>
<html lang="en">
	
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="<?php if(isset($description)){ echo esc($description); } ?>">
		<meta name="author" content="">
		<link rel="icon" href="<?= base_url('favicon.ico'); ?>?v=2" type="image/x-icon">
		<title>DreamLinks | <?= esc($title) ?></title>
		<link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
		<link href="<?= base_url('assets/css/sticky-footer-navbar.css'); ?>" rel="stylesheet">
		<link href="<?= base_url('assets/css/main.css'); ?>" rel="stylesheet">
		<link href="<?= base_url('assets/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet">
		<link href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-EDLB3K5XWK"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-EDLB3K5XWK');
		</script>
	</head>
	<body>
    <header>
      <!-- Fixed navbar -->
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="<?php echo site_url(''); ?>"><i class="bi bi-cloud"></i> Dreamlinks</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span id="main-nav-toggle" class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<?php if(!session()->get('logged_in')): ?>
					 
					<li class="nav-item">
						<a class="nav-link" href="<?php echo site_url('/learn/'); ?>">How to?</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo site_url('/contact/'); ?>">Contact</a>
					</li> 
					<li class="nav-item active">
						<a class="nav-link"  href="<?php echo site_url('/user/login'); ?>">Login</a>
					</li>
					<?php else: ?>
					<div class="dropdown">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item active dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?= $current_user['username']; ?>
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="<?php echo site_url('/user/'. session()->get('user_id') .'/account'); ?>">Account</a>
									<a class="dropdown-item" href="<?php echo site_url('/user/'. session()->get('user_id').'/dreams'); ?>">Dreams</a>
									<?php if($current_user['role'] == 'admin'): ?>		 
										<a class="dropdown-item" href="<?php echo site_url('/admin/'); ?>">Admin</a>
									<?php endif; ?>
									<hr>
									<a class="dropdown-item" href="<?php echo site_url('/user/logout'); ?>">Logout</a>
								</div>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo site_url('/learn/'); ?>">How to?</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo site_url('/contact/'); ?>">Contact</a>
							</li>
						</ul>
					</div>
					 
					<?php if(session()->get('logged_in')): ?>
					<li class="nav-item">	
						<i width="15" height="15" style="color:white;font-size:35px" class="bi bi-link-45deg"></i></i>
						<?php if(isset($pending_links) && count($pending_links)): ?>
							<div type="button" data-bs-auto-close="outside" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="badge badge-pill badge-info pending-links "><span class='pending-links-span'><span class='pending-link-count'><?php echo count($pending_links); ?></span> pending link(s)</span></a>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
									<ul class="list-group">
									<?php foreach($pending_links as $link): ?>
										<li class="list-group-item">
											<image class="auto-link profile-image-small" href="<?php echo site_url('/user/'. $link['request_user_id'].'/'); ?>" src="<?php if($link['image'] == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  base_url('uploads/'. $link['image']); } ?>">
											<?php echo $link['username']; ?><br>
											<div style="text-align:right">
												<a href="" link_id="<?php echo $link['id']; ?>" class='accept-link' title="Accept"><i class="bi bi-check-circle"></i></a>
												<a href="" link_id="<?php echo $link['id']; ?>" class='decline-link' title="Decline"><i class="bi bi-file-x"></i></a>
												<a href="" link_id="<?php echo $link['request_user_id']; ?>"  class='block-link' title="Block"><i class="bi bi-ban"></i></a>
											</div>
										</li>
									<?php endforeach; ?>
									</ul>
								</div>
							</div>
						<?php endif; ?>
					</li>
					<?php endif; ?>
					<?php endif; ?>
				</ul>
				<?php if(session()->get('logged_in')): ?>
				<form class="form-inline mt-2 mt-md-0" action="<?php echo site_url('/search'); ?>">
					<input required='required' class="form-control mr-sm-2" name="search" type="text" value="<?php if(isset($_GET['search'])){ echo esc($_GET['search']); } ?>" placeholder="Search dreamers" aria-label="Search">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="bi bi-search"></i></button>
				</form>
				<?php endif; ?>
			</div>
		</nav>
    </header>
<main role="main" id="content-wrapper" class="container">
<?php if(session()->getFlashdata('error') || (isset($error) && $error)): ?>
	<div class="alert alert-warning">
		<?= session()->getFlashdata('error') ?>
		<?php if(validation_list_errors()): ?>
			<?= validation_list_errors() ?>
		<?php endif; ?>
	</div>
<?php elseif(session()->getFlashdata('success')): ?>
	<div class="alert alert-success">
		<?= session()->getFlashdata('success') ?>
	</div>
<?php endif; ?>

<?php if(!empty($breadcrumbs)): ?>
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<?php foreach($breadcrumbs as $breadcrumb): ?>
			<li class="breadcrumb-item" <?php if(isset($breadcrumb['action'])){ echo 'active" aria-current="page"'; } ?> ><a href="<?php echo $breadcrumb['url']; ?>"><?php echo $breadcrumb['name']; ?></a></li>
		<?php endforeach; ?>
	  </ol>
	</nav>
<?php endif; ?>
