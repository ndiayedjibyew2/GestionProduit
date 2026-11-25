<?php
$u = utilisateur_courant();
$lien = function (string $page, string $libelle) use ($page_active): string {
    $actif = $page_active === $page ? ' actif' : '';
    $href = $page === 'accueil' ? 'index.php' : 'index.php?page=' . $page;
    return '<a class="nav-lien' . $actif . '" href="' . $href . '">' . e($libelle) . '</a>';
};
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $titre_page ? e($titre_page) . ' · ' : '' ?>GestionProduits</title>
    <meta name="description" content="Gestion de catalogue : produits, catégories et stock.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%230f766e'/><path d='M9 12l7-4 7 4v8l-7 4-7-4z' fill='none' stroke='white' stroke-width='2'/></svg>">
    <link rel="stylesheet" href="vues/public/style.css?v=2">
</head>
<body>
<header class="barre">
    <div class="conteneur barre-int">
        <a class="logo" href="index.php">
            <span class="logo-icone" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 8l8-4.5L20 8v8l-8 4.5L4 16z M4 8l8 4.5L20 8 M12 12.5V20.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            </span>
            GestionProduits
        </a>

        <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Ouvrir le menu">
        <label for="menu-toggle" class="menu-bouton" aria-hidden="true"><span></span></label>

        <nav class="nav" aria-label="Navigation principale">
            <?= $lien('accueil', 'Accueil') ?>
            <?php if ($u): ?>
                <?= $lien('produits', 'Produits') ?>
                <?= $lien('categories', 'Catégories') ?>
                <?php if (est_admin()): ?>
                    <?= $lien('admin', 'Tableau de bord') ?>
                    <?= $lien('utilisateurs', 'Utilisateurs') ?>
                <?php endif; ?>
            <?php endif; ?>

            <div class="nav-droite">
                <?php if ($u): ?>
                    <span class="profil">
                        <span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($u['nom'], 0, 1))) ?></span>
                        <span class="profil-nom"><?= e($u['nom']) ?></span>
                        <span class="pastille <?= $u['role'] === 'admin' ? 'pastille-accent' : '' ?>"><?= $u['role'] === 'admin' ? 'Admin' : 'Membre' ?></span>
                    </span>
                    <a class="btn btn-fantome btn-petit" href="index.php?page=deconnexion">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-fantome btn-petit" href="index.php?page=connexion">Connexion</a>
                    <a class="btn btn-primaire btn-petit" href="index.php?page=inscription">Créer un compte</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="contenu">
    <?php if ($m = lire_flash()): ?>
        <div class="conteneur">
            <div class="alerte alerte-<?= e($m['type']) ?>" role="status">
                <span><?= e($m['texte']) ?></span>
                <button type="button" class="alerte-fermer" aria-label="Fermer" onclick="this.parentElement.remove()">×</button>
            </div>
        </div>
    <?php endif; ?>
