<section class="conteneur">
    <div class="entete-page">
        <div>
            <h1>Tableau de bord</h1>
            <p class="discret">Vue d'ensemble du catalogue et du stock.</p>
        </div>
        <div class="actions">
            <a class="btn btn-fantome" href="index.php?page=categories&action=creer">+ Catégorie</a>
            <a class="btn btn-primaire" href="index.php?page=produits&action=creer">+ Produit</a>
        </div>
    </div>

    <div class="grille-stats">
        <a class="stat" href="index.php?page=produits">
            <span class="stat-libelle">Produits</span>
            <span class="stat-valeur"><?= (int) $stats['total_produits'] ?></span>
        </a>
        <a class="stat" href="index.php?page=categories">
            <span class="stat-libelle">Catégories</span>
            <span class="stat-valeur"><?= (int) $stats['total_categories'] ?></span>
        </a>
        <a class="stat" href="index.php?page=utilisateurs">
            <span class="stat-libelle">Utilisateurs</span>
            <span class="stat-valeur"><?= (int) $stats['total_utilisateurs'] ?></span>
        </a>
        <div class="stat stat-accent">
            <span class="stat-libelle">Valeur du stock</span>
            <span class="stat-valeur"><?= formater_prix($stats['valeur_stock']) ?></span>
            <span class="stat-note"><?= number_format((int) $stats['total_stock'], 0, ',', ' ') ?> unités</span>
        </div>
    </div>

    <div class="grille-2">
        <div class="panneau">
            <div class="entete-section"><h2>Derniers produits</h2><a href="index.php?page=produits">Tout voir →</a></div>
            <?php if (!$derniers_produits): ?>
                <p class="discret">Aucun produit.</p>
            <?php else: ?>
                <ul class="liste">
                    <?php foreach ($derniers_produits as $p): ?>
                        <li>
                            <a href="index.php?page=produits&action=modifier&id=<?= (int) $p['id'] ?>">
                                <span>
                                    <strong><?= e($p['nom']) ?></strong>
                                    <span class="discret petit"><?= e($p['categorie_nom'] ?: 'Sans catégorie') ?> · ajouté le <?= formater_date($p['date_creation']) ?></span>
                                </span>
                                <span class="prix"><?= formater_prix($p['prix']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="colonne">
            <div class="panneau">
                <div class="entete-section"><h2>Stock bas</h2></div>
                <?php if (!$stock_bas): ?>
                    <p class="discret">Tout est bien approvisionné.</p>
                <?php else: ?>
                    <ul class="liste">
                        <?php foreach ($stock_bas as $p): $q = (int) $p['quantite']; ?>
                            <li>
                                <a href="index.php?page=produits&action=modifier&id=<?= (int) $p['id'] ?>">
                                    <span><?= e($p['nom']) ?></span>
                                    <span class="pastille <?= $q === 0 ? 'pastille-danger' : 'pastille-alerte' ?>"><?= $q === 0 ? 'Rupture' : $q . ' restant' . ($q > 1 ? 's' : '') ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="panneau">
                <div class="entete-section"><h2>Catégories</h2></div>
                <?php $max = max(1, ...array_map(fn($c) => (int) $c['nb_produits'], $categories_populaires ?: [['nb_produits' => 1]])); ?>
                <ul class="barres">
                    <?php foreach ($categories_populaires as $c): $n = (int) $c['nb_produits']; ?>
                        <li>
                            <span class="barres-libelle"><?= e($c['nom']) ?></span>
                            <span class="barres-piste"><span class="barres-rempli" style="width: <?= round($n / $max * 100) ?>%"></span></span>
                            <span class="barres-valeur"><?= $n ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
