<h1>SpamZap2 Auth Counts</h1>
<p>See auth attempts per IP address.</p>

<?php if (count($msgs) > 0 or count($errors) > 0): ?> 
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
<?php endif ?>

<div>
    <?php if (!is_null($table)): ?>
        <?= $table->render() ?>
    <?php endif ?>
</div>
