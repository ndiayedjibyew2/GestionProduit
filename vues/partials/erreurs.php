<?php if (!empty($erreurs)): ?>
    <div class="alerte alerte-danger" role="alert">
        <div>
            <strong>Merci de corriger les points suivants :</strong>
            <ul class="liste-erreurs">
                <?php foreach ($erreurs as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
