<h1>SpamZap2 Manage Rules</h1>
<p>Delete a SpamZap2 rule.</p>

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

<div class="form-container">
    <div class="form-box">
        <div class="singleform">
            <?= $manageRulesForm->render(); ?>
        </div>
    </div>
</div>

<div>
    <?php if (!is_null($table)): ?>
        <?= $table->render() ?>
    <?php endif ?>
</div>
