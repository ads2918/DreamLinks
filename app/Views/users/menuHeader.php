<div class='float-left'><image src="<?= base_url('assets/images/dreaming.png'); ?>"><h3>Account <small>(<?php echo esc($account['username']); ?>)</small></h3></div>
<div class='float-right'><a href="<?php echo site_url('/user/'. (int) $account['id']); ?>" ><img class='profile-image'  src="<?php if($account['image'] == ''){ echo  base_url('assets/images/no-image.png'); }else{ echo  esc(base_url('uploads/'. $account['image']), 'attr'); } ?>"></a></div>
<div class='float-clear'></div>
