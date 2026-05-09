<?php if($blocked): ?>
	<div class="alert alert-warning">
		User is on  <a href="<?php echo site_url('/user/'. $session_user .'/blocks'); ?>" >block list</a> and as a result you cannot view full profile.
	</div>
<?php endif; ?>
	<div style="float:left;">
		<a href="<?php echo site_url('/user/'. $profile_user['id']); ?>" ><img class='profile-image'  src="<?php if($profile_user['image'] == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  base_url('uploads/'. $profile_user['image']); } ?>"></a>
		<h5><?php echo $profile_user['username']; ?></h5> 
<?php if(!$blocked): ?>
	<?php if($current_user['id'] != $profile_user['id']): ?>
		<?php if(!$blocked): ?>
			<?php if($link_approved): ?>
				<i title="Linked" class="bi bi-link-45deg larger-icon"></i></i>
			<?php elseif($link_pending): ?>
				<i title="Link request pending" title="Link request pending" class="bi bi-clock larger-icon link-pending"></i>
			<?php else: ?>
				<span title="Request Link" class='request-link' link_id="<?php echo $profile_user['id']; ?>">
					<i class="bi bi-patch-plus-fill larger-icon"></i>
				</span>
			<?php endif; ?>
			<span link_id="<?php echo $profile_user['id'] ?>"  class='profile-block-link pointer red' title="Block"><i class="bi bi-ban"></i></span> 

		<?php endif; ?>	
	<?php else: ?>
		<i>You is me</i>
	<?php endif; ?>
			<div>
				<?php echo $profile_user['bio']; ?>
			</div>
	</div>
	<div class="profile-right-wrapper" style="margin: 75px;float:right;width:70%">
		<?php foreach($recent_dreams as $recent_dream): ?><br>
			<ul class="list-group">
			  <li class="list-group-item">
				<h4><?php echo $recent_dream['title']; ?></h4>
				<small><b><?php echo date('m/d/Y',strtotime($recent_dream['date'])); ?></b></small>
				<?php echo $recent_dream['full_description']; ?>		
			  </li> 
			</ul>
		<?php endforeach; ?>
	</div>
<?php endif; ?>