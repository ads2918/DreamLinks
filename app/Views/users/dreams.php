<h3>
	<div>
	<a href="<?php echo site_url('dream/add'); ?>">
		<button type="button" title="Add Dream" class="btn btn-primary">+</button>
	</a>
 
	</div>
</h3>
<?php if($current_user['ai_analysis_enabled']): ?>
	<?php if($has_recent_prompt): ?>
		<small><strong>Last <image width="15px" height="15px" src="<?= base_url('assets/images/bot.png'); ?>">  Anaylsis</strong>:  
			<?php echo $dream_recent_overview; ?> (<a href="<?php echo site_url('/user/'. session()->get('user_id') .'/analysis'); ?>">View all</a>)<br><br>
		</div>
	<?php endif; ?> 
<?php else: ?>
	<div class="alert alert-warning" role="alert">
	  AI analysis is not enabled.  <a href="<?= base_url('user/edit'); ?>">Click here</a> to enabled it.
	</div>
<?php endif; ?>
<i class="bi bi-circle-fill"></i> <i><?php echo $next_moon_cycle[0]['cycle']; ?></i>
<table class='table table-sm' id="dreamsTable">
		<thead>
			<tr>
				<th column='title'>Date</th>
				<th column='full_description'>Title</th>
				<th column='tags'>Description</th>
				<th column='links'>Tags</th>
				<th column='visibility'><i class="bi bi-eye"></i></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
</table>
