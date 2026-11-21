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
		<tbody>
<?php if(!empty($pending_user_list)): ?>
	<?php foreach($pending_user_list as $user_record): ?>
		<tr>
			<td><?php echo esc($user_record['username']); ?></td>
			<td><?php echo esc($user_record['email']); ?></td>
			<td><?php echo date('m/d/Y',strtotime($user_record['created_datetime'])); ?></td>
			<td>
				<form action="<?php echo site_url('admin/approve-user/'. (int) $user_record['id']); ?>" method="post" data-confirm="Approve <?php echo esc($user_record['username'], 'attr'); ?>?">
					<?= csrf_field() ?>
					<button type="submit" class="btn btn-sm btn-primary">Approve</button>
				</form>
			</td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
	</tbody>
</table>
<hr>
<h5>Active Users</h5>
<?php if(!empty($active_user_list)): ?>
	<table class='table table-sm' id="activeUsers">
		<thead>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Created</th>
			</tr>
		</thead>
		<tbody>
	<?php foreach($active_user_list as $user_record): ?>
		<tr>
			<td><?php echo esc($user_record['username']); ?></td>
			<td><?php echo esc($user_record['email']); ?></td>
			<td><?php echo date('m/d/Y',strtotime($user_record['created_datetime'])); ?></td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
