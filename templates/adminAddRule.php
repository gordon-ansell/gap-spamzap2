<h1>SpamZap2 Add Rule</h1>
<p>Add blocks or allows to SpamZap2.</p>

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
            <?= $addRuleForm->render(); ?>
        </div>
    </div>
</div>

<div><a href="https://regex101.com" target="_blank">Regex Tester</a></div>
<div>
    <h4>Regex Examples</h4>
    <ul>
        <li>Block a domain extension: <code>.*\.xx</code>, where 'xx' is the domain extension.</li>
        <li>Block a domain extension at end of string: <code>.*\.xx$</code>, where 'xx' is the domain extension.</li>
    </ul>
</div>
