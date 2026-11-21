<h5>Link List</h5>
<table id="linkTable" class="table table-sm text-center">
	<thead>
		<tr>
			<th>Dreamer</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($links as $user_record): ?> 
			<tr>
				<td>
					<div>
						<button type="button" title="unlink" class="unlink btn btn-danger btn-sm" href="<?php echo site_url('/user/'. (int) $user_record['id'] .'/unlink/'); ?>">unlink</button>
					</div><Br>
					<a href="<?php echo site_url('/user/'. (int) $user_record['profile_id']); ?>" ><img class='profile-image'  src="<?php if($user_record['image'] == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  esc(base_url('uploads/'. $user_record['image']), 'attr'); } ?>"></a>
					<div>
						<?php echo esc($user_record['username']); ?>
					</div>	
					<div>
						<i title="Linked" class="bi bi-link-45deg larger-icon"></i></i> 
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
 