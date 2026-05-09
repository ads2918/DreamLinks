<section>
	<form action="" method="post">
		<?= csrf_field() ?>
		<div class="mask d-flex align-items-center h-100 gradient-custom-3">
			<div class="container h-100">	 
				<div class="card" style="border-radius: 15px;">
					<div class="card-body p-5">
						<h2 class="text-uppercase text-center mb-5">Log Dream</h2>
						<div data-mdb-input-init class="form-outline mb-4">
							<label class="form-label" for="dat">Date</label>
							<input type="date" name="date" id="date" value="<?php if($_POST) { echo esc(set_value('date')); }elseif(isset($date)){ echo $date; }else{ echo esc(date('Y-m-d',strtotime('now'))); } ?>" class="form-control form-control-lg" />	
						</div>
						<div data-mdb-input-init class="form-outline mb-4">
							<label class="form-label" for="title">Title</label>
							<input type="text"  maxlength="100" minlength="1"  name="title" id="title" value="<?php if($_POST){ echo esc(set_value('title')); }elseif(isset($title) && isset($id) && !$_POST){ echo $title; } ?>" class="form-control form-control-lg" />	
						</div>
						<div data-mdb-input-init class="form-outline mb-4" >
							<label class="form-label" for="full_description">Dream</label>
							<textarea name="full_description"  maxlength="700" minlength="1"  id="full_description" class="form-control form-control-lg" /><?php if($_POST){ echo esc(set_value('full_description')); }elseif(isset($full_description)){ echo $full_description; } ?></textarea>
						</div>	
						<div class="mb-3">
							<label for="newTagInput" class="form-label">Tag: <small></label>
							<input class="form-control form-control-lg"  name='tags' value='<?php if(esc(set_value('tags')) != '' && $_POST){ echo esc(set_value('tags')); }elseif(isset($tags)){ echo esc($tags); } ?>' autofocus>
						</div>
						<div class="mb-3">
							<label for="newTagInput" class="form-label">Visibility: <small></label>
							<select name="visibility" class="form-control form-control-lg" autofocus>
								<option value="">- select -</option>
								<option <?php if($visibility == 'friends' || (isset($_POST) &&  esc(set_value('visibility')) == 'friends')){ echo "selected='selected'"; } ?> value="friends">Friends - dreamers that you have approved links with can view.</option>
								<option <?php if($visibility == 'public' || (isset($_POST) &&  esc(set_value('visibility')) == 'public')){ echo "selected='selected'"; } ?> value="public">Public - all site dreamers can view.</option>
								<option <?php if($visibility == 'private' || (isset($_POST) &&  esc(set_value('visibility')) == 'private')){ echo "selected='selected'"; } ?> value="private">Private - only you can view dreams.</option>
							</select>
						</div>
						<div class="d-flex justify-content-center">
							<button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success btn-block btn-lg gradient-custom-4 text-body"><?php if(isset($id)){ echo 'Save'; }else{ echo 'Add'; } ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
 
 