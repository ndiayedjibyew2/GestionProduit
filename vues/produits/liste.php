<section class="conteneur">
    <div class="entete-page">
        <div>
            <h1>Produits</h1>
            <p class="discret"><?= count($produits) ?> résultat<?= count($produits) > 1 ? 's' : '' ?></p>
        </div>
        <?php if (est_admin()): ?>
            <a class="btn btn-primaire" href="index.php?page=produits&action=creer">+ Nouveau produit</a>
        <?php endif; ?>
    </div>

    <form class="filtres" method="get" action="index.php">
        <input type="hidden" name="page" value="produits">
        <input type="search" name="q" value="<?= e($recherche) ?>" placeholder="Rechercher un produit…" aria-label="Rechercher">
        <select name="categorie" aria-label="Catégorie">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $categorie === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-fantome" type="submit">Filtrer</button>
        <?php if ($recherche !== '' || $categorie): ?>
            <a class="lien-discret" href="index.php?page=produits">Réinitialiser</a>
        <?php endif; ?>
    </form>

    <?php if (!$produits): ?>
        <div class="vide">
            <p><?= $recherche !== '' || $categorie ? 'Aucun produit ne correspond à votre recherche.' : 'Aucun produit pour le moment.' ?></p>
        </div>
    <?php else: ?>
        <div class="tableau-cadre">
            <table class="tableau">
                <thead>
                <tr>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th class="num">Prix</th>
                    <th class="num">Stock</th>
                    <?php if (est_admin()): ?><th class="num">Actions</th><?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($produits as $p): $q = (int) $p['quantite']; ?>
                    <tr>
                        <td>
                            <strong><?= e($p['nom']) ?></strong>
                            <?php if ($p['description']): ?>
                                <div class="discret petit"><?= e(extrait($p['description'], 70)) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= $p['categorie_nom'] ? e($p['categorie_nom']) : '<span class="discret">—</span>' ?></td>
                        <td class="num"><?= formater_prix($p['prix']) ?></td>
                        <td class="num">
                            <span class="pastille <?= $q === 0 ? 'pastille-danger' : ($q <= $seuil ? 'pastille-alerte' : 'pastille-ok') ?>"><?= $q ?></span>
                        </td>
                        <?php if (est_admin()): ?>
                            <td class="num actions">
                                <a class="btn btn-fantome btn-petit" href="index.php?page=produits&action=modifier&id=<?= (int) $p['id'] ?>">Modifier</a>
                                <form method="post" action="index.php?page=produits&action=supprimer" data-confirmer="Supprimer « <?= e($p['nom']) ?> » ?">
                                    <?= champ_csrf() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button class="btn btn-danger-doux btn-petit" type="submit">Supprimer</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
