<div style="text-align:center">
	<span class='h3'>What's everyone else dreaming about?</span>
</div>
<!--<small><b>Note:</b> Dreams below have been flagged by dreamer as public or for friends to view.</small>-->
<div class="latest-dream-wrapper">
	<?php if(!empty($public_dreams)): ?>
		<?php foreach($public_dreams as $recent_dream): ?><br>
			<ul class="list-group">
			  <li class="list-group-item">
				<div style="float:left;">
					<?php 
					if($recent_dream['image'] == ''){ 
						$profile_image =  base_url('assets/images/no-image.png'); 
					}else{ 
						$profile_image =  base_url('uploads/'. $recent_dream['image']); 
					}
					?>
					<a href="<?= site_url('/user/'. $recent_dream['id']); ?>" ><img class="profile-image"  src="<?php echo  $profile_image; ?>"></a>
					<div style="text-align:center">
						<?php echo $recent_dream['username']; ?>
					</div>	
				</div>
				<div style="float:right;width:85%">
					<h4><?php echo $recent_dream['title']; ?></h4>
					<small><b><?php echo date('m/d/Y',strtotime($recent_dream['date'])); ?></b></small>
					<?php echo $recent_dream['full_description']; ?>	
				</div>
				<div style="clear:both">
			  </li> 
			</ul>
		<?php endforeach; ?>
	<?php else: ?>
		<br>
		No dreams could be located at this time...
	<?php endif; ?>
</div>