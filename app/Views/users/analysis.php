<h4><image class='ai-bot' src="<?= base_url('assets/images/bot.png'); ?>"> Analysis</h4>
<small><strong>Note: </strong>Analysis runs nightly for any new dreams added.</small><br><br>
<?php if($tags_found): ?>
	<div>
	  <canvas id="topTagsChart"></canvas>
	</div>
<?php endif; ?>
<?php
if($current_user['ai_analysis_enabled']):
	if(!empty($user_past_prompts)): 
	?>
	 <ul class="list-group">
	 <?php
		foreach($user_past_prompts as $prompt): 
			$response = json_decode($prompt['json_response']);
			if(isset($response->overview)): 
				?>
				<li class="list-group-item">
					<h5><?php echo date('m/d/Y',strtotime($prompt['prompt_datetime'])); ?></h5>
					<?php echo $response->overview; ?><br><br>
					<div>
						<small><b>Symbols:</b><br><?php echo implode('<br>',$response->symbols) ; ?></small>
					</div><br>
					<div>
						<small><b>Patterns:</b><br><?php echo implode('<br>',$response->patterns) ; ?></small>
					</div><br>
					<div>
						<small><b>Celestial:</b><br><?php echo implode('<br>',$response->significant_plantery_or_moons_syncing_with_dream_patterns) ; ?></small>
					</di>
				</li>
				<?php
			endif; 
		endforeach;
	 ?>
	 </ul>
	 <?php
	endif;
else:
?>
<div class="alert alert-warning" role="alert">
  AI analysis is not enabled.  <a href="<?= base_url('user/edit'); ?>">Click here</a> to enabled it.
</div>
<?php
endif; 
?>
