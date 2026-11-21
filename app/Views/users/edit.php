<br><div>
	<form action="/user/edit" method="post" enctype="multipart/form-data">
		<?= csrf_field() ?>
		<div class="mask d-flex align-items-center h-100 gradient-custom-3">
			<div class="container h-100">
				<div class="card" style="border-radius: 15px;">
					<div class="card-body p-5">
						<div class="mb-3">
						<div class='text-center'><img class=' profile-image' src="<?php if($image == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  base_url('uploads/'. $image); } ?>"></div>
						  <label for="formFile" class="form-label">Profile Image</label>
						  <input class="form-control" type="file" name="image" id="formFile" accept="image/jpeg, image/png">
						</div>
						<div data-mdb-input-init class="form-outline mb-4" >
							<label class="form-label" for="bio">Bio</label>
							<textarea name="bio"  maxlength="200" minlength="1"  id="bio" class="form-control form-control-lg" /><?php if($_POST){ echo esc(set_value('bio')); }elseif(isset($bio)){ echo esc($bio); } ?></textarea>
						</div>
						<div data-mdb-input-init class="form-outline mb-4">
						  <input type="text" name="username" id="form3Example1cg" value="<?php if(set_value('username')){ echo esc(set_value('username')); }elseif(isset($username)){ echo esc($username);  } ?>" class="form-control form-control-lg" />
						  <label class="form-label" for="form3Example1cg">Username</label>
						</div>
						<div data-mdb-input-init class="form-outline mb-4">
						  <input type="text" name="email" id="form3Example1cg" value="<?php if(set_value('email')){ echo esc(set_value('email')); }elseif(isset($email)){ echo esc($email);  } ?>" class="form-control form-control-lg" />
						  <label class="form-label" for="form3Example1cg">Email</label>
						</div>
						<div data-mdb-input-init class="form-outline mb-4">
						  <input type="password" name="current_password" id="form3Example4cg" value="" class="form-control form-control-lg" />
						  <label class="form-label" for="form3Example4cg">Current password</label>
						</div>
						<div data-mdb-input-init class="form-outline mb-4">
						  <input type="password" name="new_password" id="form3Example4cg" value="" class="form-control form-control-lg" />
						  <label class="form-label" for="form3Example4cg">New password</label>
						</div>
						<div class="form-check">
						  <input name="ai_analysis_enabled" class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" <?= set_checkbox('ai_analysis_enabled', '1', $current_user['ai_analysis_enabled'] == 1) ?>>
						
						  <label class="form-check-label" for="flexCheckChecked">
							Enable AI to analyze dreams?
						  </label>
						</div><br>
						<div data-mdb-input-init class="form-outline mb-4">
						  <select class='form-control' name='default_dream_visibility' required='required'>		
							<option value="">- select -</option>
							<option <?php if($current_user['default_dream_visibility']  == 'friends' || set_value('default_dream_visibility') == 'friends'){ echo 'selected="selected"'; } ?> value="friends">Friends - dreamers that you have approved links with can view.</option>
							<option <?php if($current_user['default_dream_visibility']  == 'public' || set_value('default_dream_visibility') == 'public'){ echo 'selected="selected"'; } ?> value="public">Public - all site dreamers can view.</option>
							<option <?php if($current_user['default_dream_visibility']  == 'private' || set_value('default_dream_visibility') == 'private'){ echo 'selected="selected"'; } ?> value="private">Private - only you can view dreams.</option>
						  </select>
						  <label class="form-label" for="form3Example4cg">Dream visibility default</label> <small>(will automatically select this when entering dreams.)</small>
						</div>
						<?php $reminder_on = $_POST ? (bool) set_value('reminder_enabled') : (bool) ($current_user['reminder_enabled'] ?? 0); ?>
						<?php $reminder_hour = (int) ($_POST ? set_value('reminder_hour') : ($current_user['reminder_hour'] ?? 8)); ?>
						<div class="form-check">
						  <input name="reminder_enabled" class="form-check-input" type="checkbox" value="1" id="reminder_enabled"<?= $reminder_on ? ' checked' : '' ?>>
						  <label class="form-check-label" for="reminder_enabled">
							Email me a morning reminder to log my dreams
						  </label>
						</div>
						<div class="form-outline mb-4">
						  <label class="form-label" for="reminder_hour">Remind me at</label>
						  <select class="form-control" name="reminder_hour" id="reminder_hour">
							<?php for ($h = 4; $h <= 12; $h++): ?>
								<option value="<?= $h ?>"<?= $reminder_hour === $h ? ' selected' : '' ?>><?= $h === 12 ? '12:00 pm' : $h . ':00 am' ?></option>
							<?php endfor; ?>
						  </select>
						  <small>Your time zone: <span data-dl-timezone-label><?= esc($current_user['timezone'] ?? 'America/Indianapolis') ?></span>. Skipped on mornings you've already logged a dream.</small>
						  <input type="hidden" name="timezone" value="<?= esc($current_user['timezone'] ?? 'America/Indianapolis') ?>" data-dl-timezone>
						</div>
						<div class="d-flex justify-content-center">
						  <button  type="submit" data-mdb-button-init
							data-mdb-ripple-init class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Save</button>
						</div> 
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

 
 