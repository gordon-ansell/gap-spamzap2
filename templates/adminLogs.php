<div class="ghp">
    <h1>SpamZap2 Logs</h1>
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

<div class="logstats">
    <?php if (1 == $lognew): ?>
        <p><?= $lognew ?> new entry.</p>
    <?php else: ?>
        <p><?= $lognew ?> new entries.</p>
    <?php endif ?>
</div>

<?php if (!is_null($tm)): ?>
    <?= $tm->render() ?>    
<?php endif ?>

<div class="elapsed">
    <p>Rendered in <?= $elapsed ?> seconds.</p>
</div>
