<div class="ghp">
    <h1>SpamZap2 Tech Logs</h1>
</div>

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

<?php if (!is_null($tm)): ?>
    <?= $tm->render() ?>    
<?php endif ?>

<div class="elapsed">
    <p>Rendered in <?= $elapsed ?> seconds.</p>
</div>
