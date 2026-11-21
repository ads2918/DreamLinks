<div style="font-family:Arial,sans-serif;max-width:480px;color:#26134a">
	<h1 style="font-size:22px">Morning, <?= esc($user['username']) ?>.</h1>
	<p>Anything from last night? Even one image or feeling is worth writing down before it fades.</p>
	<p><a href="<?= site_url('dream/add') ?>" style="display:inline-block;background:#ff4f9a;color:#fff;padding:10px 20px;border-radius:999px;text-decoration:none;font-weight:bold">Log a dream</a></p>
	<p style="font-size:12px;color:#5d4f86">You get this because morning reminders are on. <a href="<?= site_url('user/edit') ?>">Turn them off or change the time</a>.</p>
</div>
