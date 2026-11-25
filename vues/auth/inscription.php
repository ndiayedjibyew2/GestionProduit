<section class="conteneur auth">
    <div class="panneau">
        <h1>Créer un compte</h1>
        <p class="discret">Quelques secondes suffisent.</p>

        <?php require RACINE . '/vues/partials/erreurs.php'; ?>

        <form method="post" action="index.php?page=inscription" class="formulaire">
            <?= champ_csrf() ?>
            <label class="champ">
                <span>Nom complet</span>
                <input type="text" name="nom" value="<?= e($nom) ?>" required maxlength="100" autocomplete="name" autofocus>
            </label>
            <label class="champ">
                <span>Adresse email</span>
                <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
            </label>
            <div class="champs-2">
                <label class="champ">
                    <span>Mot de passe</span>
                    <input type="password" name="password" required minlength="8" autocomplete="new-password">
                    <small class="discret">8 caractères minimum</small>
                </label>
                <label class="champ">
                    <span>Confirmation</span>
                    <input type="password" name="confirmation" required minlength="8" autocomplete="new-password">
                </label>
            </div>
            <button type="submit" class="btn btn-primaire btn-large">Créer mon compte</button>
        </form>

        <p class="auth-bas">Déjà inscrit ? <a href="index.php?page=connexion">Se connecter</a></p>
    </div>
</section>
