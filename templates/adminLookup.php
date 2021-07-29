<h1>SpamZap2 IP Lookup</h1>
<p>Lookup an IP address.</p>

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

<?php if (!is_null($lookupForm)): ?>
    <div class="form-container">
        <div class="form-box">
            <div class="singleform">
                <?= $lookupForm->render(); ?>
            </div>
        </div>
    </div>
<?php endif ?>

<div class="lookup-results">

    <?php if (count($data) > 0): ?>
        <?php foreach ($data as $k => $v): ?>
            <div class="lookup-line">
                <span class="lookup-key"><?= $k ?></span>
                <?php if (is_array($v)): ?>
                    <span class="lookup-value"><?= implode(', ', $v) ?></span>
                <?php else: ?>
                    <span class="lookup-value"><?= $v ?></span>
                <?php endif ?>
            </div>
        <?php endforeach ?>

    <div class="lookup-raw">
        <?= $raw ?>
    </div>

    <?php endif ?>

</div>
