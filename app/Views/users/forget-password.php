<div class="card p-5">
    <h2>Reset Password</h2>
    <p>Enter your email and we'll send you a link.</p>
    <form action="<?= base_url('user/sendResetLink') ?>" method="post">
        <?= csrf_field() ?>
        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
        <button type="submit" class="btn btn-primary">Send Link</button>
    </form>
</div>