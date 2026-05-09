<h3>Admin</h3><hr>
<h5>Pending Users</h5>
<table class='table table-sm' id="pendingUsers">
		<thead>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Created</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		</tbody>
<?php if(!empty($pending_user_list)): ?>
	<?php foreach($pending_user_list as $user_record): ?>
		<tr>
			<td><?php echo $user_record['username'] ;?></td>
			<td><?php echo $user_record['email']; ?></td>
			<td><?php echo date('m/d/Y',strtotime($user_record['created_datetime'])); ?></td>
			<td><a href="<?php echo site_url('admin/approve-user/'. $user_record['id']); ?>">Approve</a></td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
	</tbody>
</table>
<hr>
<h5>Active Users</h5>
<table class='table'>
<?php if(!empty($active_user_list)): ?>
	<table class='table table-sm' id="activeUsers">
		<thead>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Created</th>
			</tr>
		</thead>
		</tbody>
	<?php foreach($active_user_list as $user_record): ?>
		<tr>
			<td><?php echo $user_record['username'] ;?></td>
			<td><?php echo $user_record['email']; ?></td>
			<td><?php echo date('m/d/Y',strtotime($user_record['created_datetime'])); ?></td>
		</tr>
	<?php endforeach; ?>
	
	 
<?php endif; ?>	
	</tbody>
</table>
