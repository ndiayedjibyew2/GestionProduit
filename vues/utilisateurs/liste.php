<section class="conteneur">
    <div class="entete-page">
        <div>
            <h1>Utilisateurs</h1>
            <p class="discret"><?= count($utilisateurs) ?> compte<?= count($utilisateurs) > 1 ? 's' : '' ?></p>
        </div>
    </div>

    <div class="tableau-cadre">
        <table class="tableau">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Inscrit le</th>
                <th>Rôle</th>
                <th class="num">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($utilisateurs as $u): $admin = $u['role'] === 'admin'; $moi = (int) $u['id'] === (int) utilisateur_courant()['id']; ?>
                <tr>
                    <td><strong><?= e($u['nom']) ?></strong><?= $moi ? ' <span class="discret petit">(vous)</span>' : '' ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= formater_date($u['date_creation']) ?></td>
                    <td><span class="pastille <?= $admin ? 'pastille-accent' : '' ?>"><?= $admin ? 'Admin' : 'Membre' ?></span></td>
                    <td class="num">
                        <?php if (!$moi): ?>
                            <form method="post" action="index.php?page=utilisateurs&action=role"
                                  data-confirmer="<?= $admin ? 'Retirer les droits admin à ' : 'Donner les droits admin à ' ?><?= e($u['nom']) ?> ?">
                                <?= champ_csrf() ?>
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <input type="hidden" name="role" value="<?= $admin ? 'utilisateur' : 'admin' ?>">
                                <button class="btn btn-fantome btn-petit" type="submit"><?= $admin ? 'Rétrograder' : 'Promouvoir admin' ?></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
