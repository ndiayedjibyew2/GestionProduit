<?php $edition = !empty($categorie['id']); ?>
<section class="conteneur etroit">
    <a class="retour" href="index.php?page=categories">← Catégories</a>
    <div class="panneau">
        <h1><?= $edition ? 'Modifier « ' . e($categorie['nom']) . ' »' : 'Nouvelle catégorie' ?></h1>

        <?php require RACINE . '/vues/partials/erreurs.php'; ?>

        <form method="post" class="formulaire"
              action="index.php?page=categories&action=<?= $edition ? 'modifier&id=' . (int) $categorie['id'] : 'creer' ?>">
            <?= champ_csrf() ?>
            <label class="champ">
                <span>Nom *</span>
                <input type="text" name="nom" value="<?= e($categorie['nom']) ?>" required maxlength="100" placeholder="Ex : Électronique">
            </label>
            <label class="champ">
                <span>Description</span>
                <textarea name="description" rows="3"><?= e($categorie['description']) ?></textarea>
            </label>
            <div class="formulaire-actions">
                <a class="btn btn-fantome" href="index.php?page=categories">Annuler</a>
                <button type="submit" class="btn btn-primaire"><?= $edition ? 'Enregistrer' : 'Ajouter la catégorie' ?></button>
            </div>
        </form>
    </div>
</section>
