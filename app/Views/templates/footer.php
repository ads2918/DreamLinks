	</div>
</main>
		<footer class="dl-footer">
			<div class="dl-wrap">
				<span>&copy; <?= date('Y'); ?> DreamLinks</span>
				<span><a href="<?= site_url('learn'); ?>">How to lucid dream</a> · <a href="<?= site_url('contact'); ?>">Contact</a></span>
			</div>
		</footer>

		<!-- legacy scripts: still needed by pages that have not been rebuilt yet -->
		<script src="<?= base_url('assets/js/jquery-3.2.1.slim.min.js'); ?>"></script>
		<script src="<?= base_url('assets/js/popper.min.js'); ?>"></script>
		<script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
		<script src="<?= base_url('assets/js/bootstrap-tagsinput.js'); ?>"></script>
		<script src="<?= base_url('assets/js/bootbox.min.js'); ?>"></script>
		<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
		<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.7/b-3.2.6/r-3.0.8/datatables.min.css" rel="stylesheet" integrity="sha384-y6r/bMvHkJcSe1KeErIn/nqHmztkxkqqe1fyIpqWBsZ3BIq5UENTFD8YwQeRUtNm" crossorigin="anonymous">
		<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.7/b-3.2.6/r-3.0.8/datatables.min.js" integrity="sha384-L+1onriy9Lrh+NdzJXjKG97EQpC5diTTkQpDdlOeFqPX8GP0a615A4y/Xt19DM2m" crossorigin="anonymous"></script>
		<script src="<?= base_url('assets/js/main.js'); ?>?v=<?= date('YmdH') ?>"></script>

		<script src="<?= base_url('assets/js/dreamlinks.js'); ?>?v=<?= date('YmdH') ?>"></script>

		<?php if (isset($page_js)): ?>
			<?php foreach ((array) $page_js as $js): ?>
				<script src="<?= base_url('assets/js/' . $js); ?>?v=<?= date('YmdH') ?>"></script>
			<?php endforeach; ?>
		<?php endif; ?>
	</body>
</html>
