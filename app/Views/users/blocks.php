<h5>Block List</h5>
<table class='table blockTable table-sm text-center'>
	<thead>
		<tr>
			<th>Dreamer</th>
		</tr>
	</thead>
	<tbody>
	<?php if($block_list): ?>
		<?php foreach($block_list as $user_record): ?>
			<tr>
				<td>
					<div>
						<button type="button" title="unblock" class="unblock btn btn-danger btn-sm" href="<?php echo site_url('/user/'. (int) $user_record['id'] .'/unblock/'); ?>">unblock</button>
					</div><Br>
					<a href="<?php echo site_url('/user/'. (int) $user_record['blocked_user_id']); ?>" ><img class='profile-image'  src="<?php if($user_record['image'] == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  esc(base_url('uploads/'. $user_record['image']), 'attr'); } ?>"></a>
					<div>
						<?php echo esc($user_record['username']); ?>
					</div>		
				</td>
			</tr>
		<?php endforeach; ?> 
	<?php endif; ?>
	</tbody>
</table>