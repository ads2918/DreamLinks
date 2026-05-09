<!DOCTYPE html>
<html>
<head><title>Contact Us</title></head>
<body>
    <h2>Contact Us</h2>

    <?php if (session()->getFlashdata('status')): ?>
        <p style="color: green;"><?= session()->getFlashdata('status') ?></p>
    <?php endif; ?>

    <?= validation_list_errors() ?>

    <?= form_open('contact/send') ?>
        <label>Name:</label>
        <input type="text" required='required' class='form-control' name="name" value="<?= set_value('name') ?>"><br>

        <label>Email:</label>
        <input type="email" required='required' class='form-control' name="email" value="<?= set_value('email') ?>"><br>

        <label>Message:</label>
        <textarea class='form-control' required='required' name="message"><?= set_value('message') ?></textarea><br>

        <button type="submit" class='btn'>Send</button>
    <?= form_close() ?>
</body>
</html>