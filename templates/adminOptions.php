<div class="ghp">
    <h1>SpamZap2 Options</h1>
</div>

<div class="topmsg">
    <?php if (count($msgs) > 0): ?>
        <div class="topmsg_msg">
            <?php foreach ($msgs as $m): ?>
                <p><?= $m ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?php if (count($errors) > 0): ?>
        <div class="topmsg_error">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>

<div class="form-container">
    <div class="form-box">
        <div class="singleform">
            <?= $optionsForm->render(); ?>
        </div>
    </div>
</div>
