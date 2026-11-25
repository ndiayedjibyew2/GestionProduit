<section class="hero">
    <div class="conteneur">
        <p class="surtitre">Catalogue</p>
        <h1>Tous vos produits, au bon endroit.</h1>
        <p class="hero-texte">
            <?= (int) $stats['produits'] ?> produit<?= $stats['produits'] > 1 ? 's' : '' ?>
            répartis dans <?= (int) $stats['categories'] ?> catégorie<?= $stats['categories'] > 1 ? 's' : '' ?>.
            Prix en FCFA, stock mis à jour en temps réel.
        </p>
        <div class="hero-actions">
            <?php if (est_admin()): ?>
                <a class="btn btn-primaire" href="index.php?page=produits&action=creer">Ajouter un produit</a>
                <a class="btn btn-fantome" href="index.php?page=admin">Tableau de bord</a>
            <?php elseif (est_connecte()): ?>
                <a class="btn btn-primaire" href="index.php?page=produits">Parcourir les produits</a>
            <?php else: ?>
                <a class="btn btn-primaire" href="index.php?page=inscription">Créer un compte</a>
                <a class="btn btn-fantome" href="index.php?page=connexion">J'ai déjà un compte</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="conteneur">
    <div class="entete-section">
        <h2>Derniers produits</h2>
        <?php if (est_connecte() && $produits): ?>
            <a href="index.php?page=produits">Voir tout →</a>
        <?php endif; ?>
    </div>

    <?php if (!$produits): ?>
        <div class="vide">
            <p>Aucun produit pour le moment.</p>
            <?php if (est_admin()): ?>
                <a class="btn btn-primaire" href="index.php?page=produits&action=creer">Ajouter le premier produit</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grille-produits">
            <?php foreach ($produits as $p): ?>
                <article class="carte-produit">
                    <div class="carte-produit-haut">
                        <span class="pastille"><?= e($p['categorie_nom'] ?: 'Sans catégorie') ?></span>
                        <?php if ((int) $p['quantite'] === 0): ?>
                            <span class="pastille pastille-danger">Rupture</span>
                        <?php elseif ((int) $p['quantite'] <= 5): ?>
                            <span class="pastille pastille-alerte">Stock bas</span>
                        <?php endif; ?>
                    </div>
                    <h3><?= e($p['nom']) ?></h3>
                    <p class="discret carte-produit-desc"><?= e(extrait($p['description'] ?: 'Pas de description.', 90)) ?></p>
                    <div class="carte-produit-bas">
                        <strong class="prix"><?= formater_prix($p['prix']) ?></strong>
                        <span class="discret"><?= (int) $p['quantite'] ?> en stock</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
