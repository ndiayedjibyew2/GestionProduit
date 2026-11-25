<section class="conteneur">
    <div class="entete-page">
        <div>
            <h1>Catégories</h1>
            <p class="discret"><?= count($categories) ?> catégorie<?= count($categories) > 1 ? 's' : '' ?></p>
        </div>
        <?php if (est_admin()): ?>
            <a class="btn btn-primaire" href="index.php?page=categories&action=creer">+ Nouvelle catégorie</a>
        <?php endif; ?>
    </div>

    <?php if (!$categories): ?>
        <div class="vide"><p>Aucune catégorie pour le moment.</p></div>
    <?php else: ?>
        <div class="grille-categories">
            <?php foreach ($categories as $c): $n = (int) $c['nb_produits']; ?>
                <article class="carte-categorie">
                    <div>
                        <h3><a href="index.php?page=produits&categorie=<?= (int) $c['id'] ?>"><?= e($c['nom']) ?></a></h3>
                        <p class="discret"><?= e($c['description'] ?: 'Pas de description.') ?></p>
                    </div>
                    <div class="carte-categorie-bas">
                        <span class="pastille"><?= $n ?> produit<?= $n > 1 ? 's' : '' ?></span>
                        <?php if (est_admin()): ?>
                            <span class="actions">
                                <a class="btn btn-fantome btn-petit" href="index.php?page=categories&action=modifier&id=<?= (int) $c['id'] ?>">Modifier</a>
                                <?php if ($n === 0): ?>
                                    <form method="post" action="index.php?page=categories&action=supprimer" data-confirmer="Supprimer la catégorie « <?= e($c['nom']) ?> » ?">
                                        <?= champ_csrf() ?>
                                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                        <button class="btn btn-danger-doux btn-petit" type="submit">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
