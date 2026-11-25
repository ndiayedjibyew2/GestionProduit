<section class="conteneur auth">
    <div class="panneau">
        <h1>Connexion</h1>
        <p class="discret">Accédez à votre espace de gestion.</p>

        <?php if ($erreur): ?>
            <div class="alerte alerte-danger" role="alert"><?= e($erreur) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?page=connexion" class="formulaire">
            <?= champ_csrf() ?>
            <label class="champ">
                <span>Adresse email</span>
                <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email" autofocus>
            </label>
            <label class="champ">
                <span>Mot de passe</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="btn btn-primaire btn-large">Se connecter</button>
        </form>

        <p class="auth-bas">Pas encore de compte ? <a href="index.php?page=inscription">Créer un compte</a></p>
    </div>
</section>
