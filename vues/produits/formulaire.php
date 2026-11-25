<?php $edition = !empty($produit['id']); ?>
<section class="conteneur etroit">
    <a class="retour" href="index.php?page=produits">← Produits</a>
    <div class="panneau">
        <h1><?= $edition ? 'Modifier « ' . e($produit['nom']) . ' »' : 'Nouveau produit' ?></h1>

        <?php require RACINE . '/vues/partials/erreurs.php'; ?>

        <form method="post" class="formulaire"
              action="index.php?page=produits&action=<?= $edition ? 'modifier&id=' . (int) $produit['id'] : 'creer' ?>">
            <?= champ_csrf() ?>
            <div class="champs-2">
                <label class="champ">
                    <span>Nom du produit *</span>
                    <input type="text" name="nom" value="<?= e($produit['nom']) ?>" required maxlength="150" placeholder="Ex : Riz parfumé 5 kg">
                </label>
                <label class="champ">
                    <span>Catégorie</span>
                    <select name="categorie_id">
                        <option value="">— Aucune —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (string) $produit['categorie_id'] === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <label class="champ">
                <span>Description</span>
                <textarea name="description" rows="4" placeholder="Caractéristiques, provenance…"><?= e($produit['description']) ?></textarea>
            </label>
            <div class="champs-2">
                <label class="champ">
                    <span>Prix (FCFA) *</span>
                    <input type="number" name="prix" value="<?= e($produit['prix']) ?>" required min="1" step="any" inputmode="decimal">
                </label>
                <label class="champ">
                    <span>Quantité en stock *</span>
                    <input type="number" name="quantite" value="<?= e($produit['quantite']) ?>" required min="0" step="1" inputmode="numeric">
                </label>
            </div>
            <div class="formulaire-actions">
                <a class="btn btn-fantome" href="index.php?page=produits">Annuler</a>
                <button type="submit" class="btn btn-primaire"><?= $edition ? 'Enregistrer' : 'Ajouter le produit' ?></button>
            </div>
        </form>
    </div>
</section>
